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
            'upgrade_url' => 'https://3j-labs.com',
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

        // 1. 3J Labs 공식 사이트 (툴팁 포함)
        $new_meta[] = '<a href="' . esc_url( 'https://3j-labs.com' ) . '" target="_blank" rel="noopener noreferrer" class="jj-tooltip" data-tooltip="' . esc_attr__( '3J Labs 공식 웹사이트 방문', $text_domain ) . '" style="color: #2271b1; font-weight: 600;">🌐 ' . __( '공식 사이트', $text_domain ) . '</a>';

        // 2. 문서 (툴팁 포함)
        $new_meta[] = '<a href="' . esc_url( $this->plugin_config['docs_url'] ) . '" class="jj-tooltip" data-tooltip="' . esc_attr__( '플러그인 문서 및 사용 가이드', $text_domain ) . '" style="color: #135e96; font-weight: 600;">📚 ' . __( '문서', $text_domain ) . '</a>';

        // 3. 지원 (툴팁 포함)
        $new_meta[] = '<a href="' . esc_url( $this->plugin_config['support_url'] ) . '" target="_blank" rel="noopener noreferrer" class="jj-tooltip" data-tooltip="' . esc_attr__( '기술 지원 및 문의', $text_domain ) . '" style="color: #50575e; font-weight: 600;">💬 ' . __( '지원', $text_domain ) . '</a>';

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

        // 6. 자동 업데이트 상태 표시 및 토글 버튼
        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
        $is_auto_update_enabled = in_array( $this->plugin_basename, $auto_updates, true );
        
        $auto_update_class = 'jj-auto-update-toggle';
        $auto_update_nonce = wp_create_nonce( 'jj_toggle_auto_update_' . $this->plugin_basename );
        $auto_update_text = $is_auto_update_enabled ? __( '자동 업데이트 활성화', $text_domain ) : __( '자동 업데이트 비활성화', $text_domain );
        $auto_update_icon = $is_auto_update_enabled ? '✅' : '⚪';
        $auto_update_color = $is_auto_update_enabled ? '#00a32a' : '#646970';
        
        $new_meta[] = sprintf(
            '<a href="#" class="%s" data-plugin="%s" data-nonce="%s" data-enabled="%s" style="color: %s; font-weight: 700; text-decoration: none; cursor: pointer;" title="%s">%s %s</a>',
            esc_attr( $auto_update_class ),
            esc_attr( $this->plugin_basename ),
            esc_attr( $auto_update_nonce ),
            $is_auto_update_enabled ? '1' : '0',
            esc_attr( $auto_update_color ),
            esc_attr( __( '클릭하여 자동 업데이트를 토글합니다', $text_domain ) ),
            $auto_update_icon,
            esc_html( $auto_update_text )
        );

        // 7. 라이센스 키 필요 안내 (Free 버전인 경우)
        if ( ! $this->is_premium() ) {
            $new_meta[] = '<a href="' . esc_url( $this->plugin_config['settings_url'] ) . '#license" style="color: #2271b1; font-weight: 700;">🔑 ' . __( '라이센스 키 입력', $text_domain ) . '</a>';
        }

        return array_merge( $plugin_meta, $new_meta );
    }

    /**
     * 플러그인 동작 링크 향상
     * 
     * [Phase 19.1] 아이콘, 색상, 볼드체로 개선
     */
    public function enhance_plugin_action_links( $links ) {
        $new_links = array();
        $text_domain = $this->plugin_config['text_domain'];

        // 1. 설정 (Admin Center 메인) - 주요 링크이므로 강조 (툴팁 포함)
        $new_links['settings'] = sprintf(
            '<a href="%s" class="jj-tooltip" data-tooltip="%s" style="font-weight: 800; color: #2271b1; text-decoration: none;">⚙️ <strong>%s</strong></a>',
            esc_url( $this->plugin_config['settings_url'] ),
            esc_attr__( '플러그인 설정 페이지로 이동', $text_domain ),
            __( '설정 열기', $text_domain )
        );

        // 2. 스타일 (Style Guide) - 색상 적용
        if ( strpos( $this->plugin_basename, 'acf-css-really-simple-style-guide' ) !== false ) {
            $new_links['styles'] = sprintf(
                '<a href="%s" style="color: #135e96; font-weight: 700; text-decoration: none;">🎨 <strong>%s</strong></a>',
                esc_url( admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ) ),
                __( '스타일 관리', $text_domain )
            );
        }

        // 3. 백업/롤백 링크
        $rollback_nonce = wp_create_nonce( 'jj_rollback_plugin_' . $this->plugin_basename );
        $new_links['rollback'] = sprintf(
            '<a href="#" class="jj-rollback-trigger" data-plugin="%s" data-nonce="%s" style="color: #856404; font-weight: 700; text-decoration: none; cursor: pointer;">🔄 <strong>%s</strong></a>',
            esc_attr( $this->plugin_basename ),
            esc_attr( $rollback_nonce ),
            __( '이전 버전으로 롤백', $text_domain )
        );

        // 4. 시스템 상태 (System Status Tab)
        if ( strpos( $this->plugin_basename, 'acf-css-really-simple-style-guide' ) !== false ) {
            $new_links['system'] = sprintf(
                '<a href="%s" style="color: #646970; font-weight: 600; text-decoration: none;">📊 %s</a>',
                esc_url( admin_url( 'options-general.php?page=jj-admin-center#system-status' ) ),
                __( '시스템 진단', $text_domain )
            );
        }

        // 5. 업그레이드 링크 또는 Pro 뱃지 표시
        if ( ! $this->is_premium() ) {
            $new_links['upgrade'] = sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" style="color: #00a32a; font-weight: 800; text-decoration: none;">🚀 <strong>%s</strong></a>',
                esc_url( $this->plugin_config['upgrade_url'] ),
                __( 'PRO로 업그레이드', $text_domain )
            );
        } else {
            // Pro 사용자에게 만족감을 주는 뱃지 표시
            $new_links['pro_badge'] = sprintf(
                '<span style="color: #00a32a; font-weight: 900; cursor: default; background: linear-gradient(135deg, #00a32a 0%%, #3fb950 100%%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;" title="%s">⭐ <strong>%s</strong></span>',
                esc_attr__( '현재 Pro 버전을 사용 중입니다.', $text_domain ),
                __( 'Pro 버전', $text_domain )
            );
        }

        // 새 링크를 기존 링크(비활성화 등) 앞에 추가
        return array_merge( $new_links, $links );
    }

    /**
     * 작성자 정보 영역 개선
     * 
     * [Phase 19.1] 작성자 정보에 추가 메타데이터 추가
     */
    public function enhance_author_info( $plugin_meta, $plugin_file ) {
        if ( $plugin_file !== $this->plugin_basename ) {
            return $plugin_meta;
        }

        // 플러그인 헤더 정보 가져오기
        $plugin_data = get_plugin_data( $this->plugin_file );
        
        $new_meta = array();
        $text_domain = $this->plugin_config['text_domain'];
        
        // 작성자 정보 강화
        if ( ! empty( $plugin_data['Author'] ) ) {
            $author_uri = ! empty( $plugin_data['AuthorURI'] ) ? $plugin_data['AuthorURI'] : 'https://3j-labs.com';
            $new_meta[] = sprintf(
                '<span style="color: #646970; font-weight: 600;" title="%s">👨‍💻 %s</span>',
                esc_attr( __( '플러그인 개발자', $text_domain ) ),
                esc_html( $plugin_data['Author'] )
            );
        }
        
        // 버전 정보 강화
        if ( ! empty( $plugin_data['Version'] ) ) {
            $version_constant = $this->plugin_config['version_constant'];
            $version = defined( $version_constant ) ? constant( $version_constant ) : $plugin_data['Version'];
            $new_meta[] = sprintf(
                '<span style="color: #2271b1; font-weight: 700;" title="%s">📦 v%s</span>',
                esc_attr( __( '현재 플러그인 버전', $text_domain ) ),
                esc_html( $version )
            );
        }
        
        // 플러그인 URI
        if ( ! empty( $plugin_data['PluginURI'] ) ) {
            $new_meta[] = sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" style="color: #135e96; font-weight: 600;" title="%s">🔗 %s</a>',
                esc_url( $plugin_data['PluginURI'] ),
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
        
        // 실제 롤백 로직은 복잡하므로 여기서는 시뮬레이션
        // WP Core의 업데이트/설치 클래스를 활용해야 함
        
        wp_send_json_success( array( 'message' => __( '롤백 기능은 준비 중입니다.', $this->plugin_config['text_domain'] ) ) );
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
        /* [Phase 19.1] 플러그인 목록 페이지 UI/UX 개선 */
        .jj-auto-update-toggle:hover {
            opacity: 0.8;
            text-decoration: underline !important;
        }
        .jj-rollback-trigger:hover {
            opacity: 0.8;
            text-decoration: underline !important;
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
            // 자동 업데이트 토글
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
