<?php
/**
 * 사용자 프로필 라이센스 섹션
 * 
 * @package JJ_LicenseManagerincludesAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 사용자 프로필에 라이센스 섹션 추가
add_action( 'show_user_profile', 'jj_license_admin_add_user_license_section' );
add_action( 'edit_user_profile', 'jj_license_admin_add_user_license_section' );

function jj_license_admin_add_user_license_section( $user ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    global $wpdb;
    
    $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
    $table_activations = JJ_License_Database::get_table_name( 'activations' );
    
    $licenses = $wpdb->get_results( $wpdb->prepare(
        "SELECT l.*, 
                COUNT(a.id) as activation_count,
                SUM(CASE WHEN a.is_active = 1 THEN 1 ELSE 0 END) as active_sites
         FROM {$table_licenses} l
         LEFT JOIN {$table_activations} a ON l.id = a.license_id
         WHERE l.user_id = %d
         GROUP BY l.id
         ORDER BY l.created_at DESC",
        $user->ID
    ), ARRAY_A );
    
    ?>
    <h2><?php esc_html_e( '라이센스', 'jj-license-manager' ); ?></h2>
    <table class="form-table">
        <tr>
            <th><?php esc_html_e( '라이센스 목록', 'jj-license-manager' ); ?></th>
            <td>
                <?php if ( empty( $licenses ) ) : ?>
                    <p><?php esc_html_e( '라이센스가 없습니다.', 'jj-license-manager' ); ?></p>
                <?php else : ?>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( '라이센스 키', 'jj-license-manager' ); ?></th>
                                <th><?php esc_html_e( '타입', 'jj-license-manager' ); ?></th>
                                <th><?php esc_html_e( '상태', 'jj-license-manager' ); ?></th>
                                <th><?php esc_html_e( '활성화', 'jj-license-manager' ); ?></th>
                                <th><?php esc_html_e( '만료일', 'jj-license-manager' ); ?></th>
                                <th><?php esc_html_e( '작업', 'jj-license-manager' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $licenses as $license ) : ?>
                                <tr>
                                    <td><code><?php echo esc_html( $license['license_key'] ); ?></code></td>
                                    <td>
                                        <span class="license-type license-type-<?php echo esc_attr( strtolower( $license['license_type'] ) ); ?>">
                                            <?php echo esc_html( $license['license_type'] ); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="license-status license-status-<?php echo esc_attr( $license['status'] ); ?>">
                                            <?php
                                            $status_labels = array(
                                                'active' => __( '활성', 'jj-license-manager' ),
                                                'inactive' => __( '비활성', 'jj-license-manager' ),
                                                'expired' => __( '만료', 'jj-license-manager' ),
                                            );
                                            echo esc_html( $status_labels[ $license['status'] ] ?? $license['status'] );
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        printf(
                                            esc_html__( '%d / %d', 'jj-license-manager' ),
                                            intval( $license['active_sites'] ),
                                            intval( $license['max_activations'] )
                                        );
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ( $license['expires_at'] ) : ?>
                                            <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $license['expires_at'] ) ) ); ?>
                                        <?php else : ?>
                                            <span style="color: #2271b1;"><?php esc_html_e( '평생', 'jj-license-manager' ); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=jj-license-manager&s=' . urlencode( $license['license_key'] ) ) ); ?>" target="_blank">
                                            <?php esc_html_e( '관리', 'jj-license-manager' ); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <p>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=jj-license-manager&action=create&user_id=' . $user->ID ) ); ?>" class="button">
                        <?php esc_html_e( '새 라이센스 생성', 'jj-license-manager' ); ?>
                    </a>
                </p>
            </td>
        </tr>
    </table>
    <?php
}

