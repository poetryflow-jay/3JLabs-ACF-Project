/**
 * 3J SEO Analyzer - Popup Script
 */

document.addEventListener('DOMContentLoaded', async () => {
    // Initialize
    await init();

    // Tab switching
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => switchTab(tab.dataset.tab));
    });

    // Analyze button
    document.getElementById('analyze-current').addEventListener('click', analyzePage);

    // Add keyword
    document.getElementById('add-keyword-btn').addEventListener('click', addKeyword);
    document.getElementById('new-keyword').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') addKeyword();
    });

    // Connect site
    document.getElementById('connect-site-btn').addEventListener('click', connectSite);

    // Settings
    document.getElementById('open-settings').addEventListener('click', () => {
        chrome.runtime.openOptionsPage();
    });

    // Quick actions
    document.getElementById('sync-to-wp')?.addEventListener('click', syncToWordPress);
    document.getElementById('track-keyword')?.addEventListener('click', trackCurrentKeyword);
});

/**
 * Initialize popup
 */
async function init() {
    // Get current tab URL
    const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });
    if (tab) {
        document.getElementById('page-url').textContent = tab.url;
    }

    // Load connection status
    await updateConnectionStatus();

    // Load keywords
    await loadKeywords();

    // Load connected sites
    await loadSites();
}

/**
 * Switch tab
 */
function switchTab(tabId) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

    document.querySelector(`.tab[data-tab="${tabId}"]`).classList.add('active');
    document.getElementById(`tab-${tabId}`).classList.add('active');
}

/**
 * Update connection status
 */
async function updateConnectionStatus() {
    const response = await chrome.runtime.sendMessage({ action: 'getConnectedSites' });
    const sites = response.connectedSites || [];

    const statusEl = document.getElementById('connection-status');
    const dot = statusEl.querySelector('.status-dot');
    const text = statusEl.querySelector('span');

    if (sites.length > 0) {
        dot.classList.remove('disconnected');
        dot.classList.add('connected');
        text.textContent = `Connected to ${sites.length} site${sites.length > 1 ? 's' : ''}`;
    } else {
        dot.classList.remove('connected');
        dot.classList.add('disconnected');
        text.textContent = 'Not connected';
    }
}

/**
 * Analyze current page
 */
async function analyzePage() {
    const btn = document.getElementById('analyze-current');
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<span class="loading-spinner"></span> Analyzing...';
    btn.disabled = true;

    try {
        const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });
        
        const response = await chrome.runtime.sendMessage({
            action: 'analyzeSerpResult',
            url: tab.url
        });

        if (response.success) {
            displayResults(response.data);
        } else {
            alert('Analysis failed: ' + (response.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Analysis error:', error);
        alert('Error analyzing page');
    } finally {
        btn.innerHTML = originalContent;
        btn.disabled = false;
    }
}

/**
 * Display analysis results
 */
function displayResults(data) {
    document.querySelector('.current-page').style.display = 'none';
    document.getElementById('analysis-results').style.display = 'block';

    // Calculate score
    const score = calculateScore(data);
    const grade = getGrade(score);
    const gradeClass = `grade-${grade.toLowerCase().replace('+', '-plus')}`;

    // Update score display
    const circle = document.getElementById('score-circle');
    circle.style.setProperty('--score', score);
    circle.style.setProperty('--score-color', getScoreColor(score));
    document.getElementById('score-value').textContent = score;

    const gradeEl = document.getElementById('score-grade');
    gradeEl.textContent = grade;
    gradeEl.className = `score-grade ${gradeClass}`;

    // Update metrics
    const metricsHtml = `
        <div class="metric-item">
            <div class="metric-label">Title</div>
            <div class="metric-value ${data.title ? 'good' : 'bad'}">${data.title ? data.title.length + ' chars' : 'Missing'}</div>
        </div>
        <div class="metric-item">
            <div class="metric-label">Description</div>
            <div class="metric-value ${data.metaDescription ? 'good' : 'bad'}">${data.metaDescription ? data.metaDescription.length + ' chars' : 'Missing'}</div>
        </div>
        <div class="metric-item">
            <div class="metric-label">Word Count</div>
            <div class="metric-value ${data.wordCount >= 800 ? 'good' : data.wordCount >= 300 ? 'warning' : 'bad'}">${data.wordCount}</div>
        </div>
        <div class="metric-item">
            <div class="metric-label">Images</div>
            <div class="metric-value">${data.imagesWithAlt}/${data.imageCount} with alt</div>
        </div>
        <div class="metric-item">
            <div class="metric-label">Internal Links</div>
            <div class="metric-value ${data.internalLinks >= 3 ? 'good' : 'warning'}">${data.internalLinks}</div>
        </div>
        <div class="metric-item">
            <div class="metric-label">Schema</div>
            <div class="metric-value ${data.hasSchema ? 'good' : 'warning'}">${data.hasSchema ? 'Yes' : 'No'}</div>
        </div>
    `;
    document.getElementById('metrics-grid').innerHTML = metricsHtml;

    // Store for sync
    window.currentAnalysis = data;
}

/**
 * Calculate SEO score
 */
function calculateScore(data) {
    let score = 0;

    // Title
    if (data.title) {
        score += data.title.length >= 30 && data.title.length <= 60 ? 15 : 8;
    }

    // Description
    if (data.metaDescription) {
        score += data.metaDescription.length >= 120 && data.metaDescription.length <= 160 ? 15 : 8;
    }

    // H1
    if (data.h1) score += 10;

    // Headings
    if (data.h2Count >= 2) score += 10;

    // Content
    if (data.wordCount >= 1500) score += 15;
    else if (data.wordCount >= 800) score += 10;
    else if (data.wordCount >= 300) score += 5;

    // Images
    if (data.imageCount > 0 && data.imagesWithAlt / data.imageCount >= 0.9) score += 10;

    // Links
    if (data.internalLinks >= 3) score += 10;

    // Schema
    if (data.hasSchema) score += 10;

    return Math.min(100, score);
}

/**
 * Get grade from score
 */
function getGrade(score) {
    if (score >= 90) return 'A+';
    if (score >= 80) return 'A';
    if (score >= 70) return 'B';
    if (score >= 60) return 'C';
    if (score >= 50) return 'D';
    return 'F';
}

/**
 * Get score color
 */
function getScoreColor(score) {
    if (score >= 80) return '#00a32a';
    if (score >= 60) return '#dba617';
    return '#d63638';
}

/**
 * Load keywords
 */
async function loadKeywords() {
    const response = await chrome.runtime.sendMessage({ action: 'getKeywords' });
    const keywords = response.keywords || [];

    const listEl = document.getElementById('keywords-list');

    if (keywords.length === 0) {
        listEl.innerHTML = `
            <div class="empty-state">
                <p>No keywords tracked yet</p>
                <small>Add keywords to monitor their rankings</small>
            </div>
        `;
        return;
    }

    const historyResponse = await chrome.runtime.sendMessage({ action: 'getRankingHistory' });
    const history = historyResponse.rankingHistory || {};

    listEl.innerHTML = keywords.map(kw => {
        const kwHistory = history[kw.id] || [];
        const latest = kwHistory[kwHistory.length - 1];
        const previous = kwHistory[kwHistory.length - 2];
        
        let change = 0;
        let changeClass = 'same';
        if (latest && previous && latest.position && previous.position) {
            change = previous.position - latest.position;
            changeClass = change > 0 ? 'up' : change < 0 ? 'down' : 'same';
        }

        return `
            <div class="keyword-item" data-id="${kw.id}">
                <div class="keyword-rank">
                    <span class="rank-position">${latest?.position || '-'}</span>
                    <span class="rank-change ${changeClass}">
                        ${change > 0 ? '↑' : change < 0 ? '↓' : '−'}${Math.abs(change) || ''}
                    </span>
                </div>
                <div class="keyword-info">
                    <div class="keyword-text">${kw.keyword}</div>
                    <div class="keyword-url">${kw.url}</div>
                </div>
                <button class="remove-keyword" data-id="${kw.id}">×</button>
            </div>
        `;
    }).join('');

    // Remove handlers
    listEl.querySelectorAll('.remove-keyword').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const id = e.target.dataset.id;
            await chrome.runtime.sendMessage({ action: 'removeKeyword', keywordId: id });
            loadKeywords();
        });
    });
}

/**
 * Add keyword
 */
async function addKeyword() {
    const input = document.getElementById('new-keyword');
    const keyword = input.value.trim();

    if (!keyword) return;

    const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });

    await chrome.runtime.sendMessage({
        action: 'addKeyword',
        keyword: keyword,
        url: tab?.url || ''
    });

    input.value = '';
    loadKeywords();
}

/**
 * Load connected sites
 */
async function loadSites() {
    const response = await chrome.runtime.sendMessage({ action: 'getConnectedSites' });
    const sites = response.connectedSites || [];

    const listEl = document.getElementById('sites-list');

    if (sites.length === 0) {
        listEl.innerHTML = `
            <div class="empty-state">
                <p>No sites connected</p>
                <small>Connect your WordPress site with WP Bulk SEO plugin</small>
            </div>
        `;
        return;
    }

    listEl.innerHTML = sites.map(site => `
        <div class="site-item" data-id="${site.id}">
            <div class="site-status"></div>
            <div class="site-info">
                <div class="site-name">${site.name || 'WordPress Site'}</div>
                <div class="site-url">${site.url}</div>
            </div>
            <button class="disconnect-site" data-id="${site.id}">Disconnect</button>
        </div>
    `).join('');

    // Disconnect handlers
    listEl.querySelectorAll('.disconnect-site').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const id = e.target.dataset.id;
            if (confirm('Disconnect this site?')) {
                await chrome.runtime.sendMessage({ action: 'disconnectWordPress', siteId: id });
                loadSites();
                updateConnectionStatus();
            }
        });
    });
}

/**
 * Connect site
 */
async function connectSite() {
    const input = document.getElementById('site-url');
    const url = input.value.trim();

    if (!url) return;

    const btn = document.getElementById('connect-site-btn');
    const originalText = btn.textContent;
    btn.textContent = 'Connecting...';
    btn.disabled = true;

    try {
        // Prompt for credentials
        const email = prompt('Enter your WordPress email:');
        if (!email) return;

        const password = prompt('Enter your password:');
        if (!password) return;

        const response = await chrome.runtime.sendMessage({
            action: 'connectWordPress',
            url: url,
            credentials: { email, password }
        });

        if (response.success) {
            input.value = '';
            loadSites();
            updateConnectionStatus();
            alert('Site connected successfully!');
        } else {
            alert('Connection failed: ' + (response.error || 'Unknown error'));
        }
    } catch (error) {
        alert('Error connecting: ' + error.message);
    } finally {
        btn.textContent = originalText;
        btn.disabled = false;
    }
}

/**
 * Sync to WordPress
 */
async function syncToWordPress() {
    if (!window.currentAnalysis) {
        alert('Analyze a page first');
        return;
    }

    const response = await chrome.runtime.sendMessage({
        action: 'syncToWordPress',
        data: window.currentAnalysis
    });

    if (response.success) {
        alert('Synced to WordPress!');
    } else {
        alert('Sync failed: ' + (response.error || 'No connected sites'));
    }
}

/**
 * Track current keyword
 */
async function trackCurrentKeyword() {
    const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });
    const url = new URL(tab.url);
    const keyword = url.searchParams.get('q');

    if (keyword) {
        await chrome.runtime.sendMessage({
            action: 'addKeyword',
            keyword: keyword,
            url: tab.url
        });
        switchTab('keywords');
        loadKeywords();
    } else {
        const keyword = prompt('Enter keyword to track:');
        if (keyword) {
            await chrome.runtime.sendMessage({
                action: 'addKeyword',
                keyword: keyword,
                url: tab.url
            });
            switchTab('keywords');
            loadKeywords();
        }
    }
}
