/**
 * 3J SEO Analyzer - Options Page Script
 */

document.addEventListener('DOMContentLoaded', async () => {
    await loadSettings();
    await loadSites();
    bindEvents();
});

/**
 * Load saved settings
 */
async function loadSettings() {
    const response = await chrome.runtime.sendMessage({ action: 'getSettings' });
    const settings = response.settings || {};

    document.getElementById('show-serp-overlay').checked = settings.showSerpOverlay !== false;
    document.getElementById('auto-analyze').checked = settings.autoAnalyze !== false;
    document.getElementById('sync-wordpress').checked = settings.syncWithWordPress !== false;
    document.getElementById('rank-frequency').value = settings.rankCheckFrequency || 2;
    document.getElementById('search-country').value = settings.searchCountry || 'us';
}

/**
 * Load connected sites
 */
async function loadSites() {
    const response = await chrome.runtime.sendMessage({ action: 'getConnectedSites' });
    const sites = response.connectedSites || [];

    const listEl = document.getElementById('sites-list');

    if (sites.length === 0) {
        listEl.innerHTML = '<div class="no-sites">No sites connected</div>';
        return;
    }

    listEl.innerHTML = sites.map(site => `
        <div class="site-item" data-id="${site.id}">
            <div>
                <div class="site-name">${site.name || 'WordPress Site'}</div>
                <div class="site-url">${site.url}</div>
            </div>
            <button class="btn btn-secondary disconnect-btn" data-id="${site.id}">Disconnect</button>
        </div>
    `).join('');

    // Bind disconnect handlers
    listEl.querySelectorAll('.disconnect-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const id = e.target.dataset.id;
            if (confirm('Disconnect this site?')) {
                await chrome.runtime.sendMessage({ action: 'disconnectWordPress', siteId: id });
                loadSites();
            }
        });
    });
}

/**
 * Bind event handlers
 */
function bindEvents() {
    // Save settings
    document.getElementById('save-settings').addEventListener('click', saveSettings);

    // Export data
    document.getElementById('export-data').addEventListener('click', exportData);

    // Import data
    document.getElementById('import-data').addEventListener('click', () => {
        document.getElementById('import-file').click();
    });
    document.getElementById('import-file').addEventListener('change', importData);

    // Clear data
    document.getElementById('clear-data').addEventListener('click', clearData);
}

/**
 * Save settings
 */
async function saveSettings() {
    const settings = {
        showSerpOverlay: document.getElementById('show-serp-overlay').checked,
        autoAnalyze: document.getElementById('auto-analyze').checked,
        syncWithWordPress: document.getElementById('sync-wordpress').checked,
        rankCheckFrequency: parseInt(document.getElementById('rank-frequency').value),
        searchCountry: document.getElementById('search-country').value
    };

    await chrome.runtime.sendMessage({ action: 'saveSettings', settings });

    const statusEl = document.getElementById('save-status');
    statusEl.textContent = 'Settings saved!';
    setTimeout(() => statusEl.textContent = '', 3000);
}

/**
 * Export all data
 */
async function exportData() {
    const [keywords, history, sites] = await Promise.all([
        chrome.runtime.sendMessage({ action: 'getKeywords' }),
        chrome.runtime.sendMessage({ action: 'getRankingHistory' }),
        chrome.runtime.sendMessage({ action: 'getConnectedSites' })
    ]);

    const data = {
        exportedAt: new Date().toISOString(),
        version: '1.0.0',
        keywords: keywords.keywords || [],
        rankingHistory: history.rankingHistory || {},
        sites: (sites.connectedSites || []).map(s => ({ url: s.url, name: s.name }))
    };

    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = `3j-seo-data-${new Date().toISOString().split('T')[0]}.json`;
    a.click();

    URL.revokeObjectURL(url);
}

/**
 * Import data
 */
async function importData(e) {
    const file = e.target.files[0];
    if (!file) return;

    try {
        const text = await file.text();
        const data = JSON.parse(text);

        if (!data.keywords || !data.version) {
            throw new Error('Invalid data format');
        }

        // Import keywords
        for (const kw of data.keywords) {
            await chrome.runtime.sendMessage({
                action: 'addKeyword',
                keyword: kw.keyword,
                url: kw.url
            });
        }

        alert(`Imported ${data.keywords.length} keywords successfully!`);
        e.target.value = '';
    } catch (error) {
        alert('Error importing data: ' + error.message);
    }
}

/**
 * Clear all data
 */
async function clearData() {
    if (!confirm('Are you sure you want to clear all data? This cannot be undone.')) {
        return;
    }

    if (!confirm('This will remove all keywords, ranking history, and site connections. Continue?')) {
        return;
    }

    await chrome.storage.sync.set({ keywords: [], connectedSites: [] });
    await chrome.storage.local.set({ rankingHistory: {} });

    loadSites();
    alert('All data cleared');
}
