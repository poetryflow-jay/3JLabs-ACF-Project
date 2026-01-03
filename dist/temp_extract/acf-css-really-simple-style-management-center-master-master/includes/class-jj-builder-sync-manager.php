<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Builder Sync Manager
 *
 * [Phase 10.4]
 * - 페이지 빌더(특히 Elementor)의 글로벌 컬러/타이포 설정을 JJ 토큰으로 "실제" 동기화(1-click)
 * - 동기화 전 자동 백업 + 롤백 지원
 *
 * 주의:
 * - 빌더별 내부 저장 구조가 버전별로 다를 수 있으므로, 본 클래스는 "최소 침습 + 복구 가능"을 최우선으로 설계합니다.
 *
 * @since 10.4.0
 */
final class JJ_Builder_Sync_Manager {

    private static $instance = null;

    /** @var string */
    private $backups_key  = 'jj_style_guide_builder_sync_backups';

    /** @var int */
    private $max_backups = 20;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function init() {
        // AJAX endpoints
        add_action( 'wp_ajax_jj_builder_sync_run', array( $this, 'ajax_run_sync' ) );
        add_action( 'wp_ajax_jj_builder_sync_list_backups', array( $this, 'ajax_list_backups' ) );
        add_action( 'wp_ajax_jj_builder_sync_rollback', array( $this, 'ajax_rollback' ) );
    }

    /**
     * ===== AJAX =====
     */
    public function ajax_run_sync() {
        $this->verify_ajax();

        $builders = isset( $_POST['builders'] ) ? (array) wp_unslash( $_POST['builders'] ) : array();
        $scope    = isset( $_POST['scope'] ) ? (array) wp_unslash( $_POST['scope'] ) : array();
        $mapping  = isset( $_POST['mapping'] ) ? (array) wp_unslash( $_POST['mapping'] ) : array();
        $dry_run  = isset( $_POST['dry_run'] ) && in_array( (string) wp_unslash( $_POST['dry_run'] ), array( '1', 'true', 'yes' ), true );

        $builders = array_values( array_filter( array_map( 'sanitize_key', $builders ) ) );
        $do_colors = ! empty( $scope['colors'] );
        $do_typo   = ! empty( $scope['typography'] );

        if ( empty( $builders ) ) {
            wp_send_json_error( array( 'message' => __( '동기화할 빌더를 선택하세요.', 'acf-css-really-simple-style-management-center' ) ), 400 );
        }
        if ( ! $do_colors && ! $do_typo ) {
            wp_send_json_error( array( 'message' => __( '동기화 범위(컬러/타이포)를 선택하세요.', 'acf-css-really-simple-style-management-center' ) ), 400 );
        }

        $results = array();
        foreach ( $builders as $builder ) {
            switch ( $builder ) {
                case 'elementor':
                    $results[ $builder ] = $this->sync_elementor( array(
                        'colors'     => $do_colors,
                        'typography' => $do_typo,
                    ), isset( $mapping['elementor'] ) && is_array( $mapping['elementor'] ) ? $mapping['elementor'] : array(), $dry_run );
                    break;
                default:
                    $results[ $builder ] = array(
                        'status'  => 'unsupported',
                        'message' => __( '이 빌더는 현재 버전에서 “DB 동기화”를 지원하지 않습니다. (안전형 토큰 브릿지/alias는 계속 적용됩니다)', 'acf-css-really-simple-style-management-center' ),
                    );
                    break;
            }
        }

        wp_send_json_success( array(
            'results' => $results,
        ) );
    }

    public function ajax_list_backups() {
        $this->verify_ajax();

        $backups = $this->get_backups();
        $items = array();

        foreach ( $backups as $id => $b ) {
            $items[] = array(
                'id'        => $id,
                'builder'   => $b['builder'] ?? '',
                'scope'     => $b['scope'] ?? array(),
                'timestamp' => (int) ( $b['timestamp'] ?? 0 ),
                'date'      => $b['date'] ?? '',
                'label'     => $b['label'] ?? '',
                'user_name' => $b['user_name'] ?? '',
            );
        }

        wp_send_json_success( array(
            'backups' => $items,
        ) );
    }

    public function ajax_rollback() {
        $this->verify_ajax();

        $backup_id = isset( $_POST['backup_id'] ) ? sanitize_text_field( wp_unslash( $_POST['backup_id'] ) ) : '';
        if ( ! $backup_id ) {
            wp_send_json_error( array( 'message' => __( 'backup_id가 필요합니다.', 'acf-css-really-simple-style-management-center' ) ), 400 );
        }

        $backups = $this->get_backups();
        if ( empty( $backups[ $backup_id ] ) || ! is_array( $backups[ $backup_id ] ) ) {
            wp_send_json_error( array( 'message' => __( '백업을 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 404 );
        }

        $backup = $backups[ $backup_id ];
        $items  = isset( $backup['items'] ) && is_array( $backup['items'] ) ? $backup['items'] : array();
        if ( empty( $items ) ) {
            wp_send_json_error( array( 'message' => __( '이 백업에는 복원할 항목이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 400 );
        }

        // 롤백 전 현재 상태 백업(안전)
        $this->create_backup( array(
            'builder' => $backup['builder'] ?? 'unknown',
            'scope'   => $backup['scope'] ?? array(),
            'label'   => __( '롤백 전 자동 백업', 'acf-css-really-simple-style-management-center' ),
            'items'   => $this->capture_backup_items_from_backup( $backup ),
        ) );

        $restored = 0;
        foreach ( $items as $item ) {
            if ( ! is_array( $item ) || empty( $item['type'] ) ) {
                continue;
            }
            $type = (string) $item['type'];

            if ( 'option' === $type ) {
                $name  = isset( $item['name'] ) ? (string) $item['name'] : '';
                $value = $item['value'] ?? null;
                if ( $name && function_exists( 'update_option' ) ) {
                    update_option( $name, $value );
                    $restored++;
                }
            } elseif ( 'post_meta' === $type ) {
                $post_id = isset( $item['post_id'] ) ? (int) $item['post_id'] : 0;
                $meta_key = isset( $item['meta_key'] ) ? (string) $item['meta_key'] : '';
                $value   = $item['value'] ?? null;
                if ( $post_id > 0 && $meta_key && function_exists( 'update_post_meta' ) ) {
                    update_post_meta( $post_id, $meta_key, $value );
                    $restored++;
                }
            }
        }

        $this->maybe_clear_elementor_cache();
        $this->flush_jj_css_cache();

        $this->add_audit_log( array(
            'action'    => 'rollback',
            'builder'   => $backup['builder'] ?? 'unknown',
            'scope'     => $backup['scope'] ?? array(),
            'dry_run'   => false,
            'backup_id' => $backup_id,
            'restored'  => $restored,
            'message'   => 'success',
        ) );

        wp_send_json_success( array(
            'message'  => __( '롤백이 완료되었습니다.', 'acf-css-really-simple-style-management-center' ),
            'restored' => $restored,
        ) );
    }

    /**
     * ===== Elementor sync =====
     */
    private function sync_elementor( $scope, $mapping, $dry_run = false ) {
        if ( ! $this->is_elementor_active() ) {
            return array(
                'status'  => 'skipped',
                'message' => __( 'Elementor가 감지되지 않아 동기화를 건너뜁니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }

        $kit_id = $this->get_elementor_active_kit_id();
        if ( $kit_id <= 0 ) {
            return array(
                'status'  => 'error',
                'message' => __( 'Elementor 활성 Kit ID를 찾지 못했습니다.', 'acf-css-really-simple-style-management-center' ),
            );
        }

        $meta_key = '_elementor_page_settings';
        $before_raw = function_exists( 'get_post_meta' ) ? get_post_meta( $kit_id, $meta_key, true ) : array();
        $before = function_exists( 'maybe_unserialize' ) ? maybe_unserialize( $before_raw ) : $before_raw;
        if ( '' === $before || null === $before ) {
            $before = array();
        }
        if ( ! is_array( $before ) ) {
            return array(
                'status'  => 'error',
                'message' => __( 'Elementor Kit 메타 구조가 예상과 달라 동기화를 중단했습니다. (_elementor_page_settings가 배열이 아닙니다)', 'acf-css-really-simple-style-management-center' ),
                'kit_id'  => $kit_id,
            );
        }
        if ( isset( $before['system_colors'] ) && ! is_array( $before['system_colors'] ) ) {
            return array(
                'status'  => 'error',
                'message' => __( 'Elementor Kit 메타 구조가 예상과 달라 동기화를 중단했습니다. (system_colors가 배열이 아닙니다)', 'acf-css-really-simple-style-management-center' ),
                'kit_id'  => $kit_id,
            );
        }
        if ( isset( $before['system_typography'] ) && ! is_array( $before['system_typography'] ) ) {
            return array(
                'status'  => 'error',
                'message' => __( 'Elementor Kit 메타 구조가 예상과 달라 동기화를 중단했습니다. (system_typography가 배열이 아닙니다)', 'acf-css-really-simple-style-management-center' ),
                'kit_id'  => $kit_id,
            );
        }

        $settings = $before;

        $tokens = $this->get_jj_tokens();
        $changed_scope = array(
            'colors'     => ! empty( $scope['colors'] ),
            'typography' => ! empty( $scope['typography'] ),
        );

        if ( ! empty( $scope['colors'] ) ) {
            $settings = $this->elementor_apply_global_colors( $settings, $tokens );
        }

        if ( ! empty( $scope['typography'] ) ) {
            $settings = $this->elementor_apply_global_typography( $settings, $tokens, $mapping );
        }

        $diff = $this->elementor_build_diff( $before, $settings, $changed_scope, $mapping );
        $total_changes = isset( $diff['counts']['total'] ) ? (int) $diff['counts']['total'] : 0;

        // Dry-run: 미리보기만 제공 (DB 변경/백업 없음)
        if ( $dry_run ) {
            $this->add_audit_log( array(
                'action'   => 'preview',
                'builder'  => 'elementor',
                'kit_id'   => $kit_id,
                'scope'    => $changed_scope,
                'dry_run'  => true,
                'changes'  => $diff['counts'] ?? array(),
                'message'  => ( $total_changes > 0 ) ? 'preview' : 'noop',
            ) );

            return array(
                'status'   => ( $total_changes > 0 ) ? 'preview' : 'noop',
                'message'  => ( $total_changes > 0 )
                    ? __( '미리보기가 완료되었습니다. 변경사항을 확인한 후 실행하세요.', 'acf-css-really-simple-style-management-center' )
                    : __( '변경 사항이 없어 미리보기에서 적용할 항목이 없습니다.', 'acf-css-really-simple-style-management-center' ),
                'kit_id'   => $kit_id,
                'diff'     => $diff,
            );
        }

        // 변경 사항이 없으면 업데이트/백업 생략
        if ( $total_changes <= 0 ) {
            $this->add_audit_log( array(
                'action'   => 'sync',
                'builder'  => 'elementor',
                'kit_id'   => $kit_id,
                'scope'    => $changed_scope,
                'dry_run'  => false,
                'changes'  => $diff['counts'] ?? array(),
                'message'  => 'noop',
            ) );
            return array(
                'status'   => 'noop',
                'message'  => __( '변경 사항이 없어 Elementor 설정을 업데이트하지 않았습니다.', 'acf-css-really-simple-style-management-center' ),
                'kit_id'   => $kit_id,
                'diff'     => $diff,
            );
        }

        // 백업 생성(필수)
        $backup_id = $this->create_backup( array(
            'builder' => 'elementor',
            'scope'   => $changed_scope,
            'label'   => __( 'Elementor 동기화 전 자동 백업', 'acf-css-really-simple-style-management-center' ),
            'items'   => array(
                array(
                    'type'     => 'post_meta',
                    'post_id'  => $kit_id,
                    'meta_key' => $meta_key,
                    'value'    => $before,
                ),
            ),
        ) );

        // 저장
        if ( function_exists( 'update_post_meta' ) ) {
            update_post_meta( $kit_id, $meta_key, $settings );
        }

        $this->maybe_clear_elementor_cache();
        $this->flush_jj_css_cache();

        $this->add_audit_log( array(
            'action'    => 'sync',
            'builder'   => 'elementor',
            'kit_id'    => $kit_id,
            'scope'     => $changed_scope,
            'dry_run'   => false,
            'backup_id' => $backup_id,
            'changes'   => $diff['counts'] ?? array(),
            'message'   => 'success',
        ) );

        return array(
            'status'    => 'success',
            'message'   => __( 'Elementor 글로벌 설정 동기화가 완료되었습니다.', 'acf-css-really-simple-style-management-center' ),
            'kit_id'    => $kit_id,
            'backup_id' => $backup_id,
            'diff'      => $diff,
        );
    }

    private function elementor_apply_global_colors( $settings, $tokens ) {
        $primary   = $tokens['colors']['primary'] ?? '';
        $secondary = $tokens['colors']['secondary'] ?? '';
        $text      = $tokens['colors']['text'] ?? '';
        $accent    = $tokens['colors']['accent'] ?? '';

        $system_colors = array();
        if ( isset( $settings['system_colors'] ) && is_array( $settings['system_colors'] ) ) {
            $system_colors = $settings['system_colors'];
        }

        $system_colors = $this->upsert_elementor_system_item( $system_colors, 'primary', __( 'Primary', 'acf-css-really-simple-style-management-center' ), $primary, 'color' );
        $system_colors = $this->upsert_elementor_system_item( $system_colors, 'secondary', __( 'Secondary', 'acf-css-really-simple-style-management-center' ), $secondary, 'color' );
        $system_colors = $this->upsert_elementor_system_item( $system_colors, 'text', __( 'Text', 'acf-css-really-simple-style-management-center' ), $text, 'color' );
        $system_colors = $this->upsert_elementor_system_item( $system_colors, 'accent', __( 'Accent', 'acf-css-really-simple-style-management-center' ), $accent, 'color' );

        $settings['system_colors'] = $system_colors;

        return $settings;
    }

    private function elementor_apply_global_typography( $settings, $tokens, $mapping ) {
        $typo = isset( $tokens['typography'] ) && is_array( $tokens['typography'] ) ? $tokens['typography'] : array();

        // 기본 매핑(보수적으로)
        $map = $this->elementor_resolve_typography_map( $mapping );

        $system_typography = array();
        if ( isset( $settings['system_typography'] ) && is_array( $settings['system_typography'] ) ) {
            $system_typography = $settings['system_typography'];
        }

        foreach ( $map as $slot => $jj_tag ) {
            $props = isset( $typo[ $jj_tag ] ) && is_array( $typo[ $jj_tag ] ) ? $typo[ $jj_tag ] : array();
            $sys_item = array(
                // Elementor는 보통 _id로 매칭
                '_id'  => $slot,
                'title' => ucfirst( $slot ),
            );

            $family = isset( $props['font_family'] ) ? $this->sanitize_font_family( $props['font_family'] ) : '';
            $weight = isset( $props['font_weight'] ) ? preg_replace( '/[^0-9a-zA-Z]/', '', (string) $props['font_weight'] ) : '';
            $style  = isset( $props['font_style'] ) ? preg_replace( '/[^a-zA-Z]/', '', (string) $props['font_style'] ) : '';
            $transform = isset( $props['text_transform'] ) ? preg_replace( '/[^a-zA-Z-]/', '', (string) $props['text_transform'] ) : '';

            $size = null;
            if ( isset( $props['font_size']['desktop'] ) && is_numeric( $props['font_size']['desktop'] ) ) {
                $size = (float) $props['font_size']['desktop'];
            } elseif ( isset( $props['font_size'] ) && is_numeric( $props['font_size'] ) ) {
                $size = (float) $props['font_size'];
            }

            $line_height = null;
            if ( isset( $props['line_height'] ) && $props['line_height'] !== '' && is_numeric( $props['line_height'] ) ) {
                $line_height = (float) $props['line_height'];
            }

            $letter_spacing = null;
            if ( isset( $props['letter_spacing'] ) && $props['letter_spacing'] !== '' && is_numeric( $props['letter_spacing'] ) ) {
                $letter_spacing = (float) $props['letter_spacing'];
            }

            // Elementor key convention(일반적인 kit 저장 형태)
            $sys_item['typography_typography']    = 'custom';
            if ( $family ) $sys_item['typography_font_family'] = $family;
            if ( $weight ) $sys_item['typography_font_weight'] = $weight;
            if ( $style ) $sys_item['typography_font_style'] = $style;
            if ( $transform ) $sys_item['typography_text_transform'] = $transform;

            if ( $size ) {
                $sys_item['typography_font_size'] = array(
                    'unit' => 'px',
                    'size' => (string) $size,
                );
            }
            if ( null !== $line_height ) {
                $sys_item['typography_line_height'] = (string) $line_height;
            }
            if ( null !== $letter_spacing ) {
                $sys_item['typography_letter_spacing'] = array(
                    'unit' => 'px',
                    'size' => (string) $letter_spacing,
                );
            }

            // JJ 토큰이 비어있어 의미있는 설정이 없으면(=예상 밖 구조) 해당 슬롯은 건너뜀
            $has_meaningful_value = (
                ! empty( $sys_item['typography_font_family'] ) ||
                ! empty( $sys_item['typography_font_weight'] ) ||
                ! empty( $sys_item['typography_font_style'] ) ||
                ! empty( $sys_item['typography_text_transform'] ) ||
                ! empty( $sys_item['typography_font_size'] ) ||
                isset( $sys_item['typography_line_height'] ) ||
                ! empty( $sys_item['typography_letter_spacing'] )
            );
            if ( ! $has_meaningful_value ) {
                continue;
            }

            $system_typography = $this->upsert_elementor_system_item( $system_typography, $slot, ucfirst( $slot ), $sys_item, 'object' );
        }

        $settings['system_typography'] = $system_typography;
        return $settings;
    }

    /**
     * Elementor system array upsert helper
     *
     * mode:
     * - color: value is hex color string, written to ['color']
     * - object: value is associative array, merged into existing item
     */
    private function upsert_elementor_system_item( $items, $id, $title, $value, $mode = 'color' ) {
        $id = sanitize_key( $id );
        if ( ! is_array( $items ) ) {
            $items = array();
        }

        $found = false;
        foreach ( $items as $i => $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }
            $item_id = '';
            if ( isset( $item['_id'] ) ) {
                $item_id = sanitize_key( (string) $item['_id'] );
            } elseif ( isset( $item['id'] ) ) {
                $item_id = sanitize_key( (string) $item['id'] );
            }
            if ( $item_id !== $id ) {
                continue;
            }

            if ( 'color' === $mode ) {
                $color = $this->sanitize_color( $value );
                if ( $color ) {
                    $items[ $i ]['color'] = $color;
                }
                if ( empty( $items[ $i ]['title'] ) ) {
                    $items[ $i ]['title'] = $title;
                }
                if ( empty( $items[ $i ]['_id'] ) ) {
                    $items[ $i ]['_id'] = $id;
                }
            } else {
                if ( is_array( $value ) ) {
                    // 기존 값 유지 + 필요한 키만 덮어쓰기
                    $items[ $i ] = array_merge( $items[ $i ], $value );
                    if ( empty( $items[ $i ]['title'] ) ) {
                        $items[ $i ]['title'] = $title;
                    }
                    if ( empty( $items[ $i ]['_id'] ) ) {
                        $items[ $i ]['_id'] = $id;
                    }
                }
            }
            $found = true;
            break;
        }

        if ( ! $found ) {
            if ( 'color' === $mode ) {
                $color = $this->sanitize_color( $value );
                if ( ! $color ) {
                    return $items;
                }
                $items[] = array(
                    '_id'   => $id,
                    'title' => $title,
                    'color' => $color,
                );
            } else {
                if ( ! is_array( $value ) ) {
                    return $items;
                }
                if ( empty( $value['_id'] ) ) {
                    $value['_id'] = $id;
                }
                if ( empty( $value['title'] ) ) {
                    $value['title'] = $title;
                }
                $items[] = $value;
            }
        }

        return $items;
    }

    /**
     * Elementor typography slot -> JJ tag mapping resolver (shared by apply/diff)
     *
     * @param array $mapping
     * @return array
     */
    private function elementor_resolve_typography_map( $mapping ) {
        $map = array(
            'primary'   => 'h2',
            'secondary' => 'h3',
            'text'      => 'p',
            'accent'    => 'h4',
        );
        if ( isset( $mapping['primary'] ) )   $map['primary']   = sanitize_key( $mapping['primary'] );
        if ( isset( $mapping['secondary'] ) ) $map['secondary'] = sanitize_key( $mapping['secondary'] );
        if ( isset( $mapping['text'] ) )      $map['text']      = sanitize_key( $mapping['text'] );
        if ( isset( $mapping['accent'] ) )    $map['accent']    = sanitize_key( $mapping['accent'] );
        return $map;
    }

    /**
     * Elementor 변경 diff 생성 (UI용 요약)
     *
     * @param array $before
     * @param array $after
     * @param array $scope
     * @param array $mapping
     * @return array
     */
    private function elementor_build_diff( $before, $after, $scope, $mapping ) {
        $diff = array(
            'colors'     => array(),
            'typography' => array(),
            'counts'     => array(
                'colors'     => 0,
                'typography' => 0,
                'total'      => 0,
            ),
        );

        $before_colors = ( isset( $before['system_colors'] ) && is_array( $before['system_colors'] ) ) ? $before['system_colors'] : array();
        $after_colors  = ( isset( $after['system_colors'] ) && is_array( $after['system_colors'] ) ) ? $after['system_colors'] : array();

        if ( ! empty( $scope['colors'] ) ) {
            $targets = array(
                'primary'   => 'Primary',
                'secondary' => 'Secondary',
                'text'      => 'Text',
                'accent'    => 'Accent',
            );
            foreach ( $targets as $id => $title ) {
                $b_item = $this->elementor_find_system_item( $before_colors, $id );
                $a_item = $this->elementor_find_system_item( $after_colors, $id );
                $b_val  = ( $b_item && isset( $b_item['color'] ) ) ? $this->sanitize_color( $b_item['color'] ) : '';
                $a_val  = ( $a_item && isset( $a_item['color'] ) ) ? $this->sanitize_color( $a_item['color'] ) : '';
                if ( $a_val && $b_val !== $a_val ) {
                    $diff['colors'][] = array(
                        'id'     => $id,
                        'title'  => $title,
                        'before' => $b_val,
                        'after'  => $a_val,
                    );
                }
            }
        }

        $before_typo = ( isset( $before['system_typography'] ) && is_array( $before['system_typography'] ) ) ? $before['system_typography'] : array();
        $after_typo  = ( isset( $after['system_typography'] ) && is_array( $after['system_typography'] ) ) ? $after['system_typography'] : array();

        if ( ! empty( $scope['typography'] ) ) {
            $map = $this->elementor_resolve_typography_map( $mapping );
            foreach ( $map as $slot => $jj_tag ) {
                $b_item = $this->elementor_find_system_item( $before_typo, $slot );
                $a_item = $this->elementor_find_system_item( $after_typo, $slot );
                $b_snap = $this->elementor_typography_snapshot( $b_item );
                $a_snap = $this->elementor_typography_snapshot( $a_item );

                $changes = array();
                foreach ( $a_snap as $k => $a_v ) {
                    $b_v = $b_snap[ $k ] ?? '';
                    if ( (string) $b_v !== (string) $a_v ) {
                        $changes[] = array(
                            'key'    => $k,
                            'before' => $b_v,
                            'after'  => $a_v,
                        );
                    }
                }

                if ( ! empty( $changes ) ) {
                    $diff['typography'][] = array(
                        'id'       => $slot,
                        'title'    => ucfirst( $slot ),
                        'jj_tag'   => $jj_tag,
                        'changes'  => $changes,
                    );
                }
            }
        }

        $diff['counts']['colors']     = count( $diff['colors'] );
        $diff['counts']['typography'] = count( $diff['typography'] );
        $diff['counts']['total']      = (int) $diff['counts']['colors'] + (int) $diff['counts']['typography'];
        return $diff;
    }

    /**
     * Elementor system_* 배열에서 id(_id|id)로 아이템 찾기
     *
     * @param array $items
     * @param string $id
     * @return array|null
     */
    private function elementor_find_system_item( $items, $id ) {
        $id = sanitize_key( (string) $id );
        if ( ! is_array( $items ) ) {
            return null;
        }
        foreach ( $items as $item ) {
            if ( ! is_array( $item ) ) {
                continue;
            }
            $item_id = '';
            if ( isset( $item['_id'] ) ) {
                $item_id = sanitize_key( (string) $item['_id'] );
            } elseif ( isset( $item['id'] ) ) {
                $item_id = sanitize_key( (string) $item['id'] );
            }
            if ( $item_id === $id ) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Elementor typography 아이템을 UI 비교용 스냅샷으로 축약
     *
     * @param array|null $item
     * @return array
     */
    private function elementor_typography_snapshot( $item ) {
        if ( ! is_array( $item ) ) {
            $item = array();
        }

        $font_size = '';
        if ( isset( $item['typography_font_size'] ) && is_array( $item['typography_font_size'] ) ) {
            $unit = isset( $item['typography_font_size']['unit'] ) ? (string) $item['typography_font_size']['unit'] : '';
            $size = isset( $item['typography_font_size']['size'] ) ? (string) $item['typography_font_size']['size'] : '';
            if ( $size !== '' ) {
                $font_size = $size . $unit;
            }
        }

        $letter_spacing = '';
        if ( isset( $item['typography_letter_spacing'] ) && is_array( $item['typography_letter_spacing'] ) ) {
            $unit = isset( $item['typography_letter_spacing']['unit'] ) ? (string) $item['typography_letter_spacing']['unit'] : '';
            $size = isset( $item['typography_letter_spacing']['size'] ) ? (string) $item['typography_letter_spacing']['size'] : '';
            if ( $size !== '' ) {
                $letter_spacing = $size . $unit;
            }
        }

        return array(
            'font_family'    => isset( $item['typography_font_family'] ) ? (string) $item['typography_font_family'] : '',
            'font_weight'    => isset( $item['typography_font_weight'] ) ? (string) $item['typography_font_weight'] : '',
            'font_style'     => isset( $item['typography_font_style'] ) ? (string) $item['typography_font_style'] : '',
            'text_transform' => isset( $item['typography_text_transform'] ) ? (string) $item['typography_text_transform'] : '',
            'font_size'      => $font_size,
            'line_height'    => isset( $item['typography_line_height'] ) ? (string) $item['typography_line_height'] : '',
            'letter_spacing' => $letter_spacing,
        );
    }

    /**
     * ===== Backups =====
     */
    private function get_backups() {
        $backups = function_exists( 'get_option' ) ? (array) get_option( $this->backups_key, array() ) : array();
        if ( ! is_array( $backups ) ) {
            $backups = array();
        }
        return $backups;
    }

    /**
     * ===== Audit Log =====
     */
    private function get_audit_logs() {
        $key = 'jj_style_guide_builder_sync_audit_log';
        $logs = function_exists( 'get_option' ) ? (array) get_option( $key, array() ) : array();
        if ( ! is_array( $logs ) ) {
            $logs = array();
        }
        return $logs;
    }

    private function add_audit_log( $event ) {
        $key = 'jj_style_guide_builder_sync_audit_log';
        $logs = $this->get_audit_logs();

        $timestamp = function_exists( 'current_time' ) ? (int) current_time( 'timestamp' ) : time();
        $user = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : null;
        $user_id = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
        $user_name = ( $user && ! empty( $user->display_name ) ) ? (string) $user->display_name : '';

        $record = array_merge(
            array(
                'timestamp' => $timestamp,
                'date'      => function_exists( 'date_i18n' ) ? date_i18n( 'Y-m-d H:i:s', $timestamp ) : date( 'Y-m-d H:i:s', $timestamp ),
                'user_id'   => $user_id,
                'user_name' => $user_name,
            ),
            is_array( $event ) ? $event : array()
        );

        $logs[] = $record;

        // 최대 200개만 유지
        if ( count( $logs ) > 200 ) {
            $logs = array_slice( $logs, -200 );
        }

        if ( function_exists( 'update_option' ) ) {
            update_option( $key, $logs );
        }
    }

    /**
     * @param array $backup_data
     * @return string backup_id
     */
    private function create_backup( $backup_data ) {
        $timestamp = function_exists( 'current_time' ) ? (int) current_time( 'timestamp' ) : time();
        $rand = function_exists( 'wp_generate_password' ) ? wp_generate_password( 6, false ) : substr( md5( uniqid() ), 0, 6 );
        $backup_id = 'bsync_' . $timestamp . '_' . $rand;

        $user = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : null;
        $user_name = ( $user && ! empty( $user->display_name ) ) ? $user->display_name : '';

        $record = array(
            'id'        => $backup_id,
            'builder'   => $backup_data['builder'] ?? 'unknown',
            'scope'     => $backup_data['scope'] ?? array(),
            'label'     => $backup_data['label'] ?? __( '자동 백업', 'acf-css-really-simple-style-management-center' ),
            'timestamp' => $timestamp,
            'date'      => function_exists( 'date_i18n' ) ? date_i18n( 'Y-m-d H:i:s', $timestamp ) : date( 'Y-m-d H:i:s', $timestamp ),
            'user_id'   => function_exists( 'get_current_user_id' ) ? get_current_user_id() : 0,
            'user_name' => $user_name,
            'items'     => isset( $backup_data['items'] ) && is_array( $backup_data['items'] ) ? $backup_data['items'] : array(),
            'version'   => defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '',
        );

        $backups = $this->get_backups();
        $backups[ $backup_id ] = $record;

        // 최신순 정렬
        uasort( $backups, function( $a, $b ) {
            return (int) ( $b['timestamp'] ?? 0 ) <=> (int) ( $a['timestamp'] ?? 0 );
        } );

        // 개수 제한
        if ( count( $backups ) > $this->max_backups ) {
            $backups = array_slice( $backups, 0, $this->max_backups, true );
        }

        if ( function_exists( 'update_option' ) ) {
            update_option( $this->backups_key, $backups );
        }

        return $backup_id;
    }

    private function capture_backup_items_from_backup( $backup ) {
        $items = array();
        $orig_items = isset( $backup['items'] ) && is_array( $backup['items'] ) ? $backup['items'] : array();

        foreach ( $orig_items as $item ) {
            if ( ! is_array( $item ) || empty( $item['type'] ) ) {
                continue;
            }
            if ( 'option' === $item['type'] ) {
                $name = isset( $item['name'] ) ? (string) $item['name'] : '';
                if ( $name && function_exists( 'get_option' ) ) {
                    $items[] = array(
                        'type'  => 'option',
                        'name'  => $name,
                        'value' => get_option( $name, null ),
                    );
                }
            } elseif ( 'post_meta' === $item['type'] ) {
                $post_id = isset( $item['post_id'] ) ? (int) $item['post_id'] : 0;
                $meta_key = isset( $item['meta_key'] ) ? (string) $item['meta_key'] : '';
                if ( $post_id > 0 && $meta_key && function_exists( 'get_post_meta' ) ) {
                    $items[] = array(
                        'type'     => 'post_meta',
                        'post_id'  => $post_id,
                        'meta_key' => $meta_key,
                        'value'    => get_post_meta( $post_id, $meta_key, true ),
                    );
                }
            }
        }

        return $items;
    }

    /**
     * ===== Utils =====
     */
    private function verify_ajax() {
        if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! function_exists( 'wp_verify_nonce' ) || ! wp_verify_nonce( $nonce, 'jj_builder_sync_action' ) ) {
            wp_send_json_error( array( 'message' => __( '유효하지 않은 요청입니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }
    }

    private function is_elementor_active() {
        if ( defined( 'ELEMENTOR_VERSION' ) ) {
            return true;
        }
        if ( did_action( 'elementor/loaded' ) ) {
            return true;
        }
        return class_exists( '\Elementor\Plugin' );
    }

    private function get_elementor_active_kit_id() {
        $kit_id = 0;
        if ( function_exists( 'get_option' ) ) {
            $kit_id = (int) get_option( 'elementor_active_kit', 0 );
        }
        if ( $kit_id > 0 ) {
            return $kit_id;
        }

        if ( class_exists( '\Elementor\Plugin' ) ) {
            try {
                $plugin = \Elementor\Plugin::$instance;
                if ( isset( $plugin->kits_manager ) ) {
                    $km = $plugin->kits_manager;
                    if ( method_exists( $km, 'get_active_id' ) ) {
                        $id = (int) $km->get_active_id();
                        if ( $id > 0 ) {
                            return $id;
                        }
                    }
                    if ( method_exists( $km, 'get_active_kit' ) ) {
                        $kit = $km->get_active_kit();
                        if ( is_object( $kit ) && method_exists( $kit, 'get_id' ) ) {
                            $id = (int) $kit->get_id();
                            if ( $id > 0 ) {
                                return $id;
                            }
                        }
                    }
                    if ( method_exists( $km, 'get_active_kit_for_frontend' ) ) {
                        $kit = $km->get_active_kit_for_frontend();
                        if ( is_object( $kit ) && method_exists( $kit, 'get_id' ) ) {
                            $id = (int) $kit->get_id();
                            if ( $id > 0 ) {
                                return $id;
                            }
                        }
                    }
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        // Fallback: 최근 kit 탐색
        if ( function_exists( 'get_posts' ) ) {
            $ids = get_posts( array(
                'post_type'      => 'elementor_library',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'fields'         => 'ids',
                'meta_query'     => array(
                    array(
                        'key'   => '_elementor_template_type',
                        'value' => 'kit',
                    ),
                ),
            ) );
            if ( is_array( $ids ) && ! empty( $ids[0] ) ) {
                return (int) $ids[0];
            }
        }

        return 0;
    }

    private function maybe_clear_elementor_cache() {
        // Best effort. Elementor는 캐시/파일 구조가 버전별로 달라질 수 있음.
        if ( function_exists( 'do_action' ) ) {
            do_action( 'elementor/core/files/clear_cache' );
            do_action( 'elementor/core/files/clear_cache', 'all' );
        }

        if ( class_exists( '\Elementor\Plugin' ) ) {
            try {
                $plugin = \Elementor\Plugin::$instance;
                if ( isset( $plugin->files_manager ) && is_object( $plugin->files_manager ) && method_exists( $plugin->files_manager, 'clear_cache' ) ) {
                    $plugin->files_manager->clear_cache();
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
    }

    private function flush_jj_css_cache() {
        if ( class_exists( 'JJ_CSS_Cache' ) ) {
            try {
                JJ_CSS_Cache::instance()->flush();
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
    }

    private function get_jj_tokens() {
        $options_key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $options = function_exists( 'get_option' ) ? (array) get_option( $options_key, array() ) : array();

        $brand = isset( $options['palettes']['brand'] ) && is_array( $options['palettes']['brand'] ) ? $options['palettes']['brand'] : array();
        $system = isset( $options['palettes']['system'] ) && is_array( $options['palettes']['system'] ) ? $options['palettes']['system'] : array();
        $typography = isset( $options['typography'] ) && is_array( $options['typography'] ) ? $options['typography'] : array();

        $primary   = $this->sanitize_color( $brand['primary_color'] ?? '' );
        $secondary = $this->sanitize_color( $brand['secondary_color'] ?? '' );
        $accent    = $this->sanitize_color( $brand['primary_color_hover'] ?? '' );
        $text      = $this->sanitize_color( $system['text_color'] ?? '' );
        $bg        = $this->sanitize_color( $system['site_bg'] ?? '' );

        if ( ! $primary )   $primary = '#2271b1';
        if ( ! $secondary ) $secondary = '#50575e';
        if ( ! $text )      $text = '#1d2327';
        if ( ! $accent )    $accent = $primary;

        return array(
            'colors' => array(
                'primary'   => $primary,
                'secondary' => $secondary,
                'text'      => $text,
                'accent'    => $accent,
                'background'=> $bg,
            ),
            'typography' => $typography,
        );
    }

    private function sanitize_color( $value ) {
        $value = trim( (string) $value );
        if ( '' === $value ) {
            return '';
        }
        if ( function_exists( 'sanitize_hex_color' ) ) {
            $hex = sanitize_hex_color( $value );
            if ( $hex ) {
                return $hex;
            }
        }
        // Elementor/빌더 "설정값"은 가급적 HEX만 저장 (예상치 못한 구조/파서 이슈 방지)
        return '';
    }

    private function sanitize_font_family( $value ) {
        $value = (string) $value;
        $value = trim( $value );
        if ( '' === $value ) {
            return '';
        }
        // 허용 문자만 보수적으로 유지
        $value = preg_replace( '/[^0-9a-zA-Z\\s\\,\\-\\_\\\"\\\']/', '', $value );
        return trim( $value );
    }
}

