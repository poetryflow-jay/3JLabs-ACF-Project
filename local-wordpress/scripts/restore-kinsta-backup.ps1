# 3J Labs - Kinsta 백업 복원 스크립트 (PowerShell)
# 제이x제니x제이슨 연구소 (3J Labs)
#
# Kinsta SQL 백업을 로컬 Docker 환경에 복원합니다.
#
# 사용법:
#   .\scripts\restore-kinsta-backup.ps1

param(
    [switch]$SkipUrlReplace  # URL 변경 건너뛰기
)

$ErrorActionPreference = "Stop"

# 경로 설정
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$RootDir = Split-Path -Parent $ScriptDir
$SqlFile = Join-Path (Split-Path -Parent $RootDir) "wordpress\drillairlines.sql"
$WpConfigLocal = Join-Path (Split-Path -Parent $RootDir) "wordpress\public\wp-config-local.php"
$WpConfig = Join-Path (Split-Path -Parent $RootDir) "wordpress\public\wp-config.php"

# 색상 출력 함수
function Write-Success { param($Message) Write-Host "[OK] $Message" -ForegroundColor Green }
function Write-Info { param($Message) Write-Host "[INFO] $Message" -ForegroundColor Cyan }
function Write-Warn { param($Message) Write-Host "[WARN] $Message" -ForegroundColor Yellow }
function Write-Err { param($Message) Write-Host "[ERROR] $Message" -ForegroundColor Red }

# 배너 출력
Write-Host ""
Write-Host "============================================" -ForegroundColor Magenta
Write-Host "  3J Labs - Kinsta Backup Restore" -ForegroundColor Magenta
Write-Host "  제이x제니x제이슨 연구소" -ForegroundColor Magenta
Write-Host "============================================" -ForegroundColor Magenta
Write-Host ""

# SQL 파일 확인
if (-not (Test-Path $SqlFile)) {
    Write-Err "SQL 백업 파일을 찾을 수 없습니다: $SqlFile"
    exit 1
}
Write-Info "SQL 백업 파일: $SqlFile"
Write-Info "파일 크기: $([math]::Round((Get-Item $SqlFile).Length / 1MB, 2)) MB"

# wp-config 확인 및 로컬 설정 적용
Write-Info "wp-config.php 설정 중..."
if (Test-Path $WpConfigLocal) {
    # 기존 wp-config.php 백업
    if (Test-Path $WpConfig) {
        $BackupPath = "$WpConfig.kinsta.bak"
        if (-not (Test-Path $BackupPath)) {
            Copy-Item $WpConfig $BackupPath
            Write-Info "기존 wp-config.php 백업: $BackupPath"
        }
    }
    
    # 로컬 설정으로 교체
    Copy-Item $WpConfigLocal $WpConfig -Force
    Write-Success "wp-config.php 로컬 설정 적용 완료!"
}

# Docker 컨테이너 확인
Write-Info "Docker 컨테이너 확인 중..."
$dbContainer = docker ps --filter "name=3j_mariadb" --format "{{.Names}}" 2>$null
if (-not $dbContainer) {
    Write-Err "MariaDB 컨테이너가 실행 중이 아닙니다."
    Write-Info "먼저 'docker-compose up -d'를 실행하세요."
    exit 1
}
Write-Success "MariaDB 컨테이너 실행 중: $dbContainer"

# 데이터베이스 초기화
Write-Info "데이터베이스 초기화 중..."
docker exec -i 3j_mariadb mysql -uroot -p3j_root_secret -e "DROP DATABASE IF EXISTS wordpress; CREATE DATABASE wordpress CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
Write-Success "데이터베이스 초기화 완료!"

# SQL 파일 복원
Write-Info "Kinsta 백업 복원 중... (약 1-2분 소요)"
$startTime = Get-Date

# SQL 파일을 컨테이너로 복사 후 실행
docker cp $SqlFile 3j_mariadb:/tmp/backup.sql
docker exec 3j_mariadb mysql -uroot -p3j_root_secret wordpress -e "source /tmp/backup.sql"
docker exec 3j_mariadb rm /tmp/backup.sql

$elapsed = (Get-Date) - $startTime
Write-Success "백업 복원 완료! (소요시간: $($elapsed.TotalSeconds.ToString('F0'))초)"

# URL 변경 (Search-Replace)
if (-not $SkipUrlReplace) {
    Write-Info "URL 변경 중 (Kinsta → 로컬)..."
    
    # 사이트 URL 변경
    $oldUrl = "https://drillairlines.kinsta.cloud"
    $newUrl = "http://localhost:8080"
    
    # wp_options 테이블 업데이트
    docker exec 3j_mariadb mysql -uroot -p3j_root_secret wordpress -e "UPDATE wp_options SET option_value = '$newUrl' WHERE option_name = 'siteurl';"
    docker exec 3j_mariadb mysql -uroot -p3j_root_secret wordpress -e "UPDATE wp_options SET option_value = '$newUrl' WHERE option_name = 'home';"
    
    # 사이트 이름 변경
    $newSiteName = "제이x제니x제이슨 연구소 (3J Labs)"
    docker exec 3j_mariadb mysql -uroot -p3j_root_secret wordpress -e "UPDATE wp_options SET option_value = '$newSiteName' WHERE option_name = 'blogname';"
    
    Write-Success "URL 및 사이트 이름 변경 완료!"
    Write-Info "  - 사이트 URL: $newUrl"
    Write-Info "  - 사이트 이름: $newSiteName"
}

# 완료 메시지
Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "  Kinsta 백업 복원 완료!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green
Write-Host ""
Write-Host "  WordPress:    http://localhost:8080" -ForegroundColor White
Write-Host "  관리자:       http://localhost:8080/wp-admin" -ForegroundColor White
Write-Host ""
Write-Host "  사이트명: 제이x제니x제이슨 연구소 (3J Labs)" -ForegroundColor Yellow
Write-Host ""
Write-Host "  [주의] 관리자 로그인 정보는 Kinsta 원본과 동일합니다." -ForegroundColor Gray
Write-Host ""

# 브라우저 열기 (선택)
$openBrowser = Read-Host "브라우저에서 WordPress를 열까요? (y/n)"
if ($openBrowser -eq "y" -or $openBrowser -eq "Y") {
    Start-Process "http://localhost:8080"
}
