#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
═══════════════════════════════════════════════════════════════════════════════
  Phase 46: Dashboard & Build System Upgrade
  대시보드 업그레이드 및 빌드 시스템 자동화
═══════════════════════════════════════════════════════════════════════════════

Features:
- 배포 시점을 연도-월-일-시각(12시간제, AM/PM) 형식으로 표시
- 코어/애드온/프리 플러그인 카테고리 분류
- 빌드 레지스트리에 Free/Pro 다운로드 버튼 추가
- 플러그인 헤더에서 실제 Plugin Name 읽기
- 모든 플러그인 버전 0.1 업데이트
- old 폴더로 이전 버전 자동 이동

@author: 3J Labs (Jay & Jason & Jenny)
@version: 1.0.0 (Phase 46)
@date: 2026-01-05
"""

import os
import sys
import re
import json
import shutil
import zipfile
import datetime
import hashlib
import hmac
from pathlib import Path

# Windows 콘솔 UTF-8 출력 설정
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')
    sys.stderr.reconfigure(encoding='utf-8')

# ═══════════════════════════════════════════════════════════════════════════════
# 설정
# ═══════════════════════════════════════════════════════════════════════════════
BASE_DIR = Path(__file__).parent.absolute()
DIST_DIR = BASE_DIR / "dist"
OLD_DIR = DIST_DIR / "old"
DASHBOARD_PATH = BASE_DIR / "dashboard.html"

SIGNATURE_SECRET_KEY = "JJ3LABS_UPDATE_SECRET_KEY_2026"

# 플러그인 정보 (폴더명 → 메인 파일)
PLUGINS = {
    'acf-css-really-simple-style-management-center-master': {
        'main_file': 'acf-css-really-simple-style-guide.php',
        'category': 'core',
        'icon': '🎨',
        'status': 'stable'
    },
    'wp-bulk-manager': {
        'main_file': 'wp-bulk-installer.php',
        'category': 'family',
        'icon': '🚀',
        'status': 'stable'
    },
    'acf-css-neural-link': {
        'main_file': 'acf-css-neural-link.php',
        'category': 'addon',
        'icon': '🔗',
        'status': 'stable'
    },
    'acf-nudge-flow': {
        'main_file': 'acf-nudge-flow.php',
        'category': 'family',
        'icon': '🎯',
        'status': 'stable'
    },
    'acf-code-snippets-box': {
        'main_file': 'acf-code-snippets-box.php',
        'category': 'family',
        'icon': '📦',
        'status': 'stable'
    },
    'acf-user-journey-analytics': {
        'main_file': 'acf-user-journey-analytics.php',
        'category': 'free',
        'icon': '📊',
        'status': 'free'
    },
    'acf-mail-smtp': {
        'main_file': 'acf-mail-smtp.php',
        'category': 'family',
        'icon': '📧',
        'status': 'stable'
    },
    'jj-marketing-automation-dashboard': {
        'main_file': 'jj-marketing-dashboard.php',
        'category': 'family',
        'icon': '📈',
        'status': 'stable'
    },
    'SEO/wp-bulk-seo-aeo': {
        'main_file': 'wp-bulk-seo-aeo.php',
        'category': 'seo',
        'icon': '🔍',
        'status': 'beta'
    },
    'acf-css-woocommerce-toolkit': {
        'main_file': 'acf-css-woocommerce-toolkit.php',
        'category': 'family',
        'icon': '🛒',
        'status': 'stable'
    },
    'acf-css-ai-extension': {
        'main_file': 'acf-css-ai-extension.php',
        'category': 'addon',
        'icon': '🤖',
        'status': 'stable'
    },
    'admin-menu-editor-pro': {
        'main_file': 'admin-menu-editor-pro.php',
        'category': 'family',
        'icon': '🛠️',
        'status': 'stable'
    },
    'acf-css-woo-license': {
        'main_file': 'acf-css-woo-license.php',
        'category': 'addon',
        'icon': '🔑',
        'status': 'stable'
    },
    'jj-analytics-dashboard': {
        'main_file': 'jj-analytics-dashboard.php',
        'category': 'family',
        'icon': '📉',
        'status': 'stable'
    }
}

# ═══════════════════════════════════════════════════════════════════════════════
# 유틸리티 함수
# ═══════════════════════════════════════════════════════════════════════════════
def get_plugin_header_info(file_path):
    """PHP 파일에서 플러그인 헤더 정보 추출"""
    info = {
        'name': 'Unknown',
        'version': '1.0.0',
        'description': ''
    }

    if not file_path.exists():
        return info

    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read(5000)  # 헤더는 파일 상단에 있음

        # Plugin Name
        match = re.search(r'\*\s*Plugin Name:\s*(.+?)(?:\n|\r)', content)
        if match:
            info['name'] = match.group(1).strip()

        # Version
        match = re.search(r'\*\s*Version:\s*([\d.]+)', content)
        if match:
            info['version'] = match.group(1).strip()

        # Description
        match = re.search(r'\*\s*Description:\s*(.+?)(?:\n|\r)', content)
        if match:
            info['description'] = match.group(1).strip()[:200]

    except Exception as e:
        print(f"Error reading {file_path}: {e}")

    return info

def increment_version(version_str, increment=0.1):
    """버전 문자열을 0.1 증가 (마지막 숫자 부분에 1 추가)"""
    try:
        parts = version_str.split('.')
        if len(parts) >= 2:
            # 마지막 부분을 정수로 1 증가
            last = int(parts[-1]) + 1
            parts[-1] = str(last)
            return '.'.join(parts)
        elif len(parts) == 1:
            # 단일 버전인 경우
            return str(int(version_str) + 1)
        else:
            return version_str
    except:
        return version_str

def update_version_in_file(file_path, new_version):
    """PHP 파일의 버전 업데이트"""
    if not file_path.exists():
        return False

    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()

        # 헤더의 Version 업데이트
        content = re.sub(
            r'(\*\s*Version:\s*)[\d.]+',
            f'\\g<1>{new_version}',
            content
        )

        # 상수 버전 업데이트 (다양한 패턴)
        patterns = [
            (r"define\(\s*'[A-Z_]*VERSION'\s*,\s*'[\d.]+'\s*\)",
             lambda m: re.sub(r"'[\d.]+'(?=\s*\))", f"'{new_version}'", m.group())),
        ]

        for pattern, replacer in patterns:
            content = re.sub(pattern, replacer, content)

        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)

        return True
    except Exception as e:
        print(f"Error updating {file_path}: {e}")
        return False

def format_datetime_12h():
    """현재 시간을 12시간제 AM/PM 형식으로 반환"""
    now = datetime.datetime.now()

    hour = now.hour
    if hour == 0:
        period = "AM"
        display_hour = 12
    elif hour < 12:
        period = "AM"
        display_hour = hour
    elif hour == 12:
        period = "PM"
        display_hour = 12
    else:
        period = "PM"
        display_hour = hour - 12

    return now.strftime(f"%Y-%m-%d {period} {display_hour}:%M")

def generate_signature(zip_path):
    """ZIP 파일의 HMAC-SHA256 서명 생성"""
    try:
        with open(zip_path, 'rb') as f:
            file_content = f.read()

        signature = hmac.new(
            SIGNATURE_SECRET_KEY.encode('utf-8'),
            file_content,
            hashlib.sha256
        ).hexdigest()

        return {
            'signature': signature,
            'md5': hashlib.md5(file_content).hexdigest(),
            'size': len(file_content),
            'generated_at': datetime.datetime.now().isoformat()
        }
    except:
        return None

# ═══════════════════════════════════════════════════════════════════════════════
# 메인 기능
# ═══════════════════════════════════════════════════════════════════════════════
def collect_plugin_info():
    """모든 플러그인 정보 수집"""
    plugins_info = {}

    for folder, config in PLUGINS.items():
        folder_path = BASE_DIR / folder
        main_file = folder_path / config['main_file']

        if main_file.exists():
            header = get_plugin_header_info(main_file)
            plugins_info[folder] = {
                **config,
                **header,
                'folder': folder,
                'main_file': config['main_file'],
                'exists': True
            }
        else:
            plugins_info[folder] = {
                **config,
                'name': folder,
                'version': 'N/A',
                'description': '',
                'folder': folder,
                'main_file': config['main_file'],
                'exists': False
            }

    return plugins_info

def update_all_versions():
    """모든 플러그인 버전 0.1 업데이트"""
    print("\n📦 Updating all plugin versions (+0.1)...")

    updated = []
    for folder, config in PLUGINS.items():
        folder_path = BASE_DIR / folder
        main_file = folder_path / config['main_file']

        if main_file.exists():
            header = get_plugin_header_info(main_file)
            old_version = header['version']
            new_version = increment_version(old_version, 0.1)

            if update_version_in_file(main_file, new_version):
                updated.append((folder, old_version, new_version))
                print(f"  ✅ {folder}: {old_version} → {new_version}")
            else:
                print(f"  ❌ {folder}: Failed to update")
        else:
            print(f"  ⚠️ {folder}: File not found")

    return updated

def move_old_zips_to_archive():
    """기존 ZIP 파일을 old 폴더로 이동"""
    print("\n📁 Moving old ZIP files to archive...")

    OLD_DIR.mkdir(parents=True, exist_ok=True)

    # 날짜별 폴더 생성
    date_folder = OLD_DIR / datetime.datetime.now().strftime("%Y-%m-%d_%H%M%S")
    date_folder.mkdir(parents=True, exist_ok=True)

    moved = 0
    for zip_file in DIST_DIR.glob("*.zip"):
        try:
            shutil.move(str(zip_file), str(date_folder / zip_file.name))
            moved += 1
            print(f"  📦 Moved: {zip_file.name}")
        except Exception as e:
            print(f"  ❌ Failed to move {zip_file.name}: {e}")

    print(f"  Total moved: {moved} files")
    return moved

def build_all_plugins():
    """모든 플러그인 빌드"""
    print("\n🏭 Building all plugins...")

    DIST_DIR.mkdir(parents=True, exist_ok=True)
    signatures = {}
    built = []

    for folder, config in PLUGINS.items():
        folder_path = BASE_DIR / folder

        if not folder_path.exists():
            print(f"  ⚠️ {folder}: Source folder not found")
            continue

        # 헤더 정보 가져오기
        main_file = folder_path / config['main_file']
        header = get_plugin_header_info(main_file)
        version = header['version']

        # ZIP 파일명 결정
        clean_folder = folder.replace('SEO/', '').replace('/', '-')
        zip_filename = f"{clean_folder}-master-v{version}.zip"
        zip_path = DIST_DIR / zip_filename

        # SEO 폴더는 별도 처리
        if folder.startswith('SEO/'):
            seo_dist = DIST_DIR / "SEO"
            seo_dist.mkdir(parents=True, exist_ok=True)
            zip_path = seo_dist / zip_filename

        try:
            # ZIP 생성
            with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
                for item in folder_path.rglob('*'):
                    if item.is_file():
                        # 제외 패턴
                        rel_str = str(item.relative_to(folder_path))
                        if any(x in rel_str for x in ['.git', '__pycache__', '.DS_Store', 'node_modules']):
                            continue

                        arcname = Path(clean_folder + '-master') / item.relative_to(folder_path)
                        zipf.write(item, arcname)

            # 서명 생성
            sig = generate_signature(zip_path)
            if sig:
                sig['plugin'] = folder
                sig['version'] = version
                signatures[zip_filename] = sig

            built.append((folder, version, zip_path))
            print(f"  ✅ {clean_folder}: v{version} → {zip_filename}")

        except Exception as e:
            print(f"  ❌ {folder}: Build failed - {e}")

    # 서명 파일 저장
    sig_path = DIST_DIR / "package_signatures.json"
    try:
        existing = {}
        if sig_path.exists():
            with open(sig_path, 'r', encoding='utf-8') as f:
                existing = json.load(f)

        existing.update(signatures)
        existing['_updated_at'] = datetime.datetime.now().isoformat()
        existing['_phase'] = 'Phase 46'

        with open(sig_path, 'w', encoding='utf-8') as f:
            json.dump(existing, f, indent=2, ensure_ascii=False)
    except Exception as e:
        print(f"  ⚠️ Failed to save signatures: {e}")

    print(f"\n  Total built: {len(built)} plugins")
    return built

def generate_dashboard_html(plugins_info):
    """새로운 대시보드 HTML 생성"""
    print("\n📊 Generating dashboard HTML...")

    now_str = format_datetime_12h()

    # 카테고리별 플러그인 수 계산
    total = len([p for p in plugins_info.values() if p['exists']])
    core_count = len([p for p in plugins_info.values() if p.get('category') == 'core' and p['exists']])
    addon_count = len([p for p in plugins_info.values() if p.get('category') == 'addon' and p['exists']])
    free_count = len([p for p in plugins_info.values() if p.get('category') == 'free' and p['exists']])
    family_count = len([p for p in plugins_info.values() if p.get('category') in ['family', 'seo'] and p['exists']])

    # 플러그인 카드 HTML 생성
    cards_html = ""
    for folder, info in plugins_info.items():
        if not info['exists']:
            continue

        clean_folder = folder.replace('SEO/', '')
        category = info.get('category', 'family')
        icon = info.get('icon', '📦')
        status = info.get('status', 'stable')
        version = info.get('version', '1.0.0')
        name = info.get('name', folder)
        description = info.get('description', '')[:150]

        # 카테고리 태그
        tags = []
        if category == 'core':
            tags.append('<span class="tag core">CORE</span>')
        elif category == 'addon':
            tags.append('<span class="tag addon">ADDON</span>')
        elif category == 'free':
            tags.append('<span class="tag free">FREE</span>')
        elif category == 'seo':
            tags.append('<span class="tag seo">SEO</span>')

        tags.append('<span class="tag master">MASTER</span>')

        # 다운로드 버튼
        if category == 'free':
            btn_class = 'free'
            btn_text = 'Free Download'
        else:
            btn_class = 'master'
            btn_text = 'Download Master'

        # SEO 폴더 경로 처리
        if 'SEO/' in folder:
            download_path = f"dist/SEO/{clean_folder}-master-v{version}.zip"
        else:
            download_path = f"dist/{clean_folder}-master-v{version}.zip"

        cards_html += f'''
            <div class="plugin-card {category}" data-category="{category} {'family' if category in ['addon', 'seo'] else ''}">
                <div class="plugin-header">
                    <div class="plugin-icon">{icon}</div>
                    <div class="plugin-name-row">
                        <div class="plugin-name">{name}</div>
                        <span class="version-tag">v{version}</span>
                    </div>
                    <div class="plugin-fullname">{folder}</div>
                </div>
                <div class="plugin-body">
                    <div class="plugin-description">{description}</div>
                    <div class="editions-row">
                        {''.join(tags)}
                    </div>
                    <a href="{download_path}" class="btn-download {btn_class}">{btn_text}</a>
                </div>
            </div>
'''

    # 빌드 레지스트리 테이블 생성
    registry_html = ""
    for folder, info in plugins_info.items():
        if not info['exists']:
            continue

        clean_folder = folder.replace('SEO/', '')
        name = info.get('name', folder)
        version = info.get('version', '1.0.0')
        status = info.get('status', 'stable')
        category = info.get('category', 'family')

        # 상태 색상
        if status == 'free':
            status_color = 'var(--accent-cyan)'
            status_text = 'Free'
        elif status == 'beta':
            status_color = 'var(--accent-orange)'
            status_text = 'Beta'
        else:
            status_color = 'var(--accent-green)'
            status_text = 'Stable'

        # 다운로드 링크
        if 'SEO/' in folder:
            download_path = f"dist/SEO/{clean_folder}-master-v{version}.zip"
        else:
            download_path = f"dist/{clean_folder}-master-v{version}.zip"

        registry_html += f'''
                    <tr>
                        <td>{name}</td>
                        <td>v{version}</td>
                        <td class="status-dot" style="color: {status_color};">{status_text}</td>
                        <td>HMAC-SHA256</td>
                        <td>
                            <a href="{download_path}" class="btn-mini master">Pro</a>
                            {'<a href="' + download_path + '" class="btn-mini free">Free</a>' if category == 'free' else ''}
                        </td>
                    </tr>
'''

    # 전체 HTML 템플릿
    html = f'''<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3J Labs - ACF CSS Plugin Distribution Dashboard v25</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {{
            --bg-primary: #0a0c10;
            --bg-secondary: #12151a;
            --bg-tertiary: #1c2128;
            --bg-glass: rgba(22, 27, 34, 0.85);
            --bg-glass-light: rgba(255, 255, 255, 0.03);

            --text-primary: #f0f6fc;
            --text-secondary: #8b949e;
            --text-muted: #6e7681;

            --accent-blue: #58a6ff;
            --accent-green: #3fb950;
            --accent-purple: #a371f7;
            --accent-orange: #d29922;
            --accent-red: #f85149;
            --accent-pink: #db61a2;
            --accent-cyan: #39c5bb;
            --accent-gold: #ffd700;

            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --gradient-hero: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);

            --border-color: #30363d;
            --border-hover: #444c56;

            --shadow-sm: 0 2px 8px rgba(0,0,0,0.5);
            --shadow-md: 0 8px 24px rgba(0,0,0,0.6);
            --shadow-lg: 0 12px 48px rgba(0,0,0,0.8);
            --shadow-glow: 0 0 40px rgba(88, 166, 255, 0.15);

            --radius-sm: 8px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-xl: 32px;
        }}

        * {{ box-sizing: border-box; margin: 0; padding: 0; }}

        body {{
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
        }}

        .bg-animation {{
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            overflow: hidden;
        }}

        .bg-animation::before {{
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background:
                radial-gradient(circle at 20% 20%, rgba(88, 166, 255, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(163, 113, 247, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(219, 97, 162, 0.05) 0%, transparent 50%);
            animation: bgFloat 20s ease-in-out infinite;
        }}

        @keyframes bgFloat {{
            0%, 100% {{ transform: translate(0, 0) rotate(0deg); }}
            50% {{ transform: translate(-2%, -2%) rotate(1deg); }}
        }}

        .container {{
            max-width: 1500px;
            margin: 0 auto;
            padding: 40px 24px;
            position: relative;
        }}

        header {{
            text-align: center;
            margin-bottom: 60px;
            position: relative;
            padding: 40px 0;
        }}

        .logo-container {{
            display: inline-block;
            margin-bottom: 24px;
            position: relative;
        }}

        .logo {{
            font-size: 4.5rem;
            font-weight: 900;
            letter-spacing: -3px;
            background: var(--gradient-hero);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 8s ease infinite;
            position: relative;
            z-index: 1;
        }}

        @keyframes gradientShift {{
            0% {{ background-position: 0% 50%; }}
            50% {{ background-position: 100% 50%; }}
            100% {{ background-position: 0% 50%; }}
        }}

        .logo-glow {{
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 150%; height: 150%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.3), transparent 60%);
            filter: blur(40px);
            z-index: 0;
            animation: pulse 4s ease-in-out infinite;
        }}

        @keyframes pulse {{
            0%, 100% {{ opacity: 0.5; transform: translate(-50%, -50%) scale(1); }}
            50% {{ opacity: 0.8; transform: translate(-50%, -50%) scale(1.1); }}
        }}

        .subtitle {{
            color: var(--text-secondary);
            font-size: 1.25rem;
            max-width: 700px;
            margin: 0 auto 32px;
            font-weight: 500;
            line-height: 1.8;
        }}

        .header-badges {{
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }}

        .badge {{
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 600;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }}

        .badge:hover {{
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }}

        .badge.version {{
            background: linear-gradient(135deg, rgba(163, 113, 247, 0.2), rgba(219, 97, 162, 0.2));
            border-color: rgba(163, 113, 247, 0.4);
        }}

        .badge.version span {{
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }}

        .badge.live {{
            border-color: var(--accent-green);
            animation: livePulse 2s infinite;
        }}

        @keyframes livePulse {{
            0% {{ box-shadow: 0 0 0 0 rgba(63, 185, 80, 0.4); }}
            70% {{ box-shadow: 0 0 0 12px rgba(63, 185, 80, 0); }}
            100% {{ box-shadow: 0 0 0 0 rgba(63, 185, 80, 0); }}
        }}

        .badge .dot {{
            width: 8px; height: 8px;
            background: var(--accent-green);
            border-radius: 50%;
            animation: blink 1s infinite;
        }}

        @keyframes blink {{
            0%, 100% {{ opacity: 1; }}
            50% {{ opacity: 0.4; }}
        }}

        .quick-stats {{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 50px;
        }}

        .stat-card {{
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            padding: 28px;
            text-align: center;
            transition: all 0.3s ease;
        }}

        .stat-card:hover {{
            transform: translateY(-4px);
            border-color: var(--accent-blue);
            box-shadow: var(--shadow-glow);
        }}

        .stat-icon {{ font-size: 2.5rem; margin-bottom: 12px; }}

        .stat-value {{
            font-size: 2.8rem;
            font-weight: 900;
            background: var(--gradient-3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 4px;
        }}

        .stat-label {{
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }}

        .filter-container {{
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }}

        .filter-btn {{
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            padding: 10px 24px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }}

        .filter-btn:hover {{
            color: var(--text-primary);
            border-color: var(--accent-blue);
            background: rgba(88, 166, 255, 0.1);
        }}

        .filter-btn.active {{
            background: var(--gradient-1);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }}

        .master-bundle-btn {{
            background: var(--gradient-hero);
            background-size: 200% 200%;
            color: white;
            border: none;
            padding: 18px 48px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 800;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin: 0 auto 60px;
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            animation: gradientShift 6s ease infinite;
        }}

        .master-bundle-btn:hover {{
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 12px 40px rgba(102, 126, 234, 0.6);
        }}

        .plugins-grid {{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 28px;
            margin-bottom: 80px;
        }}

        .plugin-card {{
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            position: relative;
        }}

        .plugin-card::before {{
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--gradient-1);
            opacity: 0;
            transition: opacity 0.3s ease;
        }}

        .plugin-card:hover {{
            transform: translateY(-10px);
            border-color: transparent;
            box-shadow: var(--shadow-lg), var(--shadow-glow);
        }}

        .plugin-card:hover::before {{ opacity: 1; }}
        .plugin-card.core::before {{ background: var(--gradient-3); }}
        .plugin-card.free::before {{ background: linear-gradient(135deg, var(--accent-green), var(--accent-cyan)); }}
        .plugin-card.addon::before {{ background: linear-gradient(135deg, var(--accent-purple), var(--accent-pink)); }}

        .plugin-header {{
            padding: 32px 28px 20px;
            background: var(--bg-glass-light);
            position: relative;
        }}

        .plugin-icon {{
            font-size: 3.5rem;
            margin-bottom: 20px;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));
        }}

        .plugin-name-row {{
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 8px;
        }}

        .plugin-name {{
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.3;
        }}

        .version-tag {{
            background: linear-gradient(135deg, rgba(57, 197, 187, 0.15), rgba(88, 166, 255, 0.15));
            color: var(--accent-cyan);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            border: 1px solid rgba(57, 197, 187, 0.3);
            white-space: nowrap;
        }}

        .plugin-fullname {{
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }}

        .plugin-body {{
            padding: 20px 28px 28px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }}

        .plugin-description {{
            color: var(--text-secondary);
            font-size: 1rem;
            margin-bottom: 24px;
            line-height: 1.7;
            flex-grow: 1;
        }}

        .editions-row {{
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }}

        .tag {{
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }}

        .tag.master {{
            background: linear-gradient(135deg, rgba(163, 113, 247, 0.2), rgba(163, 113, 247, 0.1));
            color: var(--accent-purple);
            border: 1px solid rgba(163, 113, 247, 0.3);
        }}
        .tag.core {{
            background: linear-gradient(135deg, rgba(63, 185, 80, 0.2), rgba(63, 185, 80, 0.1));
            color: var(--accent-green);
            border: 1px solid rgba(63, 185, 80, 0.3);
        }}
        .tag.addon {{
            background: linear-gradient(135deg, rgba(219, 97, 162, 0.2), rgba(219, 97, 162, 0.1));
            color: var(--accent-pink);
            border: 1px solid rgba(219, 97, 162, 0.3);
        }}
        .tag.family {{
            background: linear-gradient(135deg, rgba(88, 166, 255, 0.2), rgba(88, 166, 255, 0.1));
            color: var(--accent-blue);
            border: 1px solid rgba(88, 166, 255, 0.3);
        }}
        .tag.free {{
            background: linear-gradient(135deg, rgba(57, 197, 187, 0.2), rgba(57, 197, 187, 0.1));
            color: var(--accent-cyan);
            border: 1px solid rgba(57, 197, 187, 0.3);
        }}
        .tag.seo {{
            background: linear-gradient(135deg, rgba(210, 153, 34, 0.2), rgba(210, 153, 34, 0.1));
            color: var(--accent-orange);
            border: 1px solid rgba(210, 153, 34, 0.3);
        }}

        .btn-download {{
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 24px;
            border-radius: var(--radius-sm);
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }}

        .btn-download.master {{
            background: var(--gradient-1);
            color: white;
        }}

        .btn-download.master:hover {{
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }}

        .btn-download.free {{
            background: linear-gradient(135deg, var(--accent-green), var(--accent-cyan));
            color: white;
        }}

        .btn-download.free:hover {{
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(63, 185, 80, 0.4);
        }}

        .section-glass {{
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            padding: 40px;
            margin-bottom: 40px;
        }}

        .section-title {{
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 16px;
        }}

        .build-table {{
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }}

        .build-table th {{
            text-align: left;
            padding: 16px 20px;
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--border-color);
            background: rgba(0,0,0,0.2);
        }}

        .build-table td {{
            padding: 18px 20px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.95rem;
        }}

        .build-table tr:last-child td {{ border-bottom: none; }}

        .build-table tr {{
            transition: background 0.2s ease;
        }}

        .build-table tr:hover td {{
            background: rgba(88, 166, 255, 0.05);
        }}

        .status-dot {{
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }}

        .status-dot::before {{
            content: '';
            width: 8px; height: 8px;
            border-radius: 50%;
            background: currentColor;
        }}

        .btn-mini {{
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            margin-right: 8px;
            transition: all 0.2s ease;
        }}

        .btn-mini.master {{
            background: var(--gradient-1);
            color: white;
        }}

        .btn-mini.free {{
            background: linear-gradient(135deg, var(--accent-green), var(--accent-cyan));
            color: white;
        }}

        .btn-mini:hover {{
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }}

        footer {{
            text-align: center;
            padding: 60px 20px;
            border-top: 1px solid var(--border-color);
            margin-top: 40px;
        }}

        .footer-logo {{
            font-size: 2rem;
            font-weight: 900;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 16px;
        }}

        .footer-links {{
            display: flex;
            justify-content: center;
            gap: 32px;
            margin: 24px 0;
        }}

        .footer-links a {{
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }}

        .footer-links a:hover {{ color: var(--accent-blue); }}

        .footer-meta {{
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-top: 20px;
        }}

        @media (max-width: 768px) {{
            .logo {{ font-size: 2.8rem; }}
            .plugins-grid {{ grid-template-columns: 1fr; }}
            .section-glass {{ padding: 24px; }}
            .build-table {{ display: block; overflow-x: auto; }}
            .quick-stats {{ grid-template-columns: repeat(2, 1fr); }}
        }}
    </style>
</head>
<body>
    <div class="bg-animation"></div>

    <div class="container">
        <header>
            <div class="logo-container">
                <div class="logo">3J Labs</div>
                <div class="logo-glow"></div>
            </div>
            <p class="subtitle">
                Next-Generation WordPress Management Solutions.<br>
                Crafted with Precision by Jay, Jenny & Jason.
            </p>

            <div class="header-badges">
                <div class="badge version">
                    <span>v25.1.0</span> Phase 46
                </div>
                <div class="badge live">
                    <span class="dot"></span>
                    <span id="last-updated">{now_str}</span>
                </div>
                <div class="badge">
                    <span id="total-plugins">{total}</span> Plugins
                </div>
            </div>
        </header>

        <section class="quick-stats">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-value">{total}</div>
                <div class="stat-label">Total Plugins</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🛡️</div>
                <div class="stat-value">{core_count}</div>
                <div class="stat-label">Core Plugins</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🧩</div>
                <div class="stat-value">{addon_count}</div>
                <div class="stat-label">Addon Plugins</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🎁</div>
                <div class="stat-value">{free_count}</div>
                <div class="stat-label">Free Plugin</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🔒</div>
                <div class="stat-value">HMAC</div>
                <div class="stat-label">Secure Updates</div>
            </div>
        </section>

        <div class="filter-container">
            <button class="filter-btn active" onclick="filterPlugins('all')">All Plugins</button>
            <button class="filter-btn" onclick="filterPlugins('core')">Core Engine</button>
            <button class="filter-btn" onclick="filterPlugins('addon')">Addons</button>
            <button class="filter-btn" onclick="filterPlugins('family')">Family Apps</button>
            <button class="filter-btn" onclick="filterPlugins('free')">Free</button>
            <button class="filter-btn" onclick="filterPlugins('seo')">SEO</button>
        </div>

        <div style="text-align: center;">
            <button class="master-bundle-btn" onclick="downloadAllMaster()">
                💎 Download Master Bundle ({total} Plugins)
            </button>
        </div>

        <section class="plugins-grid" id="plugins-grid">
            {cards_html}
        </section>

        <section class="section-glass">
            <h2 class="section-title">🔧 Build Registry (Phase 46)</h2>
            <table class="build-table">
                <thead>
                    <tr>
                        <th>Plugin Name</th>
                        <th>Version</th>
                        <th>Status</th>
                        <th>Security</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody id="build-tbody">
                    {registry_html}
                </tbody>
            </table>
        </section>
    </div>

    <footer>
        <div class="footer-logo">3J Labs</div>
        <p style="color: var(--text-secondary);">Advanced Solutions for Modern WordPress Ecosystems.</p>
        <div class="footer-links">
            <a href="https://3j-labs.com">Official Site</a>
            <a href="#">Support</a>
            <a href="#">Documentation</a>
            <a href="https://github.com/poetryflow-jay/3JLabs-ACF-Project">GitHub</a>
        </div>
        <p class="footer-meta">
            Dashboard v25.1.0 (Phase 46) | Build ID: <span id="build-id">RELEASE-{datetime.datetime.now().strftime("%Y%m%d-%H%M")}</span><br>
            Last Updated: {now_str}<br>
            Powered by HMAC-SHA256 Package Signatures
        </p>
    </footer>

    <script>
        function filterPlugins(category) {{
            const cards = document.querySelectorAll('.plugin-card');
            const btns = document.querySelectorAll('.filter-btn');

            btns.forEach(btn => {{
                btn.classList.toggle('active', btn.getAttribute('onclick').includes(`'${{category}}'`));
            }});

            cards.forEach(card => {{
                const cardCategories = card.dataset.category.split(' ');
                if (category === 'all' || cardCategories.includes(category)) {{
                    card.style.display = 'flex';
                    card.style.animation = 'fadeIn 0.3s ease';
                }} else {{
                    card.style.display = 'none';
                }}
            }});
        }}

        function downloadAllMaster() {{
            if (!confirm('Download all {total} Master plugins?\\n\\nThis will start multiple downloads.')) return;

            const links = Array.from(document.querySelectorAll('.btn-download')).map(a => a.href);
            links.forEach((link, index) => {{
                setTimeout(() => {{
                    const a = document.createElement('a');
                    a.href = link;
                    a.download = '';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }}, index * 600);
            }});
        }}

        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {{
                from {{ opacity: 0; transform: translateY(10px); }}
                to {{ opacity: 1; transform: translateY(0); }}
            }}
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
'''

    with open(DASHBOARD_PATH, 'w', encoding='utf-8') as f:
        f.write(html)

    print(f"  ✅ Dashboard saved: {DASHBOARD_PATH}")

# ═══════════════════════════════════════════════════════════════════════════════
# 메인 실행
# ═══════════════════════════════════════════════════════════════════════════════
def main():
    print("=" * 70)
    print("  Phase 46: Dashboard & Build System Upgrade")
    print("=" * 70)

    # 1. 모든 플러그인 버전 업데이트
    updated = update_all_versions()

    # 2. 플러그인 정보 수집
    plugins_info = collect_plugin_info()

    # 3. 기존 ZIP을 old로 이동
    move_old_zips_to_archive()

    # 4. 모든 플러그인 빌드
    built = build_all_plugins()

    # 5. 대시보드 HTML 생성
    generate_dashboard_html(plugins_info)

    print("\n" + "=" * 70)
    print("  Phase 46 Complete!")
    print(f"  - Updated: {len(updated)} plugins")
    print(f"  - Built: {len(built)} plugins")
    print(f"  - Dashboard: {DASHBOARD_PATH}")
    print("=" * 70)

if __name__ == "__main__":
    main()
