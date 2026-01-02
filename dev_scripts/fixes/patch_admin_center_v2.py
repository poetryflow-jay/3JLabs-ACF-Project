import os

FILE_PATH = r"acf-css-really-simple-style-management-center-master\includes\class-jj-admin-center.php"

# 검색 마커 설정 (파일 내용을 확인하여 정확한 문자열 지정)
# 1. Admin Menu 탭 시작 부분
START_MARKER = '<!-- Admin Menu 탭 (2패널 레이아웃) -->'

# 2. Admin Menu 탭 끝나는 부분
# 1004라인 </div> 뒤에 나오는 주석을 찾습니다.
# "<!-- [v3.7.0 '신규'] 관리자 센터 설정 내보내기/불러오기 (푸터) -->"
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

def patch_file():
    if not os.path.exists(FILE_PATH):
        print(f"❌ File not found: {FILE_PATH}")
        return

    with open(FILE_PATH, 'r', encoding='utf-8') as f:
        content = f.read()

    # 위치 찾기
    start_idx = content.find(START_MARKER)
    if start_idx == -1:
        # 혹시 공백이나 줄바꿈 문제일 수 있으니 조금 더 짧게 검색
        START_MARKER_SHORT = 'data-tab="admin-menu">'
        temp_idx = content.find(START_MARKER_SHORT)
        if temp_idx != -1:
            # data-tab="admin-menu"> 앞쪽의 <div... 부터 찾아야 함
            # 역으로 <div class="jj-admin-center-tab-content" 찾기
            start_idx = content.rfind('<div class="jj-admin-center-tab-content"', 0, temp_idx)
            # 그 앞의 주석도 포함하면 좋음
            comment_idx = content.rfind('<!-- Admin Menu 탭', 0, start_idx)
            if comment_idx != -1:
                start_idx = comment_idx
        else:
            print("❌ Start marker not found.")
            return

    end_idx = content.find(END_MARKER)
    if end_idx == -1:
        print("❌ End marker not found.")
        return

    print(f"✅ Found block: {start_idx} ~ {end_idx}")

    # 교체 (end_idx 바로 앞까지, 즉 </div> 다음 줄바꿈까지 포함될 수 있음)
    # END_MARKER는 유지해야 하므로 end_idx 앞까지 자름
    
    # 하지만 탭이 닫히는 </div>가 END_MARKER 바로 앞에 있을 것임.
    # 안전하게 하기 위해 END_MARKER 바로 앞의 공백/줄바꿈을 포함하여 교체
    
    new_content = content[:start_idx] + REPLACEMENT_CODE + "\n" + content[end_idx:]

    with open(FILE_PATH, 'w', encoding='utf-8') as f:
        f.write(new_content)
    
    print("✅ File patched successfully.")

if __name__ == "__main__":
    patch_file()

