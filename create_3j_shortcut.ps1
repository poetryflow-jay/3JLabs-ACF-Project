$WshShell = New-Object -ComObject WScript.Shell

# 타겟 경로 설정
$Targets = @(
    "C:\Users\computer\OneDrive\Desktop",
    "C:\Users\computer\Desktop"
)

$TargetExe = "C:\my-ai\venv\Scripts\pythonw.exe"
$ScriptPath = Convert-Path ".\3j_launcher.py"
$IconPath = "C:\Windows\System32\shell32.dll,13"

foreach ($DesktopPath in $Targets) {
    if (Test-Path $DesktopPath) {
        $ShortcutPath = Join-Path $DesktopPath "3J Labs Launcher.lnk"
        $Shortcut = $WshShell.CreateShortcut($ShortcutPath)
        $Shortcut.TargetPath = $TargetExe
        $Shortcut.Arguments = """$ScriptPath"""
        $Shortcut.WorkingDirectory = Split-Path $ScriptPath
        $Shortcut.IconLocation = $IconPath
        $Shortcut.Save()
        Write-Host "✅ 바로가기 생성 완료: $ShortcutPath"
    } else {
        Write-Host "⚠️ 경로를 찾을 수 없음: $DesktopPath"
    }
}
