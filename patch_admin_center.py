import os

FILE_PATH = r"acf-css-really-simple-style-management-center-master\includes\class-jj-admin-center.php"

START_MARKER = "<!-- Admin Menu 탭 (2패널 레이아웃) -->"
END_MARKER = "<!-- [v3.7.0 '신규'] 관리자 센터 설정 내보내기/불러오기 (푸터) -->"

REPLACEMENT_CODE = """                <!-- Admin Menu 탭 -->
                <?php 
                $tab_admin_menu = JJ_STYLE_GUIDE_PATH . 'includes/admin/views/tabs/tab-admin-menu.php';
                if ( file_exists( $tab_admin_menu ) ) {
                    include $tab_admin_menu; 
                } else {
                    echo '<div class="notice notice-error"><p>탭 파일을 찾을 수 없습니다: tab-admin-menu.php</p></div>';
                }
                ?>
"""

def patch_admin_center():
    if not os.path.exists(FILE_PATH):
        print(f"❌ File not found: {FILE_PATH}")
        return

    with open(FILE_PATH, 'r', encoding='utf-8') as f:
        content = f.read()

    start_idx = content.find(START_MARKER)
    end_idx = content.find(END_MARKER)

    if start_idx == -1 or end_idx == -1:
        print("❌ Markers not found in file.")
        print(f"Start found: {start_idx != -1}")
        print(f"End found: {end_idx != -1}")
        return

    # Replace content between markers
    # 주의: END_MARKER 앞부분까지 교체해야 함
    new_content = content[:start_idx] + REPLACEMENT_CODE + "\n" + (" " * 16) + content[end_idx:]

    with open(FILE_PATH, 'w', encoding='utf-8') as f:
        f.write(new_content)
    
    print("✅ Admin Center file patched successfully.")

if __name__ == "__main__":
    patch_admin_center()

