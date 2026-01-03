import zipfile
import os
from pathlib import Path

BASE_DIR = Path(__file__).parent
DIST_DIR = BASE_DIR / "dist"
DIST_DIR.mkdir(exist_ok=True)

PLUGINS = [
    {
        "folder": "acf-css-really-simple-style-management-center-master",
        "zip_name": "acf-css-manager-master-v21.0.1.zip"
    },
    {
        "folder": "wp-bulk-manager",
        "zip_name": "wp-bulk-manager-master-v3.0.0.zip"
    },
    {
        "folder": "acf-css-neural-link",
        "zip_name": "acf-css-neural-link-master-v5.0.0.zip"
    },
    {
        "folder": "acf-css-woo-license",
        "zip_name": "acf-css-woo-license-master-v21.0.0.zip"
    },
    {
        "folder": "acf-nudge-flow",
        "zip_name": "acf-nudge-flow-master-v21.0.0.zip"
    },
    {
        "folder": "acf-code-snippets-box",
        "zip_name": "acf-code-snippets-box-master-v2.0.0.zip"
    }
]

EXCLUDE_PATTERNS = [
    '.git', '.vscode', '.idea', '__pycache__', '.DS_Store',
    'tests', 'phpunit.xml', 'composer.json', 'node_modules',
    'package.json', 'package-lock.json', 'gulpfile.js',
    '.editorconfig', '.env', 'Thumbs.db', 'local-server/venv',
    'README.md', 'CHANGELOG.md'
]

def should_exclude(path_str):
    for pattern in EXCLUDE_PATTERNS:
        if pattern in path_str:
            return True
    return False

for plugin in PLUGINS:
    plugin_dir = BASE_DIR / plugin["folder"]
    if not plugin_dir.exists():
        print(f"⚠️ 폴더를 찾을 수 없음: {plugin['folder']}")
        continue
        
    zip_path = DIST_DIR / plugin["zip_name"]
    print(f"📦 ZIP 생성 중: {plugin['zip_name']}")
    
    with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
        for root, dirs, files in os.walk(plugin_dir):
            dirs[:] = [d for d in dirs if not should_exclude(d)]
            for file in files:
                if should_exclude(file):
                    continue
                file_path = Path(root) / file
                arcname = file_path.relative_to(plugin_dir.parent)
                zf.write(file_path, arcname)
    print(f"✅ 완료: {zip_path}")

print("\n🚀 모든 플러그인 빌드 완료!")
