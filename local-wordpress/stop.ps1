# 3J Labs - Local WordPress Stop (PowerShell)
# 로컬 WordPress 환경을 중지합니다.

param(
    [switch]$Clean  # 데이터 삭제
)

$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "============================================" -ForegroundColor Yellow
Write-Host "  3J Labs - Stopping Local WordPress" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Yellow
Write-Host ""

# 현재 디렉토리로 이동
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $ScriptDir

if ($Clean) {
    Write-Host "[WARN] 모든 데이터가 삭제됩니다!" -ForegroundColor Red
    $confirm = Read-Host "계속하시겠습니까? (yes/no)"
    if ($confirm -ne "yes") {
        Write-Host "취소되었습니다." -ForegroundColor Yellow
        exit 0
    }
    
    Write-Host "컨테이너 및 볼륨 삭제 중..." -ForegroundColor Cyan
    docker-compose down -v --remove-orphans
    Write-Host "모든 데이터가 삭제되었습니다." -ForegroundColor Green
} else {
    Write-Host "컨테이너 중지 중..." -ForegroundColor Cyan
    docker-compose down
    Write-Host "환경이 중지되었습니다. (데이터 보존)" -ForegroundColor Green
}

Write-Host ""
Write-Host "다시 시작하려면: .\start.ps1" -ForegroundColor Gray
Write-Host ""
