<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Style Guide Page Duplicator
 *
 * Pages 목록에 "Duplicate to Style Guide by ACF CSS" 액션(행/벌크)을 추가하여,
 * 선택한 페이지를 Style Guide 샌드박스 페이지로 복제합니다.
 *
 * - 샌드박스 페이지는 기본적으로 private 처리(관리자/편집자만 접근)하여 사이트 외부 노출을 방지합니다.
 * - 블록(구텐베르크) 콘텐츠는 post_content 복제로 충분하지만,
 *   Elementor/Bricks 등 빌더의 경우 렌더링에 필요한 post meta가 존재하므로 관련 meta도 함께 복제합니다.
 *
 * @since 10.6.0 (WIP)
 */
final class JJ_Style_Guide_Page_Duplicator {

    private static $instance = null;

    private $sandbox_option_key = 'jj_style_guide_sandbox_page_id';
    private $row_action         = 'jj_duplicate_to_style_guide';
    private $bulk_action        = 'jj_duplicate_to_style_guide_bulk';

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function init() {
        // Row action
        add_filter( 'page_row_actions', array( $this, 'add_row_action' ), 10, 2 );
        add_action( 'admin_action_' . $this->row_action, array( $this, 'handle_row_action' ) );

        // Bulk action
        add_filter( 'bulk_actions-edit-page', array( $this, 'add_bulk_action' ) );
        add_filter( 'handle_bulk_actions-edit-page', array( $this, 'handle_bulk_action' ), 10, 3 );

        // Notice
        add_action( 'admin_notices', array( $this, 'maybe_notice' ) );
    }

    public function add_row_action( $actions, $post ) {
        if ( ! $post || 'page' !== ( $post->post_type ?? '' ) ) {
            return $actions;
        }
        if ( ! current_user_can( 'manage_options' ) ) {
            return $actions;
        }

        $url = add_query_arg(
            array(
                'action' => $this->row_action,
                'post'   => (int) $post->ID,
                '_wpnonce' => wp_create_nonce( $this->row_action . ':' . (int) $post->ID ),
            ),
            admin_url( 'admin.php' )
        );

        $actions[ $this->row_action ] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Duplicate to Style Guide by ACF CSS', 'acf-css-really-simple-style-management-center' ) . '</a>';
        return $actions;
    }

    public function handle_row_action() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $source_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
        check_admin_referer( $this->row_action . ':' . $source_id );

        $sandbox_id = $this->duplicate_to_sandbox( $source_id );

        $redirect = wp_get_referer();
        if ( ! $redirect ) {
            $redirect = admin_url( 'edit.php?post_type=page' );
        }
        $redirect = add_query_arg(
            array(
                'jj_sg_duplicated' => $sandbox_id ? 1 : 0,
                'jj_sg_source'     => $source_id,
                'jj_sg_sandbox'    => $sandbox_id,
            ),
            $redirect
        );
        wp_safe_redirect( $redirect );
        exit;
    }

    public function add_bulk_action( $bulk_actions ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return $bulk_actions;
        }
        $bulk_actions[ $this->bulk_action ] = __( 'Duplicate to Style Guide by ACF CSS', 'acf-css-really-simple-style-management-center' );
        return $bulk_actions;
    }

    public function handle_bulk_action( $redirect_to, $doaction, $post_ids ) {
        if ( $doaction !== $this->bulk_action ) {
            return $redirect_to;
        }
        if ( ! current_user_can( 'manage_options' ) ) {
            return add_query_arg( array( 'jj_sg_duplicated' => 0 ), $redirect_to );
        }

        $post_ids = is_array( $post_ids ) ? array_map( 'intval', $post_ids ) : array();
        $post_ids = array_values( array_filter( $post_ids ) );
        if ( empty( $post_ids ) ) {
            return add_query_arg( array( 'jj_sg_duplicated' => 0 ), $redirect_to );
        }

        // Bulk는 “마지막 선택”을 샌드박스로 사용 (필요 시 확장 가능)
        $source_id  = end( $post_ids );
        $sandbox_id = $this->duplicate_to_sandbox( (int) $source_id );

        return add_query_arg(
            array(
                'jj_sg_duplicated' => $sandbox_id ? 1 : 0,
                'jj_sg_source'     => (int) $source_id,
                'jj_sg_sandbox'    => (int) $sandbox_id,
            ),
            $redirect_to
        );
    }

    public function maybe_notice() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        if ( ! isset( $_GET['jj_sg_duplicated'] ) ) {
            return;
        }
        $ok = (int) $_GET['jj_sg_duplicated'] === 1;
        if ( $ok ) {
            $sandbox_id = isset( $_GET['jj_sg_sandbox'] ) ? (int) $_GET['jj_sg_sandbox'] : 0;
            echo '<div class="notice notice-success is-dismissible"><p>' .
                esc_html__( 'Style Guide 샌드박스에 페이지가 복제되었습니다.', 'acf-css-really-simple-style-management-center' ) .
                ( $sandbox_id ? ' <a href="' . esc_url( get_permalink( $sandbox_id ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( '샌드박스 열기', 'acf-css-really-simple-style-management-center' ) . '</a>' : '' ) .
            '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( '페이지 복제에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) . '</p></div>';
        }
    }

    public function get_sandbox_page_id() {
        $id = (int) get_option( $this->sandbox_option_key, 0 );
        if ( $id && get_post( $id ) && 'page' === get_post_type( $id ) && 'trash' !== get_post_status( $id ) ) {
            return $id;
        }
        return 0;
    }

    private function duplicate_to_sandbox( $source_id ) {
        $source = get_post( $source_id );
        if ( ! $source || 'page' !== $source->post_type ) {
            return 0;
        }

        $sandbox_id = $this->get_sandbox_page_id();
        $title = sprintf(
            /* translators: %s: source page title */
            __( 'Style Guide Sandbox — %s', 'acf-css-really-simple-style-management-center' ),
            $source->post_title
        );

        $post_data = array(
            'post_title'      => $title,
            'post_name'       => 'jj-style-guide-sandbox',
            'post_content'    => $source->post_content,
            'post_excerpt'    => $source->post_excerpt,
            'post_status'     => 'private',
            'post_type'       => 'page',
            'comment_status'  => 'closed',
            'ping_status'     => 'closed',
        );

        if ( $sandbox_id ) {
            $post_data['ID'] = $sandbox_id;
            $sandbox_id = wp_update_post( wp_slash( $post_data ), true );
        } else {
            $sandbox_id = wp_insert_post( wp_slash( $post_data ), true );
        }

        if ( ! $sandbox_id || is_wp_error( $sandbox_id ) ) {
            return 0;
        }

        update_option( $this->sandbox_option_key, (int) $sandbox_id );

        // Source 추적용 메타
        update_post_meta( $sandbox_id, '_jj_style_guide_sandbox_source_post', (int) $source_id );
        update_post_meta( $sandbox_id, '_jj_style_guide_sandbox_updated_at', current_time( 'mysql' ) );

        // 빌더/템플릿 관련 메타 복제
        $this->copy_builder_meta( $source_id, $sandbox_id );

        return (int) $sandbox_id;
    }

    private function copy_builder_meta( $source_id, $sandbox_id ) {
        $all_meta = get_post_meta( $source_id );
        if ( ! is_array( $all_meta ) ) {
            return;
        }

        $exclude = array(
            '_edit_lock',
            '_edit_last',
            '_wp_old_slug',
            '_wp_old_date',
            '_wp_trash_meta_status',
            '_wp_trash_meta_time',
            '_wp_desired_post_slug',
        );

        $whitelist_exact = array(
            '_wp_page_template',
            '_thumbnail_id',
        );

        foreach ( $all_meta as $key => $values ) {
            if ( in_array( $key, $exclude, true ) ) {
                continue;
            }

            $is_whitelisted = false;
            if ( in_array( $key, $whitelist_exact, true ) ) {
                $is_whitelisted = true;
            }

            // Elementor / Bricks 관련 메타는 prefix로 복제
            if ( 0 === strpos( $key, '_elementor' ) ) {
                $is_whitelisted = true;
            }
            if ( 0 === strpos( $key, '_bricks' ) || 0 === strpos( $key, 'bricks_' ) ) {
                $is_whitelisted = true;
            }

            if ( ! $is_whitelisted ) {
                continue;
            }

            // 기존 값 제거 후 복제
            delete_post_meta( $sandbox_id, $key );
            if ( is_array( $values ) ) {
                foreach ( $values as $v ) {
                    add_post_meta( $sandbox_id, $key, maybe_unserialize( $v ) );
                }
            }
        }
    }
}

