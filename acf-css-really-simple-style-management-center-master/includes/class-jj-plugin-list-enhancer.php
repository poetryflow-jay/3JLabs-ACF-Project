<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 19.1] 플러그인 목록 페이지 UI/UX 향상
 * 
 * 플러그인 목록 페이지에 다음 기능 추가:
 * - 자동 업데이트 토글 버튼 (AJAX)
 * - 향상된 액션 링크 (아이콘, 색상, 볼드체)
 * - 작성자 정보 영역 개선
 * - 툴팁 및 넛지 메시지 시스템
 * - 롤백 기능
 * - 필수/권장 플러그인 안내
 * 
 * @since 19.1.0
 */
class JJ_Plugin_List_Enhancer {

    private static $instance = null;
    private $plugin_file = '';
    private $plugin_basename = '';
    private $plugin_config = array();

    /**
     * 플러그인 설정 초기화
     * 
     * @param array $config 플러그인 설정 배열
     *   - 'plugin_file': 플러그인 메인 파일 경로
     *   - 'plugin_name': 플러그인 이름
     *   - 'settings_url': 설정 페이지 URL
     *   - 'text_domain': 텍스트 도메인
     *   - 'version_constant': 버전 상수명
     *   - 'license_constant': 라이센스 상수명
     *   - 'upgrade_url': 업그레이드 URL
     *   - 'docs_url': 문서 URL
     *   - 'support_url': 지원 URL
     */
    public function init( $config = array() ) {
        $this->plugin_config = wp_parse_args( $config, array(
            'plugin_file' => JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php',
            'plugin_name' => 'ACF CSS 설정 관리자',
            'settings_url' => admin_url( 'options-general.php?page=jj-admin-center' ),
            'text_domain' => 'acf-css-really-simple-style-management-center',
            'version_constant' => 'JJ_STYLE_GUIDE_VERSION',
            'license_constant' => 'JJ_STYLE_GUIDE_LICENSE_TYPE',
            'upgrade_url' => 'https://3j-labs.com/',
            'docs_url' => admin_url( 'options-general.php?page=jj-admin-center#system-status' ),
            'support_url' => 'https://3j-labs.com/support',
        ) );
        
        $this->plugin_file = $this->plugin_config['plugin_file'];
        $this->plugin_basename = plugin_basename( $this->plugin_file );
        
        // 플러그인 행에 메타 정보 추가 (버전 아래)
        add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 2 );
        
        // 플러그인 행에 동작 링크 추가 (비활성화 옆)
        add_filter( 'plugin_action_links_' . $this->plugin_basename, array( $this, 'enhance_plugin_action_links' ), 10, 1 );
        
        // 작성자 정보 개선 (플러그인 설명 아래)
        add_filter( 'plugin_row_meta', array( $this, 'enhance_author_info' ), 5, 2 );
        
        // 자동 업데이트 토글 AJAX 핸들러
        $ajax_action = 'jj_toggle_auto_update_' . sanitize_key( str_replace( array( '/', '\\' ), '_', $this->plugin_basename ) );
        add_action( 'wp_ajax_' . $ajax_action, array( $this, 'ajax_toggle_auto_update' ) );
        
        // 롤백 AJAX 핸들러
        $rollback_action = 'jj_rollback_plugin_' . sanitize_key( str_replace( array( '/', '\\' ), '_', $this->plugin_basename ) );
        add_action( 'wp_ajax_' . $rollback_action, array( $this, 'ajax_rollback_plugin' ) );
        
        // 넛지 메시지 dismiss AJAX 핸들러 (한 번만 등록)
        if ( ! has_action( 'wp_ajax_jj_dismiss_nudge', array( $this, 'ajax_dismiss_nudge' ) ) ) {
            add_action( 'wp_ajax_jj_dismiss_nudge', array( $this, 'ajax_dismiss_nudge' ) );
        }
        
        // 스타일 및 스크립트 로드
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        
        // 넛지 메시지 시스템
        add_action( 'admin_footer', array( $this, 'render_nudge_overlay' ) );
    }

    /**
     * 플러그인 행 메타 정보 추가 (버전 아래)
     */
    public function add_plugin_row_meta( $plugin_meta, $plugin_file ) {
        // 우리 플러그인이 아니면 반환
        if ( $plugin_file !== $this->plugin_basename ) {
            return $plugin_meta;
        }

        $new_meta = array();
        $text_domain = $this->plugin_config['text_domain'];

        // 1. 3J Labs 공식 사이트 (툴팁 포함, 강조)
        $new_meta[] = '<a href="' . esc_url( 'https://3j-labs.com/' ) . '" target="_blank" rel="noopener noreferrer" class="jj-tooltip" data-tooltip="' . esc_attr__( '3J Labs 공식 웹사이트 방문', $text_domain ) . '" style="font-size: 14px; color: #2271b1; font-weight: 800; text-decoration: none; border-bottom: 2px solid #2271b1; padding-bottom: 2px; margin-right: 10px;">🌐 <strong>' . __( '공식 사이트', $text_domain ) . '</strong></a>';

        // 2. 문서 (툴팁 포함, 강조)
        $new_meta[] = '<a href="' . esc_url( $this->plugin_config['docs_url'] ) . '" class="jj-tooltip" data-tooltip="' . esc_attr__( '플러그인 문서 및 사용 가이드', $text_domain ) . '" style="font-size: 14px; color: #135e96; font-weight: 800; text-decoration: none; border-bottom: 2px solid #135e96; padding-bottom: 2px; margin-right: 10px;">📚 <strong>' . __( '문서', $text_domain ) . '</strong></a>';

        // 3. 지원 (툴팁 포함, 강조)
        $new_meta[] = '<a href="' . esc_url( $this->plugin_config['support_url'] ) . '" target="_blank" rel="noopener noreferrer" class="jj-tooltip" data-tooltip="' . esc_attr__( '기술 지원 및 문의', $text_domain ) . '" style="font-size: 14px; color: #50575e; font-weight: 800; text-decoration: none; border-bottom: 2px solid #50575e; padding-bottom: 2px; margin-right: 10px;">💬 <strong>' . __( '지원', $text_domain ) . '</strong></a>';
        
        // 4. 추가 리소스 링크들 (강조)
        $new_meta[] = '<a href="' . esc_url( $this->plugin_config['upgrade_url'] ) . '" target="_blank" rel="noopener noreferrer" class="jj-tooltip" data-tooltip="' . esc_attr__( '업그레이드 및 프리미엄 기능', $text_domain ) . '" style="font-size: 14px; color: #00a32a; font-weight: 800; text-decoration: none; margin-right: 10px;">⭐ <strong>' . __( '업그레이드', $text_domain ) . '</strong></a>';
        
        $new_meta[] = '<a href="' . esc_url( $this->plugin_config['docs_url'] ) . '#changelog" class="jj-tooltip" data-tooltip="' . esc_attr__( '최신 변경사항 및 릴리즈 노트', $text_domain ) . '" style="font-size: 14px; color: #856404; font-weight: 800; text-decoration: none; margin-right: 10px;">📝 <strong>' . __( '변경사항', $text_domain ) . '</strong></a>';
        
        $new_meta[] = '<a href="' . esc_url( $this->plugin_config['support_url'] ) . '/community" target="_blank" rel="noopener noreferrer" class="jj-tooltip" data-tooltip="' . esc_attr__( '커뮤니티에 참여하세요', $text_domain ) . '" style="font-size: 14px; color: #f5576c; font-weight: 800; text-decoration: none; margin-right: 10px;">👥 <strong>' . __( '커뮤니티', $text_domain ) . '</strong></a>';

        // 4. 필수 플러그인 안내 (필요시)
        $required_plugins = $this->get_required_plugins();
        if ( ! empty( $required_plugins ) ) {
            $required_list = array();
            foreach ( $required_plugins as $req ) {
                if ( ! is_plugin_active( $req['file'] ) ) {
                    $required_list[] = $req['name'];
                }
            }
            if ( ! empty( $required_list ) ) {
                $new_meta[] = '<span style="color: #d63638; font-weight: 700;">⚠️ ' . __( '필수: ', $text_domain ) . esc_html( implode( ', ', $required_list ) ) . '</span>';
            }
        }

        // 5. 권장 플러그인 안내
        $recommended_plugins = $this->get_recommended_plugins();
        if ( ! empty( $recommended_plugins ) ) {
            $recommended_list = array();
            foreach ( $recommended_plugins as $rec ) {
                if ( ! is_plugin_active( $rec['file'] ) ) {
                    $recommended_list[] = $rec['name'];
                }
            }
            if ( ! empty( $recommended_list ) ) {
                $new_meta[] = '<span style="color: #856404; font-weight: 600;">💡 ' . __( '권장: ', $text_domain ) . esc_html( implode( ', ', $recommended_list ) ) . '</span>';
            }
        }

        // 6. 자동 업데이트 상태 표시 및 토글 버튼 (모든 플러그인에 필수, 마스터 버전이든 아니든)
        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
        $is_auto_update_enabled = in_array( $this->plugin_basename, $auto_updates, true );
        
        $auto_update_class = 'jj-auto-update-toggle';
        $auto_update_nonce = wp_create_nonce( 'jj_toggle_auto_update_' . $this->plugin_basename );
        $auto_update_text = $is_auto_update_enabled ? __( '자동 업데이트 활성화', $text_domain ) : __( '자동 업데이트 비활성화', $text_domain );
        $auto_update_icon = $is_auto_update_enabled ? '✅' : '⚪';
        $auto_update_color = $is_auto_update_enabled ? '#00a32a' : '#d63638';
        
        $new_meta[] = sprintf(
            '<a href="#" class="%s jj-tooltip" data-plugin="%s" data-nonce="%s" data-enabled="%s" data-tooltip="%s" style="font-size: 14px; font-weight: 800; color: %s; text-decoration: none; cursor: pointer; border: 1px solid %s; padding: 4px 10px; border-radius: 4px; background: %s; display: inline-block; margin-left: 5px;">%s <strong>%s</strong></a>',
            esc_attr( $auto_update_class ),
            esc_attr( $this->plugin_basename ),
            esc_attr( $auto_update_nonce ),
            $is_auto_update_enabled ? '1' : '0',
            esc_attr( __( '클릭하여 자동 업데이트를 토글합니다', $text_domain ) ),
            esc_attr( $auto_update_color ),
            esc_attr( $auto_update_color ),
            $is_auto_update_enabled ? 'rgba(0, 163, 42, 0.1)' : 'rgba(214, 54, 56, 0.1)',
            $auto_update_icon,
            esc_html( $auto_update_text )
        );

        // 7. 라이센스 키 필요 안내 (Free 버전인 경우, 강조)
        if ( ! $this->is_premium() ) {
            $new_meta[] = '<a href="' . esc_url( $this->plugin_config['settings_url'] ) . '#license" class="jj-tooltip" data-tooltip="' . esc_attr__( '라이센스 키를 입력하여 Pro 기능을 활성화하세요', $text_domain ) . '" style="font-size: 14px; color: #2271b1; font-weight: 800; text-decoration: none; border: 2px solid #2271b1; padding: 4px 10px; border-radius: 4px; background: rgba(34, 113, 177, 0.1); display: inline-block; margin-left: 5px;">🔑 <strong>' . __( '라이센스 키 입력', $text_domain ) . '</strong></a>';
        }
        
        // 8. 비활성화 링크 강조 (기존 링크에 스타일 추가를 위해)
        // WordPress 기본 비활성화 링크는 이미 있으므로, 여기서는 추가 메타 정보만 제공

        return array_merge( $plugin_meta, $new_meta );
    }

    /**
     * 플러그인 동작 링크 향상
     * 
     * [v23.0.2] 모든 플러그인에 적용, 볼드, 아이콘, 색상, 큰 글꼴, 기능별 숏컷 링크로 대폭 개선
     */
    public function enhance_plugin_action_links( $links ) {
        $new_links = array();
        $text_domain = $this->plugin_config['text_domain'];
        
        // 플러그인별 기능 링크 정의
        $feature_links = $this->get_feature_links();
        
        // 1. 설정 링크 (모든 플러그인에 필수)
        if ( ! empty( $this->plugin_config['settings_url'] ) ) {
            $new_links['settings'] = sprintf(
                '<a href="%s" class="jj-tooltip" data-tooltip="%s" style="font-size: 15px; font-weight: 800; color: #2271b1; text-decoration: none; margin-right: 10px; display: inline-block; border-bottom: 2px solid #2271b1; padding-bottom: 2px;">⚙️ <strong>%s</strong></a>',
                esc_url( $this->plugin_config['settings_url'] ),
                esc_attr__( '플러그인 설정 페이지로 이동', $text_domain ),
                __( '설정', $text_domain )
            );
        }
        
        // 2. 주요 기능 링크들 (플러그인별로 다름)
        foreach ( $feature_links as $key => $link_config ) {
            if ( $key === 'settings' ) continue; // 이미 추가됨
            $new_links[ $key ] = sprintf(
                '<a href="%s" class="jj-tooltip" data-tooltip="%s" style="font-size: 14px; font-weight: 700; color: %s; text-decoration: none; margin-right: 8px; display: inline-block;">%s <strong>%s</strong></a>',
                esc_url( $link_config['url'] ),
                esc_attr( $link_config['tooltip'] ),
                esc_attr( $link_config['color'] ),
                $link_config['icon'],
                esc_html( $link_config['label'] )
            );
        }

        // 3. 롤백 링크 (모든 플러그인에 필수) - [v23.0.3] 완전한 롤백 기능
        $rollback_nonce = wp_create_nonce( 'jj_rollback_plugin_' . $this->plugin_basename );
        
        // 롤백 가능한 버전 목록 가져오기
        $shared_path = dirname( dirname( __FILE__ ) ) . '/../shared-ui-assets/php/';
        $available_versions = array();
        if ( file_exists( $shared_path . 'class-jj-shared-loader.php' ) ) {
            require_once $shared_path . 'class-jj-shared-loader.php';
            JJ_Shared_Loader::load( 'class-jj-rollback-shared' );
            if ( class_exists( 'JJ_Rollback_Shared' ) ) {
                $rollback = JJ_Rollback_Shared::instance();
                $available_versions = $rollback->get_available_rollback_versions( $this->plugin_basename );
            }
        }
        
        $new_links['rollback'] = sprintf(
            '<a href="#" class="jj-rollback-trigger jj-tooltip" data-tooltip="%s" data-plugin="%s" data-nonce="%s" data-versions=\'%s\' style="font-size: 14px; font-weight: 800; color: #856404; text-decoration: none; cursor: pointer; margin-right: 8px; display: inline-block; background: linear-gradient(135deg, #fbbf24 0%%, #f59e0b 100%%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">🔄 <strong>%s</strong></a>',
            esc_attr__( '이전 버전으로 되돌리기', $text_domain ),
            esc_attr( $this->plugin_basename ),
            esc_attr( $rollback_nonce ),
            esc_attr( wp_json_encode( $available_versions ) ),
            __( '롤백', $text_domain )
        );

        // 4. 업그레이드 유도 (마스터/파트너/언리미티드 제외)
        $license_type = $this->get_license_type();
        if ( ! in_array( strtoupper( $license_type ), array( 'MASTER', 'PARTNER', 'UNLIMITED' ), true ) ) {
            $upgrade_text = $this->get_upgrade_text( $license_type );
            $upgrade_url = $this->plugin_config['upgrade_url'] . '/upgrade?from=' . strtolower( $license_type );
            
            $new_links['upgrade'] = sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" class="jj-tooltip" data-tooltip="%s" style="font-size: 15px; font-weight: 800; color: #00a32a; text-decoration: none; margin-right: 8px; display: inline-block; background: linear-gradient(135deg, #00a32a 0%%, #3fb950 100%%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">🚀 <strong>%s</strong></a>',
                esc_url( $upgrade_url ),
                esc_attr__( '더 많은 기능을 사용하려면 업그레이드하세요', $text_domain ),
                esc_html( $upgrade_text )
            );
        }

        // 5. 자동 업데이트 토글 버튼 (모든 플러그인에 필수, 마스터 버전이든 아니든)
        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
        $is_auto_update_enabled = in_array( $this->plugin_basename, $auto_updates, true );
        $auto_update_nonce = wp_create_nonce( 'jj_toggle_auto_update_' . $this->plugin_basename );
        
        // 텍스트를 "자동 업데이트 활성화" / "자동 업데이트 비활성화"로 명확하게 표시
        $auto_update_text = $is_auto_update_enabled ? __( '자동 업데이트 활성화', $text_domain ) : __( '자동 업데이트 비활성화', $text_domain );
        $auto_update_color = $is_auto_update_enabled ? '#00a32a' : '#d63638';
        $auto_update_icon = $is_auto_update_enabled ? '✅' : '⚪';
        
        $new_links['auto_update'] = sprintf(
            '<a href="#" class="jj-auto-update-toggle jj-tooltip" data-tooltip="%s" data-plugin="%s" data-nonce="%s" data-enabled="%s" style="font-size: 14px; font-weight: 800; color: %s; text-decoration: none; cursor: pointer; margin-right: 8px; display: inline-block; border: 1px solid %s; padding: 4px 8px; border-radius: 4px; background: %s;">%s <strong>%s</strong></a>',
            esc_attr__( '클릭하여 자동 업데이트를 토글합니다', $text_domain ),
            esc_attr( $this->plugin_basename ),
            esc_attr( $auto_update_nonce ),
            $is_auto_update_enabled ? '1' : '0',
            esc_attr( $auto_update_color ),
            esc_attr( $auto_update_color ),
            $is_auto_update_enabled ? 'rgba(0, 163, 42, 0.1)' : 'rgba(214, 54, 56, 0.1)',
            $auto_update_icon,
            esc_html( $auto_update_text )
        );

        // 새 링크를 기존 링크(비활성화 등) 앞에 추가
        return array_merge( $new_links, $links );
    }
    
    /**
     * 플러그인별 기능 링크 정의
     */
    private function get_feature_links() {
        $links = array();
        $text_domain = $this->plugin_config['text_domain'];
        
        // ACF CSS Manager
        if ( strpos( $this->plugin_basename, 'acf-css-really-simple-style-guide' ) !== false ) {
            $links['style_center'] = array(
                'url' => admin_url( 'admin.php?page=jj-style-guide-cockpit' ),
                'label' => __( '스타일 센터', $text_domain ),
                'icon' => '🎨',
                'color' => '#2271b1',
                'tooltip' => __( '스타일 센터로 이동하여 색상, 타이포그래피, 버튼 등을 관리하세요', $text_domain ),
            );
            $links['admin_center'] = array(
                'url' => admin_url( 'admin.php?page=jj-admin-center' ),
                'label' => __( '설정 관리자', $text_domain ),
                'icon' => '⚙️',
                'color' => '#135e96',
                'tooltip' => __( '플러그인 설정 및 시스템 관리', $text_domain ),
            );
        }
        // 마케팅 대시보드
        elseif ( strpos( $this->plugin_basename, 'jj-marketing-dashboard' ) !== false ) {
            $links['dashboard'] = array(
                'url' => admin_url( 'admin.php?page=jj-marketing-dashboard' ),
                'label' => __( '마케팅 대시보드', $text_domain ),
                'icon' => '📊',
                'color' => '#667eea',
                'tooltip' => __( '종합 마케팅 대시보드 열기', $text_domain ),
            );
            $links['analytics'] = array(
                'url' => admin_url( 'admin.php?page=jj-marketing-analytics' ),
                'label' => __( '통계 분석', $text_domain ),
                'icon' => '📈',
                'color' => '#f5576c',
                'tooltip' => __( '통계 분석 페이지로 이동', $text_domain ),
            );
        }
        // 코드 박스
        elseif ( strpos( $this->plugin_basename, 'acf-code-snippets-box' ) !== false ) {
            $links['snippets'] = array(
                'url' => admin_url( 'admin.php?page=acf-code-snippets' ),
                'label' => __( '코드 박스', $text_domain ),
                'icon' => '📦',
                'color' => '#4facfe',
                'tooltip' => __( '코드 스니펫 관리 페이지로 이동', $text_domain ),
            );
            $links['presets'] = array(
                'url' => admin_url( 'admin.php?page=acf-code-snippets-presets' ),
                'label' => __( '프리셋 라이브러리', $text_domain ),
                'icon' => '📚',
                'color' => '#00f2fe',
                'tooltip' => __( '프리셋 라이브러리 열기', $text_domain ),
            );
        }
        // 원 클릭 SEO
        elseif ( strpos( $this->plugin_basename, 'wp-bulk-seo-aeo' ) !== false ) {
            $links['dashboard'] = array(
                'url' => admin_url( 'admin.php?page=wp-bulk-seo-aeo' ),
                'label' => __( '원 클릭 SEO', $text_domain ),
                'icon' => '📈',
                'color' => '#f093fb',
                'tooltip' => __( 'SEO 대시보드로 이동', $text_domain ),
            );
            $links['analyzer'] = array(
                'url' => admin_url( 'admin.php?page=wp-bulk-seo-aeo-analyzer' ),
                'label' => __( '벌크 분석', $text_domain ),
                'icon' => '🔍',
                'color' => '#f5576c',
                'tooltip' => __( '벌크 SEO 분석 도구', $text_domain ),
            );
        }
        // 넛지 플로우
        elseif ( strpos( $this->plugin_basename, 'acf-nudge-flow' ) !== false ) {
            $links['workflows'] = array(
                'url' => admin_url( 'admin.php?page=acf-nudge-flow-workflows' ),
                'label' => __( '워크플로우', $text_domain ),
                'icon' => '⚡',
                'color' => '#667eea',
                'tooltip' => __( '워크플로우 관리', $text_domain ),
            );
            $links['templates'] = array(
                'url' => admin_url( 'admin.php?page=acf-nudge-flow-templates' ),
                'label' => __( '템플릿', $text_domain ),
                'icon' => '📋',
                'color' => '#f5576c',
                'tooltip' => __( '템플릿 센터', $text_domain ),
            );
        }
        // Neural Link
        elseif ( strpos( $this->plugin_basename, 'acf-css-neural-link' ) !== false ) {
            $links['licenses'] = array(
                'url' => admin_url( 'admin.php?page=jj-license-manager' ),
                'label' => __( '라이센스 관리', $text_domain ),
                'icon' => '🔑',
                'color' => '#f59e0b',
                'tooltip' => __( '라이센스 관리 페이지', $text_domain ),
            );
        }
        // WooCommerce Toolkit
        elseif ( strpos( $this->plugin_basename, 'acf-css-woocommerce-toolkit' ) !== false ) {
            $links['settings'] = array(
                'url' => admin_url( 'admin.php?page=acf-css-wc-settings' ),
                'label' => __( 'WooCommerce 설정', $text_domain ),
                'icon' => '🛒',
                'color' => '#7f54b3',
                'tooltip' => __( 'WooCommerce Toolkit 설정', $text_domain ),
            );
        }
        // AI Extension
        elseif ( strpos( $this->plugin_basename, 'acf-css-ai-extension' ) !== false ) {
            $links['ai_dashboard'] = array(
                'url' => admin_url( 'admin.php?page=jj-ai-extension' ),
                'label' => __( 'AI 대시보드', $text_domain ),
                'icon' => '🤖',
                'color' => '#10b981',
                'tooltip' => __( 'AI 스타일 생성기', $text_domain ),
            );
        }
        // 기본 설정 링크 (다른 플러그인들)
        else {
            $links['settings'] = array(
                'url' => $this->plugin_config['settings_url'],
                'label' => __( '설정', $text_domain ),
                'icon' => '⚙️',
                'color' => '#2271b1',
                'tooltip' => __( '플러그인 설정 페이지로 이동', $text_domain ),
            );
        }
        
        return $links;
    }
    
    /**
     * 라이센스 타입 가져오기
     */
    private function get_license_type() {
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $controller = JJ_Edition_Controller::instance();
                return $controller->get_edition();
            } catch ( Exception $e ) {
                // ignore
            }
        }
        
        if ( defined( $this->plugin_config['license_constant'] ) ) {
            return constant( $this->plugin_config['license_constant'] );
        }
        
        return 'free';
    }
    
    /**
     * 업그레이드 텍스트 가져오기
     */
    private function get_upgrade_text( $current_license ) {
        $text_domain = $this->plugin_config['text_domain'];
        $license_upper = strtoupper( $current_license );
        
        switch ( $license_upper ) {
            case 'FREE':
                return __( 'PRO로 업그레이드', $text_domain );
            case 'BASIC':
                return __( 'Premium으로 업그레이드', $text_domain );
            case 'PREMIUM':
                return __( 'Unlimited으로 업그레이드', $text_domain );
            default:
                return __( '업그레이드', $text_domain );
        }
    }

    /**
     * 작성자 정보 영역 개선
     * 
     * [v23.0.1] 작성자 정보에 추가 메타데이터 추가 (볼드, 큰 글꼴, 색상)
     */
    public function enhance_author_info( $plugin_meta, $plugin_file ) {
        if ( $plugin_file !== $this->plugin_basename ) {
            return $plugin_meta;
        }

        // 플러그인 헤더 정보 가져오기
        $plugin_data = get_plugin_data( $this->plugin_file );
        
        $new_meta = array();
        $text_domain = $this->plugin_config['text_domain'];
        
        // 버전 정보 강화
        if ( ! empty( $plugin_data['Version'] ) ) {
            $version_constant = $this->plugin_config['version_constant'];
            $version = defined( $version_constant ) ? constant( $version_constant ) : $plugin_data['Version'];
            $new_meta[] = sprintf(
                '<span style="font-size: 13px; color: #2271b1; font-weight: 700;" title="%s">📦 <strong>v%s</strong></span>',
                esc_attr( __( '현재 플러그인 버전', $text_domain ) ),
                esc_html( $version )
            );
        }
        
        // 플러그인 URI
        if ( ! empty( $plugin_data['PluginURI'] ) ) {
            $new_meta[] = sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" class="jj-tooltip" data-tooltip="%s" style="font-size: 13px; color: #135e96; font-weight: 700;" title="%s">🔗 <strong>%s</strong></a>',
                esc_url( $plugin_data['PluginURI'] ),
                esc_attr( __( '플러그인 공식 사이트 방문', $text_domain ) ),
                esc_attr( __( '플러그인 공식 사이트 방문', $text_domain ) ),
                __( '공식 사이트', $text_domain )
            );
        }

        return array_merge( $plugin_meta, $new_meta );
    }

    /**
     * AJAX: 자동 업데이트 토글
     */
    public function ajax_toggle_auto_update() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
        $plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( $_POST['plugin'] ) : '';
        
        if ( ! wp_verify_nonce( $nonce, 'jj_toggle_auto_update_' . $plugin ) ) {
            wp_send_json_error( array( 'message' => __( '보안 검증 실패', $this->plugin_config['text_domain'] ) ) );
        }
        
        if ( ! current_user_can( 'update_plugins' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', $this->plugin_config['text_domain'] ) ) );
        }
        
        if ( $plugin !== $this->plugin_basename ) {
            wp_send_json_error( array( 'message' => __( '잘못된 플러그인입니다.', $this->plugin_config['text_domain'] ) ) );
        }
        
        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
        $is_enabled = in_array( $plugin, $auto_updates, true );
        
        if ( $is_enabled ) {
            // 비활성화
            $auto_updates = array_diff( $auto_updates, array( $plugin ) );
            $message = __( '자동 업데이트가 비활성화되었습니다.', $this->plugin_config['text_domain'] );
        } else {
            // 활성화
            if ( ! in_array( $plugin, $auto_updates, true ) ) {
                $auto_updates[] = $plugin;
            }
            $message = __( '자동 업데이트가 활성화되었습니다.', $this->plugin_config['text_domain'] );
        }
        
        update_site_option( 'auto_update_plugins', array_values( $auto_updates ) );
        
        wp_send_json_success( array(
            'message' => $message,
            'enabled' => ! $is_enabled,
        ) );
    }

    /**
     * AJAX: 플러그인 롤백
     *
     * 3J Labs 플러그인 또는 WordPress.org 플러그인의 이전 버전으로 롤백
     */
    public function ajax_rollback_plugin() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
        $plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( $_POST['plugin'] ) : '';
        $version = isset( $_POST['version'] ) ? sanitize_text_field( $_POST['version'] ) : '';

        if ( ! wp_verify_nonce( $nonce, 'jj_rollback_plugin_' . $plugin ) ) {
            wp_send_json_error( array( 'message' => __( '보안 검증 실패', $this->plugin_config['text_domain'] ) ) );
        }

        if ( ! current_user_can( 'update_plugins' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', $this->plugin_config['text_domain'] ) ) );
        }

        if ( empty( $plugin ) ) {
            wp_send_json_error( array( 'message' => __( '플러그인이 지정되지 않았습니다.', $this->plugin_config['text_domain'] ) ) );
        }

        // 공유 롤백 클래스 로드
        $shared_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/../shared-ui-assets/php/';
        if ( file_exists( $shared_path . 'class-jj-shared-loader.php' ) ) {
            require_once $shared_path . 'class-jj-shared-loader.php';
            JJ_Shared_Loader::load( 'class-jj-rollback-shared' );
        }

        if ( ! class_exists( 'JJ_Rollback_Shared' ) ) {
            wp_send_json_error( array( 'message' => __( '롤백 클래스를 로드할 수 없습니다.', $this->plugin_config['text_domain'] ) ) );
        }

        // 롤백 실행
        $rollback = JJ_Rollback_Shared::instance();
        $result = $rollback->rollback_plugin( $plugin, $version );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 
                'message' => $result->get_error_message(),
                'code' => $result->get_error_code()
            ) );
        }

        wp_send_json_success( array(
            'message' => sprintf(
                __( '플러그인이 버전 %s로 롤백되었습니다.', $this->plugin_config['text_domain'] ),
                $result['to_version']
            ),
            'data' => $result,
            'reload' => true
        ) );
    }

    /**
     * 롤백 가능한 버전 목록 가져오기
     *
     * @since 1.0.0
     * @return array 버전 목록
     */
    private function get_available_rollback_versions() {
        // 공유 롤백 클래스 로드
        $shared_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/../shared-ui-assets/php/';
        if ( file_exists( $shared_path . 'class-jj-shared-loader.php' ) ) {
            require_once $shared_path . 'class-jj-shared-loader.php';
            JJ_Shared_Loader::load( 'class-jj-rollback-shared' );
        }

        if ( ! class_exists( 'JJ_Rollback_Shared' ) ) {
            return array();
        }

        $rollback = JJ_Rollback_Shared::instance();
        return $rollback->get_available_rollback_versions( $this->plugin_basename );
    }

    /**
     * 실제 롤백 수행
     *
     * @param string $plugin_slug 플러그인 슬러그
     * @param string $version 대상 버전
     * @param string $plugin_file 플러그인 파일 경로
     * @return true|WP_Error
     */
    private function perform_rollback( $plugin_slug, $version, $plugin_file ) {
        // WordPress 업그레이더 클래스 로드
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';

        // 3J Labs 플러그인인지 확인
        $is_3j_plugin = $this->is_3j_labs_plugin( $plugin_slug );

        if ( $is_3j_plugin ) {
            // 3J Labs 플러그인은 자체 저장소에서 다운로드
            $download_url = $this->get_3j_labs_download_url( $plugin_slug, $version );
        } else {
            // WordPress.org 플러그인은 SVN에서 다운로드
            $download_url = $this->get_wporg_download_url( $plugin_slug, $version );
        }

        if ( is_wp_error( $download_url ) ) {
            return $download_url;
        }

        // 현재 플러그인 상태 저장 (활성화 여부)
        $was_active = is_plugin_active( $plugin_file );
        $was_network_active = is_plugin_active_for_network( $plugin_file );

        // 플러그인 비활성화 (안전을 위해)
        if ( $was_active ) {
            deactivate_plugins( $plugin_file );
        }

        // 업그레이더 초기화 (조용한 스킨 사용)
        $skin = new WP_Ajax_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader( $skin );

        // 플러그인 디렉토리 백업
        $plugin_dir = WP_PLUGIN_DIR . '/' . $plugin_slug;
        $backup_dir = WP_PLUGIN_DIR . '/' . $plugin_slug . '-backup-' . time();

        if ( is_dir( $plugin_dir ) ) {
            // 백업 생성
            if ( ! $this->copy_directory( $plugin_dir, $backup_dir ) ) {
                return new WP_Error( 'backup_failed', __( '플러그인 백업 생성에 실패했습니다.', $this->plugin_config['text_domain'] ) );
            }

            // 기존 플러그인 삭제
            $this->delete_directory( $plugin_dir );
        }

        // 지정된 버전 설치
        $result = $upgrader->install( $download_url );

        if ( is_wp_error( $result ) ) {
            // 설치 실패 시 백업 복원
            if ( is_dir( $backup_dir ) ) {
                $this->copy_directory( $backup_dir, $plugin_dir );
                $this->delete_directory( $backup_dir );
            }
            return $result;
        }

        if ( $result === false ) {
            // 설치 실패 시 백업 복원
            if ( is_dir( $backup_dir ) ) {
                $this->copy_directory( $backup_dir, $plugin_dir );
                $this->delete_directory( $backup_dir );
            }
            return new WP_Error( 'install_failed', __( '플러그인 설치에 실패했습니다.', $this->plugin_config['text_domain'] ) );
        }

        // 백업 디렉토리 삭제
        if ( is_dir( $backup_dir ) ) {
            $this->delete_directory( $backup_dir );
        }

        // 이전 활성화 상태 복원
        if ( $was_network_active ) {
            activate_plugin( $plugin_file, '', true );
        } elseif ( $was_active ) {
            activate_plugin( $plugin_file );
        }

        return true;
    }

    /**
     * 3J Labs 플러그인인지 확인
     */
    private function is_3j_labs_plugin( $plugin_slug ) {
        $jj_plugins = array(
            'acf-css-really-simple-style-management-center-master',
            'acf-nudge-flow',
            'acf-code-snippets-box',
            'acf-css-neural-link',
            'acf-css-ai-extension',
            'acf-css-woocommerce-toolkit',
            'acf-css-woo-license',
            'wp-bulk-manager',
            'admin-menu-editor-pro',
            'acf-user-journey-analytics',
            'oneclick-seo-pro',
        );

        return in_array( $plugin_slug, $jj_plugins, true );
    }

    /**
     * 3J Labs 다운로드 URL 가져오기
     */
    private function get_3j_labs_download_url( $plugin_slug, $version ) {
        // 3J Labs 업데이트 서버 URL
        $api_url = 'https://update.3j-labs.com/api/v1/download';

        $response = wp_remote_get( add_query_arg( array(
            'plugin' => $plugin_slug,
            'version' => $version,
            'site_url' => home_url(),
        ), $api_url ), array( 'timeout' => 30 ) );

        if ( is_wp_error( $response ) ) {
            // 폴백: 로컬 dist 폴더에서 확인
            $local_zip = WP_PLUGIN_DIR . '/../3J-ACF-CSS/dist/' . $plugin_slug . '-v' . $version . '.zip';
            if ( file_exists( $local_zip ) ) {
                return $local_zip;
            }
            return new WP_Error( 'api_error', __( '업데이트 서버에 연결할 수 없습니다.', $this->plugin_config['text_domain'] ) );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( empty( $body['download_url'] ) ) {
            return new WP_Error( 'no_version', sprintf( __( '버전 %s을(를) 찾을 수 없습니다.', $this->plugin_config['text_domain'] ), $version ) );
        }

        return $body['download_url'];
    }

    /**
     * WordPress.org 다운로드 URL 가져오기
     */
    private function get_wporg_download_url( $plugin_slug, $version ) {
        // WordPress.org SVN 저장소에서 특정 버전 다운로드
        $download_url = sprintf(
            'https://downloads.wordpress.org/plugin/%s.%s.zip',
            $plugin_slug,
            $version
        );

        // URL 유효성 확인
        $response = wp_remote_head( $download_url, array( 'timeout' => 10 ) );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'connection_error', __( 'WordPress.org에 연결할 수 없습니다.', $this->plugin_config['text_domain'] ) );
        }

        $status_code = wp_remote_retrieve_response_code( $response );

        if ( $status_code !== 200 ) {
            return new WP_Error( 'no_version', sprintf( __( '버전 %s을(를) WordPress.org에서 찾을 수 없습니다.', $this->plugin_config['text_domain'] ), $version ) );
        }

        return $download_url;
    }

    /**
     * 디렉토리 복사 (재귀)
     */
    private function copy_directory( $source, $dest ) {
        if ( ! is_dir( $source ) ) {
            return false;
        }

        if ( ! is_dir( $dest ) ) {
            wp_mkdir_p( $dest );
        }

        $dir = opendir( $source );
        if ( ! $dir ) {
            return false;
        }

        while ( ( $file = readdir( $dir ) ) !== false ) {
            if ( $file === '.' || $file === '..' ) {
                continue;
            }

            $src_path = $source . '/' . $file;
            $dest_path = $dest . '/' . $file;

            if ( is_dir( $src_path ) ) {
                $this->copy_directory( $src_path, $dest_path );
            } else {
                copy( $src_path, $dest_path );
            }
        }

        closedir( $dir );
        return true;
    }

    /**
     * 디렉토리 삭제 (재귀)
     */
    private function delete_directory( $dir ) {
        if ( ! is_dir( $dir ) ) {
            return false;
        }

        $files = array_diff( scandir( $dir ), array( '.', '..' ) );

        foreach ( $files as $file ) {
            $path = $dir . '/' . $file;
            if ( is_dir( $path ) ) {
                $this->delete_directory( $path );
            } else {
                unlink( $path );
            }
        }

        return rmdir( $dir );
    }

    /**
     * AJAX: 넛지 메시지 dismiss
     */
    public function ajax_dismiss_nudge() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
        $type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
        $user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : get_current_user_id();
        
        if ( ! wp_verify_nonce( $nonce, 'jj_dismiss_nudge' ) ) {
            wp_send_json_error( array( 'message' => __( '보안 검증 실패', $this->plugin_config['text_domain'] ) ) );
        }
        
        if ( $user_id !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', $this->plugin_config['text_domain'] ) ) );
        }
        
        if ( 'dismiss-forever' === $type ) {
            update_user_meta( $user_id, 'jj_plugin_list_nudge_dismissed', true );
        } elseif ( 'hide-3days' === $type ) {
            update_user_meta( $user_id, 'jj_plugin_list_nudge_hidden_until', time() + ( 3 * DAY_IN_SECONDS ) );
        }
        
        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', $this->plugin_config['text_domain'] ) ) );
    }

    /**
     * 스타일 및 스크립트 로드
     */
    public function enqueue_assets( $hook ) {
        // 플러그인 목록 페이지에서만 로드
        if ( 'plugins.php' !== $hook ) {
            return;
        }
        
        $version = defined( $this->plugin_config['version_constant'] ) ? constant( $this->plugin_config['version_constant'] ) : '1.0.0';
        
        // 인라인 CSS
        wp_add_inline_style( 'wp-admin', $this->get_inline_css() );
        
        // 인라인 JavaScript
        wp_add_inline_script( 'jquery', $this->get_inline_js() );
    }

    /**
     * 인라인 CSS
     */
    private function get_inline_css() {
        return '
        /* [v23.0.3] 플러그인 목록 페이지 UI/UX 대폭 개선 - 모든 플러그인에 적용 */
        .jj-auto-update-toggle:hover,
        .jj-auto-update-toggle-global:hover,
        .jj-rollback-trigger:hover,
        .jj-tooltip:hover {
            opacity: 0.8;
            text-decoration: underline !important;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        
        /* 롤백 모달 스타일 */
        .jj-rollback-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 100000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .jj-rollback-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .jj-rollback-modal {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        
        .jj-rollback-modal-overlay.active .jj-rollback-modal {
            transform: scale(1);
        }
        
        .jj-rollback-modal-header {
            padding: 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .jj-rollback-modal-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
        
        .jj-rollback-modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            padding: 0;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
        }
        
        .jj-rollback-modal-close:hover {
            color: #000;
        }
        
        .jj-rollback-modal-body {
            padding: 20px;
            overflow-y: auto;
            flex: 1;
        }
        
        .jj-rollback-versions-list {
            margin: 15px 0;
        }
        
        .jj-rollback-version-item {
            display: block;
            padding: 12px;
            margin: 8px 0;
            border: 2px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .jj-rollback-version-item:hover {
            border-color: #2271b1;
            background: #f0f6fc;
        }
        
        .jj-rollback-version-item input[type="radio"] {
            margin-right: 10px;
        }
        
        .jj-rollback-version-item input[type="radio"]:checked + .version-label {
            font-weight: 700;
            color: #2271b1;
        }
        
        .jj-rollback-warning {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
        }
        
        .jj-rollback-warning p {
            margin: 0;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .jj-rollback-modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .jj-rollback-progress {
            margin-top: 20px;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 4px;
        }
        
        .jj-rollback-progress .spinner {
            float: left;
            margin: 0 10px 0 0;
        }
        
        /* 플러그인 액션 링크 스타일 개선 - 볼드, 큰 글꼴, 색상 강조 */
        .wp-list-table.plugins .plugin-title strong {
            font-size: 15px !important;
            font-weight: 800 !important;
        }
        
        /* 비활성화 링크 강조 */
        .wp-list-table.plugins .row-actions a[href*="action=deactivate"],
        .wp-list-table.plugins .row-actions .deactivate a {
            font-size: 15px !important;
            font-weight: 800 !important;
            color: #d63638 !important;
            border-bottom: 2px solid #d63638 !important;
            padding-bottom: 2px !important;
        }
        
        .wp-list-table.plugins .row-actions a {
            font-size: 15px !important;
            font-weight: 800 !important;
        }
        
        /* 플러그인 메타 링크 스타일 개선 */
        .wp-list-table.plugins .plugin-description p {
            margin-bottom: 8px;
        }
        
        .wp-list-table.plugins .plugin-description .jj-tooltip {
            margin-right: 12px;
        }
        
        /* 플러그인 행 메타 정보 영역 강조 */
        .wp-list-table.plugins .plugin-description p + .row-meta,
        .wp-list-table.plugins .plugin-description + .row-meta {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e5e5e5;
        }
        .jj-nudge-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999999;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .jj-nudge-overlay.active {
            display: flex;
        }
        .jj-nudge-popup {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            position: relative;
        }
        .jj-nudge-popup .jj-nudge-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #646970;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }
        .jj-nudge-popup .jj-nudge-close:hover {
            background: #f0f0f1;
            color: #1d2327;
        }
        .jj-nudge-popup .jj-nudge-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .jj-nudge-popup .jj-nudge-actions button {
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #c3c4c7;
            background: #fff;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }
        .jj-nudge-popup .jj-nudge-actions button:hover {
            background: #f0f0f1;
        }
        .jj-nudge-popup .jj-nudge-actions button.jj-nudge-primary {
            background: #2271b1;
            color: #fff;
            border-color: #2271b1;
        }
        .jj-nudge-popup .jj-nudge-actions button.jj-nudge-primary:hover {
            background: #135e96;
        }
        /* [Phase 19.1] 툴팁 시스템 */
        .jj-tooltip {
            position: relative;
            cursor: help;
        }
        .jj-tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 8px 12px;
            background: #1d2327;
            color: #fff;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s, transform 0.3s;
            transform: translateX(-50%) translateY(-5px);
            z-index: 1000000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        .jj-tooltip::before {
            content: "";
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: #1d2327;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
            z-index: 1000001;
        }
        .jj-tooltip:hover::after,
        .jj-tooltip:hover::before {
            opacity: 1;
            transform: translateX(-50%) translateY(-10px);
        }
        .jj-tooltip:hover::before {
            transform: translateX(-50%) translateY(-4px);
        }
        ';
    }

    /**
     * 인라인 JavaScript
     */
    private function get_inline_js() {
        $plugin_basename = $this->plugin_basename;
        $text_domain = $this->plugin_config['text_domain'];
        
        $ajax_action = 'jj_toggle_auto_update_' . sanitize_key( str_replace( array( '/', '\\' ), '_', $this->plugin_basename ) );
        
        return "
        jQuery(document).ready(function($) {
            // 자동 업데이트 토글 (플러그인별)
            $(document).on('click', '.jj-auto-update-toggle', function(e) {
                e.preventDefault();
                var \$link = $(this);
                var plugin = \$link.data('plugin');
                var nonce = \$link.data('nonce');
                var enabled = \$link.data('enabled') === '1';
                
                if (!confirm(enabled ? '자동 업데이트를 비활성화하시겠습니까?' : '자동 업데이트를 활성화하시겠습니까?')) {
                    return;
                }
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: '" . esc_js( $ajax_action ) . "',
                        nonce: nonce,
                        plugin: plugin
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.data.message || '오류가 발생했습니다.');
                        }
                    },
                    error: function() {
                        alert('서버 통신 오류가 발생했습니다.');
                    }
                });
            });
            
            // 전역 자동 업데이트 토글 (모든 플러그인)
            $(document).on('click', '.jj-auto-update-toggle-global', function(e) {
                e.preventDefault();
                var \$link = $(this);
                var plugin = \$link.data('plugin');
                var nonce = \$link.data('nonce');
                var enabled = \$link.data('enabled') === '1';
                
                if (!confirm(enabled ? '자동 업데이트를 비활성화하시겠습니까?' : '자동 업데이트를 활성화하시겠습니까?')) {
                    return;
                }
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'jj_toggle_auto_update_global',
                        nonce: nonce,
                        plugin: plugin
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.data.message || '오류가 발생했습니다.');
                        }
                    },
                    error: function() {
                        alert('서버 통신 오류가 발생했습니다.');
                    }
                });
            });
            
            // 롤백 트리거
            $(document).on('click', '.jj-rollback-trigger', function(e) {
                e.preventDefault();
                alert('롤백 기능은 준비 중입니다.');
            });
        });
        ";
    }

    /**
     * 넛지 메시지 오버레이 렌더링
     */
    public function render_nudge_overlay() {
        $screen = get_current_screen();
        if ( ! $screen || 'plugins' !== $screen->id ) {
            return;
        }
        
        // 넛지 메시지 표시 여부 확인 (사용자가 "다시 보지 않기"를 선택했는지)
        $nudge_dismissed = get_user_meta( get_current_user_id(), 'jj_plugin_list_nudge_dismissed', true );
        if ( $nudge_dismissed ) {
            return;
        }
        
        // 3일 간 안 보기 확인
        $nudge_hidden_until = get_user_meta( get_current_user_id(), 'jj_plugin_list_nudge_hidden_until', true );
        if ( $nudge_hidden_until && time() < $nudge_hidden_until ) {
            return;
        }
        
        $text_domain = $this->plugin_config['text_domain'];
        ?>
        <div class="jj-nudge-overlay" id="jj-plugin-list-nudge">
            <div class="jj-nudge-popup">
                <button type="button" class="jj-nudge-close" aria-label="<?php esc_attr_e( '닫기', $text_domain ); ?>">×</button>
                <h2 style="margin-top: 0;"><?php esc_html_e( '플러그인 목록 페이지가 개선되었습니다!', $text_domain ); ?></h2>
                <p><?php esc_html_e( '이제 자동 업데이트를 바로 토글하고, 롤백 기능을 사용할 수 있습니다. 더 많은 기능을 확인해보세요!', $text_domain ); ?></p>
                <div class="jj-nudge-actions">
                    <button type="button" class="jj-nudge-dismiss-3days" data-action="hide-3days"><?php esc_html_e( '3일 간 안 보기', $text_domain ); ?></button>
                    <button type="button" class="jj-nudge-dismiss-forever" data-action="dismiss-forever"><?php esc_html_e( '다시 보지 않기', $text_domain ); ?></button>
                    <button type="button" class="jj-nudge-primary jj-nudge-close"><?php esc_html_e( '확인', $text_domain ); ?></button>
                </div>
            </div>
        </div>
        <script>
        jQuery(document).ready(function($) {
            var \$overlay = $('#jj-plugin-list-nudge');
            var userId = <?php echo get_current_user_id(); ?>;
            
            // 닫기 버튼
            \$overlay.find('.jj-nudge-close').on('click', function() {
                \$overlay.removeClass('active');
            });
            
            // 3일 간 안 보기
            \$overlay.find('.jj-nudge-dismiss-3days').on('click', function() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'jj_dismiss_nudge',
                        nonce: '<?php echo wp_create_nonce( 'jj_dismiss_nudge' ); ?>',
                        type: 'hide-3days',
                        user_id: userId
                    },
                    success: function() {
                        \$overlay.removeClass('active');
                    }
                });
            });
            
            // 다시 보지 않기
            \$overlay.find('.jj-nudge-dismiss-forever').on('click', function() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'jj_dismiss_nudge',
                        nonce: '<?php echo wp_create_nonce( 'jj_dismiss_nudge' ); ?>',
                        type: 'dismiss-forever',
                        user_id: userId
                    },
                    success: function() {
                        \$overlay.removeClass('active');
                    }
                });
            });
            
            // 처음 로드 시 표시 (체크 후)
            setTimeout(function() {
                \$overlay.addClass('active');
            }, 1000);
        });
        </script>
        <?php
    }

    /**
     * 필수 플러그인 목록
     */
    private function get_required_plugins() {
        return array();
    }

    /**
     * 권장 플러그인 목록
     */
    private function get_recommended_plugins() {
        return array(
            array(
                'name' => 'Advanced Custom Fields',
                'file' => 'advanced-custom-fields/acf.php',
            ),
        );
    }

    /**
     * Premium 버전 여부 확인
     */
    private function is_premium() {
        // 1. Edition Controller 확인 (가장 정확)
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                return JJ_Edition_Controller::instance()->is_at_least( 'basic' );
            } catch ( Exception $e ) {
                // ignore
            }
        }
        
        // 2. 상수 확인 (Edition Controller가 없거나 로드 전일 경우)
        if ( defined( $this->plugin_config['license_constant'] ) ) {
            $type = strtoupper( constant( $this->plugin_config['license_constant'] ) );
            return in_array( $type, array( 'BASIC', 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }
        
        return false;
    }
}

/**
 * [v23.0.2] 모든 플러그인에 자동 업데이트 버튼 및 향상된 링크 추가 (전역 필터)
 * 마스터 버전이든 아니든 모든 플러그인에 적용
 */
class JJ_Global_Plugin_List_Enhancer {
    
    private static $instance = null;
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // 모든 플러그인에 자동 업데이트 버튼 추가
        add_filter( 'plugin_action_links', array( $this, 'add_auto_update_to_all_plugins' ), 10, 2 );
        
        // 모든 플러그인에 롤백 버튼 추가 - [v23.0.3] 완전한 롤백 기능 구현
        add_filter( 'plugin_action_links', array( $this, 'add_rollback_to_all_plugins' ), 10, 2 );
        
        // 모든 플러그인에 향상된 메타 링크 추가
        add_filter( 'plugin_row_meta', array( $this, 'enhance_all_plugin_meta' ), 10, 2 );
        
        // 자동 업데이트 토글 AJAX (전역)
        add_action( 'wp_ajax_jj_toggle_auto_update_global', array( $this, 'ajax_toggle_auto_update_global' ) );
        
        // 전역 롤백 AJAX 핸들러 - [v23.0.3]
        add_action( 'wp_ajax_jj_rollback_plugin_global', array( $this, 'ajax_rollback_plugin_global' ) );
        
        // 스타일 및 스크립트 로드
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_global_assets' ) );
    }
    
    /**
     * 모든 플러그인에 자동 업데이트 버튼 추가
     */
    public function add_auto_update_to_all_plugins( $links, $plugin_file ) {
        // WordPress 코어 플러그인 제외
        if ( strpos( $plugin_file, 'wordpress-seo' ) !== false || 
             strpos( $plugin_file, 'akismet' ) !== false ||
             strpos( $plugin_file, 'hello.php' ) !== false ) {
            return $links;
        }
        
        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
        $is_auto_update_enabled = in_array( $plugin_file, $auto_updates, true );
        $auto_update_nonce = wp_create_nonce( 'jj_toggle_auto_update_global_' . $plugin_file );
        
        // 텍스트를 "자동 업데이트 활성화" / "자동 업데이트 비활성화"로 명확하게 표시
        $auto_update_text = $is_auto_update_enabled ? __( '자동 업데이트 활성화', 'acf-css-really-simple-style-management-center' ) : __( '자동 업데이트 비활성화', 'acf-css-really-simple-style-management-center' );
        $auto_update_color = $is_auto_update_enabled ? '#00a32a' : '#d63638';
        $auto_update_icon = $is_auto_update_enabled ? '✅' : '⚪';
        
        $auto_update_link = sprintf(
            '<a href="#" class="jj-auto-update-toggle-global jj-tooltip" data-tooltip="%s" data-plugin="%s" data-nonce="%s" data-enabled="%s" style="font-size: 14px; font-weight: 800; color: %s; text-decoration: none; cursor: pointer; margin-right: 8px; display: inline-block; border: 1px solid %s; padding: 4px 8px; border-radius: 4px; background: %s;">%s <strong>%s</strong></a>',
            esc_attr__( '클릭하여 자동 업데이트를 토글합니다', 'acf-css-really-simple-style-management-center' ),
            esc_attr( $plugin_file ),
            esc_attr( $auto_update_nonce ),
            $is_auto_update_enabled ? '1' : '0',
            esc_attr( $auto_update_color ),
            esc_attr( $auto_update_color ),
            $is_auto_update_enabled ? 'rgba(0, 163, 42, 0.1)' : 'rgba(214, 54, 56, 0.1)',
            $auto_update_icon,
            esc_html( $auto_update_text )
        );
        
        // 기존 링크 앞에 추가
        array_unshift( $links, $auto_update_link );
        
        return $links;
    }
    
    /**
     * 모든 플러그인에 향상된 메타 링크 추가
     * [v23.0.2] 플러그인 설명 아래에 다양한 링크 추가 (볼드, 색상, 아이콘 강조)
     */
    public function enhance_all_plugin_meta( $plugin_meta, $plugin_file ) {
        // WordPress 코어 플러그인 제외
        if ( strpos( $plugin_file, 'wordpress-seo' ) !== false || 
             strpos( $plugin_file, 'akismet' ) !== false ||
             strpos( $plugin_file, 'hello.php' ) !== false ) {
            return $plugin_meta;
        }
        
        $new_meta = array();
        
        // 플러그인 데이터 가져오기
        $plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file );
        
        // 자동 업데이트 버튼 (메타 영역에도 추가, 모든 플러그인에 필수)
        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
        $is_auto_update_enabled = in_array( $plugin_file, $auto_updates, true );
        $auto_update_nonce = wp_create_nonce( 'jj_toggle_auto_update_global_' . $plugin_file );
        
        $auto_update_text = $is_auto_update_enabled ? __( '자동 업데이트 활성화', 'acf-css-really-simple-style-management-center' ) : __( '자동 업데이트 비활성화', 'acf-css-really-simple-style-management-center' );
        $auto_update_color = $is_auto_update_enabled ? '#00a32a' : '#d63638';
        $auto_update_icon = $is_auto_update_enabled ? '✅' : '⚪';
        
        $new_meta[] = sprintf(
            '<a href="#" class="jj-auto-update-toggle-global jj-tooltip" data-plugin="%s" data-nonce="%s" data-enabled="%s" data-tooltip="%s" style="font-size: 14px; font-weight: 800; color: %s; text-decoration: none; cursor: pointer; border: 1px solid %s; padding: 4px 10px; border-radius: 4px; background: %s; display: inline-block; margin-left: 5px;">%s <strong>%s</strong></a>',
            esc_attr( $plugin_file ),
            esc_attr( $auto_update_nonce ),
            $is_auto_update_enabled ? '1' : '0',
            esc_attr__( '클릭하여 자동 업데이트를 토글합니다', 'acf-css-really-simple-style-management-center' ),
            esc_attr( $auto_update_color ),
            esc_attr( $auto_update_color ),
            $is_auto_update_enabled ? 'rgba(0, 163, 42, 0.1)' : 'rgba(214, 54, 56, 0.1)',
            $auto_update_icon,
            esc_html( $auto_update_text )
        );
        
        // 플러그인 URI가 있으면 추가 링크 제공 (강조)
        if ( ! empty( $plugin_data['PluginURI'] ) ) {
            $new_meta[] = sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" class="jj-tooltip" data-tooltip="%s" style="font-size: 14px; color: #2271b1; font-weight: 800; text-decoration: none; border-bottom: 2px solid #2271b1; padding-bottom: 2px; margin-right: 10px;">🌐 <strong>%s</strong></a>',
                esc_url( $plugin_data['PluginURI'] ),
                esc_attr__( '플러그인 공식 사이트 방문', 'acf-css-really-simple-style-management-center' ),
                __( '공식 사이트', 'acf-css-really-simple-style-management-center' )
            );
        }
        
        // 추가 리소스 링크들 (모든 플러그인에 적용, 강조)
        if ( ! empty( $plugin_data['AuthorURI'] ) ) {
            $new_meta[] = sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" class="jj-tooltip" data-tooltip="%s" style="font-size: 14px; color: #135e96; font-weight: 800; text-decoration: none; border-bottom: 2px solid #135e96; padding-bottom: 2px; margin-right: 10px;">👤 <strong>%s</strong></a>',
                esc_url( $plugin_data['AuthorURI'] ),
                esc_attr__( '작성자 사이트 방문', 'acf-css-really-simple-style-management-center' ),
                __( '작성자', 'acf-css-really-simple-style-management-center' )
            );
        }
        
        // 버전 정보 강조
        if ( ! empty( $plugin_data['Version'] ) ) {
            $new_meta[] = sprintf(
                '<span style="font-size: 14px; color: #50575e; font-weight: 800; margin-right: 10px;">📦 <strong>v%s</strong></span>',
                esc_html( $plugin_data['Version'] )
            );
        }
        
        return array_merge( $plugin_meta, $new_meta );
    }
    
    /**
     * 모든 플러그인에 롤백 버튼 추가 - [v23.0.3]
     */
    public function add_rollback_to_all_plugins( $links, $plugin_file ) {
        // WordPress 코어 플러그인 제외
        if ( strpos( $plugin_file, 'wordpress-seo' ) !== false || 
             strpos( $plugin_file, 'akismet' ) !== false ||
             strpos( $plugin_file, 'hello.php' ) !== false ) {
            return $links;
        }
        
        // 공유 롤백 클래스 로드
        $shared_path = dirname( dirname( __FILE__ ) ) . '/../shared-ui-assets/php/';
        if ( ! file_exists( $shared_path . 'class-jj-shared-loader.php' ) ) {
            // 상대 경로로 찾기
            $shared_path = WP_PLUGIN_DIR . '/acf-css-really-simple-style-management-center-master/shared-ui-assets/php/';
        }
        
        $available_versions = array();
        if ( file_exists( $shared_path . 'class-jj-shared-loader.php' ) ) {
            require_once $shared_path . 'class-jj-shared-loader.php';
            JJ_Shared_Loader::load( 'class-jj-rollback-shared' );
            if ( class_exists( 'JJ_Rollback_Shared' ) ) {
                $rollback = JJ_Rollback_Shared::instance();
                $available_versions = $rollback->get_available_rollback_versions( $plugin_file );
            }
        }
        
        // 롤백 가능한 버전이 없으면 버튼 표시 안 함
        if ( empty( $available_versions ) ) {
            return $links;
        }
        
        $rollback_nonce = wp_create_nonce( 'jj_rollback_plugin_global_' . $plugin_file );
        
        // 버전 배열을 JSON 문자열로 변환 (키는 버전, 값은 URL이지만 여기서는 버전만 사용)
        $versions_array = is_array( $available_versions ) ? array_values( $available_versions ) : array();
        
        $rollback_link = sprintf(
            '<a href="#" class="jj-rollback-trigger jj-tooltip" data-tooltip="%s" data-plugin="%s" data-nonce="%s" data-versions=\'%s\' style="font-size: 14px; font-weight: 800; color: #856404; text-decoration: none; cursor: pointer; margin-right: 8px; display: inline-block; background: linear-gradient(135deg, #fbbf24 0%%, #f59e0b 100%%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">🔄 <strong>%s</strong></a>',
            esc_attr__( '이전 버전으로 되돌리기', 'acf-css-really-simple-style-management-center' ),
            esc_attr( $plugin_file ),
            esc_attr( $rollback_nonce ),
            esc_attr( wp_json_encode( $versions_array ) ),
            __( '롤백', 'acf-css-really-simple-style-management-center' )
        );
        
        // 기존 링크 앞에 추가
        array_unshift( $links, $rollback_link );
        
        return $links;
    }
    
    /**
     * 전역 롤백 AJAX 핸들러 - [v23.0.3]
     */
    public function ajax_rollback_plugin_global() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
        $plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( $_POST['plugin'] ) : '';
        $version = isset( $_POST['version'] ) ? sanitize_text_field( $_POST['version'] ) : '';
        $zip_url = isset( $_POST['zip_url'] ) ? esc_url_raw( $_POST['zip_url'] ) : '';
        
        if ( ! wp_verify_nonce( $nonce, 'jj_rollback_plugin_global_' . $plugin ) ) {
            wp_send_json_error( array( 'message' => __( '보안 검증 실패', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        if ( ! current_user_can( 'update_plugins' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        if ( empty( $plugin ) || empty( $version ) ) {
            wp_send_json_error( array( 'message' => __( '플러그인 또는 버전이 지정되지 않았습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        // 공유 롤백 클래스 로드
        $shared_path = dirname( dirname( __FILE__ ) ) . '/../shared-ui-assets/php/';
        if ( ! file_exists( $shared_path . 'class-jj-shared-loader.php' ) ) {
            $shared_path = WP_PLUGIN_DIR . '/acf-css-really-simple-style-management-center-master/shared-ui-assets/php/';
        }
        
        if ( file_exists( $shared_path . 'class-jj-shared-loader.php' ) ) {
            require_once $shared_path . 'class-jj-shared-loader.php';
            JJ_Shared_Loader::load( 'class-jj-rollback-shared' );
        }
        
        if ( ! class_exists( 'JJ_Rollback_Shared' ) ) {
            wp_send_json_error( array( 'message' => __( '롤백 클래스를 로드할 수 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        // 롤백 실행 (zip_url이 있으면 사용, 없으면 자동으로 찾기)
        $rollback = JJ_Rollback_Shared::instance();
        $result = $rollback->rollback_plugin( $plugin, $version );
        
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 
                'message' => $result->get_error_message(),
                'code' => $result->get_error_code(),
            ) );
        }
        
        wp_send_json_success( array(
            'message' => sprintf(
                __( '플러그인이 버전 %s로 롤백되었습니다.', 'acf-css-really-simple-style-management-center' ),
                $result['to_version']
            ),
            'from_version' => $result['from_version'],
            'to_version' => $result['to_version'],
            'data' => $result,
            'reload' => true,
        ) );
    }
    
    /**
     * 전역 자동 업데이트 토글 AJAX
     */
    public function ajax_toggle_auto_update_global() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
        $plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( $_POST['plugin'] ) : '';
        
        if ( ! wp_verify_nonce( $nonce, 'jj_toggle_auto_update_global_' . $plugin ) ) {
            wp_send_json_error( array( 'message' => __( '보안 검증 실패', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        if ( ! current_user_can( 'update_plugins' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
        $is_enabled = in_array( $plugin, $auto_updates, true );
        
        if ( $is_enabled ) {
            // 비활성화
            $auto_updates = array_diff( $auto_updates, array( $plugin ) );
            $message = __( '자동 업데이트가 비활성화되었습니다.', 'acf-css-really-simple-style-management-center' );
        } else {
            // 활성화
            if ( ! in_array( $plugin, $auto_updates, true ) ) {
                $auto_updates[] = $plugin;
            }
            $message = __( '자동 업데이트가 활성화되었습니다.', 'acf-css-really-simple-style-management-center' );
        }
        
        update_site_option( 'auto_update_plugins', array_values( $auto_updates ) );
        
        wp_send_json_success( array(
            'message' => $message,
            'enabled' => ! $is_enabled,
        ) );
    }
    
    /**
     * 전역 스타일 및 스크립트 로드
     */
    public function enqueue_global_assets( $hook ) {
        if ( 'plugins.php' !== $hook ) {
            return;
        }
        
        // 전역 JavaScript 추가
        wp_add_inline_script( 'jquery', "
        jQuery(document).ready(function($) {
            // 전역 자동 업데이트 토글
            $(document).on('click', '.jj-auto-update-toggle-global', function(e) {
                e.preventDefault();
                var \$link = $(this);
                var plugin = \$link.data('plugin');
                var nonce = \$link.data('nonce');
                var enabled = \$link.data('enabled') === '1';
                
                if (!confirm(enabled ? '자동 업데이트를 비활성화하시겠습니까?' : '자동 업데이트를 활성화하시겠습니까?')) {
                    return;
                }
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'jj_toggle_auto_update_global',
                        nonce: nonce,
                        plugin: plugin
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.data.message || '오류가 발생했습니다.');
                        }
                    },
                    error: function() {
                        alert('서버 통신 오류가 발생했습니다.');
                    }
                });
            });
            
            // 비활성화 링크 강조 (WordPress 기본 링크에 스타일 추가)
            $('.row-actions a[href*=\"action=deactivate\"], .row-actions .deactivate a').each(function() {
                if (!$(this).hasClass('jj-enhanced')) {
                    $(this).addClass('jj-enhanced').css({
                        'font-size': '15px',
                        'font-weight': '800',
                        'color': '#d63638',
                        'border-bottom': '2px solid #d63638',
                        'padding-bottom': '2px'
                    });
                }
            });
            
            // 전역 롤백 트리거 - [v23.0.3] 완전한 롤백 기능 구현
            $(document).on('click', '.jj-rollback-trigger', function(e) {
                e.preventDefault();
                var $trigger = $(this);
                var plugin = $trigger.data('plugin');
                var nonce = $trigger.data('nonce');
                var versions = [];
                
                try {
                    versions = JSON.parse($trigger.data('versions') || '[]');
                } catch (e) {
                    console.error('버전 데이터 파싱 실패:', e);
                }
                
                if (versions.length === 0) {
                    alert('롤백 가능한 버전을 찾을 수 없습니다.');
                    return;
                }
                
                // 버전 선택 모달 표시
                showRollbackModal(plugin, nonce, versions);
            });
            
            // 롤백 모달 표시 함수
            function showRollbackModal(plugin, nonce, versions) {
                // 모달 HTML 생성
                var modalHtml = '<div id="jj-rollback-modal" class="jj-rollback-modal-overlay">' +
                    '<div class="jj-rollback-modal">' +
                    '<div class="jj-rollback-modal-header">' +
                    '<h2>🔄 플러그인 롤백</h2>' +
                    '<button type="button" class="jj-rollback-modal-close" aria-label="닫기">×</button>' +
                    '</div>' +
                    '<div class="jj-rollback-modal-body">' +
                    '<p>롤백할 버전을 선택하세요:</p>' +
                    '<div class="jj-rollback-versions-list">';
                
                // versions가 배열인 경우와 객체인 경우 모두 처리
                if (Array.isArray(versions)) {
                    versions.forEach(function(version) {
                        modalHtml += '<label class="jj-rollback-version-item">' +
                            '<input type="radio" name="rollback_version" value="' + version + '">' +
                            '<span class="version-label">v' + version + '</span>' +
                            '</label>';
                    });
                } else if (typeof versions === 'object') {
                    // 객체인 경우 키(버전)만 사용
                    Object.keys(versions).forEach(function(version) {
                        modalHtml += '<label class="jj-rollback-version-item">' +
                            '<input type="radio" name="rollback_version" value="' + version + '">' +
                            '<span class="version-label">v' + version + '</span>' +
                            '</label>';
                    });
                }
                
                modalHtml += '</div>' +
                    '<div class="jj-rollback-warning">' +
                    '<p><strong>⚠️ 주의:</strong> 롤백 시 현재 버전의 데이터는 백업됩니다. 롤백 후 플러그인을 다시 활성화해야 할 수 있습니다.</p>' +
                    '</div>' +
                    '</div>' +
                    '<div class="jj-rollback-modal-footer">' +
                    '<button type="button" class="button jj-rollback-cancel">취소</button>' +
                    '<button type="button" class="button button-primary jj-rollback-confirm">롤백 실행</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                
                // 기존 모달 제거
                $('#jj-rollback-modal').remove();
                
                // 모달 추가
                $('body').append(modalHtml);
                var $modal = $('#jj-rollback-modal');
                
                // 모달 표시
                setTimeout(function() {
                    $modal.addClass('active');
                }, 10);
                
                // 닫기 버튼
                $modal.find('.jj-rollback-modal-close, .jj-rollback-cancel').on('click', function() {
                    $modal.removeClass('active');
                    setTimeout(function() {
                        $modal.remove();
                    }, 300);
                });
                
                // 롤백 실행
                $modal.find('.jj-rollback-confirm').on('click', function() {
                    var selectedVersion = $modal.find('input[name=\"rollback_version\"]:checked').val();
                    
                    if (!selectedVersion) {
                        alert('버전을 선택해주세요.');
                        return;
                    }
                    
                    if (!confirm('정말로 버전 ' + selectedVersion + '으로 롤백하시겠습니까?\\n\\n이 작업은 되돌릴 수 없습니다.')) {
                        return;
                    }
                    
                    // 롤백 실행
                    executeRollback(plugin, nonce, selectedVersion, $modal);
                });
            }
            
            // 롤백 실행 함수
            function executeRollback(plugin, nonce, version, $modal) {
                var $confirmBtn = $modal.find('.jj-rollback-confirm');
                var originalText = $confirmBtn.text();
                
                // 버튼 비활성화 및 로딩 표시
                $confirmBtn.prop('disabled', true).text('롤백 중...');
                $modal.find('.jj-rollback-modal-body').append('<div class=\"jj-rollback-progress\"><div class=\"spinner is-active\"></div> <span>롤백을 실행하고 있습니다...</span></div>');
                
                // AJAX 액션 결정 (전역 또는 개별)
                var action = 'jj_rollback_plugin_global';
                var actionSuffix = plugin.replace(/[\/\\\\]/g, '_');
                
                // 개별 AJAX 액션이 있는지 확인
                if ($('[data-plugin=\"' + plugin + '\"]').length > 0) {
                    // 개별 플러그인용 롤백 액션 사용
                    action = 'jj_rollback_plugin_' + actionSuffix;
                }
                
                // AJAX 요청
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: action,
                        nonce: nonce,
                        plugin: plugin,
                        version: version
                    },
                    timeout: 300000, // 5분 타임아웃
                    success: function(response) {
                        if (response.success) {
                            $modal.find('.jj-rollback-progress').html('<div class=\"notice notice-success\"><p>✅ ' + response.data.message + '</p></div>');
                            
                            setTimeout(function() {
                                // 페이지 새로고침
                                if (response.data.reload !== false) {
                                    location.reload();
                                }
                            }, 2000);
                        } else {
                            $modal.find('.jj-rollback-progress').html('<div class=\"notice notice-error\"><p>❌ ' + (response.data.message || '롤백에 실패했습니다.') + '</p></div>');
                            $confirmBtn.prop('disabled', false).text(originalText);
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMsg = '롤백 중 오류가 발생했습니다.';
                        if (status === 'timeout') {
                            errorMsg = '롤백 시간이 초과되었습니다. 서버 로그를 확인해주세요.';
                        } else if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                            errorMsg = xhr.responseJSON.data.message;
                        }
                        
                        $modal.find('.jj-rollback-progress').html('<div class=\"notice notice-error\"><p>❌ ' + errorMsg + '</p></div>');
                        $confirmBtn.prop('disabled', false).text(originalText);
                    }
                });
            }
        });
        " );
        
        // 롤백 모달 CSS 추가
        wp_add_inline_style( 'wp-admin', $this->get_rollback_modal_css() );
    }
    
    /**
     * 롤백 모달 CSS - [v23.0.3]
     */
    private function get_rollback_modal_css() {
        return '
        /* 롤백 모달 스타일 */
        .jj-rollback-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 100000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .jj-rollback-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .jj-rollback-modal {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        
        .jj-rollback-modal-overlay.active .jj-rollback-modal {
            transform: scale(1);
        }
        
        .jj-rollback-modal-header {
            padding: 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .jj-rollback-modal-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
        
        .jj-rollback-modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            padding: 0;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
        }
        
        .jj-rollback-modal-close:hover {
            color: #000;
        }
        
        .jj-rollback-modal-body {
            padding: 20px;
            overflow-y: auto;
            flex: 1;
        }
        
        .jj-rollback-versions-list {
            margin: 15px 0;
        }
        
        .jj-rollback-version-item {
            display: block;
            padding: 12px;
            margin: 8px 0;
            border: 2px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .jj-rollback-version-item:hover {
            border-color: #2271b1;
            background: #f0f6fc;
        }
        
        .jj-rollback-version-item input[type="radio"] {
            margin-right: 10px;
        }
        
        .jj-rollback-version-item input[type="radio"]:checked + .version-label {
            font-weight: 700;
            color: #2271b1;
        }
        
        .jj-rollback-warning {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
        }
        
        .jj-rollback-warning p {
            margin: 0;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .jj-rollback-modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .jj-rollback-progress {
            margin-top: 20px;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 4px;
        }
        
        .jj-rollback-progress .spinner {
            float: left;
            margin: 0 10px 0 0;
        }
        ';
    }
}

// 전역 플러그인 목록 향상기 초기화 (모든 플러그인에 적용)
if ( is_admin() ) {
    add_action( 'plugins_loaded', array( 'JJ_Global_Plugin_List_Enhancer', 'instance' ), 5 );
}

// ACF CSS Manager용 인스턴스 초기화
if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    $jj_plugin_list_enhancer = new JJ_Plugin_List_Enhancer();
    $jj_plugin_list_enhancer->init( array(
        'plugin_file' => JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php',
        'plugin_name' => 'ACF CSS 설정 관리자',
        'settings_url' => admin_url( 'options-general.php?page=jj-admin-center' ),
        'text_domain' => 'acf-css-really-simple-style-management-center',
        'version_constant' => 'JJ_STYLE_GUIDE_VERSION',
        'license_constant' => 'JJ_STYLE_GUIDE_LICENSE_TYPE',
    ) );
}
