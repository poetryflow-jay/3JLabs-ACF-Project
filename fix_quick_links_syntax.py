file_path = 'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 문제의 구간: add_plugin_settings_link 메서드가 중복/파손됨
# 376번 줄 근처부터 시작되는 메서드를 찾아서, 그 뒤에 붙은 찌꺼기 코드(412~424번 줄)까지 포함하여 제거하고
# 깔끔한 메서드 하나로 대체

# 패턴: public function add_plugin_settings_link 부터... return $links; } ... return $links; } 까지 (두 번 반복되는 듯한 구조)

# 일단 'public function add_plugin_settings_link'의 시작 위치를 찾습니다.
start_index = content.find('public function add_plugin_settings_link')

if start_index != -1:
    # 시작 위치부터, 다음 함수 정의(public function add_admin_menu_page) 전까지의 내용을 가져옵니다.
    next_function_index = content.find('public function add_admin_menu_page', start_index)
    
    if next_function_index != -1:
        # 이 구간을 통째로 교체합니다.
        
        correct_method = """    /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * [Phase 4.99] 퀵 링크 확장: 설정 | 스타일 | 메뉴 | 비주얼
     */
    public function add_plugin_settings_link( $links ) {
        try {
            if ( ! function_exists( 'admin_url' ) || ! function_exists( 'esc_url' ) ) {
                return $links;
            }
            
            $new_links = array();
            
            // 1. 설정 (Admin Center 메인)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ) . '" style="font-weight: 600;">' . __( 'Settings', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 2. 스타일 (Style Guide)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ) ) . '">' . __( 'Styles', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 3. 어드민 메뉴 (Admin Menu Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#admin-menu' ) ) . '">' . __( 'Menu', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 4. 비주얼 (Visual Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#visual' ) ) . '">' . __( 'Visual', 'acf-css-really-simple-style-management-center' ) . '</a>';

            // 기존 링크와 병합
            return array_merge( $new_links, $links );
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Simple Style Guide: add_plugin_settings_link 오류 - ' . $e->getMessage() );
            }
            return $links;
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Simple Style Guide: add_plugin_settings_link 치명적 오류 - ' . $e->getMessage() );
            }
            return $links;
        }
    }

    """
        # 기존 내용 교체
        new_content = content[:start_index] + correct_method + content[next_function_index:]
        
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
            
        print("Fixed add_plugin_settings_link syntax error.")
    else:
        print("Error: Could not find the next function definition.")
else:
    print("Error: Could not find add_plugin_settings_link method.")

