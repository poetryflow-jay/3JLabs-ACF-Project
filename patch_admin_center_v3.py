import os

FILE_PATH = r"acf-css-really-simple-style-management-center-master\includes\class-jj-admin-center.php"

# 1. 업그레이드 버튼 로직 수정
MARKER_1 = "$is_master_version = ( defined( 'JJ_STYLE_GUIDE_ENV' ) && 'dev' === JJ_STYLE_GUIDE_ENV );"
REPLACE_1 = """$is_master_version = false;
                    if ( class_exists( 'JJ_Edition_Controller' ) ) {
                        $is_master_version = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                    }"""

# 2. 탭 메뉴 추가 (Updates 탭 앞에 Tools 추가)
MARKER_2 = '<li data-tab="updates">'
REPLACE_2 = """<li data-tab="tools">
                    <a href="#tools"><?php esc_html_e( '도구', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="updates">"""

# 3. 탭 내용 추가 (Updates 탭 앞에 Tools 추가)
# tab-updates.php include 구문을 찾아서 그 앞에 넣음
MARKER_3 = "<?php include JJ_STYLE_GUIDE_PATH . 'includes/admin/views/tabs/tab-updates.php'; ?>"
# 주의: tab-updates.php는 include가 아니라 하드코딩 되어 있을 수도 있음.
# 아까 tab-general.php는 include였음. tab-updates.php도 확인 필요.
# 만약 include가 아니라면, 다른 마커를 써야 함.
# 안전하게 License 탭 앞을 찾자.
MARKER_3_ALT = '<li data-tab="license">' # 이건 탭 메뉴고...
# 탭 내용 영역에서 찾아야 함.
# "<!-- 업데이트 탭 -->" 주석이 있을 수 있음.

# 그냥 파일 끝부분의 </div> (footer 앞) 앞에 넣는 게 나을 수도 있음.
# 하지만 탭 순서를 맞추려면 Updates 탭을 찾아야 함.

def patch_file():
    if not os.path.exists(FILE_PATH):
        print(f"❌ File not found: {FILE_PATH}")
        return

    with open(FILE_PATH, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. 업그레이드 버튼 로직
    if MARKER_1 in content:
        content = content.replace(MARKER_1, REPLACE_1)
        print("✅ Upgrade button logic patched.")
    else:
        print("⚠️ Upgrade button marker not found.")

    # 2. 탭 메뉴
    if MARKER_2 in content:
        content = content.replace(MARKER_2, REPLACE_2)
        print("✅ Tab menu patched.")
    else:
        print("⚠️ Tab menu marker not found.")

    # 3. 탭 내용 (Updates 탭 include 구문 찾기)
    # 아까 read_file에서 tab-general.php include는 봤음.
    # tab-updates.php include가 있는지 확인.
    if "tab-updates.php" in content:
        # include 구문이 있다고 가정
        marker_updates = "<?php include JJ_STYLE_GUIDE_PATH . 'includes/admin/views/tabs/tab-updates.php'; ?>"
        replace_updates = """<!-- Tools 탭 -->
                <?php 
                $tab_tools = JJ_STYLE_GUIDE_PATH . 'includes/admin/views/tabs/tab-tools.php';
                if ( file_exists( $tab_tools ) ) {
                    include $tab_tools;
                }
                ?>
                
                """ + marker_updates
        
        # 정확한 매칭이 안될 수 있으니 부분 매칭
        idx = content.find("tab-updates.php")
        if idx != -1:
            # 해당 라인의 시작(<?php include...)부터 찾아서 교체하기엔 복잡함.
            # 그냥 tab-updates.php가 포함된 줄 전체를 찾아서 교체 시도.
            # 하지만 줄바꿈/공백 때문에 위험.
            
            # 대안: '<!-- 업데이트 탭 -->' 같은 주석을 찾음.
            pass
    
    # 더 쉬운 방법: 탭 내용은 순서가 중요하지 않음 (CSS로 hide/show 하므로).
    # 그냥 탭 컨테이너의 끝부분(Footer 앞)에 추가해도 됨.
    # Footer 시작: <div class="jj-admin-center-footer"
    MARKER_FOOTER = '<div class="jj-admin-center-footer"'
    REPLACE_FOOTER = """<!-- Tools 탭 -->
                <?php 
                $tab_tools = JJ_STYLE_GUIDE_PATH . 'includes/admin/views/tabs/tab-tools.php';
                if ( file_exists( $tab_tools ) ) {
                    include $tab_tools;
                }
                ?>
                
            <div class="jj-admin-center-footer" """
    
    if MARKER_FOOTER in content:
        content = content.replace(MARKER_FOOTER, REPLACE_FOOTER)
        print("✅ Tab content added (before footer).")
    else:
        print("⚠️ Footer marker not found.")

    with open(FILE_PATH, 'w', encoding='utf-8') as f:
        f.write(content)

if __name__ == "__main__":
    patch_file()

