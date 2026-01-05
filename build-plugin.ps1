# ==============================================================================
# 3J Labs Plugin Builder - 범용 빌드 스크립트
# ==============================================================================
#
# 사용법:
#   .\build-plugin.ps1 <plugin-name>
#   .\build-plugin.ps1 all
#
# 예시:
#   .\build-plugin.ps1 neural-link
#   .\build-plugin.ps1 acf-css
#   .\build-plugin.ps1 wp-bulk-manager
#   .\build-plugin.ps1 all
#
# 빌드 원칙:
#   1. ZIP 파일명에 버전 번호 포함하지 않음
#   2. 내부 폴더명도 버전 번호 없이 고정
#   3. WordPress 표준 플러그인 구조 유지
#   4. 버전은 플러그인 헤더에서만 관리
#
# ==============================================================================

param(
    [Parameter(Position=0)]
    [string]$PluginName = ""
)

# 플러그인 정의 (소스 폴더 => ZIP 내부 폴더명)
$plugins = @{
    "acf-css" = @{
        "source" = "acf-css-really-simple-style-management-center-master"
        "target" = "acf-css-really-simple-style-management-center-master"
        "zip"    = "acf-css-master.zip"
    }
    "neural-link" = @{
        "source" = "acf-css-neural-link"
        "target" = "jj-neural-link"
        "zip"    = "jj-neural-link.zip"
        "rename" = @{ "acf-css-neural-link.php" = "jj-neural-link.php" }
    }
    "wp-bulk-manager" = @{
        "source" = "wp-bulk-manager"
        "target" = "wp-bulk-manager"
        "zip"    = "wp-bulk-manager.zip"
    }
    "code-snippets" = @{
        "source" = "acf-code-snippets-box"
        "target" = "acf-code-snippets-box"
        "zip"    = "acf-code-snippets-box.zip"
    }
    "marketing-dashboard" = @{
        "source" = "jj-marketing-automation-dashboard"
        "target" = "jj-marketing-automation-dashboard"
        "zip"    = "jj-marketing-automation-dashboard.zip"
    }
    "nudge-flow" = @{
        "source" = "acf-nudge-flow"
        "target" = "acf-nudge-flow"
        "zip"    = "acf-nudge-flow.zip"
    }
    "user-journey" = @{
        "source" = "acf-user-journey-analytics"
        "target" = "acf-user-journey-analytics"
        "zip"    = "acf-user-journey-analytics.zip"
    }
    "mail-smtp" = @{
        "source" = "acf-mail-smtp"
        "target" = "acf-mail-smtp"
        "zip"    = "acf-mail-smtp.zip"
    }
    "woo-toolkit" = @{
        "source" = "acf-css-woocommerce-toolkit"
        "target" = "acf-css-woocommerce-toolkit"
        "zip"    = "acf-css-woocommerce-toolkit.zip"
    }
    "ai-extension" = @{
        "source" = "acf-css-ai-extension"
        "target" = "acf-css-ai-extension"
        "zip"    = "acf-css-ai-extension.zip"
    }
    "woo-license" = @{
        "source" = "acf-css-woo-license"
        "target" = "acf-css-woo-license"
        "zip"    = "acf-css-woo-license.zip"
    }
    "admin-menu-editor" = @{
        "source" = "admin-menu-editor-pro"
        "target" = "admin-menu-editor-pro"
        "zip"    = "admin-menu-editor-pro.zip"
    }
    "analytics-dashboard" = @{
        "source" = "jj-analytics-dashboard"
        "target" = "jj-analytics-dashboard"
        "zip"    = "jj-analytics-dashboard.zip"
    }
}

# dist 폴더 확인/생성
$distDir = "dist"
if (!(Test-Path $distDir)) {
    New-Item -ItemType Directory -Path $distDir -Force | Out-Null
}

function Build-Plugin {
    param([string]$name)

    if (-not $plugins.ContainsKey($name)) {
        Write-Host "❌ 알 수 없는 플러그인: $name" -ForegroundColor Red
        Write-Host "사용 가능한 플러그인: $($plugins.Keys -join ', ')" -ForegroundColor Yellow
        return $false
    }

    $plugin = $plugins[$name]
    $sourcePath = $plugin.source
    $targetFolder = $plugin.target
    $zipName = $plugin.zip
    $zipPath = Join-Path $distDir $zipName

    # 소스 폴더 확인
    if (!(Test-Path $sourcePath)) {
        Write-Host "⚠️ 소스 폴더 없음: $sourcePath" -ForegroundColor Yellow
        return $false
    }

    Write-Host "`n📦 빌드 중: $name" -ForegroundColor Cyan
    Write-Host "   소스: $sourcePath"
    Write-Host "   대상: $zipPath"

    # 기존 ZIP 삭제
    if (Test-Path $zipPath) {
        Remove-Item $zipPath -Force
    }

    # 임시 폴더 생성 (폴더명 정규화를 위해)
    $tempDir = Join-Path $env:TEMP "3j-build-$(Get-Random)"
    $tempTarget = Join-Path $tempDir $targetFolder

    try {
        # 소스를 임시 폴더로 복사 (폴더명 정규화)
        New-Item -ItemType Directory -Path $tempDir -Force | Out-Null
        Copy-Item -Path $sourcePath -Destination $tempTarget -Recurse -Force

        # 파일명 변경이 필요한 경우 (rename 설정)
        if ($plugin.rename) {
            foreach ($oldName in $plugin.rename.Keys) {
                $newName = $plugin.rename[$oldName]
                $oldPath = Join-Path $tempTarget $oldName
                if (Test-Path $oldPath) {
                    Rename-Item -Path $oldPath -NewName $newName -Force
                    Write-Host "   파일명 변경: $oldName -> $newName" -ForegroundColor Gray
                }
            }
        }

        # ZIP 생성
        Compress-Archive -Path $tempTarget -DestinationPath $zipPath -Force

        # 버전 정보 추출 (메인 PHP 파일에서)
        $version = "unknown"
        $mainFiles = Get-ChildItem -Path $sourcePath -Filter "*.php" -File | Where-Object { $_.Length -gt 1000 }
        foreach ($file in $mainFiles) {
            $content = Get-Content $file.FullName -Raw -ErrorAction SilentlyContinue
            if ($content -match "Version:\s*([\d\.]+)") {
                $version = $matches[1]
                break
            }
        }

        $fileSize = (Get-Item $zipPath).Length / 1KB
        Write-Host "✅ 완료: $zipName (v$version, $([math]::Round($fileSize, 1)) KB)" -ForegroundColor Green
        return $true
    }
    catch {
        Write-Host "❌ 빌드 실패: $_" -ForegroundColor Red
        return $false
    }
    finally {
        # 임시 폴더 정리
        if (Test-Path $tempDir) {
            Remove-Item $tempDir -Recurse -Force -ErrorAction SilentlyContinue
        }
    }
}

# 메인 실행
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  3J Labs Plugin Builder" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan

if ($PluginName -eq "") {
    Write-Host "`n사용법:" -ForegroundColor Yellow
    Write-Host "  .\build-plugin.ps1 <plugin-name>"
    Write-Host "  .\build-plugin.ps1 all"
    Write-Host "`n사용 가능한 플러그인:" -ForegroundColor Yellow
    $plugins.Keys | ForEach-Object { Write-Host "  - $_" }
    exit 0
}

$successCount = 0
$failCount = 0

if ($PluginName -eq "all") {
    Write-Host "`n모든 플러그인 빌드 시작..." -ForegroundColor Yellow
    foreach ($name in $plugins.Keys) {
        if (Build-Plugin $name) { $successCount++ } else { $failCount++ }
    }
}
else {
    if (Build-Plugin $PluginName) { $successCount++ } else { $failCount++ }
}

Write-Host "`n============================================" -ForegroundColor Cyan
Write-Host "빌드 완료: 성공 $successCount, 실패 $failCount" -ForegroundColor $(if ($failCount -eq 0) { "Green" } else { "Yellow" })
Write-Host "출력 폴더: $((Resolve-Path $distDir).Path)" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan

# dist 폴더 내용 표시
Write-Host "`n📁 dist 폴더 내용:" -ForegroundColor Yellow
Get-ChildItem $distDir -Filter "*.zip" | Sort-Object LastWriteTime -Descending |
    Format-Table Name, @{N="Size(KB)";E={[math]::Round($_.Length/1KB,1)}}, LastWriteTime -AutoSize
