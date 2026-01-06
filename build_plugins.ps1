$plugins = @(
    'acf-css-really-simple-style-management-center-master',
    'acf-css-neural-link',
    'acf-css-ai-extension',
    'acf-mail-smtp',
    'acf-nudge-flow',
    'wp-bulk-manager',
    'admin-menu-editor-pro',
    'acf-css-woo-license',
    'acf-user-journey-analytics',
    'jj-marketing-automation-dashboard',
    'jj-analytics-dashboard'
)

New-Item -ItemType Directory -Force -Path dist | Out-Null

foreach ($plugin in $plugins) {
    if (Test-Path $plugin) {
        $zipName = "$plugin.zip"
        $zipPath = "dist/$zipName"
        if (Test-Path $zipPath) { Remove-Item $zipPath }
        Compress-Archive -Path $plugin -DestinationPath $zipPath -Force
        Write-Host "Built: $zipName"
    } else {
        Write-Host "Skipped: $plugin (not found)"
    }
}

Write-Host "Build completed!"
