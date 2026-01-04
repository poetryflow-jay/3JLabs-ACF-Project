/**
 * 3J SEO Analyzer - SERP Content Script
 * 
 * Analyzes Google search results in real-time
 * Displays SEO metrics overlay on each result
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        selectors: {
            searchResults: '#search .g',
            resultLink: 'a[href^="http"]',
            resultTitle: 'h3',
            resultDescription: '.VwiC3b',
            peopleAlsoAsk: '.related-question-pair'
        },
        debounceDelay: 300
    };

    // State
    let analyzedResults = new Map();
    let currentKeyword = '';
    let settings = null;

    /**
     * Initialize SERP analyzer
     */
    async function init() {
        // Get settings
        const response = await chrome.runtime.sendMessage({ action: 'getSettings' });
        settings = response.settings || {};

        if (!settings.showSerpOverlay) {
            return;
        }

        // Extract current keyword
        currentKeyword = extractKeyword();

        // Analyze SERP
        await analyzeSERP();

        // Watch for dynamic content changes
        observeSerpChanges();
    }

    /**
     * Extract search keyword from URL
     */
    function extractKeyword() {
        const params = new URLSearchParams(window.location.search);
        return params.get('q') || '';
    }

    /**
     * Analyze all SERP results
     */
    async function analyzeSERP() {
        const results = document.querySelectorAll(CONFIG.selectors.searchResults);
        const serpData = {
            keyword: currentKeyword,
            totalResults: results.length,
            analyzedAt: new Date().toISOString(),
            results: []
        };

        let position = 0;
        
        for (const result of results) {
            const link = result.querySelector(CONFIG.selectors.resultLink);
            if (!link || !link.href || link.href.startsWith('javascript:')) continue;

            position++;
            const url = link.href;
            
            // Skip already analyzed
            if (analyzedResults.has(url)) {
                continue;
            }

            // Create overlay container
            const overlay = createOverlay(position);
            result.style.position = 'relative';
            result.appendChild(overlay);

            // Analyze in background
            analyzeResult(url, position, overlay).then(data => {
                if (data) {
                    serpData.results.push(data);
                    analyzedResults.set(url, data);
                }
            });
        }

        // Extract SERP features
        const serpFeatures = extractSerpFeatures();
        
        // Send data to background
        await chrome.runtime.sendMessage({
            action: 'serpAnalyzed',
            data: {
                ...serpData,
                features: serpFeatures
            }
        });
    }

    /**
     * Create overlay element
     */
    function createOverlay(position) {
        const overlay = document.createElement('div');
        overlay.className = 'jjseo-overlay';
        overlay.innerHTML = `
            <div class="jjseo-position">#${position}</div>
            <div class="jjseo-metrics">
                <div class="jjseo-loading">
                    <span class="jjseo-spinner"></span>
                    Analyzing...
                </div>
            </div>
            <div class="jjseo-expand-btn" title="View details">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </div>
        `;

        // Expand button click
        overlay.querySelector('.jjseo-expand-btn').addEventListener('click', (e) => {
            e.stopPropagation();
            overlay.classList.toggle('jjseo-expanded');
        });

        return overlay;
    }

    /**
     * Analyze a single search result
     */
    async function analyzeResult(url, position, overlay) {
        try {
            const response = await chrome.runtime.sendMessage({
                action: 'analyzeSerpResult',
                url: url
            });

            if (!response.success) {
                showError(overlay, 'Analysis failed');
                return null;
            }

            const data = response.data;
            data.position = position;

            // Calculate SEO score
            const score = calculateSEOScore(data);
            data.seoScore = score;

            // Update overlay
            updateOverlay(overlay, data, score);

            return data;
        } catch (error) {
            console.error('Error analyzing:', url, error);
            showError(overlay, 'Error');
            return null;
        }
    }

    /**
     * Calculate SEO score based on metrics
     */
    function calculateSEOScore(data) {
        let score = 0;
        const factors = [];

        // Title (0-15 points)
        if (data.title) {
            const titleLength = data.title.length;
            if (titleLength >= 30 && titleLength <= 60) {
                score += 15;
                factors.push({ name: 'Title length', status: 'good' });
            } else if (titleLength > 0) {
                score += 8;
                factors.push({ name: 'Title length', status: 'warning' });
            } else {
                factors.push({ name: 'Title missing', status: 'bad' });
            }

            // Keyword in title
            if (currentKeyword && data.title.toLowerCase().includes(currentKeyword.toLowerCase())) {
                score += 5;
                factors.push({ name: 'Keyword in title', status: 'good' });
            }
        }

        // Meta description (0-15 points)
        if (data.metaDescription) {
            const descLength = data.metaDescription.length;
            if (descLength >= 120 && descLength <= 160) {
                score += 15;
                factors.push({ name: 'Description length', status: 'good' });
            } else if (descLength > 0) {
                score += 8;
                factors.push({ name: 'Description length', status: 'warning' });
            }
        } else {
            factors.push({ name: 'Description missing', status: 'bad' });
        }

        // H1 (0-10 points)
        if (data.h1) {
            score += 10;
            factors.push({ name: 'H1 present', status: 'good' });
        } else {
            factors.push({ name: 'H1 missing', status: 'bad' });
        }

        // Heading structure (0-10 points)
        if (data.h2Count >= 2 && data.h3Count >= 1) {
            score += 10;
            factors.push({ name: 'Good heading structure', status: 'good' });
        } else if (data.h2Count >= 1) {
            score += 5;
            factors.push({ name: 'Basic heading structure', status: 'warning' });
        }

        // Images (0-10 points)
        if (data.imageCount > 0) {
            const altRatio = data.imagesWithAlt / data.imageCount;
            if (altRatio >= 0.9) {
                score += 10;
                factors.push({ name: 'Images with alt', status: 'good' });
            } else if (altRatio >= 0.5) {
                score += 5;
                factors.push({ name: 'Some images missing alt', status: 'warning' });
            } else {
                factors.push({ name: 'Images missing alt', status: 'bad' });
            }
        }

        // Content length (0-15 points)
        if (data.wordCount >= 1500) {
            score += 15;
            factors.push({ name: 'Comprehensive content', status: 'good' });
        } else if (data.wordCount >= 800) {
            score += 10;
            factors.push({ name: 'Good content length', status: 'good' });
        } else if (data.wordCount >= 300) {
            score += 5;
            factors.push({ name: 'Short content', status: 'warning' });
        } else {
            factors.push({ name: 'Thin content', status: 'bad' });
        }

        // Links (0-10 points)
        if (data.internalLinks >= 3 && data.externalLinks >= 1) {
            score += 10;
            factors.push({ name: 'Good link structure', status: 'good' });
        } else if (data.internalLinks >= 1) {
            score += 5;
            factors.push({ name: 'Basic linking', status: 'warning' });
        }

        // Schema markup (0-10 points)
        if (data.hasSchema) {
            score += 10;
            factors.push({ name: 'Schema markup', status: 'good' });
        } else {
            factors.push({ name: 'No schema markup', status: 'warning' });
        }

        return {
            score: Math.min(100, score),
            grade: getGrade(score),
            factors: factors
        };
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
     * Update overlay with analysis data
     */
    function updateOverlay(overlay, data, scoreData) {
        const gradeClass = `grade-${scoreData.grade.toLowerCase().replace('+', '-plus')}`;
        
        overlay.innerHTML = `
            <div class="jjseo-position">#${data.position}</div>
            <div class="jjseo-score ${gradeClass}">
                <span class="jjseo-score-value">${scoreData.score}</span>
                <span class="jjseo-score-grade">${scoreData.grade}</span>
            </div>
            <div class="jjseo-quick-metrics">
                <span class="jjseo-metric" title="Word count">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                    </svg>
                    ${formatNumber(data.wordCount)}
                </span>
                <span class="jjseo-metric" title="Images">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                    </svg>
                    ${data.imageCount}
                </span>
                <span class="jjseo-metric ${data.hasSchema ? 'jjseo-good' : ''}" title="Schema">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/>
                    </svg>
                </span>
            </div>
            <div class="jjseo-expand-btn" title="View details">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </div>
            <div class="jjseo-details">
                <div class="jjseo-detail-row">
                    <span class="jjseo-label">Title</span>
                    <span class="jjseo-value">${data.title ? data.title.substring(0, 50) + '...' : 'Missing'}</span>
                </div>
                <div class="jjseo-detail-row">
                    <span class="jjseo-label">H1</span>
                    <span class="jjseo-value">${data.h1 ? data.h1.substring(0, 40) + '...' : 'Missing'}</span>
                </div>
                <div class="jjseo-detail-row">
                    <span class="jjseo-label">Headers</span>
                    <span class="jjseo-value">H2: ${data.h2Count}, H3: ${data.h3Count}</span>
                </div>
                <div class="jjseo-detail-row">
                    <span class="jjseo-label">Links</span>
                    <span class="jjseo-value">Int: ${data.internalLinks}, Ext: ${data.externalLinks}</span>
                </div>
                <div class="jjseo-factors">
                    ${scoreData.factors.map(f => `
                        <span class="jjseo-factor jjseo-${f.status}">${f.name}</span>
                    `).join('')}
                </div>
                <div class="jjseo-actions">
                    <button class="jjseo-btn jjseo-track-btn" data-url="${data.url}">
                        Track Keyword
                    </button>
                    <button class="jjseo-btn jjseo-compare-btn" data-url="${data.url}">
                        Compare
                    </button>
                </div>
            </div>
        `;

        // Re-attach event listeners
        overlay.querySelector('.jjseo-expand-btn').addEventListener('click', (e) => {
            e.stopPropagation();
            overlay.classList.toggle('jjseo-expanded');
        });

        overlay.querySelector('.jjseo-track-btn')?.addEventListener('click', async (e) => {
            e.stopPropagation();
            await chrome.runtime.sendMessage({
                action: 'addKeyword',
                keyword: currentKeyword,
                url: data.url
            });
            e.target.textContent = 'Tracked!';
            e.target.disabled = true;
        });

        overlay.querySelector('.jjseo-compare-btn')?.addEventListener('click', (e) => {
            e.stopPropagation();
            chrome.runtime.sendMessage({
                action: 'openCompare',
                urls: Array.from(analyzedResults.keys())
            });
        });
    }

    /**
     * Show error in overlay
     */
    function showError(overlay, message) {
        overlay.querySelector('.jjseo-metrics').innerHTML = `
            <span class="jjseo-error">${message}</span>
        `;
    }

    /**
     * Extract SERP features
     */
    function extractSerpFeatures() {
        const features = {
            featuredSnippet: !!document.querySelector('.xpdopen'),
            peopleAlsoAsk: document.querySelectorAll(CONFIG.selectors.peopleAlsoAsk).length,
            knowledgePanel: !!document.querySelector('.kp-wholepage'),
            localPack: !!document.querySelector('.AEprdc'),
            imageCarousel: !!document.querySelector('.immersive-carousel'),
            videoResults: document.querySelectorAll('.RzdJxc').length,
            newsResults: !!document.querySelector('.IJl0Z'),
            shoppingResults: !!document.querySelector('.commercial-unit-desktop-top'),
            sitelinks: document.querySelectorAll('.HiHjCd').length
        };

        // Extract PAA questions
        const paaQuestions = [];
        document.querySelectorAll(CONFIG.selectors.peopleAlsoAsk).forEach(q => {
            const question = q.querySelector('.JlqpRe')?.textContent;
            if (question) paaQuestions.push(question);
        });
        features.paaQuestions = paaQuestions;

        return features;
    }

    /**
     * Observe SERP changes for infinite scroll
     */
    function observeSerpChanges() {
        const observer = new MutationObserver(debounce(() => {
            analyzeSERP();
        }, CONFIG.debounceDelay));

        const searchContainer = document.querySelector('#search');
        if (searchContainer) {
            observer.observe(searchContainer, {
                childList: true,
                subtree: true
            });
        }
    }

    /**
     * Utility: Debounce function
     */
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    /**
     * Utility: Format number
     */
    function formatNumber(num) {
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'k';
        }
        return num.toString();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
