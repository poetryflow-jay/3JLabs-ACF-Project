import os
import shutil

# 설정
SOURCE_DIR = r"c:\Users\computer\.cursor\worktrees\jj-css-premium\ezh\acf-css-really-simple-style-management-center-master\includes"
DEST_DIR = r"acf-css-really-simple-style-management-center-master\includes"

# 복사할 파일 목록 (누락된 파일들)
FILES_TO_COPY = [
    "class-jj-backup-manager.php",
    "class-jj-code-integrity-monitor.php",
    "class-jj-css-cache.php",
    "class-jj-css-injector.php",
    "class-jj-customizer-manager.php",
    "class-jj-customizer-sync.php",
    "class-jj-error-handler.php",
    "class-jj-error-logger.php",
    "class-jj-labs-center.php",
    "class-jj-license-enforcement.php",
    "class-jj-license-issuer.php",
    "class-jj-license-manager.php",
    "class-jj-license-security-hardening.php",
    "class-jj-memory-manager.php",
    "class-jj-migration-manager.php",
    "class-jj-options-cache.php",
    "class-jj-safe-mode.php",
    "class-jj-selector-registry.php",
    "class-jj-strategy-0-customizer.php",
    "class-jj-strategy-2-php-filters.php",
    "class-jj-strategy-3-dequeue.php",
    "class-jj-sync-manager.php",
    "class-jj-theme-metadata.php",
    "class-jj-version-features.php"
]

def restore_files():
    print("🚑 Starting File Transfusion...")
    
    # 1. 개별 파일 복사
    for filename in FILES_TO_COPY:
        src = os.path.join(SOURCE_DIR, filename)
        dst = os.path.join(DEST_DIR, filename)
        
        if os.path.exists(src):
            shutil.copy2(src, dst)
            print(f"  ✅ Restored: {filename}")
        else:
            print(f"  ❌ Missing in Source: {filename}")

    # 2. editor-views 폴더 복사
    src_views = os.path.join(SOURCE_DIR, "editor-views")
    dst_views = os.path.join(DEST_DIR, "editor-views")
    if os.path.exists(src_views):
        if os.path.exists(dst_views):
            shutil.rmtree(dst_views)
        shutil.copytree(src_views, dst_views)
        print("  ✅ Restored: editor-views/")
    
    print("🎉 Transfusion Complete.")

if __name__ == "__main__":
    restore_files()

