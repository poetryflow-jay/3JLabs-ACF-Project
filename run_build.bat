@echo off
chcp 65001 >nul
title JJ Build Manager v3.2.0
color 0A

echo ========================================
echo   JJ Build Manager v3.2.0
echo   CTO Jason - Smart Build System
echo ========================================
echo.

:: 파이썬 설치 확인
python --version >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ❌ 오류: 파이썬(python)이 설치되어 있지 않거나 PATH에 없습니다.
    echo    파이썬을 설치하거나 PATH 설정을 확인해주세요.
    pause
    exit /b 1
)

echo 📂 현재 디렉토리: %cd%
echo 🚀 빌드를 시작합니다...
echo.

cd /d "%~dp0"
python smart_build_manager.py

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ❌ 빌드 중 오류가 발생했습니다.
    echo    위의 오류 메시지를 확인해주세요.
    pause
) else (
    echo.
    echo ✅ 빌드가 성공적으로 완료되었습니다!
    echo 📂 결과물 위치: %USERPROFILE%\Desktop\JJ_Distributions_v3.2
    echo.
    echo 💡 바탕화면의 [JJ_Distributions_v3.2] 폴더를 확인하세요.
    pause
)
