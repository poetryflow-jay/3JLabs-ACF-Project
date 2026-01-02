# ==============================================================================
# 3J Labs ACF CSS - v13.4.7 빌드 스크립트
# ==============================================================================
# 작업 원칙 (Development Principles):
# 1. ZIP 빌드 시 플러그인 폴더가 포함되도록 주의
#    - Compress-Archive -Path $folder (not $folder\*)
#    - WordPress 업로드 설치 시 올바른 폴더 구조 유지
# 2. 터미널이 Python REPL(>>>) 상태면 exit() 후 재시도
# 3. 40초 이상 응답 없으면 중지 후 다른 방법으로 재시도
# ==============================================================================

$buildDir = "builds\3J-Labs-Release-v13.4.7"
if (!(Test-Path $buildDir)) { New-Item -ItemType Directory -Path $buildDir -Force }

# 1. ACF CSS Master 빌드
$masterPath = "acf-css-really-simple-style-management-center-master"
$masterZip = "$buildDir\acf-css-master-v13.4.7.zip"
if (Test-Path $masterZip) { Remove-Item $masterZip -Force }
Compress-Archive -Path $masterPath -DestinationPath $masterZip -Force
Write-Output "✅ Built: $masterZip"

# 2. Admin Menu Editor Pro 빌드
$amePath = "admin-menu-editor-pro"
$ameZip = "$buildDir\admin-menu-editor-pro-v1.0.0.zip"
if (Test-Path $amePath) {
    if (Test-Path $ameZip) { Remove-Item $ameZip -Force }
    Compress-Archive -Path $amePath -DestinationPath $ameZip -Force
    Write-Output "✅ Built: $ameZip"
} else {
    Write-Output "⚠️ Skipping Admin Menu Editor Pro (folder not found)"
}

# 결과 확인
Get-ChildItem $buildDir -Filter "*.zip" | Select-Object Name, Length, LastWriteTime

# 대시보드 자동 업데이트
Write-Output "📊 대시보드 업데이트 중..."
python 3j_dev_toolkit.py --dashboard
if ($LASTEXITCODE -eq 0) {
    Write-Output "✅ 대시보드 업데이트 완료"
} else {
    Write-Output "⚠️ 대시보드 업데이트 실패 (계속 진행)"
}
