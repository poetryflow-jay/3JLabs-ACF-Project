# 3J Labs - Local WordPress Quick Start (PowerShell)
# 한 번의 명령으로 로컬 WordPress 환경을 시작합니다.

$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "============================================" -ForegroundColor Magenta
Write-Host "  3J Labs - Local WordPress Environment" -ForegroundColor Magenta
Write-Host "============================================" -ForegroundColor Magenta
Write-Host ""

# 현재 디렉토리로 이동
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $ScriptDir

# Docker 설치 확인
Write-Host "[1/5] Docker 설치 확인..." -ForegroundColor Cyan
try {
    $dockerVersion = docker --version
    Write-Host "      $dockerVersion" -ForegroundColor Green
} catch {
    Write-Host "      Docker가 설치되어 있지 않습니다!" -ForegroundColor Red
    Write-Host "      https://www.docker.com/products/docker-desktop 에서 설치하세요." -ForegroundColor Yellow
    exit 1
}

# .env 파일 확인
Write-Host "[2/5] 환경 변수 파일 확인..." -ForegroundColor Cyan
if (-not (Test-Path ".env")) {
    Write-Host "      .env 파일 생성 중..." -ForegroundColor Yellow
    Copy-Item "env.example" ".env"
    Write-Host "      .env 파일 생성 완료!" -ForegroundColor Green
} else {
    Write-Host "      .env 파일 확인됨" -ForegroundColor Green
}

# Docker 이미지 빌드
Write-Host "[3/5] Docker 이미지 빌드..." -ForegroundColor Cyan
docker-compose build --quiet
Write-Host "      빌드 완료!" -ForegroundColor Green

# 컨테이너 시작
Write-Host "[4/5] 컨테이너 시작..." -ForegroundColor Cyan
docker-compose up -d
Write-Host "      컨테이너 시작 완료!" -ForegroundColor Green

# 플러그인 배포
Write-Host "[5/5] ACF CSS 플러그인 배포..." -ForegroundColor Cyan
& "$ScriptDir\scripts\deploy-plugin.ps1"

# 완료 메시지
Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "  환경 시작 완료!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green
Write-Host ""
Write-Host "  WordPress:    http://localhost:8080" -ForegroundColor White
Write-Host "  관리자:       http://localhost:8080/wp-admin" -ForegroundColor White
Write-Host "  phpMyAdmin:   http://localhost:8081 (별도 시작 필요)" -ForegroundColor Gray
Write-Host ""
Write-Host "  관리자 계정:" -ForegroundColor White
Write-Host "    ID:       admin" -ForegroundColor Yellow
Write-Host "    Password: admin123!" -ForegroundColor Yellow
Write-Host ""
Write-Host "  phpMyAdmin 시작:" -ForegroundColor Gray
Write-Host "    docker-compose --profile tools up -d" -ForegroundColor Gray
Write-Host ""
Write-Host "  환경 중지:" -ForegroundColor Gray
Write-Host "    docker-compose down" -ForegroundColor Gray
Write-Host ""

# 브라우저 열기 (선택)
$openBrowser = Read-Host "브라우저에서 WordPress를 열까요? (y/n)"
if ($openBrowser -eq "y" -or $openBrowser -eq "Y") {
    Start-Sleep -Seconds 5  # WordPress 초기화 대기
    Start-Process "http://localhost:8080"
}
