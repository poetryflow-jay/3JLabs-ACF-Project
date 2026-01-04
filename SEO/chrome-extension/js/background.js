/**
 * 3J SEO Analyzer - Background Service Worker
 * 
 * Handles:
 * - WordPress site connection
 * - API communication
 * - Rank tracking schedules
 * - Data synchronization
 */

// Configuration
const API_BASE = 'https://api.3jlabs.com/seo/v1';
const RANK_CHECK_ALARM = 'rank-check-alarm';

// State
let connectedSites = [];
let currentUser = null;

/**
 * Initialize extension
 */
chrome.runtime.onInstalled.addListener(async (details) => {
    console.log('3J SEO Analyzer installed/updated:', details.reason);
    
    // Set default settings
    if (details.reason === 'install') {
        await chrome.storage.sync.set({
            settings: {
                showSerpOverlay: true,
                autoAnalyze: true,
                syncWithWordPress: true,
                rankCheckFrequency: 2, // times per day
                preferredLanguage: 'auto'
            },
            connectedSites: [],
            keywords: []
        });
    }
    
    // Setup rank checking alarm
    setupRankCheckAlarm();
});

/**
 * Setup periodic rank checking
 */
async function setupRankCheckAlarm() {
    const { settings } = await chrome.storage.sync.get('settings');
    const frequency = settings?.rankCheckFrequency || 2;
    
    // Calculate period in minutes (24 hours / frequency)
    const periodInMinutes = Math.floor((24 * 60) / frequency);
    
    chrome.alarms.create(RANK_CHECK_ALARM, {
        periodInMinutes: periodInMinutes,
        delayInMinutes: 1
    });
}

/**
 * Handle alarms
 */
chrome.alarms.onAlarm.addListener(async (alarm) => {
    if (alarm.name === RANK_CHECK_ALARM) {
        await checkAllKeywordRankings();
    }
});

/**
 * Check rankings for all tracked keywords
 */
async function checkAllKeywordRankings() {
    const { keywords, connectedSites } = await chrome.storage.sync.get(['keywords', 'connectedSites']);
    
    if (!keywords || keywords.length === 0) return;
    
    for (const keyword of keywords) {
        try {
            const ranking = await checkKeywordRanking(keyword);
            
            // Save to local storage
            await saveRankingData(keyword.id, ranking);
            
            // Sync with connected WordPress sites
            if (connectedSites && connectedSites.length > 0) {
                await syncRankingToWordPress(connectedSites, keyword, ranking);
            }
        } catch (error) {
            console.error('Error checking ranking for:', keyword.keyword, error);
        }
    }
}

/**
 * Check ranking for a single keyword
 */
async function checkKeywordRanking(keyword) {
    // This would make a real Google search and find the position
    // For now, return mock data structure
    return {
        keyword: keyword.keyword,
        url: keyword.url,
        position: null, // Will be filled by SERP analyzer
        previousPosition: keyword.lastPosition || null,
        checkedAt: new Date().toISOString(),
        searchEngine: 'google',
        country: keyword.country || 'us',
        device: 'desktop'
    };
}

/**
 * Save ranking data to local storage
 */
async function saveRankingData(keywordId, ranking) {
    const { rankingHistory = {} } = await chrome.storage.local.get('rankingHistory');
    
    if (!rankingHistory[keywordId]) {
        rankingHistory[keywordId] = [];
    }
    
    rankingHistory[keywordId].push(ranking);
    
    // Keep only last 90 days of data
    const ninetyDaysAgo = Date.now() - (90 * 24 * 60 * 60 * 1000);
    rankingHistory[keywordId] = rankingHistory[keywordId].filter(r => 
        new Date(r.checkedAt).getTime() > ninetyDaysAgo
    );
    
    await chrome.storage.local.set({ rankingHistory });
}

/**
 * Sync ranking data to WordPress site
 */
async function syncRankingToWordPress(sites, keyword, ranking) {
    for (const site of sites) {
        try {
            const response = await fetch(`${site.url}/wp-json/wp-bulk-seo/v1/rankings`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': site.nonce,
                    'Authorization': `Bearer ${site.token}`
                },
                body: JSON.stringify({
                    keyword_id: keyword.id,
                    keyword: keyword.keyword,
                    url: keyword.url,
                    position: ranking.position,
                    checked_at: ranking.checkedAt
                })
            });
            
            if (!response.ok) {
                console.error('Failed to sync to:', site.url);
            }
        } catch (error) {
            console.error('Error syncing to WordPress:', error);
        }
    }
}

/**
 * Message handler
 */
chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    handleMessage(message, sender).then(sendResponse);
    return true; // Indicates async response
});

/**
 * Handle incoming messages
 */
async function handleMessage(message, sender) {
    switch (message.action) {
        case 'getSettings':
            return await chrome.storage.sync.get('settings');
            
        case 'saveSettings':
            await chrome.storage.sync.set({ settings: message.settings });
            setupRankCheckAlarm(); // Reconfigure alarm
            return { success: true };
            
        case 'connectWordPress':
            return await connectWordPressSite(message.url, message.credentials);
            
        case 'disconnectWordPress':
            return await disconnectWordPressSite(message.siteId);
            
        case 'getConnectedSites':
            return await chrome.storage.sync.get('connectedSites');
            
        case 'addKeyword':
            return await addKeywordToTrack(message.keyword, message.url);
            
        case 'removeKeyword':
            return await removeKeywordFromTrack(message.keywordId);
            
        case 'getKeywords':
            return await chrome.storage.sync.get('keywords');
            
        case 'getRankingHistory':
            return await chrome.storage.local.get('rankingHistory');
            
        case 'analyzeSerpResult':
            return await analyzeSerpResult(message.url);
            
        case 'syncToWordPress':
            return await syncDataToWordPress(message.data);
            
        case 'getCompetitorData':
            return await getCompetitorSEOData(message.urls);
            
        default:
            return { error: 'Unknown action' };
    }
}

/**
 * Connect a WordPress site
 */
async function connectWordPressSite(url, credentials) {
    try {
        // Validate WordPress site has our plugin
        const response = await fetch(`${url}/wp-json/wp-bulk-seo/v1/connect`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: credentials.email,
                password: credentials.password,
                extension_id: chrome.runtime.id
            })
        });
        
        if (!response.ok) {
            throw new Error('Connection failed');
        }
        
        const data = await response.json();
        
        // Save connection
        const { connectedSites = [] } = await chrome.storage.sync.get('connectedSites');
        
        const newSite = {
            id: generateId(),
            url: url,
            name: data.site_name,
            token: data.token,
            nonce: data.nonce,
            connectedAt: new Date().toISOString()
        };
        
        connectedSites.push(newSite);
        await chrome.storage.sync.set({ connectedSites });
        
        return { success: true, site: newSite };
    } catch (error) {
        return { success: false, error: error.message };
    }
}

/**
 * Disconnect a WordPress site
 */
async function disconnectWordPressSite(siteId) {
    const { connectedSites = [] } = await chrome.storage.sync.get('connectedSites');
    
    const updatedSites = connectedSites.filter(s => s.id !== siteId);
    await chrome.storage.sync.set({ connectedSites: updatedSites });
    
    return { success: true };
}

/**
 * Add keyword to track
 */
async function addKeywordToTrack(keyword, url) {
    const { keywords = [] } = await chrome.storage.sync.get('keywords');
    
    const newKeyword = {
        id: generateId(),
        keyword: keyword,
        url: url,
        addedAt: new Date().toISOString(),
        lastPosition: null,
        lastChecked: null
    };
    
    keywords.push(newKeyword);
    await chrome.storage.sync.set({ keywords });
    
    return { success: true, keyword: newKeyword };
}

/**
 * Remove keyword from tracking
 */
async function removeKeywordFromTrack(keywordId) {
    const { keywords = [] } = await chrome.storage.sync.get('keywords');
    
    const updatedKeywords = keywords.filter(k => k.id !== keywordId);
    await chrome.storage.sync.set({ keywords: updatedKeywords });
    
    // Also remove history
    const { rankingHistory = {} } = await chrome.storage.local.get('rankingHistory');
    delete rankingHistory[keywordId];
    await chrome.storage.local.set({ rankingHistory });
    
    return { success: true };
}

/**
 * Analyze a SERP result URL
 */
async function analyzeSerpResult(url) {
    try {
        // Fetch page data
        const response = await fetch(url);
        const html = await response.text();
        
        // Parse HTML
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Extract SEO data
        const seoData = {
            url: url,
            title: doc.querySelector('title')?.textContent || '',
            metaDescription: doc.querySelector('meta[name="description"]')?.content || '',
            h1: doc.querySelector('h1')?.textContent || '',
            h2Count: doc.querySelectorAll('h2').length,
            h3Count: doc.querySelectorAll('h3').length,
            imageCount: doc.querySelectorAll('img').length,
            imagesWithAlt: doc.querySelectorAll('img[alt]:not([alt=""])').length,
            internalLinks: 0,
            externalLinks: 0,
            wordCount: 0,
            hasSchema: doc.querySelector('script[type="application/ld+json"]') !== null,
            canonical: doc.querySelector('link[rel="canonical"]')?.href || '',
            robots: doc.querySelector('meta[name="robots"]')?.content || '',
            analyzedAt: new Date().toISOString()
        };
        
        // Count words
        const bodyText = doc.body?.textContent || '';
        seoData.wordCount = bodyText.split(/\s+/).filter(w => w.length > 0).length;
        
        // Count links
        const links = doc.querySelectorAll('a[href]');
        const urlObj = new URL(url);
        links.forEach(link => {
            try {
                const linkUrl = new URL(link.href, url);
                if (linkUrl.hostname === urlObj.hostname) {
                    seoData.internalLinks++;
                } else {
                    seoData.externalLinks++;
                }
            } catch (e) {}
        });
        
        return { success: true, data: seoData };
    } catch (error) {
        return { success: false, error: error.message };
    }
}

/**
 * Get SEO data for competitor URLs
 */
async function getCompetitorSEOData(urls) {
    const results = [];
    
    for (const url of urls) {
        const analysis = await analyzeSerpResult(url);
        if (analysis.success) {
            results.push(analysis.data);
        }
    }
    
    return { success: true, competitors: results };
}

/**
 * Sync data to connected WordPress sites
 */
async function syncDataToWordPress(data) {
    const { connectedSites = [] } = await chrome.storage.sync.get('connectedSites');
    
    if (connectedSites.length === 0) {
        return { success: false, error: 'No connected sites' };
    }
    
    const results = [];
    
    for (const site of connectedSites) {
        try {
            const response = await fetch(`${site.url}/wp-json/wp-bulk-seo/v1/sync`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${site.token}`
                },
                body: JSON.stringify(data)
            });
            
            results.push({
                site: site.url,
                success: response.ok
            });
        } catch (error) {
            results.push({
                site: site.url,
                success: false,
                error: error.message
            });
        }
    }
    
    return { success: true, results };
}

/**
 * Generate unique ID
 */
function generateId() {
    return Date.now().toString(36) + Math.random().toString(36).substr(2);
}

/**
 * Context menu for quick actions
 */
chrome.runtime.onInstalled.addListener(() => {
    chrome.contextMenus.create({
        id: 'analyze-page',
        title: 'Analyze this page with 3J SEO',
        contexts: ['page']
    });
    
    chrome.contextMenus.create({
        id: 'analyze-link',
        title: 'Analyze linked page',
        contexts: ['link']
    });
    
    chrome.contextMenus.create({
        id: 'track-keyword',
        title: 'Track this keyword',
        contexts: ['selection']
    });
});

chrome.contextMenus.onClicked.addListener(async (info, tab) => {
    switch (info.menuItemId) {
        case 'analyze-page':
            const pageAnalysis = await analyzeSerpResult(tab.url);
            // Send to popup or sidebar
            chrome.runtime.sendMessage({ action: 'showAnalysis', data: pageAnalysis });
            break;
            
        case 'analyze-link':
            const linkAnalysis = await analyzeSerpResult(info.linkUrl);
            chrome.runtime.sendMessage({ action: 'showAnalysis', data: linkAnalysis });
            break;
            
        case 'track-keyword':
            const keyword = info.selectionText.trim();
            if (keyword) {
                await addKeywordToTrack(keyword, tab.url);
                // Notify user
                chrome.action.setBadgeText({ text: '+1' });
                setTimeout(() => chrome.action.setBadgeText({ text: '' }), 2000);
            }
            break;
    }
});
