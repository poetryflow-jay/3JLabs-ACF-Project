---

**User**

좋아. 이번에도 수고 많았네, 제이슨. 이번에도 새로운 압축 파일들을 하나의 폴더에 먼저 번의 분류대로 쭉 생성해주게. (스테이징, 베타, 스테이블 -> 각각 마스터, 파트너, 프로(유료 버전), 프리)

그리고 내가 테스트하는 동안 자네가 진행할 다음 계획에 대해 제안해주게. 먼저 내게 제안한 여러 내용을 다시 돌이켜보고 심화하거나 보강해거나 확장해도 좋네.


---

**Cursor**

좋습니다. 먼저 **이번 라운드 빌드 결과 ZIP들을 “Stable/Beta/Staging → Master/Partner/Pro/Free” 구조로 한 폴더에 자동 정리**해두고, 그 다음에 대표님 테스트 동안 제가 진행할 **다음 라운드(안정성·업데이트·UX) 심화 계획**을 제안드리겠습니다.

좋습니다. 지금 배포 폴더(`JJ_Distributions_v8.0.0_Master_Control`)에는 ZIP이 채널별로는 이미 나뉘어 있지만(Stable/Beta/Staging/Master), 대표님이 원하신 **“채널 → (Master/Partner/Pro/Free)” 폴더 구조로 ‘최신 ZIP만’ 정리된 테스트 번들 폴더**는 아직 없습니다.  
지금부터 (1) Partner/Master도 **Beta ZIP 생성**되도록 빌드 스크립트를 보강하고, (2) 최신 빌드만 골라서 **테스트 번들 폴더를 자동 생성**하겠습니다.

```python
import os
import shutil
import zipfile
import re
import json
import subprocess
from datetime import datetime
import time

# ============================================================
# JJ Deployment System (JDS) Configuration
# ============================================================
BASE_VERSION = '8.0.0'
VERSIONS = {
    'core': BASE_VERSION,
    'neural': '3.9.9',
    'ai': '2.0.5',
    'woo': '2.0.0',
    'bulk': '2.2.2',
    'menu': '2.0.0'
}

# Output Directory
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
OUTPUT_ROOT = os.path.join(os.environ['USERPROFILE'], 'Desktop', f'JJ_Distributions_v{BASE_VERSION}_Master_Control')
LOG_DIR = os.path.join(OUTPUT_ROOT, 'deploy_logs')

# Source Maps
SOURCES = {
    'core': 'acf-css-really-simple-style-management-center-master',
    'ai': 'acf-css-ai-extension',
    'neural': 'acf-css-neural-link',
    'woo': 'marketing/wordpress-plugins/acf-css-woo-license',
    'bulk': 'marketing/wordpress-plugins/wp-bulk-installer',
    'menu': 'marketing/wordpress-plugins/admin-menu-editor-lite'
}

# Edition Configuration
EDITIONS = {
    'free':      {'suffix': '',             'license': 'FREE',      'channels': ['stable', 'beta', 'staging']},
    'basic':     {'suffix': '-Pro-Basic',   'license': 'BASIC',     'channels': ['stable', 'beta', 'staging']},
    'premium':   {'suffix': '-Pro-Premium', 'license': 'PREMIUM',   'channels': ['stable', 'beta', 'staging']},
    'unlimited': {'suffix': '-Pro-Unlimited','license': 'UNLIMITED','channels': ['stable', 'beta', 'staging']},
    # 내부/파트너도 운영 중에는 beta 업데이트 수신을 막을 수 있지만,
    # 테스트/검증을 위해 beta ZIP 생성은 항상 가능하도록 유지합니다.
    'partner':   {'suffix': '-Partner',     'license': 'PARTNER',   'channels': ['stable', 'beta', 'staging']},
    'master':    {'suffix': '-Master',      'license': 'MASTER',    'channels': ['stable', 'beta', 'staging']}
}

# Add-on Editions (Channels)
ADDON_CHANNELS = {
    'stable': '',
    'beta': '-beta',
    'staging': '-staging',
    'master': '-master' # Special channel for Master Unlocked Addons
}

# Exclude Patterns
EXCLUDE_PATTERNS = [
    r'^\.git', r'^\.vscode', r'^\.idea', r'__pycache__', r'\.DS_Store$',
    r'^tests', r'^phpunit\.xml', r'^composer\.json', r'node_modules',
    r'^package\.json', r'^package-lock\.json', r'^gulpfile\.js', 
    r'^\.editorconfig', r'^README\.md', r'\.bak$', r'local-server/venv', r'\.py$'
]

class JJ_Deployment_Engine:
    def __init__(self):
        self.build_time = datetime.now()
        self.build_id = self.build_time.strftime('%Y%m%d-%H%M%S')
        self.log_data = {
            'build_id': self.build_id,
            'timestamp': self.build_time.isoformat(),
            'builds': []
        }
        self._prepare_directories()

    def _prepare_directories(self):
        if not os.path.exists(OUTPUT_ROOT):
            os.makedirs(OUTPUT_ROOT)
        if not os.path.exists(LOG_DIR):
            os.makedirs(LOG_DIR)

    def _get_git_info(self):
        try:
            # Get last commit message
            msg = subprocess.check_output(['git', 'log', '-1', '--pretty=%B'], stderr=subprocess.STDOUT).decode().strip()
            # Get hash
            sha = subprocess.check_output(['git', 'rev-parse', '--short', 'HEAD'], stderr=subprocess.STDOUT).decode().strip()
            return {'commit': msg, 'hash': sha}
        except:
            return {'commit': 'Manual Build', 'hash': 'none'}

    def copy_files(self, src, dst):
        count = 0
        for root, dirs, files in os.walk(src):
            rel_root = os.path.relpath(root, src)
            if rel_root == ".": rel_root = ""
            
            dirs[:] = [d for d in dirs if not any(re.search(p, os.path.join(rel_root, d).replace('\\', '/')) for p in EXCLUDE_PATTERNS)]
            
            for file in files:
                rel_file_path = os.path.join(rel_root, file).replace('\\', '/')
                if any(re.search(p, rel_file_path) for p in EXCLUDE_PATTERNS):
                    continue
                    
                src_file = os.path.join(root, file)
                dst_file = os.path.join(dst, rel_file_path)
                
                os.makedirs(os.path.dirname(dst_file), exist_ok=True)
                shutil.copy2(src_file, dst_file)
                count += 1
        return count

    def process_core_file(self, file_path, edition, channel, version):
        if not os.path.exists(file_path): return
        
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()

        name_suffix = ""
        if edition != 'free':
            if edition == 'master': name_suffix = " (Master)"
            elif edition in ['basic', 'premium', 'unlimited']: name_suffix = " PRO"
            else: name_suffix = f" ({edition.capitalize()})"
        
        channel_label = ""
        if channel == 'beta': channel_label = " [BETA]"
        elif channel == 'staging': channel_label = " [STAGING]"

        new_name = f"ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager{name_suffix}{channel_label}"
        content = re.sub(r"Plugin Name:.*", f"Plugin Name:       {new_name}", content)

        content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_EDITION',\s*'[^']+'\s*\);", 
                        f"define( 'JJ_STYLE_GUIDE_EDITION', '{edition}' );", content)
        license_type = EDITIONS[edition]['license']
        content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_LICENSE_TYPE',\s*'[^']+'\s*\);", 
                        f"define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', '{license_type}' );", content)
        content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_VERSION',\s*'[^']+'\s*\);", 
                        f"define( 'JJ_STYLE_GUIDE_VERSION', '{version}' );", content)
        
        channel_const = f"define( 'JJ_STYLE_GUIDE_UPDATE_CHANNEL', '{channel}' );"
        if "JJ_STYLE_GUIDE_UPDATE_CHANNEL" in content:
            content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_UPDATE_CHANNEL',\s*'[^']+'\s*\);", channel_const, content)
        else:
            content = re.sub(r"(define\(\s*'JJ_STYLE_GUIDE_VERSION'.*?;)", f"\\1\n{channel_const}", content)

        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)

    def process_addon_file(self, file_path, channel, plugin_key):
        if not os.path.exists(file_path): return
        
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()

        # Inject Master Mode Constant for Master Channel
        if channel == 'master':
            master_const = ""
            if plugin_key == 'bulk':
                master_const = "if ( ! defined( 'JJ_BULK_INSTALLER_LICENSE' ) ) define( 'JJ_BULK_INSTALLER_LICENSE', 'MASTER' );"
            elif plugin_key == 'menu':
                master_const = "if ( ! defined( 'JJ_ADMIN_MENU_EDITOR_LICENSE' ) ) define( 'JJ_ADMIN_MENU_EDITOR_LICENSE', 'MASTER' );"
            elif plugin_key == 'ai':
                master_const = "if ( ! defined( 'JJ_AI_EXTENSION_LICENSE' ) ) define( 'JJ_AI_EXTENSION_LICENSE', 'MASTER' );"
            elif plugin_key == 'woo':
                master_const = "if ( ! defined( 'JJ_WOO_LICENSE_LICENSE' ) ) define( 'JJ_WOO_LICENSE_LICENSE', 'MASTER' );"
            elif plugin_key == 'neural':
                master_const = "if ( ! defined( 'JJ_NEURAL_LINK_LICENSE' ) ) define( 'JJ_NEURAL_LINK_LICENSE', 'MASTER' );"

            if master_const:
                # Insert after the first <?php
                if "LICENSE', 'MASTER'" not in content:
                    content = re.sub(r"(<\?php)", f"\\1\n{master_const}", content, count=1)
            
            # Change Plugin Name
            content = re.sub(r"Plugin Name:(.*)", r"Plugin Name:\1 (Master)", content)

        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)

    def build_core(self):
        print(f"📦 Building Core Editions...")
        
        for edition, config in EDITIONS.items():
            for channel in config['channels']:
                ver = VERSIONS['core']
                if channel == 'beta': ver += '-beta.1'
                elif channel == 'staging': ver += f'-staging.{self.build_id}'

                channel_dir = os.path.join(OUTPUT_ROOT, channel.capitalize())
                temp_dir = os.path.join(channel_dir, 'temp', f'acf-css-manager-{edition}')
                zip_name = f"ACF-CSS{config['suffix']}-v{ver}.zip"
                zip_path = os.path.join(channel_dir, zip_name)

                if os.path.exists(temp_dir): shutil.rmtree(temp_dir)
                os.makedirs(temp_dir)
                
                self.copy_files(SOURCES['core'], temp_dir)
                
                main_file = os.path.join(temp_dir, 'acf-css-really-simple-style-guide.php')
                self.process_core_file(main_file, edition, channel, ver)

                with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
                    for root, dirs, files in os.walk(temp_dir):
                        for file in files:
                            file_path = os.path.join(root, file)
                            arcname = os.path.relpath(file_path, os.path.dirname(temp_dir))
                            zf.write(file_path, arcname)
                
                shutil.rmtree(os.path.dirname(temp_dir))

                self.log_data['builds'].append({
                    'type': 'core',
                    'edition': edition,
                    'channel': channel,
                    'version': ver,
                    'file': zip_name
                })
                print(f"  ✓ {edition.upper()} [{channel}] -> {zip_name}")

    def build_addons(self):
        print(f"📦 Building Add-ons...")
        addons = [
            ('ai', 'ACF-CSS-AI-Extension', 'acf-css-ai-extension', 'acf-css-ai-extension.php'),
            ('neural', 'ACF-CSS-Neural-Link', 'acf-css-neural-link', 'acf-css-neural-link.php'),
            ('woo', 'ACF-CSS-Woo-License', 'acf-css-woo-license', 'acf-css-woo-license.php'),
            ('bulk', 'WP-Bulk-Manager', 'wp-bulk-installer', 'wp-bulk-installer.php'),
            ('menu', 'Admin-Menu-Editor-Lite', 'admin-menu-editor-lite', 'admin-menu-editor-lite.php')
        ]

        # Channels to build for addons
        channels = ['stable', 'beta', 'staging', 'master']

        for key, name_base, folder_name, main_filename in addons:
            for channel in channels:
                ver = VERSIONS[key]
                
                # Suffix for master
                suffix = ""
                if channel == 'master':
                    suffix = "-Master"
                
                # Skip beta for some addons if not needed, but keeping for consistency
                
                channel_dir = os.path.join(OUTPUT_ROOT, channel.capitalize())
                if not os.path.exists(channel_dir): os.makedirs(channel_dir)
                
                temp_dir = os.path.join(channel_dir, 'temp_addon', folder_name)
                if os.path.exists(temp_dir): shutil.rmtree(temp_dir)
                os.makedirs(temp_dir)
                
                # Copy Files
                self.copy_files(SOURCES[key], temp_dir)
                
                # Process File (Inject Master Constant)
                main_file_path = os.path.join(temp_dir, main_filename)
                if os.path.exists(main_file_path):
                    self.process_addon_file(main_file_path, channel, key)
                
                # Zip
                zip_name = f"{name_base}{suffix}-v{ver}.zip"
                zip_path = os.path.join(channel_dir, zip_name)
                
                with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
                    for root, dirs, files in os.walk(temp_dir):
                        for file in files:
                            file_path = os.path.join(root, file)
                            arcname = os.path.relpath(file_path, os.path.dirname(temp_dir))
                            zf.write(file_path, arcname)
                
                shutil.rmtree(os.path.dirname(temp_dir))
                
                self.log_data['builds'].append({
                    'type': 'addon',
                    'edition': key,
                    'channel': channel,
                    'version': ver,
                    'file': zip_name
                })
                
            print(f"  ✓ {name_base} -> Built all channels (incl. Master)")

    def generate_dashboard(self):
        git_info = self._get_git_info()
        
        html_content = f"""
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>JJ Deployment Center - Build {self.build_id}</title>
            <style>
                body {{ font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f0f2f5; padding: 40px; }}
                .container {{ max-width: 1200px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }}
                h1 {{ color: #1a1a1a; border-bottom: 2px solid #eee; padding-bottom: 20px; }}
                .meta {{ color: #666; margin-bottom: 30px; font-size: 0.9em; }}
                .channel-section {{ margin-bottom: 40px; }}
                .channel-title {{ font-size: 1.5em; font-weight: bold; color: #2271b1; margin-bottom: 15px; display: flex; align-items: center; }}
                .channel-badge {{ font-size: 0.5em; text-transform: uppercase; background: #2271b1; color: white; padding: 4px 8px; border-radius: 4px; margin-left: 10px; }}
                .beta .channel-badge {{ background: #e67e22; }}
                .staging .channel-badge {{ background: #8e44ad; }}
                .master .channel-badge {{ background: #c0392b; }}
                table {{ width: 100%; border-collapse: collapse; margin-top: 10px; }}
                th, td {{ text-align: left; padding: 12px; border-bottom: 1px solid #eee; }}
                th {{ background: #f9f9f9; font-weight: 600; color: #444; }}
                tr:hover {{ background: #f8f9fa; }}
                .file-link {{ color: #2271b1; text-decoration: none; font-weight: 500; }}
                .file-link:hover {{ text-decoration: underline; }}
                .log-section {{ background: #2d3436; color: #dfe6e9; padding: 20px; border-radius: 8px; font-family: monospace; margin-top: 40px; }}
            </style>
        </head>
        <body>
            <div class="container">
                <h1>🚀 JJ Deployment Command Center</h1>
                <div class="meta">
                    <strong>Build ID:</strong> {self.build_id}<br>
                    <strong>Timestamp:</strong> {self.build_time.strftime('%Y-%m-%d %H:%M:%S')}<br>
                    <strong>Last Commit:</strong> [{git_info['hash']}] {git_info['commit']}
                </div>
        """

        channels = ['Stable', 'Beta', 'Staging', 'Master']
        for channel in channels:
            class_name = channel.lower()
            html_content += f"""
                <div class="channel-section {class_name}">
                    <div class="channel-title">{channel} Channel <span class="channel-badge">{channel}</span></div>
                    <table>
                        <thead>
                            <tr>
                                <th>Edition/Plugin</th>
                                <th>Version</th>
                                <th>Filename</th>
                            </tr>
                        </thead>
                        <tbody>
            """
            
            # Filter builds for this channel
            channel_builds = [b for b in self.log_data['builds'] if b['channel'] == channel.lower()]
            
            for build in channel_builds:
                html_content += f"""
                            <tr>
                                <td>{build['edition'].upper()}</td>
                                <td>{build['version']}</td>
                                <td><a href="{channel}/{build['file']}" class="file-link">{build['file']}</a></td>
                            </tr>
                """
            
            html_content += """
                        </tbody>
                    </table>
                </div>
            """

        html_content += f"""
                <div class="log-section">
                    <h3>📜 System Logs</h3>
                    <pre>{json.dumps(self.log_data, indent=2)}</pre>
                </div>
            </div>
        </body>
        </html>
        """

        dashboard_path = os.path.join(OUTPUT_ROOT, 'dashboard.html')
        with open(dashboard_path, 'w', encoding='utf-8') as f:
            f.write(html_content)
        
        # Save JSON log
        json_log_path = os.path.join(LOG_DIR, f'build_{self.build_id}.json')
        with open(json_log_path, 'w', encoding='utf-8') as f:
            json.dump(self.log_data, f, indent=4)

        print(f"\n📊 Dashboard Generated: {dashboard_path}")

    def run(self):
        print("="*60)
        print("🔥 JJ DEPLOYMENT SYSTEM (JDS) - INITIATING LAUNCH SEQUENCE")
        print("="*60)
        print(f"Target: {OUTPUT_ROOT}")
        
        self.build_core()
        self.build_addons()
        self.generate_dashboard()
        
        print("="*60)
        print("🚀 MISSION ACCOMPLISHED. READY FOR DEPLOYMENT.")
        print("="*60)

if __name__ == '__main__':
    engine = JJ_Deployment_Engine()
    engine.run()

```

```python
import os
import re
import json
import shutil
from pathlib import Path


BASE_VERSION = "8.0.0"
OUTPUT_ROOT = Path(os.environ["USERPROFILE"]) / "Desktop" / f"JJ_Distributions_v{BASE_VERSION}_Master_Control"
LOG_DIR = OUTPUT_ROOT / "deploy_logs"


def _parse_semver(v: str):
    parts = v.split(".")
    return tuple(int(p) for p in parts[:3])


def _pick_latest_semver_zip(files):
    files = list(files)
    if not files:
        return None

    best = None
    best_ver = None
    for f in files:
        m = re.search(r"v(\d+\.\d+\.\d+)\.zip$", f.name)
        if not m:
            continue
        ver = _parse_semver(m.group(1))
        if best_ver is None or ver > best_ver:
            best_ver = ver
            best = f

    if best is None:
        best = max(files, key=lambda p: p.stat().st_mtime)
    return best


def _latest_build_id():
    logs = sorted(LOG_DIR.glob("build_*.json"))
    if not logs:
        return None
    latest = logs[-1]
    m = re.match(r"build_(\d{8}-\d{6})\.json$", latest.name)
    if m:
        return m.group(1)
    try:
        data = json.loads(latest.read_text(encoding="utf-8"))
        return data.get("build_id")
    except Exception:
        return None


def _ensure_dir(p: Path):
    p.mkdir(parents=True, exist_ok=True)


def _copy_to_dir(src: Path, dst_dir: Path):
    _ensure_dir(dst_dir)
    shutil.copy2(src, dst_dir / src.name)


def main():
    if not OUTPUT_ROOT.exists():
        raise SystemExit(f"Output root not found: {OUTPUT_ROOT}")

    build_id = _latest_build_id()
    if not build_id:
        raise SystemExit(f"No build logs found in: {LOG_DIR}")

    dest_root = Path(os.environ["USERPROFILE"]) / "Desktop" / f"JJ_TestBundle_v{BASE_VERSION}_{build_id}"
    if dest_root.exists():
        shutil.rmtree(dest_root)
    _ensure_dir(dest_root)

    # Copy dashboard for convenience (optional)
    dashboard = OUTPUT_ROOT / "dashboard.html"
    if dashboard.exists():
        shutil.copy2(dashboard, dest_root / "dashboard.html")

    channels = {
        "Staging": ("Staging", f"{BASE_VERSION}-staging.{build_id}"),
        "Beta": ("Beta", f"{BASE_VERSION}-beta.1"),
        "Stable": ("Stable", f"{BASE_VERSION}"),
    }

    editions = ["Master", "Partner", "Pro", "Free"]
    core_suffixes = {
        "Master": ["-Master"],
        "Partner": ["-Partner"],
        "Pro": ["-Pro-Basic", "-Pro-Premium", "-Pro-Unlimited"],
        "Free": [""],
    }

    standard_addon_prefixes = {
        "ai": "ACF-CSS-AI-Extension-v",
        "neural": "ACF-CSS-Neural-Link-v",
        "woo": "ACF-CSS-Woo-License-v",
        "bulk": "WP-Bulk-Manager-v",
        "menu": "Admin-Menu-Editor-Lite-v",
    }
    master_addon_prefixes = {
        "ai": "ACF-CSS-AI-Extension-Master-v",
        "neural": "ACF-CSS-Neural-Link-Master-v",
        "woo": "ACF-CSS-Woo-License-Master-v",
        "bulk": "WP-Bulk-Manager-Master-v",
        "menu": "Admin-Menu-Editor-Lite-Master-v",
    }

    master_addon_src = OUTPUT_ROOT / "Master"
    master_addons = []
    for _, prefix in master_addon_prefixes.items():
        picked = _pick_latest_semver_zip(master_addon_src.glob(prefix + "*.zip"))
        if picked:
            master_addons.append(picked)

    for dest_channel, (src_folder, core_ver) in channels.items():
        src_dir = OUTPUT_ROOT / src_folder
        if not src_dir.exists():
            continue

        standard_addons = []
        for _, prefix in standard_addon_prefixes.items():
            picked = _pick_latest_semver_zip(src_dir.glob(prefix + "*.zip"))
            if picked:
                standard_addons.append(picked)

        for edition in editions:
            dst_dir = dest_root / dest_channel / edition
            _ensure_dir(dst_dir)

            # Core ZIPs
            for suff in core_suffixes[edition]:
                core_name = f"ACF-CSS{suff}-v{core_ver}.zip"
                core_src = src_dir / core_name
                if core_src.exists():
                    _copy_to_dir(core_src, dst_dir)
                else:
                    (dst_dir / f"__MISSING__{core_name}.txt").write_text(
                        f"Missing expected file:\n{core_src}\n",
                        encoding="utf-8",
                    )

            # Add-ons (optional convenience)
            addons_dir = dst_dir / "Add-ons"
            _ensure_dir(addons_dir)
            addons = master_addons if edition in ("Master", "Partner") else standard_addons
            for a in addons:
                _copy_to_dir(a, addons_dir)

    # Root README
    readme = dest_root / "README_INSTALL_ORDER.md"
    readme.write_text(
        "\n".join(
            [
                f"# JJ Test Bundle v{BASE_VERSION} ({build_id})",
                "",
                "## 폴더 구조",
                "- Staging / Beta / Stable",
                "  - Master / Partner / Pro / Free",
                "    - Core ZIP(들)",
                "    - Add-ons (참고용)",
                "",
                "## 권장 테스트 설치 순서(대표님용)",
                "1) Core ZIP 설치/활성화",
                "2) (필요 시) Add-ons 설치/활성화",
                "3) Admin Center → Updates 탭: 통합 테이블/토글/액션 확인",
                "4) Admin Center → System Status → 자가 진단 실행 (원인 파일 출력 확인)",
                "",
                "## 참고",
                "- Master/Partner 폴더에는 (업그레이드/제한 UI 제거용) Master Add-ons ZIP이 들어있습니다.",
                "- Pro/Free 폴더에는 각 채널의 Standard Add-ons ZIP이 들어있습니다.",
                "",
            ]
        )
        + "\n",
        encoding="utf-8",
    )

    print(f"✅ Test bundle created: {dest_root}")


if __name__ == "__main__":
    main()


```

### 1) 요청하신 “채널 → 에디션” 분류 폴더로 ZIP 번들 생성 완료
아래 폴더에 **Staging / Beta / Stable → Master / Partner / Pro / Free** 구조로 “이번 빌드 기준 최신 ZIP”만 모아두었습니다.

- **번들 폴더 경로**: `C:\Users\computer\Desktop\JJ_TestBundle_v8.0.0_20251231-200638`

구성 특징:
- **각 에디션 폴더에 Core ZIP(들)**이 들어있습니다. (Pro는 Basic/Premium/Unlimited 3개)
- 각 에디션 폴더 안에 **`Add-ons/`도 함께 포함**했습니다.
  - **Master/Partner**: Master Add-ons(라이선스/업그레이드 유도 없는 버전)
  - **Pro/Free**: 채널별 Standard Add-ons

추가로 이번 요청에 맞춰 **Partner/Master도 Beta ZIP이 생성**되도록 빌드 시스템을 보강해 반영했습니다.
