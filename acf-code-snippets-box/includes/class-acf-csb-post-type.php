<?php
/**
 * ACF Code Snippets Box - Custom Post Type
 * 
 * 코드 스니펫을 저장하는 커스텀 포스트 타입 등록
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Post Type 클래스
 */
class ACF_CSB_Post_Type {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * Post Type 슬러그
     */
    const POST_TYPE = 'acf_code_snippet';

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_meta_boxes' ), 10, 2 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }

    /**
     * Post Type 등록
     */
    public function register() {
        $labels = array(
            'name'                  => _x( '코드 스니펫', 'Post Type 이름', 'acf-code-snippets-box' ),
            'singular_name'         => _x( '코드 스니펫', 'Post Type 단수 이름', 'acf-code-snippets-box' ),
            'menu_name'             => __( 'Code Snippets', 'acf-code-snippets-box' ),
            'add_new'               => __( '새 스니펫 추가', 'acf-code-snippets-box' ),
            'add_new_item'          => __( '새 코드 스니펫 추가', 'acf-code-snippets-box' ),
            'edit_item'             => __( '코드 스니펫 편집', 'acf-code-snippets-box' ),
            'new_item'              => __( '새 코드 스니펫', 'acf-code-snippets-box' ),
            'view_item'             => __( '코드 스니펫 보기', 'acf-code-snippets-box' ),
            'search_items'          => __( '코드 스니펫 검색', 'acf-code-snippets-box' ),
            'not_found'             => __( '스니펫을 찾을 수 없습니다.', 'acf-code-snippets-box' ),
            'not_found_in_trash'    => __( '휴지통에 스니펫이 없습니다.', 'acf-code-snippets-box' ),
            'all_items'             => __( '모든 스니펫', 'acf-code-snippets-box' ),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => false, // 커스텀 메뉴에 표시
            'query_var'           => false,
            'rewrite'             => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => null,
            'supports'            => array( 'title' ),
            'show_in_rest'        => false,
        );

        register_post_type( self::POST_TYPE, $args );

        // 카테고리 택소노미 등록
        $this->register_taxonomy();
    }

    /**
     * 택소노미 등록
     */
    private function register_taxonomy() {
        $labels = array(
            'name'              => _x( '스니펫 카테고리', 'taxonomy 이름', 'acf-code-snippets-box' ),
            'singular_name'     => _x( '카테고리', 'taxonomy 단수 이름', 'acf-code-snippets-box' ),
            'search_items'      => __( '카테고리 검색', 'acf-code-snippets-box' ),
            'all_items'         => __( '모든 카테고리', 'acf-code-snippets-box' ),
            'edit_item'         => __( '카테고리 편집', 'acf-code-snippets-box' ),
            'update_item'       => __( '카테고리 업데이트', 'acf-code-snippets-box' ),
            'add_new_item'      => __( '새 카테고리 추가', 'acf-code-snippets-box' ),
            'new_item_name'     => __( '새 카테고리 이름', 'acf-code-snippets-box' ),
            'menu_name'         => __( '카테고리', 'acf-code-snippets-box' ),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => false,
            'rewrite'           => false,
        );

        register_taxonomy( 'snippet_category', self::POST_TYPE, $args );
    }

    /**
     * 메타 박스 추가
     */
    public function add_meta_boxes() {
        // 코드 에디터
        add_meta_box(
            'acf_csb_code_editor',
            __( '코드 에디터', 'acf-code-snippets-box' ),
            array( $this, 'render_code_editor' ),
            self::POST_TYPE,
            'normal',
            'high'
        );

        // 코드 타입 및 설정
        add_meta_box(
            'acf_csb_settings',
            __( '스니펫 설정', 'acf-code-snippets-box' ),
            array( $this, 'render_settings_meta_box' ),
            self::POST_TYPE,
            'side',
            'high'
        );

        // 트리거 조건
        add_meta_box(
            'acf_csb_triggers',
            __( '실행 조건 (트리거)', 'acf-code-snippets-box' ),
            array( $this, 'render_triggers_meta_box' ),
            self::POST_TYPE,
            'normal',
            'default'
        );

        // ACF CSS 연동 (활성화된 경우)
        if ( ACF_Code_Snippets_Box::is_acf_css_active() ) {
            add_meta_box(
                'acf_csb_acf_css',
                __( 'ACF CSS 연동', 'acf-code-snippets-box' ),
                array( $this, 'render_acf_css_meta_box' ),
                self::POST_TYPE,
                'side',
                'default'
            );
        }
    }

    /**
     * 코드 에디터 렌더링
     */
    public function render_code_editor( $post ) {
        wp_nonce_field( 'acf_csb_save_meta', 'acf_csb_nonce' );
        $code = get_post_meta( $post->ID, '_acf_csb_code', true );
        ?>
        <div class="acf-csb-code-editor-wrapper">
            <textarea 
                id="acf_csb_code" 
                name="acf_csb_code" 
                rows="20" 
                style="width: 100%; font-family: 'Fira Code', 'Monaco', 'Consolas', monospace; font-size: 14px; tab-size: 4;"
            ><?php echo esc_textarea( $code ); ?></textarea>
            <p class="description">
                <?php esc_html_e( 'PHP 코드는 <?php ?> 태그 없이 작성하세요. 보안상 PHP 실행은 설정에서 활성화해야 합니다.', 'acf-code-snippets-box' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * 설정 메타 박스 렌더링
     * [v5.0.0] 코드 위치 지정 및 향상된 우선순위 시스템 추가
     */
    public function render_settings_meta_box( $post ) {
        $code_type = get_post_meta( $post->ID, '_acf_csb_code_type', true ) ?: 'css';
        $is_active = get_post_meta( $post->ID, '_acf_csb_active', true );
        $priority  = get_post_meta( $post->ID, '_acf_csb_priority', true ) ?: 10;
        $description = get_post_meta( $post->ID, '_acf_csb_description', true );
        $code_location = get_post_meta( $post->ID, '_acf_csb_code_location', true );
        $custom_hook = get_post_meta( $post->ID, '_acf_csb_custom_hook', true );

        $code_locations = ACF_CSB_Triggers::get_code_locations();
        $priority_presets = ACF_CSB_Triggers::get_priority_presets();
        ?>
        <style>
        .acf-csb-settings-v5 .setting-group { margin-bottom: 18px; padding-bottom: 18px; border-bottom: 1px solid #eee; }
        .acf-csb-settings-v5 .setting-group:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .acf-csb-settings-v5 .setting-label { display: block; font-weight: 600; margin-bottom: 6px; color: #1d2327; font-size: 13px; }
        .acf-csb-settings-v5 .setting-desc { font-size: 11px; color: #646970; margin-top: 4px; }
        .acf-csb-settings-v5 .active-toggle { display: flex; align-items: center; gap: 10px; padding: 12px; background: #f6f7f7; border-radius: 6px; }
        .acf-csb-settings-v5 .active-toggle.is-active { background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); }
        .acf-csb-settings-v5 .active-toggle input[type="checkbox"] { width: 20px; height: 20px; margin: 0; }
        .acf-csb-settings-v5 .active-toggle label { font-weight: 600; font-size: 14px; color: #1d2327; }
        .acf-csb-settings-v5 .code-type-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px; }
        .acf-csb-settings-v5 .code-type-btn { padding: 10px 8px; text-align: center; background: #fff; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 12px; transition: all 0.2s; }
        .acf-csb-settings-v5 .code-type-btn:hover { border-color: #2271b1; }
        .acf-csb-settings-v5 .code-type-btn.selected { border-color: #2271b1; background: #e7f3ff; color: #2271b1; }
        .acf-csb-settings-v5 .code-type-btn input { display: none; }
        .acf-csb-settings-v5 .priority-preset { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; }
        .acf-csb-settings-v5 .priority-chip { padding: 4px 8px; font-size: 11px; background: #f0f0f1; border: 1px solid #ddd; border-radius: 12px; cursor: pointer; transition: all 0.2s; }
        .acf-csb-settings-v5 .priority-chip:hover { border-color: #2271b1; background: #e7f3ff; }
        .acf-csb-settings-v5 .priority-chip.selected { background: #2271b1; color: #fff; border-color: #2271b1; }
        .acf-csb-settings-v5 .location-select { margin-top: 10px; }
        .acf-csb-settings-v5 .custom-hook-field { display: none; margin-top: 10px; }
        .acf-csb-settings-v5 .custom-hook-field.visible { display: block; }
        </style>

        <div class="acf-csb-settings-v5">
            <!-- 활성화 토글 -->
            <div class="setting-group">
                <div class="active-toggle <?php echo $is_active ? 'is-active' : ''; ?>" id="acf-csb-active-toggle">
                    <input type="checkbox" name="acf_csb_active" id="acf_csb_active" value="1" <?php checked( $is_active, '1' ); ?>>
                    <label for="acf_csb_active"><?php echo $is_active ? '✅ 활성화됨' : '⏸️ 비활성화'; ?></label>
                </div>
            </div>

            <!-- 코드 타입 -->
            <div class="setting-group">
                <label class="setting-label"><?php esc_html_e( '📝 코드 타입', 'acf-code-snippets-box' ); ?></label>
                <div class="code-type-grid">
                    <?php
                    $code_types = array(
                        'css'  => array( 'label' => 'CSS', 'icon' => '🎨', 'color' => '#f5576c' ),
                        'js'   => array( 'label' => 'JavaScript', 'icon' => '⚡', 'color' => '#f0ad4e' ),
                        'html' => array( 'label' => 'HTML', 'icon' => '📄', 'color' => '#e34c26' ),
                        'php'  => array( 'label' => 'PHP', 'icon' => '🐘', 'color' => '#8892bf' ),
                    );
                    foreach ( $code_types as $type_key => $type_data ) :
                    ?>
                    <label class="code-type-btn <?php echo $code_type === $type_key ? 'selected' : ''; ?>" data-type="<?php echo esc_attr( $type_key ); ?>">
                        <input type="radio" name="acf_csb_code_type" value="<?php echo esc_attr( $type_key ); ?>" <?php checked( $code_type, $type_key ); ?>>
                        <span style="color: <?php echo esc_attr( $type_data['color'] ); ?>;"><?php echo esc_html( $type_data['icon'] ); ?></span>
                        <?php echo esc_html( $type_data['label'] ); ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 코드 출력 위치 (코드 타입별) -->
            <div class="setting-group">
                <label class="setting-label"><?php esc_html_e( '📍 코드 출력 위치', 'acf-code-snippets-box' ); ?></label>
                <?php foreach ( $code_locations as $type_key => $location_data ) : ?>
                <div class="location-select" data-for-type="<?php echo esc_attr( $type_key ); ?>" style="<?php echo $code_type !== $type_key ? 'display:none;' : ''; ?>">
                    <select name="acf_csb_code_location_<?php echo esc_attr( $type_key ); ?>" class="code-location-select" style="width: 100%;">
                        <?php
                        $current_location = $code_location ?: array_key_first( $location_data['options'] );
                        foreach ( $location_data['options'] as $loc_key => $loc_label ) :
                        ?>
                        <option value="<?php echo esc_attr( $loc_key ); ?>" <?php selected( $current_location, $loc_key ); ?>><?php echo esc_html( $loc_label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endforeach; ?>

                <!-- 커스텀 훅 필드 (PHP용) -->
                <div class="custom-hook-field" id="custom-hook-field" style="<?php echo ( $code_type === 'php' && $code_location === 'custom' ) ? '' : 'display:none;'; ?>">
                    <label class="setting-label" style="font-size: 12px;"><?php esc_html_e( '커스텀 훅 이름', 'acf-code-snippets-box' ); ?></label>
                    <input type="text" name="acf_csb_custom_hook" value="<?php echo esc_attr( $custom_hook ); ?>" placeholder="예: woocommerce_before_cart" style="width: 100%;">
                </div>
                <p class="setting-desc"><?php esc_html_e( '코드가 출력될 WordPress 훅을 선택하세요.', 'acf-code-snippets-box' ); ?></p>
            </div>

            <!-- 우선순위 -->
            <div class="setting-group">
                <label class="setting-label"><?php esc_html_e( '🔢 우선순위', 'acf-code-snippets-box' ); ?></label>
                <div class="priority-preset">
                    <?php foreach ( $priority_presets as $preset_value => $preset_label ) : ?>
                    <span class="priority-chip <?php echo absint( $priority ) === $preset_value ? 'selected' : ''; ?>" data-value="<?php echo esc_attr( $preset_value ); ?>">
                        <?php echo esc_html( $preset_label ); ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <input type="number" id="acf_csb_priority" name="acf_csb_priority" value="<?php echo esc_attr( $priority ); ?>" min="1" max="9999" style="width: 100%;">
                <p class="setting-desc"><?php esc_html_e( '숫자가 낮을수록 먼저 실행됩니다.', 'acf-code-snippets-box' ); ?></p>
            </div>

            <!-- 설명 -->
            <div class="setting-group">
                <label for="acf_csb_description" class="setting-label"><?php esc_html_e( '📝 설명', 'acf-code-snippets-box' ); ?></label>
                <textarea id="acf_csb_description" name="acf_csb_description" rows="2" style="width: 100%;" placeholder="<?php esc_attr_e( '이 스니펫에 대한 간단한 설명...', 'acf-code-snippets-box' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // 활성화 토글 스타일
            $('#acf_csb_active').on('change', function() {
                var $toggle = $('#acf-csb-active-toggle');
                var $label = $toggle.find('label');
                if ($(this).is(':checked')) {
                    $toggle.addClass('is-active');
                    $label.text('✅ 활성화됨');
                } else {
                    $toggle.removeClass('is-active');
                    $label.text('⏸️ 비활성화');
                }
            });

            // 코드 타입 선택
            $('.code-type-btn').on('click', function() {
                var $btn = $(this);
                var type = $btn.data('type');

                $('.code-type-btn').removeClass('selected');
                $btn.addClass('selected');
                $btn.find('input').prop('checked', true);

                // 해당 타입의 위치 선택 표시
                $('.location-select').hide();
                $('.location-select[data-for-type="' + type + '"]').show();

                // 커스텀 훅 필드 표시/숨김
                updateCustomHookField();
            });

            // 우선순위 프리셋 클릭
            $('.priority-chip').on('click', function() {
                var value = $(this).data('value');
                $('#acf_csb_priority').val(value);
                $('.priority-chip').removeClass('selected');
                $(this).addClass('selected');
            });

            // 우선순위 입력 시 프리셋 선택 업데이트
            $('#acf_csb_priority').on('input', function() {
                var value = parseInt($(this).val());
                $('.priority-chip').removeClass('selected');
                $('.priority-chip[data-value="' + value + '"]').addClass('selected');
            });

            // 커스텀 훅 필드 표시 함수
            function updateCustomHookField() {
                var codeType = $('input[name="acf_csb_code_type"]:checked').val();
                var location = $('.location-select[data-for-type="' + codeType + '"] select').val();

                if (codeType === 'php' && location === 'custom') {
                    $('#custom-hook-field').show();
                } else {
                    $('#custom-hook-field').hide();
                }
            }

            // 위치 변경 시 커스텀 훅 필드 업데이트
            $('.code-location-select').on('change', updateCustomHookField);

            // 초기 상태 설정
            updateCustomHookField();
        });
        </script>
        <?php
    }

    /**
     * 트리거 조건 메타 박스 렌더링
     * [v5.0.0] 탭 기반 고급 트리거 UI
     */
    public function render_triggers_meta_box( $post ) {
        $triggers = get_post_meta( $post->ID, '_acf_csb_triggers', true ) ?: array();
        $trigger_groups = ACF_CSB_Triggers::get_trigger_groups();
        $available_triggers = ACF_CSB_Triggers::get_available_triggers();

        // 기본값 설정
        $location = isset( $triggers['location'] ) ? $triggers['location'] : 'everywhere';
        $pages    = isset( $triggers['pages'] ) ? $triggers['pages'] : '';
        $posts    = isset( $triggers['posts'] ) ? $triggers['posts'] : '';
        $post_types_selected = isset( $triggers['post_types_selected'] ) ? $triggers['post_types_selected'] : array();
        $user_roles = isset( $triggers['user_roles'] ) ? $triggers['user_roles'] : array();
        $device   = isset( $triggers['device'] ) ? $triggers['device'] : 'all';
        $logged_in = isset( $triggers['logged_in'] ) ? $triggers['logged_in'] : 'all';
        ?>
        <style>
        .acf-csb-triggers-v5 { background: #f9f9f9; border-radius: 8px; overflow: hidden; }
        .acf-csb-triggers-v5 .trigger-tabs { display: flex; flex-wrap: wrap; border-bottom: 2px solid #ddd; background: #fff; }
        .acf-csb-triggers-v5 .trigger-tab { padding: 12px 16px; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; font-size: 13px; color: #666; transition: all 0.2s; }
        .acf-csb-triggers-v5 .trigger-tab:hover { background: #f0f0f1; color: #1d2327; }
        .acf-csb-triggers-v5 .trigger-tab.active { border-bottom-color: #2271b1; color: #2271b1; font-weight: 600; }
        .acf-csb-triggers-v5 .trigger-panel { display: none; padding: 20px; }
        .acf-csb-triggers-v5 .trigger-panel.active { display: block; }
        .acf-csb-triggers-v5 .trigger-row { margin-bottom: 20px; }
        .acf-csb-triggers-v5 .trigger-row:last-child { margin-bottom: 0; }
        .acf-csb-triggers-v5 .trigger-label { display: block; font-weight: 600; margin-bottom: 8px; color: #1d2327; }
        .acf-csb-triggers-v5 .trigger-desc { font-size: 12px; color: #646970; margin-top: 4px; }
        .acf-csb-triggers-v5 select, .acf-csb-triggers-v5 input[type="text"], .acf-csb-triggers-v5 input[type="date"], .acf-csb-triggers-v5 input[type="time"], .acf-csb-triggers-v5 textarea { width: 100%; }
        .acf-csb-triggers-v5 .checkbox-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 8px; }
        .acf-csb-triggers-v5 .checkbox-grid label { display: flex; align-items: center; gap: 6px; padding: 6px 10px; background: #fff; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; font-size: 13px; }
        .acf-csb-triggers-v5 .checkbox-grid label:hover { border-color: #2271b1; }
        .acf-csb-triggers-v5 .conditional-field { display: none; margin-top: 15px; padding: 15px; background: #fff; border: 1px dashed #ccc; border-radius: 4px; }
        .acf-csb-triggers-v5 .conditional-field.visible { display: block; }
        .acf-csb-triggers-v5 .weekday-grid { display: flex; flex-wrap: wrap; gap: 8px; }
        .acf-csb-triggers-v5 .weekday-btn { padding: 8px 14px; background: #f0f0f1; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; font-size: 13px; }
        .acf-csb-triggers-v5 .weekday-btn.selected { background: #2271b1; color: #fff; border-color: #2271b1; }
        </style>

        <div class="acf-csb-triggers-v5">
            <!-- 탭 네비게이션 -->
            <div class="trigger-tabs">
                <?php
                $first = true;
                foreach ( $trigger_groups as $group_key => $group ) :
                    // WooCommerce/멀티사이트 그룹은 조건부 표시
                    if ( $group_key === 'integrations' && ! class_exists( 'WooCommerce' ) && ! is_multisite() ) {
                        continue;
                    }
                ?>
                <div class="trigger-tab <?php echo $first ? 'active' : ''; ?>" data-tab="<?php echo esc_attr( $group_key ); ?>">
                    <?php echo esc_html( $group['label'] ); ?>
                </div>
                <?php
                    $first = false;
                endforeach;
                ?>
            </div>

            <!-- 위치 탭 -->
            <div class="trigger-panel active" data-panel="location">
                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '📍 실행 위치', 'acf-code-snippets-box' ); ?></label>
                    <select name="acf_csb_triggers[location]" id="acf_csb_location">
                        <?php
                        $location_options = $available_triggers['location']['options'];
                        foreach ( $location_options as $opt_key => $opt_label ) :
                        ?>
                        <option value="<?php echo esc_attr( $opt_key ); ?>" <?php selected( $location, $opt_key ); ?>><?php echo esc_html( $opt_label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 특정 페이지 -->
                <div class="conditional-field" data-show-when="location" data-show-value="specific_pages">
                    <label class="trigger-label"><?php esc_html_e( '페이지 ID (쉼표 구분)', 'acf-code-snippets-box' ); ?></label>
                    <input type="text" name="acf_csb_triggers[pages]" value="<?php echo esc_attr( $pages ); ?>" placeholder="1, 2, 3">
                    <p class="trigger-desc"><?php esc_html_e( '페이지 ID를 쉼표로 구분하여 입력하세요.', 'acf-code-snippets-box' ); ?></p>
                </div>

                <!-- 특정 포스트 -->
                <div class="conditional-field" data-show-when="location" data-show-value="specific_posts">
                    <label class="trigger-label"><?php esc_html_e( '포스트 ID (쉼표 구분)', 'acf-code-snippets-box' ); ?></label>
                    <input type="text" name="acf_csb_triggers[posts]" value="<?php echo esc_attr( $posts ); ?>" placeholder="10, 20, 30">
                </div>

                <!-- 특정 포스트 타입 -->
                <div class="conditional-field" data-show-when="location" data-show-value="post_types">
                    <label class="trigger-label"><?php esc_html_e( '포스트 타입 선택', 'acf-code-snippets-box' ); ?></label>
                    <div class="checkbox-grid">
                        <?php
                        $all_post_types = ACF_CSB_Triggers::get_post_types();
                        foreach ( $all_post_types as $pt_key => $pt_label ) :
                        ?>
                        <label>
                            <input type="checkbox" name="acf_csb_triggers[post_types_selected][]" value="<?php echo esc_attr( $pt_key ); ?>" <?php checked( in_array( $pt_key, $post_types_selected, true ) ); ?>>
                            <?php echo esc_html( $pt_label ); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- URL 탭 -->
            <div class="trigger-panel" data-panel="url">
                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '🔗 URL 패턴 (포함)', 'acf-code-snippets-box' ); ?></label>
                    <textarea name="acf_csb_triggers[url_pattern]" rows="3" placeholder="<?php echo esc_attr( $available_triggers['url_pattern']['placeholder'] ); ?>"><?php echo esc_textarea( isset( $triggers['url_pattern'] ) ? $triggers['url_pattern'] : '' ); ?></textarea>
                    <p class="trigger-desc"><?php echo esc_html( $available_triggers['url_pattern']['description'] ); ?></p>
                </div>

                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '🚫 URL 패턴 (제외)', 'acf-code-snippets-box' ); ?></label>
                    <textarea name="acf_csb_triggers[url_exclude]" rows="3" placeholder="<?php echo esc_attr( $available_triggers['url_exclude']['placeholder'] ); ?>"><?php echo esc_textarea( isset( $triggers['url_exclude'] ) ? $triggers['url_exclude'] : '' ); ?></textarea>
                    <p class="trigger-desc"><?php echo esc_html( $available_triggers['url_exclude']['description'] ); ?></p>
                </div>

                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '❓ 쿼리 파라미터', 'acf-code-snippets-box' ); ?></label>
                    <input type="text" name="acf_csb_triggers[query_string]" value="<?php echo esc_attr( isset( $triggers['query_string'] ) ? $triggers['query_string'] : '' ); ?>" placeholder="<?php echo esc_attr( $available_triggers['query_string']['placeholder'] ); ?>">
                    <p class="trigger-desc"><?php echo esc_html( $available_triggers['query_string']['description'] ); ?></p>
                </div>
            </div>

            <!-- 사용자 탭 -->
            <div class="trigger-panel" data-panel="user">
                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '🔐 로그인 상태', 'acf-code-snippets-box' ); ?></label>
                    <select name="acf_csb_triggers[logged_in]">
                        <?php foreach ( $available_triggers['logged_in']['options'] as $opt_key => $opt_label ) : ?>
                        <option value="<?php echo esc_attr( $opt_key ); ?>" <?php selected( $logged_in, $opt_key ); ?>><?php echo esc_html( $opt_label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '👤 사용자 역할', 'acf-code-snippets-box' ); ?></label>
                    <div class="checkbox-grid">
                        <?php
                        $all_roles = wp_roles()->get_names();
                        foreach ( $all_roles as $role_key => $role_name ) :
                        ?>
                        <label>
                            <input type="checkbox" name="acf_csb_triggers[user_roles][]" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( in_array( $role_key, $user_roles, true ) ); ?>>
                            <?php echo esc_html( $role_name ); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="trigger-desc"><?php esc_html_e( '선택하지 않으면 모든 사용자에게 적용됩니다.', 'acf-code-snippets-box' ); ?></p>
                </div>

                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '🛡️ 사용자 권한 (Capability)', 'acf-code-snippets-box' ); ?></label>
                    <select name="acf_csb_triggers[user_capability]">
                        <?php foreach ( $available_triggers['user_capability']['options'] as $opt_key => $opt_label ) : ?>
                        <option value="<?php echo esc_attr( $opt_key ); ?>" <?php selected( isset( $triggers['user_capability'] ) ? $triggers['user_capability'] : '', $opt_key ); ?>><?php echo esc_html( $opt_label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- 디바이스 탭 -->
            <div class="trigger-panel" data-panel="device">
                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '📱 디바이스', 'acf-code-snippets-box' ); ?></label>
                    <select name="acf_csb_triggers[device]">
                        <?php foreach ( $available_triggers['device']['options'] as $opt_key => $opt_label ) : ?>
                        <option value="<?php echo esc_attr( $opt_key ); ?>" <?php selected( $device, $opt_key ); ?>><?php echo esc_html( $opt_label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '🌐 브라우저', 'acf-code-snippets-box' ); ?></label>
                    <select name="acf_csb_triggers[browser]">
                        <?php foreach ( $available_triggers['browser']['options'] as $opt_key => $opt_label ) : ?>
                        <option value="<?php echo esc_attr( $opt_key ); ?>" <?php selected( isset( $triggers['browser'] ) ? $triggers['browser'] : 'all', $opt_key ); ?>><?php echo esc_html( $opt_label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- 시간 탭 -->
            <div class="trigger-panel" data-panel="time">
                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '⏰ 시간 조건', 'acf-code-snippets-box' ); ?></label>
                    <select name="acf_csb_triggers[time_based]" id="acf_csb_time_based">
                        <?php foreach ( $available_triggers['time_based']['options'] as $opt_key => $opt_label ) : ?>
                        <option value="<?php echo esc_attr( $opt_key ); ?>" <?php selected( isset( $triggers['time_based'] ) ? $triggers['time_based'] : 'always', $opt_key ); ?>><?php echo esc_html( $opt_label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 날짜 범위 -->
                <div class="conditional-field" data-show-when="time_based" data-show-value="date_range">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label class="trigger-label"><?php esc_html_e( '📅 시작 날짜', 'acf-code-snippets-box' ); ?></label>
                            <input type="date" name="acf_csb_triggers[date_start]" value="<?php echo esc_attr( isset( $triggers['date_start'] ) ? $triggers['date_start'] : '' ); ?>">
                        </div>
                        <div>
                            <label class="trigger-label"><?php esc_html_e( '📅 종료 날짜', 'acf-code-snippets-box' ); ?></label>
                            <input type="date" name="acf_csb_triggers[date_end]" value="<?php echo esc_attr( isset( $triggers['date_end'] ) ? $triggers['date_end'] : '' ); ?>">
                        </div>
                    </div>
                </div>

                <!-- 시간 범위 -->
                <div class="conditional-field" data-show-when="time_based" data-show-value="time_range">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label class="trigger-label"><?php esc_html_e( '🕐 시작 시간', 'acf-code-snippets-box' ); ?></label>
                            <input type="time" name="acf_csb_triggers[time_start]" value="<?php echo esc_attr( isset( $triggers['time_start'] ) ? $triggers['time_start'] : '' ); ?>">
                        </div>
                        <div>
                            <label class="trigger-label"><?php esc_html_e( '🕐 종료 시간', 'acf-code-snippets-box' ); ?></label>
                            <input type="time" name="acf_csb_triggers[time_end]" value="<?php echo esc_attr( isset( $triggers['time_end'] ) ? $triggers['time_end'] : '' ); ?>">
                        </div>
                    </div>
                </div>

                <!-- 요일 선택 -->
                <div class="conditional-field" data-show-when="time_based" data-show-value="weekdays">
                    <label class="trigger-label"><?php esc_html_e( '📆 요일 선택', 'acf-code-snippets-box' ); ?></label>
                    <div class="weekday-grid">
                        <?php
                        $weekdays = array( '0' => '일', '1' => '월', '2' => '화', '3' => '수', '4' => '목', '5' => '금', '6' => '토' );
                        $selected_weekdays = isset( $triggers['weekday_select'] ) ? (array) $triggers['weekday_select'] : array();
                        foreach ( $weekdays as $day_key => $day_label ) :
                        ?>
                        <label class="weekday-btn <?php echo in_array( $day_key, $selected_weekdays, true ) ? 'selected' : ''; ?>">
                            <input type="checkbox" name="acf_csb_triggers[weekday_select][]" value="<?php echo esc_attr( $day_key ); ?>" <?php checked( in_array( $day_key, $selected_weekdays, true ) ); ?> style="display: none;">
                            <?php echo esc_html( $day_label ); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- 고급 탭 -->
            <div class="trigger-panel" data-panel="advanced">
                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '🍪 쿠키 존재 확인', 'acf-code-snippets-box' ); ?></label>
                    <input type="text" name="acf_csb_triggers[cookie_exists]" value="<?php echo esc_attr( isset( $triggers['cookie_exists'] ) ? $triggers['cookie_exists'] : '' ); ?>" placeholder="<?php echo esc_attr( $available_triggers['cookie_exists']['placeholder'] ); ?>">
                </div>

                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '🍪 쿠키 값 확인', 'acf-code-snippets-box' ); ?></label>
                    <input type="text" name="acf_csb_triggers[cookie_value]" value="<?php echo esc_attr( isset( $triggers['cookie_value'] ) ? $triggers['cookie_value'] : '' ); ?>" placeholder="<?php echo esc_attr( $available_triggers['cookie_value']['placeholder'] ); ?>">
                </div>

                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '🔙 리퍼러 (출처)', 'acf-code-snippets-box' ); ?></label>
                    <input type="text" name="acf_csb_triggers[referrer]" value="<?php echo esc_attr( isset( $triggers['referrer'] ) ? $triggers['referrer'] : '' ); ?>" placeholder="<?php echo esc_attr( $available_triggers['referrer']['placeholder'] ); ?>">
                    <p class="trigger-desc"><?php echo esc_html( $available_triggers['referrer']['description'] ); ?></p>
                </div>

                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '🌍 언어', 'acf-code-snippets-box' ); ?></label>
                    <select name="acf_csb_triggers[language]">
                        <?php
                        $languages = ACF_CSB_Triggers::get_available_languages();
                        foreach ( $languages as $lang_key => $lang_label ) :
                        ?>
                        <option value="<?php echo esc_attr( $lang_key ); ?>" <?php selected( isset( $triggers['language'] ) ? $triggers['language'] : '', $lang_key ); ?>><?php echo esc_html( $lang_label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- 통합 탭 (WooCommerce 등) -->
            <?php if ( class_exists( 'WooCommerce' ) || is_multisite() ) : ?>
            <div class="trigger-panel" data-panel="integrations">
                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '🛒 WooCommerce 조건', 'acf-code-snippets-box' ); ?></label>
                    <select name="acf_csb_triggers[woocommerce]">
                        <?php foreach ( $available_triggers['woocommerce']['options'] as $opt_key => $opt_label ) : ?>
                        <option value="<?php echo esc_attr( $opt_key ); ?>" <?php selected( isset( $triggers['woocommerce'] ) ? $triggers['woocommerce'] : '', $opt_key ); ?>><?php echo esc_html( $opt_label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if ( is_multisite() ) : ?>
                <div class="trigger-row">
                    <label class="trigger-label"><?php esc_html_e( '🏢 멀티사이트', 'acf-code-snippets-box' ); ?></label>
                    <select name="acf_csb_triggers[multisite]">
                        <option value=""><?php esc_html_e( '모든 사이트', 'acf-code-snippets-box' ); ?></option>
                        <?php
                        $sites = get_sites();
                        foreach ( $sites as $site ) :
                        ?>
                        <option value="<?php echo esc_attr( $site->blog_id ); ?>" <?php selected( isset( $triggers['multisite'] ) ? $triggers['multisite'] : '', $site->blog_id ); ?>><?php echo esc_html( get_blog_details( $site->blog_id )->blogname ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // 탭 전환
            $('.acf-csb-triggers-v5 .trigger-tab').on('click', function() {
                var tab = $(this).data('tab');
                $('.acf-csb-triggers-v5 .trigger-tab').removeClass('active');
                $(this).addClass('active');
                $('.acf-csb-triggers-v5 .trigger-panel').removeClass('active');
                $('.acf-csb-triggers-v5 .trigger-panel[data-panel="' + tab + '"]').addClass('active');
            });

            // 조건부 필드 표시
            function updateConditionalFields() {
                $('.acf-csb-triggers-v5 .conditional-field').each(function() {
                    var $field = $(this);
                    var showWhen = $field.data('show-when');
                    var showValue = $field.data('show-value');
                    var $trigger = $('[name="acf_csb_triggers[' + showWhen + ']"]');

                    if ($trigger.val() === showValue) {
                        $field.addClass('visible');
                    } else {
                        $field.removeClass('visible');
                    }
                });
            }

            // 초기 상태 설정
            updateConditionalFields();

            // 변경 시 업데이트
            $('select[name^="acf_csb_triggers"]').on('change', updateConditionalFields);

            // 요일 버튼 토글
            $('.weekday-btn').on('click', function() {
                $(this).toggleClass('selected');
            });
        });
        </script>
        <?php
    }

    /**
     * ACF CSS 연동 메타 박스 렌더링
     */
    public function render_acf_css_meta_box( $post ) {
        $use_css_vars = get_post_meta( $post->ID, '_acf_csb_use_css_vars', true );
        ?>
        <p>
            <label>
                <input type="checkbox" name="acf_csb_use_css_vars" value="1" <?php checked( $use_css_vars, '1' ); ?>>
                <?php esc_html_e( 'ACF CSS 변수 사용', 'acf-code-snippets-box' ); ?>
            </label>
        </p>
        <p class="description">
            <?php esc_html_e( 'CSS 코드에서 var(--jj-*) 변수를 자동완성합니다.', 'acf-code-snippets-box' ); ?>
        </p>
        <hr>
        <p><strong><?php esc_html_e( '사용 가능한 변수:', 'acf-code-snippets-box' ); ?></strong></p>
        <ul style="font-size: 12px; margin-left: 15px;">
            <li><code>--jj-primary-color</code></li>
            <li><code>--jj-secondary-color</code></li>
            <li><code>--jj-font-family-primary</code></li>
            <li><code>--jj-font-size-base</code></li>
            <li><?php esc_html_e( '... 더 많은 변수', 'acf-code-snippets-box' ); ?></li>
        </ul>
        <?php
    }

    /**
     * 메타 박스 저장
     * [v5.0.0] 확장된 트리거 및 코드 위치 저장
     */
    public function save_meta_boxes( $post_id, $post ) {
        // Nonce 확인
        if ( ! isset( $_POST['acf_csb_nonce'] ) || ! wp_verify_nonce( $_POST['acf_csb_nonce'], 'acf_csb_save_meta' ) ) {
            return;
        }

        // 권한 확인
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // 자동 저장 방지
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // 코드 저장
        if ( isset( $_POST['acf_csb_code'] ) ) {
            update_post_meta( $post_id, '_acf_csb_code', wp_unslash( $_POST['acf_csb_code'] ) );
        }

        // 코드 타입
        $code_type = 'css';
        if ( isset( $_POST['acf_csb_code_type'] ) ) {
            $code_type = sanitize_text_field( $_POST['acf_csb_code_type'] );
            update_post_meta( $post_id, '_acf_csb_code_type', $code_type );
        }

        // [v5.0.0] 코드 출력 위치 (코드 타입별로 저장)
        $location_key = 'acf_csb_code_location_' . $code_type;
        if ( isset( $_POST[ $location_key ] ) ) {
            update_post_meta( $post_id, '_acf_csb_code_location', sanitize_text_field( $_POST[ $location_key ] ) );
        }

        // [v5.0.0] 커스텀 훅
        if ( isset( $_POST['acf_csb_custom_hook'] ) ) {
            update_post_meta( $post_id, '_acf_csb_custom_hook', sanitize_text_field( $_POST['acf_csb_custom_hook'] ) );
        }

        // 활성화 상태
        update_post_meta( $post_id, '_acf_csb_active', isset( $_POST['acf_csb_active'] ) ? '1' : '0' );

        // 우선순위
        if ( isset( $_POST['acf_csb_priority'] ) ) {
            update_post_meta( $post_id, '_acf_csb_priority', absint( $_POST['acf_csb_priority'] ) );
        }

        // 설명
        if ( isset( $_POST['acf_csb_description'] ) ) {
            update_post_meta( $post_id, '_acf_csb_description', sanitize_textarea_field( $_POST['acf_csb_description'] ) );
        }

        // [v5.0.0] 확장된 트리거 조건 저장
        if ( isset( $_POST['acf_csb_triggers'] ) ) {
            $raw_triggers = wp_unslash( $_POST['acf_csb_triggers'] );
            $triggers = array();

            // 기본 필드들 sanitize
            $text_fields = array(
                'location', 'pages', 'posts', 'device', 'browser', 'logged_in',
                'user_capability', 'time_based', 'date_start', 'date_end',
                'time_start', 'time_end', 'cookie_exists', 'cookie_value',
                'referrer', 'language', 'woocommerce', 'multisite', 'query_string'
            );

            foreach ( $text_fields as $field ) {
                if ( isset( $raw_triggers[ $field ] ) ) {
                    $triggers[ $field ] = sanitize_text_field( $raw_triggers[ $field ] );
                }
            }

            // textarea 필드 (여러 줄)
            $textarea_fields = array( 'url_pattern', 'url_exclude' );
            foreach ( $textarea_fields as $field ) {
                if ( isset( $raw_triggers[ $field ] ) ) {
                    $triggers[ $field ] = sanitize_textarea_field( $raw_triggers[ $field ] );
                }
            }

            // 배열 필드
            if ( isset( $raw_triggers['user_roles'] ) && is_array( $raw_triggers['user_roles'] ) ) {
                $triggers['user_roles'] = array_map( 'sanitize_text_field', $raw_triggers['user_roles'] );
            }

            if ( isset( $raw_triggers['post_types_selected'] ) && is_array( $raw_triggers['post_types_selected'] ) ) {
                $triggers['post_types_selected'] = array_map( 'sanitize_text_field', $raw_triggers['post_types_selected'] );
            }

            if ( isset( $raw_triggers['weekday_select'] ) && is_array( $raw_triggers['weekday_select'] ) ) {
                $triggers['weekday_select'] = array_map( 'sanitize_text_field', $raw_triggers['weekday_select'] );
            }

            update_post_meta( $post_id, '_acf_csb_triggers', $triggers );
        }

        // ACF CSS 연동
        update_post_meta( $post_id, '_acf_csb_use_css_vars', isset( $_POST['acf_csb_use_css_vars'] ) ? '1' : '0' );
    }

    /**
     * 관리자 에셋 로드
     */
    public function enqueue_admin_assets( $hook ) {
        global $post_type;

        if ( $post_type !== self::POST_TYPE ) {
            return;
        }

        // CodeMirror (WordPress 내장)
        $settings = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );

        if ( false !== $settings ) {
            wp_enqueue_script( 'acf-csb-editor', ACF_CSB_URL . 'assets/js/editor.js', array( 'jquery', 'wp-theme-plugin-editor' ), ACF_CSB_VERSION, true );
            wp_localize_script( 'acf-csb-editor', 'acfCsbEditor', array(
                'codeEditorSettings' => $settings,
                'nonce'              => wp_create_nonce( 'acf_csb_nonce' ),
                'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
            ) );
        }

        // 관리자 스타일
        wp_enqueue_style( 'acf-csb-admin', ACF_CSB_URL . 'assets/css/admin.css', array(), ACF_CSB_VERSION );
    }
    
    /**
     * 프리셋 스니펫 목록에 표시
     * [v2.3.4] 활성화되지 않은 프리셋 스니펫도 목록에 표시
     */
    public function display_preset_snippets_in_list( $which ) {
        global $post_type;
        
        if ( $post_type !== self::POST_TYPE || $which !== 'top' ) {
            return;
        }
        
        // 모든 프리셋 가져오기
        $all_presets = ACF_CSB_Presets::get_all_presets();
        $preset_snippets = array();
        
        // 각 프리셋 타입별로 순회
        foreach ( $all_presets as $preset_type => $presets ) {
            foreach ( $presets as $preset_id => $preset ) {
                // 기존 스니펫이 있는지 확인
                $existing = get_posts( array(
                    'post_type'      => self::POST_TYPE,
                    'meta_key'       => '_acf_csb_preset_id',
                    'meta_value'     => $preset_id,
                    'posts_per_page' => 1,
                    'post_status'    => 'any',
                ) );
                
                if ( empty( $existing ) ) {
                    // 스니펫이 없으면 프리셋 정보 저장
                    $preset_snippets[] = array(
                        'id'          => $preset_id,
                        'type'        => $preset_type,
                        'name'        => $preset['name'],
                        'description' => isset( $preset['description'] ) ? $preset['description'] : '',
                        'category'    => isset( $preset['category'] ) ? $preset['category'] : '',
                        'pro_only'    => isset( $preset['pro_only'] ) && $preset['pro_only'],
                    );
                }
            }
        }
        
        if ( ! empty( $preset_snippets ) ) {
            ?>
            <div class="acf-csb-preset-snippets-list" style="margin: 20px 0; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                <h3 style="margin-top: 0;"><?php esc_html_e( '📦 사용 가능한 프리셋 스니펫', 'acf-code-snippets-box' ); ?></h3>
                <p style="color: #666; font-size: 13px;">
                    <?php esc_html_e( '아래 프리셋들은 아직 스니펫으로 추가되지 않았습니다. 클릭하여 추가하세요.', 'acf-code-snippets-box' ); ?>
                </p>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 10px; margin-top: 15px;">
                    <?php foreach ( $preset_snippets as $preset ) : ?>
                        <div class="acf-csb-preset-item" style="background: #fff; padding: 12px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; transition: all 0.2s;" 
                             onclick="acfCsbAddPresetSnippet('<?php echo esc_js( $preset['type'] ); ?>', '<?php echo esc_js( $preset['id'] ); ?>')"
                             onmouseover="this.style.borderColor='#0073aa'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'"
                             onmouseout="this.style.borderColor='#ddd'; this.style.boxShadow='none'">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div style="flex: 1;">
                                    <strong style="display: block; margin-bottom: 5px; color: #1d2327;">
                                        <?php echo esc_html( $preset['name'] ); ?>
                                        <?php if ( $preset['pro_only'] ) : ?>
                                            <span style="background: #ff6b6b; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-left: 5px;">PRO</span>
                                        <?php endif; ?>
                                    </strong>
                                    <span style="font-size: 11px; color: #666; display: block; margin-bottom: 3px;">
                                        <?php echo esc_html( $preset['description'] ); ?>
                                    </span>
                                    <span style="font-size: 10px; color: #999;">
                                        <?php echo esc_html( ucfirst( $preset['type'] ) ); ?> • <?php echo esc_html( $preset['category'] ); ?>
                                    </span>
                                </div>
                                <span style="color: #0073aa; font-size: 18px; margin-left: 10px;">➕</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <script>
            function acfCsbAddPresetSnippet(type, id) {
                if (confirm('<?php echo esc_js( __( '이 프리셋을 스니펫으로 추가하시겠습니까?', 'acf-code-snippets-box' ) ); ?>')) {
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'acf_csb_create_preset_snippet',
                            nonce: '<?php echo wp_create_nonce( "acf_csb_nonce" ); ?>',
                            preset_type: type,
                            preset_id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                window.location.href = '<?php echo esc_url( admin_url( "post.php" ) ); ?>?post=' + response.data.post_id + '&action=edit';
                            } else {
                                alert('오류: ' + (response.data || '<?php echo esc_js( __( "스니펫 생성 실패", "acf-code-snippets-box" ) ); ?>'));
                            }
                        },
                        error: function() {
                            alert('<?php echo esc_js( __( "서버 통신 오류가 발생했습니다.", "acf-code-snippets-box" ) ); ?>');
                        }
                    });
                }
            }
            </script>
            <?php
        }
    }
    
    /**
     * 프리셋 스니펫 뷰 추가
     * [v2.3.4] 목록 페이지 뷰에 프리셋 섹션 추가
     */
    public function add_preset_snippets_view( $views ) {
        // 기본 뷰는 그대로 유지
        return $views;
    }
}
