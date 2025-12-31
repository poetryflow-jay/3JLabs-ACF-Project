import os
import shutil
import zipfile
import json
import re
import time
from datetime import datetime

# ============================================================
# ⚙️ 설정 (Configuration)
# ============================================================
VERSION = '3.2.0'
BUILD_DIR = os.path.join(os.environ['USERPROFILE'], 'Desktop', 'JJ_Distributions_v3.2')
STATE_FILE = 'build_state.json'

# 경로 설정
PATHS = {
    'neural_link': 'acf-css-neural-link',
    'main_plugin': 'acf-css-really-simple-style-management-center-master',
    'marketing': 'marketing',
    'ai_extension': 'acf-css-ai-extension'
}

EDITIONS = ['free', 'basic', 'premium', 'partner', 'master']

# 제외 패턴
EXCLUDE_PATTERNS = [
    r'\.git', r'\.vscode', r'\.idea', r'__pycache__', r'\.DS_Store',
    r'tests', r'phpunit\.xml', r'composer\.json', r'node_modules',
    r'package\.json', r'package-lock\.json', r'gulpfile\.js', 
    r'\.editorconfig', r'README\.md'
]

class SmartBuilder:
    def __init__(self):
        self.state = self.load_state()
        self.ensure_build_dir()

    def ensure_build_dir(self):
        if not os.path.exists(BUILD_DIR):
            os.makedirs(BUILD_DIR)

    def load_state(self):
        if os.path.exists(STATE_FILE):
            try:
                with open(STATE_FILE, 'r') as f:
                    return json.load(f)
            except:
                pass
        return {'completed_steps': [], 'last_run': str(datetime.now())}

    def save_state(self):
        self.state['last_run'] = str(datetime.now())
        with open(STATE_FILE, 'w') as f:
            json.dump(self.state, f, indent=4)

    def reset_state(self):
        if os.path.exists(STATE_FILE):
            os.remove(STATE_FILE)
        self.state = {'completed_steps': [], 'last_run': str(datetime.now())}

    def run_step(self, step_id, description, func, *args, **kwargs):
        if step_id in self.state['completed_steps']:
            print(f"✅ [SKIP] {description} (이미 완료됨)")
            return

        print(f"🚀 [RUN] {description}...")
        try:
            func(*args, **kwargs)
            self.state['completed_steps'].append(step_id)
            self.save_state()
            print(f"   ✓ 완료")
            time.sleep(0.5) # 시스템 숨돌리기
        except Exception as e:
            print(f"   ❌ 오류 발생: {str(e)}")
            raise e

    # --- 유틸리티 함수 ---
    def copy_files(self, src, dst):
        if os.path.exists(dst):
            shutil.rmtree(dst)
        
        # shutil.copytree with ignore function
        def ignore_func(directory, files):
            return [f for f in files if any(re.search(p, f) for p in EXCLUDE_PATTERNS)]

        if os.path.exists(src):
            shutil.copytree(src, dst, ignore=ignore_func)
        else:
            print(f"⚠️ 경고: 소스 폴더를 찾을 수 없음 ({src})")

    def create_zip(self, source, zip_path):
        if not os.path.exists(source):
            return
            
        with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
            for root, dirs, files in os.walk(source):
                # 제외 패턴 폴더 필터링
                dirs[:] = [d for d in dirs if not any(re.search(p, d) for p in EXCLUDE_PATTERNS)]
                for file in files:
                    if any(re.search(p, file) for p in EXCLUDE_PATTERNS):
                        continue
                    file_path = os.path.join(root, file)
                    arcname = os.path.relpath(file_path, os.path.dirname(source))
                    zipf.write(file_path, arcname)

    # --- 실제 작업 함수들 ---
    def build_neural_link(self):
        src = PATHS['neural_link']
        dst_name = f"acf-css-neural-link-v{VERSION}"
        dst_path = os.path.join(BUILD_DIR, "temp", dst_name)
        zip_path = os.path.join(BUILD_DIR, f"{dst_name}.zip")
        
        self.copy_files(src, dst_path)
        self.create_zip(dst_path, zip_path)

    def build_ai_extension(self):
        src = PATHS['ai_extension']
        dst_name = f"acf-css-ai-extension-v{VERSION}"
        dst_path = os.path.join(BUILD_DIR, "temp", dst_name)
        zip_path = os.path.join(BUILD_DIR, f"{dst_name}.zip")
        
        self.copy_files(src, dst_path)
        self.create_zip(dst_path, zip_path)

    def build_edition(self, edition):
        src = PATHS['main_plugin']
        folder_name = f"acf-css-{edition}" # 폴더명은 에디션별로 다르게
        dst_path = os.path.join(BUILD_DIR, "temp", folder_name)
        zip_path = os.path.join(BUILD_DIR, f"acf-css-{edition}-v{VERSION}.zip")
        
        self.copy_files(src, dst_path)
        
        # 메인 파일 수정 (버전 및 에디션 정보)
        main_file = os.path.join(dst_path, 'acf-css-really-simple-style-guide.php')
        if os.path.exists(main_file):
            with open(main_file, 'r', encoding='utf-8') as f:
                content = f.read()
                
            content = re.sub(r"Version:\s*[\d\.]+", f"Version:           {VERSION}", content)
            content = content.replace("define( 'JJ_STYLE_GUIDE_VERSION', '1.0.0' );", f"define( 'JJ_STYLE_GUIDE_VERSION', '{VERSION}' );") 
            content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_VERSION',\s*'[^']+'\s*\);", f"define( 'JJ_STYLE_GUIDE_VERSION', '{VERSION}' );", content)
            content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_EDITION',\s*'[^']+'\s*\);", f"define( 'JJ_STYLE_GUIDE_EDITION', '{edition}' );", content)
            content = re.sub(r"define\(\s*'JJ_STYLE_GUIDE_LICENSE_TYPE',\s*'[^']+'\s*\);", f"define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', '{edition.upper()}' );", content)
            
            with open(main_file, 'w', encoding='utf-8') as f:
                f.write(content)
            
        self.create_zip(dst_path, zip_path)

    def build_marketing(self):
        src = PATHS['marketing']
        dst_name = "marketing-assets"
        dst_path = os.path.join(BUILD_DIR, "temp", dst_name)
        zip_path = os.path.join(BUILD_DIR, f"jj-marketing-assets-v{VERSION}.zip")
        
        self.copy_files(src, dst_path)
        self.create_zip(dst_path, zip_path)

    def cleanup(self):
        temp_dir = os.path.join(BUILD_DIR, "temp")
        if os.path.exists(temp_dir):
            shutil.rmtree(temp_dir)
        # 상태 파일 초기화 (모든 작업 완료 시)
        self.reset_state()

def main():
    print(f"🏁 JJ Build Manager v{VERSION} 시작")
    builder = SmartBuilder()
    
    # 1. Neural Link
    builder.run_step('build_neural', 'Neural Link 빌드', builder.build_neural_link)

    # 2. AI Extension
    builder.run_step('build_ai_ext', 'AI Extension 빌드', builder.build_ai_extension)

    # 3. Main Plugin Editions
    for edition in EDITIONS:
        builder.run_step(f'build_{edition}', f'Main Plugin [{edition}] 빌드', builder.build_edition, edition)

    # 4. Marketing Assets
    builder.run_step('build_marketing', 'Marketing Assets 패키징', builder.build_marketing)

    # 5. Cleanup
    builder.run_step('cleanup', '임시 파일 정리', builder.cleanup)
    
    print(f"\n✨ 모든 작업이 완료되었습니다! 결과물: {BUILD_DIR}")

if __name__ == '__main__':
    main()


