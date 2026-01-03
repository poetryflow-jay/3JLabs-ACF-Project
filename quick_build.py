#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Quick ZIP build script for ACF CSS Manager"""
import zipfile
import os
from pathlib import Path

BASE_DIR = Path(__file__).parent
PLUGIN_DIR = BASE_DIR / "acf-css-really-simple-style-management-center-master"
DIST_DIR = BASE_DIR / "dist"
ZIP_NAME = "acf-css-really-simple-style-management-center-master-master-v20.2.1.zip"

if not PLUGIN_DIR.exists():
    print(f"❌ 플러그인 폴더를 찾을 수 없습니다: {PLUGIN_DIR}")
    exit(1)

DIST_DIR.mkdir(exist_ok=True)
zip_path = DIST_DIR / ZIP_NAME

# 제외할 파일/폴더 패턴
EXCLUDE_PATTERNS = [
    '.git', '.vscode', '.idea', '__pycache__', '.DS_Store',
    'tests', 'phpunit.xml', 'composer.json', 'node_modules',
    'package.json', 'package-lock.json', 'gulpfile.js',
    '.editorconfig', '.env', 'Thumbs.db', 'local-server/venv',
    'README.md', 'CHANGELOG.md'
]

def should_exclude(path_str):
    """파일/폴더를 제외할지 확인"""
    for pattern in EXCLUDE_PATTERNS:
        if pattern in path_str:
            return True
    return False

print(f"📦 ZIP 생성 중: {ZIP_NAME}")
print(f"📂 소스: {PLUGIN_DIR}")
print(f"💾 출력: {zip_path}")

with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
    for root, dirs, files in os.walk(PLUGIN_DIR):
        # 제외할 디렉토리 필터링
        dirs[:] = [d for d in dirs if not should_exclude(d)]
        
        for file in files:
            if should_exclude(file):
                continue
            
            file_path = Path(root) / file
            arcname = file_path.relative_to(PLUGIN_DIR.parent)
            
            try:
                zf.write(file_path, arcname)
                print(f"  ✓ {arcname}")
            except Exception as e:
                print(f"  ✗ {arcname}: {e}")

print(f"\n✅ 빌드 완료: {zip_path}")
print(f"📊 크기: {zip_path.stat().st_size / 1024 / 1024:.2f} MB")
