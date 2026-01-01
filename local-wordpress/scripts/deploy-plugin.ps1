# 3J Labs - 플러그인 자동 배포 스크립트 (PowerShell)
# ACF CSS 플러그인을 로컬 WordPress에 배포합니다.
#
# 사용법:
#   .\scripts\deploy-plugin.ps1                    # 전체 동기화
#   .\scripts\deploy-plugin.ps1 -Watch             # 파일 변경 감시 및 자동 배포
#   .\scripts\deploy-plugin.ps1 -Clean             # 기존 플러그인 삭제 후 재배포
#   .\scripts\deploy-plugin.ps1 -Activate          # 배포 후 플러그인 활성화

param(
    [switch]$Watch,
    [switch]$Clean,
    [switch]$Activate,
    [switch]$Deactivate
)

$ErrorActionPreference = "Stop"

# 경로 설정
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$RootDir = Split-Path -Parent $ScriptDir
$PluginSourceDir = Join-Path (Split-Path -Parent $RootDir) "acf-css-really-simple-style-management-center-master"
$PluginDestDir = Join-Path $RootDir "plugins"
$DockerComposeFile = Join-Path $RootDir "docker-compose.yml"

# 색상 출력 함수
function Write-Success { param($Message) Write-Host "[OK] $Message" -ForegroundColor Green }
function Write-Info { param($Message) Write-Host "[INFO] $Message" -ForegroundColor Cyan }
function Write-Warn { param($Message) Write-Host "[WARN] $Message" -ForegroundColor Yellow }
function Write-Err { param($Message) Write-Host "[ERROR] $Message" -ForegroundColor Red }

# 배너 출력
Write-Host ""
Write-Host "============================================" -ForegroundColor Magenta
Write-Host "  3J Labs - Plugin Auto-Deploy Script" -ForegroundColor Magenta
Write-Host "============================================" -ForegroundColor Magenta
Write-Host ""

# 소스 경로 확인
if (-not (Test-Path $PluginSourceDir)) {
    Write-Err "플러그인 소스 경로를 찾을 수 없습니다: $PluginSourceDir"
    exit 1
}
Write-Info "소스 경로: $PluginSourceDir"

# 대상 경로 생성
if (-not (Test-Path $PluginDestDir)) {
    New-Item -ItemType Directory -Path $PluginDestDir -Force | Out-Null
    Write-Info "대상 폴더 생성: $PluginDestDir"
}

# 플러그인 배포 함수
function Deploy-Plugin {
    Write-Info "플러그인 배포 시작..."
    
    $startTime = Get-Date
    
    # Clean 옵션: 기존 파일 삭제
    if ($Clean -and (Test-Path $PluginDestDir)) {
        Write-Warn "기존 플러그인 파일 삭제 중..."
        Remove-Item -Path "$PluginDestDir\*" -Recurse -Force -ErrorAction SilentlyContinue
    }
    
    # 파일 복사 (변경된 파일만)
    $sourceFiles = Get-ChildItem -Path $PluginSourceDir -Recurse -File
    $copiedCount = 0
    $skippedCount = 0
    
    foreach ($file in $sourceFiles) {
        $relativePath = $file.FullName.Substring($PluginSourceDir.Length + 1)
        $destPath = Join-Path $PluginDestDir $relativePath
        $destDir = Split-Path -Parent $destPath
        
        # 불필요한 파일/폴더 제외
        if ($relativePath -match "^(\.git|node_modules|tests|\.github|vendor)") {
            continue
        }
        
        # 대상 디렉토리 생성
        if (-not (Test-Path $destDir)) {
            New-Item -ItemType Directory -Path $destDir -Force | Out-Null
        }
        
        # 파일 비교 및 복사
        $needsCopy = $false
        if (-not (Test-Path $destPath)) {
            $needsCopy = $true
        } else {
            $destFile = Get-Item $destPath
            if ($file.LastWriteTime -gt $destFile.LastWriteTime) {
                $needsCopy = $true
            }
        }
        
        if ($needsCopy) {
            Copy-Item -Path $file.FullName -Destination $destPath -Force
            $copiedCount++
        } else {
            $skippedCount++
        }
    }
    
    $elapsed = (Get-Date) - $startTime
    Write-Success "배포 완료! (복사: $copiedCount개, 스킵: $skippedCount개, 소요시간: $($elapsed.TotalSeconds.ToString('F2'))초)"
    
    # 플러그인 활성화 옵션
    if ($Activate) {
        Write-Info "플러그인 활성화 중..."
        docker exec 3j_wpcli wp plugin activate acf-css-dev --path=/var/www/html 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Success "플러그인 활성화 완료!"
        } else {
            Write-Warn "플러그인 활성화 실패 (수동 활성화 필요)"
        }
    }
    
    # 플러그인 비활성화 옵션
    if ($Deactivate) {
        Write-Info "플러그인 비활성화 중..."
        docker exec 3j_wpcli wp plugin deactivate acf-css-dev --path=/var/www/html 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Success "플러그인 비활성화 완료!"
        } else {
            Write-Warn "플러그인 비활성화 실패"
        }
    }
}

# Watch 모드: 파일 변경 감시
function Watch-Plugin {
    Write-Info "파일 변경 감시 모드 시작..."
    Write-Info "종료하려면 Ctrl+C를 누르세요."
    Write-Host ""
    
    # 초기 배포
    Deploy-Plugin
    
    # FileSystemWatcher 설정
    $watcher = New-Object System.IO.FileSystemWatcher
    $watcher.Path = $PluginSourceDir
    $watcher.IncludeSubdirectories = $true
    $watcher.EnableRaisingEvents = $true
    $watcher.NotifyFilter = [System.IO.NotifyFilters]::LastWrite -bor [System.IO.NotifyFilters]::FileName
    
    # 디바운스를 위한 변수
    $lastEvent = [DateTime]::MinValue
    $debounceInterval = [TimeSpan]::FromSeconds(2)
    
    # 이벤트 핸들러
    $action = {
        $path = $Event.SourceEventArgs.FullPath
        $now = [DateTime]::Now
        
        # 불필요한 파일 무시
        if ($path -match "(\.git|node_modules|tests|\.github|vendor|\.swp|\.tmp)") {
            return
        }
        
        # 디바운스 체크
        if (($now - $script:lastEvent) -lt $script:debounceInterval) {
            return
        }
        $script:lastEvent = $now
        
        Write-Host ""
        Write-Host "[$(Get-Date -Format 'HH:mm:ss')] 변경 감지: $($Event.SourceEventArgs.Name)" -ForegroundColor Yellow
        
        # 재배포
        & "$script:ScriptDir\deploy-plugin.ps1"
    }
    
    # 이벤트 등록
    $handlers = @()
    $handlers += Register-ObjectEvent $watcher "Changed" -Action $action
    $handlers += Register-ObjectEvent $watcher "Created" -Action $action
    $handlers += Register-ObjectEvent $watcher "Deleted" -Action $action
    $handlers += Register-ObjectEvent $watcher "Renamed" -Action $action
    
    try {
        while ($true) {
            Start-Sleep -Seconds 1
        }
    }
    finally {
        # 정리
        foreach ($handler in $handlers) {
            Unregister-Event -SubscriptionId $handler.Id -ErrorAction SilentlyContinue
        }
        $watcher.Dispose()
        Write-Info "파일 감시 종료"
    }
}

# 메인 실행
if ($Watch) {
    Watch-Plugin
} else {
    Deploy-Plugin
}

Write-Host ""
