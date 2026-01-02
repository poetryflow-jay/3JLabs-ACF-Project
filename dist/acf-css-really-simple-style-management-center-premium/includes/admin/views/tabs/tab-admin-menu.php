<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $menu, $submenu;
// 데이터 준비 로직 (Controller에서 넘겨주는 것이 이상적이지만, 리팩토링 단계에서는 View 내에서 처리)
$menu_layout = $this->get_menu_layout(); // Controller 메소드 호출 필요

$menu_items = array();
if ( is_array( $menu ) ) {
    foreach ( $menu as $item ) {
        $menu_title = isset( $item[0] ) ? wp_strip_all_tags( $item[0] ) : '';
        $menu_slug  = isset( $item[2] ) ? sanitize_key( $item[2] ) : '';
        if ( empty( $menu_slug ) ) {
            continue;
        }
        $meta = $menu_layout[ $menu_slug ] ?? array();
        $enabled = isset( $meta['enabled'] ) ? (bool) $meta['enabled'] : true;
        $menu_items[ $menu_slug ] = array(
            'title' => $menu_title,
            'slug' => $menu_slug,
            'enabled' => $enabled,
        );
    }
}

// 메뉴 항목에 순서와 메타데이터 추가
$sorted_menu_items = array();
foreach ( $menu_items as $slug => $item ) {
    $meta = $menu_layout[ $slug ] ?? array();
    $order = isset( $meta['order'] ) ? (int) $meta['order'] : 0;
    $label = isset( $meta['label'] ) && '' !== $meta['label'] ? $meta['label'] : $item['title'];
    $item['order'] = $order;
    $item['label'] = $label;
    $item['meta'] = $meta;
    $sorted_menu_items[ $slug ] = $item;
}

// 순서대로 정렬
uasort( $sorted_menu_items, function( $a, $b ) {
    return (int) $a['order'] <=> (int) $b['order'];
} );

// 순서 번호 재할당 (1부터 시작)
$menu_index = 1;
$reordered_items = array();
foreach ( $sorted_menu_items as $slug => $item ) {
    $item['display_order'] = $menu_index;
    $reordered_items[ $slug ] = $item;
    $menu_index++;
}
?>
<div class="jj-admin-center-tab-content" data-tab="admin-menu">
    <div class="jj-admin-center-2panel">
        <div class="jj-admin-center-left-panel">
            <h3><?php esc_html_e( '메뉴 목록', 'acf-css-really-simple-style-management-center' ); ?></h3>
            <ul class="jj-admin-center-menu-list">
                <?php
                $first_item = true;
                $menu_position = 1;
                foreach ( $reordered_items as $slug => $item ) :
                    $meta = $item['meta'];
                    $enabled = isset( $meta['enabled'] ) ? (bool) $meta['enabled'] : true;
                    $label = $item['label'];
                    $order = $item['display_order'];
                    
                    // 서브메뉴 확인
                    $has_submenu = ! empty( $submenu[ $slug ] ) && is_array( $submenu[ $slug ] );
                    ?>
                    <li class="jj-admin-center-menu-item<?php echo $first_item ? ' active' : ''; ?><?php echo $has_submenu ? ' has-submenu' : ''; ?>" 
                        data-item-id="<?php echo esc_attr( $slug ); ?>"
                        data-order="<?php echo esc_attr( $order ); ?>">
                        <span class="jj-admin-center-menu-item-handle dashicons dashicons-menu" style="cursor: move; color: #8c8f94; margin-right: 5px;"></span>
                        <span class="jj-admin-center-menu-item-order" style="display: inline-block; min-width: 25px; text-align: center; font-weight: bold; margin-right: 8px;"><?php echo esc_html( $order ); ?></span>
                        <span class="jj-admin-center-menu-item-title" data-original-title="<?php echo esc_attr( $item['title'] ); ?>"><?php echo esc_html( $label ); ?></span>
                        <?php if ( $has_submenu ) : ?>
                        <span class="dashicons dashicons-arrow-down jj-toggle-submenu" style="float: right; cursor: pointer; color: #8c8f94;"></span>
                        <?php endif; ?>
                        <span class="jj-admin-center-menu-item-badge"><?php echo $enabled ? esc_html__( '표시', 'acf-css-really-simple-style-management-center' ) : esc_html__( '숨김', 'acf-css-really-simple-style-management-center' ); ?></span>
                        <?php if ( $has_submenu ) : ?>
                        <ul class="jj-admin-center-submenu-list" style="display: none; list-style: none; margin: 10px 0 0 30px; padding: 0;">
                            <?php
                            $submenu_meta = isset( $meta['submenus'] ) ? $meta['submenus'] : array();
                            $submenu_items_array = array();
                            foreach ( $submenu[ $slug ] as $submenu_item ) {
                                $submenu_slug = isset( $submenu_item[2] ) ? sanitize_key( $submenu_item[2] ) : '';
                                if ( empty( $submenu_slug ) ) continue;
                                $submenu_title = isset( $submenu_item[0] ) ? wp_strip_all_tags( $submenu_item[0] ) : '';
                                $submenu_item_meta = isset( $submenu_meta[ $submenu_slug ] ) ? $submenu_meta[ $submenu_slug ] : array();
                                $submenu_order = isset( $submenu_item_meta['order'] ) ? (int) $submenu_item_meta['order'] : 0;
                                $submenu_label = isset( $submenu_item_meta['label'] ) && '' !== $submenu_item_meta['label'] ? $submenu_item_meta['label'] : $submenu_title;
                                $submenu_items_array[ $submenu_slug ] = array(
                                    'title' => $submenu_title,
                                    'slug' => $submenu_slug,
                                    'label' => $submenu_label,
                                    'order' => $submenu_order,
                                    'meta' => $submenu_item_meta,
                                );
                            }
                            uasort( $submenu_items_array, function( $a, $b ) {
                                return (int) $a['order'] <=> (int) $b['order'];
                            } );
                            $submenu_position = 1;
                            foreach ( $submenu_items_array as $submenu_slug => $submenu_item_data ) :
                                $submenu_item_data['display_order'] = $submenu_position;
                                $submenu_enabled = isset( $submenu_item_data['meta']['enabled'] ) ? (bool) $submenu_item_data['meta']['enabled'] : true;
                            ?>
                            <li class="jj-admin-center-submenu-item" 
                                data-parent-id="<?php echo esc_attr( $slug ); ?>"
                                data-item-id="<?php echo esc_attr( $submenu_slug ); ?>"
                                data-order="<?php echo esc_attr( $submenu_position ); ?>">
                                <span class="jj-admin-center-menu-item-handle dashicons dashicons-menu" style="cursor: move; color: #8c8f94; margin-right: 5px;"></span>
                                <span class="jj-admin-center-menu-item-order" style="display: inline-block; min-width: 25px; text-align: center; font-weight: bold; margin-right: 8px; font-size: 11px;"><?php echo esc_html( $submenu_position ); ?></span>
                                <span class="jj-admin-center-menu-item-title" data-original-title="<?php echo esc_attr( $submenu_item_data['title'] ); ?>"><?php echo esc_html( $submenu_item_data['label'] ); ?></span>
                                <span class="jj-admin-center-menu-item-badge" style="font-size: 11px;"><?php echo $submenu_enabled ? esc_html__( '표시', 'acf-css-really-simple-style-management-center' ) : esc_html__( '숨김', 'acf-css-really-simple-style-management-center' ); ?></span>
                            </li>
                            <?php
                                $submenu_position++;
                            endforeach;
                            ?>
                        </ul>
                        <?php endif; ?>
                    </li>
                    <?php
                    $first_item = false;
                    $menu_position++;
                endforeach;
                ?>
            </ul>
        </div>

        <div class="jj-admin-center-right-panel">
            <?php
            $first_item_id = '';
            foreach ( $menu_items as $slug => $item ) {
                $first_item_id = $slug;
                break;
            }
            foreach ( $menu_items as $slug => $item ) :
                $meta = $menu_layout[ $slug ] ?? array();
                $enabled = isset( $meta['enabled'] ) ? (bool) $meta['enabled'] : true;
                $order = isset( $meta['order'] ) ? (int) $meta['order'] : 0;
                $label = isset( $meta['label'] ) && '' !== $meta['label'] ? $meta['label'] : $item['title'];
                
                // [v3.7.0 '신규'] 현재 메뉴 항목의 원본 정보 가져오기
                $original_item = null;
                $original_icon = '';
                $original_capability = '';
                if ( is_array( $menu ) ) {
                    foreach ( $menu as $menu_item ) {
                        if ( isset( $menu_item[2] ) && $menu_item[2] === $slug ) {
                            $original_item = $menu_item;
                            $original_icon = isset( $menu_item[6] ) ? $menu_item[6] : 'dashicons-admin-generic';
                            $original_capability = isset( $menu_item[1] ) ? $menu_item[1] : 'manage_options';
                            break;
                        }
                    }
                }
                
                $icon = isset( $meta['icon'] ) ? $meta['icon'] : $original_icon;
                $capability = isset( $meta['capability'] ) ? $meta['capability'] : $original_capability;
                
                // [v3.7.0 '신규'] 서브메뉴 가져오기
                $submenu_items = array();
                if ( isset( $submenu[ $slug ] ) && is_array( $submenu[ $slug ] ) ) {
                    foreach ( $submenu[ $slug ] as $submenu_item ) {
                        $submenu_slug = isset( $submenu_item[2] ) ? sanitize_key( $submenu_item[2] ) : '';
                        if ( empty( $submenu_slug ) ) {
                            continue;
                        }
                        $submenu_title = isset( $submenu_item[0] ) ? wp_strip_all_tags( $submenu_item[0] ) : '';
                        $submenu_capability = isset( $submenu_item[1] ) ? $submenu_item[1] : '';
                        
                        $submenu_meta = isset( $meta['submenus'][ $submenu_slug ] ) ? $meta['submenus'][ $submenu_slug ] : array();
                        $submenu_enabled = isset( $submenu_meta['enabled'] ) ? (bool) $submenu_meta['enabled'] : true;
                        $submenu_label = isset( $submenu_meta['label'] ) && '' !== $submenu_meta['label'] ? $submenu_meta['label'] : $submenu_title;
                        $submenu_order = isset( $submenu_meta['order'] ) ? (int) $submenu_meta['order'] : 0;
                        $submenu_capability_override = isset( $submenu_meta['capability'] ) ? $submenu_meta['capability'] : $submenu_capability;
                        
                        $submenu_items[ $submenu_slug ] = array(
                            'title' => $submenu_title,
                            'slug' => $submenu_slug,
                            'enabled' => $submenu_enabled,
                            'label' => $submenu_label,
                            'order' => $submenu_order,
                            'capability' => $submenu_capability_override,
                            'original_capability' => $submenu_capability,
                        );
                    }
                }
                ?>
                <div class="jj-admin-center-item-details<?php echo $slug === $first_item_id ? ' active' : ''; ?>" data-item-id="<?php echo esc_attr( $slug ); ?>">
                    <h3><?php echo esc_html( $item['title'] ); ?></h3>
                    <p class="description">
                        <code><?php echo esc_html( $slug ); ?></code>
                    </p>
                    <table class="form-table" role="presentation">
                        <tbody>
                        <tr>
                            <th scope="row"><?php esc_html_e( '표시', 'acf-css-really-simple-style-management-center' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox"
                                           name="jj_admin_menu_layout[<?php echo esc_attr( $slug ); ?>][enabled]"
                                           value="1"
                                        <?php checked( $enabled ); ?> />
                                    <?php esc_html_e( '이 메뉴 항목 표시', 'acf-css-really-simple-style-management-center' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( '순서', 'acf-css-really-simple-style-management-center' ); ?></th>
                            <td>
                                <div class="jj-order-control" style="display: flex; align-items: center; gap: 10px;">
                                    <button type="button" class="button button-small jj-order-up" 
                                            data-item-id="<?php echo esc_attr( $slug ); ?>"
                                            style="padding: 2px 8px; height: 28px; min-width: 28px;">
                                        <span class="dashicons dashicons-arrow-up"></span>
                                    </button>
                                    <input type="number"
                                           name="jj_admin_menu_layout[<?php echo esc_attr( $slug ); ?>][order]"
                                           class="jj-order-input"
                                           data-item-id="<?php echo esc_attr( $slug ); ?>"
                                           value="<?php echo esc_attr( $order ); ?>"
                                           min="1"
                                           style="width:80px; text-align: center;" />
                                    <button type="button" class="button button-small jj-order-down" 
                                            data-item-id="<?php echo esc_attr( $slug ); ?>"
                                            style="padding: 2px 8px; height: 28px; min-width: 28px;">
                                        <span class="dashicons dashicons-arrow-down"></span>
                                    </button>
                                    <span class="jj-order-display" style="margin-left: 10px; font-weight: bold; color: #2271b1;">
                                        <?php esc_html_e( '현재 순서:', 'acf-css-really-simple-style-management-center' ); ?> <span class="jj-order-number"><?php echo esc_html( $order ); ?></span>
                                    </span>
                                </div>
                                <p class="description" style="margin-top: 8px;">
                                    <?php esc_html_e( '순서는 1부터 시작하며, 현재 순서는 왼쪽 메뉴 목록에도 표시됩니다. 드래그 앤 드롭 또는 화살표 버튼으로 조정할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( '레이블', 'acf-css-really-simple-style-management-center' ); ?></th>
                            <td>
                                <input type="text"
                                       name="jj_admin_menu_layout[<?php echo esc_attr( $slug ); ?>][label]"
                                       class="regular-text jj-menu-label-input"
                                       data-item-id="<?php echo esc_attr( $slug ); ?>"
                                       value="<?php echo esc_attr( $label ); ?>" />
                                <p class="description"><?php esc_html_e( '비워두면 기본 레이블이 사용됩니다. 변경 시 왼쪽 메뉴 목록에 즉시 반영됩니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( '아이콘', 'acf-css-really-simple-style-management-center' ); ?></th>
                            <td>
                                <input type="text"
                                       name="jj_admin_menu_layout[<?php echo esc_attr( $slug ); ?>][icon]"
                                       value="<?php echo esc_attr( $icon ); ?>"
                                       class="regular-text jj-menu-icon-input"
                                       placeholder="dashicons-admin-generic" />
                                <p class="description">
                                    <?php esc_html_e( 'Dashicons 클래스 이름 (예: dashicons-admin-generic) 또는 이미지 URL을 입력하세요.', 'acf-css-really-simple-style-management-center' ); ?>
                                    <br>
                                    <?php esc_html_e( '기본값:', 'acf-css-really-simple-style-management-center' ); ?> <code><?php echo esc_html( $original_icon ); ?></code>
                                    <a href="https://developer.wordpress.org/resource/dashicons/" target="_blank" rel="noopener"><?php esc_html_e( 'Dashicons 목록 보기', 'acf-css-really-simple-style-management-center' ); ?></a>
                                </p>
                                <div class="jj-menu-icon-preview" style="margin-top: 8px; padding: 8px; background: #f9f9f9; border: 1px solid #ddd; display: inline-block; min-width: 40px; min-height: 40px; text-align: center; vertical-align: middle;">
                                    <?php
                                    if ( ! empty( $icon ) ) {
                                        if ( strpos( $icon, 'dashicons-' ) === 0 || strpos( $icon, 'dashicons ' ) === 0 ) {
                                            $icon_class = strpos( $icon, 'dashicons-' ) === 0 ? $icon : 'dashicons-' . str_replace( 'dashicons ', '', $icon );
                                            echo '<span class="dashicons ' . esc_attr( $icon_class ) . '" style="font-size: 24px; width: 24px; height: 24px;"></span>';
                                        } elseif ( filter_var( $icon, FILTER_VALIDATE_URL ) ) {
                                            echo '<img src="' . esc_url( $icon ) . '" style="max-width: 24px; max-height: 24px;" alt="Icon" />';
                                        } else {
                                            echo '<span class="dashicons dashicons-admin-generic" style="font-size: 24px; width: 24px; height: 24px;"></span>';
                                        }
                                    } else {
                                        echo '<span class="dashicons dashicons-admin-generic" style="font-size: 24px; width: 24px; height: 24px;"></span>';
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( '권한', 'acf-css-really-simple-style-management-center' ); ?></th>
                            <td>
                                <select name="jj_admin_menu_layout[<?php echo esc_attr( $slug ); ?>][capability]"
                                        class="regular-text">
                                    <option value=""><?php esc_html_e( '-- 기본값 사용 --', 'acf-css-really-simple-style-management-center' ); ?></option>
                                    <option value="manage_options" <?php selected( $capability, 'manage_options' ); ?>><?php esc_html_e( 'manage_options (관리자)', 'acf-css-really-simple-style-management-center' ); ?></option>
                                    <option value="edit_posts" <?php selected( $capability, 'edit_posts' ); ?>><?php esc_html_e( 'edit_posts (편집자 이상)', 'acf-css-really-simple-style-management-center' ); ?></option>
                                    <option value="publish_posts" <?php selected( $capability, 'publish_posts' ); ?>><?php esc_html_e( 'publish_posts (작성자 이상)', 'acf-css-really-simple-style-management-center' ); ?></option>
                                    <option value="edit_published_posts" <?php selected( $capability, 'edit_published_posts' ); ?>><?php esc_html_e( 'edit_published_posts (작성자 이상)', 'acf-css-really-simple-style-management-center' ); ?></option>
                                    <option value="read" <?php selected( $capability, 'read' ); ?>><?php esc_html_e( 'read (구독자 이상)', 'acf-css-really-simple-style-management-center' ); ?></option>
                                    <option value="manage_network" <?php selected( $capability, 'manage_network' ); ?>><?php esc_html_e( 'manage_network (네트워크 관리자)', 'acf-css-really-simple-style-management-center' ); ?></option>
                                </select>
                                <p class="description">
                                    <?php esc_html_e( '이 메뉴를 볼 수 있는 최소 권한을 설정합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                                    <br>
                                    <?php esc_html_e( '기본값:', 'acf-css-really-simple-style-management-center' ); ?> <code><?php echo esc_html( $original_capability ); ?></code>
                                </p>
                            </td>
                        </tr>
                        <?php if ( ! empty( $submenu_items ) ) : ?>
                        <tr>
                            <th scope="row" colspan="2">
                                <h4 style="margin-top: 20px; margin-bottom: 10px;"><?php esc_html_e( '서브메뉴', 'acf-css-really-simple-style-management-center' ); ?></h4>
                                <p class="description" style="margin-bottom: 15px;">
                                    <?php esc_html_e( '이 메뉴 항목의 서브메뉴를 편집할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                                </p>
                            </th>
                        </tr>
                        <?php
                        // 서브메뉴 정렬 (order 기준)
                        uasort( $submenu_items, function( $a, $b ) {
                            return (int) $a['order'] <=> (int) $b['order'];
                        } );
                        foreach ( $submenu_items as $submenu_slug => $submenu_item ) :
                        ?>
                        <tr>
                            <td colspan="2" style="padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px;">
                                <h5 style="margin-top: 0; margin-bottom: 10px;">
                                    <?php echo esc_html( $submenu_item['title'] ); ?>
                                    <code style="font-size: 11px; margin-left: 5px;"><?php echo esc_html( $submenu_slug ); ?></code>
                                </h5>
                                <table class="form-table" style="margin: 0;">
                                    <tbody>
                                    <tr>
                                        <th scope="row" style="width: 120px;"><?php esc_html_e( '표시', 'acf-css-really-simple-style-management-center' ); ?></th>
                                        <td>
                                            <label>
                                                <input type="checkbox"
                                                       name="jj_admin_menu_layout[<?php echo esc_attr( $slug ); ?>][submenus][<?php echo esc_attr( $submenu_slug ); ?>][enabled]"
                                                       value="1"
                                                    <?php checked( $submenu_item['enabled'] ); ?> />
                                                <?php esc_html_e( '이 서브메뉴 표시', 'acf-css-really-simple-style-management-center' ); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e( '레이블', 'acf-css-really-simple-style-management-center' ); ?></th>
                                        <td>
                                            <input type="text"
                                                   name="jj_admin_menu_layout[<?php echo esc_attr( $slug ); ?>][submenus][<?php echo esc_attr( $submenu_slug ); ?>][label]"
                                                   class="regular-text jj-submenu-label-input"
                                                   data-parent-id="<?php echo esc_attr( $slug ); ?>"
                                                   data-item-id="<?php echo esc_attr( $submenu_slug ); ?>"
                                                   value="<?php echo esc_attr( $submenu_item['label'] ); ?>" />
                                            <p class="description" style="margin-top: 5px; font-size: 12px;">
                                                <?php esc_html_e( '변경 시 왼쪽 메뉴 목록에 즉시 반영됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e( '순서', 'acf-css-really-simple-style-management-center' ); ?></th>
                                        <td>
                                            <div class="jj-order-control" style="display: flex; align-items: center; gap: 10px;">
                                                <button type="button" class="button button-small jj-submenu-order-up" 
                                                        data-parent-id="<?php echo esc_attr( $slug ); ?>"
                                                        data-item-id="<?php echo esc_attr( $submenu_slug ); ?>"
                                                        style="padding: 2px 8px; height: 28px; min-width: 28px;">
                                                    <span class="dashicons dashicons-arrow-up"></span>
                                                </button>
                                                <input type="number"
                                                       name="jj_admin_menu_layout[<?php echo esc_attr( $slug ); ?>][submenus][<?php echo esc_attr( $submenu_slug ); ?>][order]"
                                                       class="jj-submenu-order-input"
                                                       data-parent-id="<?php echo esc_attr( $slug ); ?>"
                                                       data-item-id="<?php echo esc_attr( $submenu_slug ); ?>"
                                                       value="<?php echo esc_attr( $submenu_item['order'] ); ?>"
                                                       min="1"
                                                       style="width:80px; text-align: center;" />
                                                <button type="button" class="button button-small jj-submenu-order-down" 
                                                        data-parent-id="<?php echo esc_attr( $slug ); ?>"
                                                        data-item-id="<?php echo esc_attr( $submenu_slug ); ?>"
                                                        style="padding: 2px 8px; height: 28px; min-width: 28px;">
                                                    <span class="dashicons dashicons-arrow-down"></span>
                                                </button>
                                                <span class="jj-order-display" style="margin-left: 10px; font-weight: bold; color: #2271b1; font-size: 12px;">
                                                    <?php esc_html_e( '현재 순서:', 'acf-css-really-simple-style-management-center' ); ?> <span class="jj-order-number"><?php echo esc_html( $submenu_item['order'] ); ?></span>
                                                </span>
                                            </div>
                                            <p class="description" style="margin-top: 8px; font-size: 12px;">
                                                <?php esc_html_e( '순서는 1부터 시작하며, 왼쪽 메뉴 목록에도 표시됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e( '권한', 'acf-css-really-simple-style-management-center' ); ?></th>
                                        <td>
                                            <select name="jj_admin_menu_layout[<?php echo esc_attr( $slug ); ?>][submenus][<?php echo esc_attr( $submenu_slug ); ?>][capability]"
                                                    class="regular-text">
                                                <option value=""><?php esc_html_e( '-- 기본값 사용 --', 'acf-css-really-simple-style-management-center' ); ?></option>
                                                <option value="manage_options" <?php selected( $submenu_item['capability'], 'manage_options' ); ?>><?php esc_html_e( 'manage_options', 'acf-css-really-simple-style-management-center' ); ?></option>
                                                <option value="edit_posts" <?php selected( $submenu_item['capability'], 'edit_posts' ); ?>><?php esc_html_e( 'edit_posts', 'acf-css-really-simple-style-management-center' ); ?></option>
                                                <option value="publish_posts" <?php selected( $submenu_item['capability'], 'publish_posts' ); ?>><?php esc_html_e( 'publish_posts', 'acf-css-really-simple-style-management-center' ); ?></option>
                                                <option value="read" <?php selected( $submenu_item['capability'], 'read' ); ?>><?php esc_html_e( 'read', 'acf-css-really-simple-style-management-center' ); ?></option>
                                            </select>
                                            <p class="description" style="margin-top: 5px;">
                                                <?php esc_html_e( '기본값:', 'acf-css-really-simple-style-management-center' ); ?> <code><?php echo esc_html( $submenu_item['original_capability'] ); ?></code>
                                            </p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

