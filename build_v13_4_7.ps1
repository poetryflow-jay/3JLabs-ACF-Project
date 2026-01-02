$buildDir = "builds\3J-Labs-Release-v13.4.7"
if (!(Test-Path $buildDir)) { New-Item -ItemType Directory -Path $buildDir -Force }

# 1. ACF CSS Master 빌드
$masterPath = "acf-css-really-simple-style-management-center-master"
$masterZip = "$buildDir\acf-css-master-v13.4.7.zip"
if (Test-Path $masterZip) { Remove-Item $masterZip -Force }
Compress-Archive -Path "$masterPath\*" -DestinationPath $masterZip -Force
Write-Output "✅ Built: $masterZip"

# 2. Admin Menu Editor Pro 빌드
$amePath = "admin-menu-editor-pro"
$ameZip = "$buildDir\admin-menu-editor-pro-v1.0.0.zip"
if (Test-Path $amePath) {
    if (Test-Path $ameZip) { Remove-Item $ameZip -Force }
    Compress-Archive -Path "$amePath\*" -DestinationPath $ameZip -Force
    Write-Output "✅ Built: $ameZip"
} else {
    Write-Output "⚠️ Skipping Admin Menu Editor Pro (folder not found)"
}

# 결과 확인
Get-ChildItem $buildDir -Filter "*.zip" | Select-Object Name, Length, LastWriteTime
