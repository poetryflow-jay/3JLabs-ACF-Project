@echo off
chcp 65001 > nul
echo 🚀 J&Jenny AI Launcher의 환경을 사용하여 로컬 AI 서버를 시작합니다...
echo.

set LAUNCHER_PYTHON=C:\my-ai\venv\Scripts\python.exe
set SERVER_SCRIPT=%~dp0inference.py

if exist "%LAUNCHER_PYTHON%" (
    echo ✅ Python 환경 감지됨: %LAUNCHER_PYTHON%
    echo ✅ 모델 서버 스크립트: %SERVER_SCRIPT%
    echo.
    echo 서버가 시작되면 창을 닫지 마세요.
    echo.
    "%LAUNCHER_PYTHON%" "%SERVER_SCRIPT%"
) else (
    echo ❌ J&Jenny AI Launcher 가상환경을 찾을 수 없습니다. (C:\my-ai\venv)
    echo 직접 Python을 설치하고 실행해주세요.
    pause
)

