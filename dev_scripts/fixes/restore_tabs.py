import os
import shutil

# 설정
SOURCE_DIR = r"c:\Users\computer\.cursor\worktrees\jj-css-premium\ezh\acf-css-really-simple-style-management-center-master\includes\admin\views\tabs"
DEST_DIR = r"acf-css-really-simple-style-management-center-master\includes\admin\views\tabs"

def restore_tabs():
    print("🚑 Starting Tab Recovery...")
    
    if not os.path.exists(SOURCE_DIR):
        print("❌ Source tabs directory not found in 'ezh' worktree.")
        return

    if not os.path.exists(DEST_DIR):
        os.makedirs(DEST_DIR)
        print(f"  ✅ Created directory: {DEST_DIR}")

    # 파일 복사
    for filename in os.listdir(SOURCE_DIR):
        src_file = os.path.join(SOURCE_DIR, filename)
        dst_file = os.path.join(DEST_DIR, filename)
        
        if os.path.isfile(src_file):
            shutil.copy2(src_file, dst_file)
            print(f"  ✅ Restored Tab: {filename}")
    
    print("🎉 Tab Recovery Complete.")

if __name__ == "__main__":
    restore_tabs()
