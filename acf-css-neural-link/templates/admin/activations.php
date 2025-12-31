<?php
/**
 * 활성화 목록 템플릿
 * 
 * @package JJ_LicenseManagerTemplates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h1><?php esc_html_e( '라이센스 활성화', 'jj-license-manager' ); ?></h1>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col"><?php esc_html_e( '라이센스 키', 'jj-license-manager' ); ?></th>
                <th scope="col"><?php esc_html_e( '타입', 'jj-license-manager' ); ?></th>
                <th scope="col"><?php esc_html_e( '사용자', 'jj-license-manager' ); ?></th>
                <th scope="col"><?php esc_html_e( '사이트 ID', 'jj-license-manager' ); ?></th>
                <th scope="col"><?php esc_html_e( '사이트 URL', 'jj-license-manager' ); ?></th>
                <th scope="col"><?php esc_html_e( '활성화일', 'jj-license-manager' ); ?></th>
                <th scope="col"><?php esc_html_e( '비활성화일', 'jj-license-manager' ); ?></th>
                <th scope="col"><?php esc_html_e( '상태', 'jj-license-manager' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $activations ) ) : ?>
                <tr>
                    <td colspan="8" class="no-items"><?php esc_html_e( '활성화 기록이 없습니다.', 'jj-license-manager' ); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ( $activations as $activation ) : ?>
                    <tr>
                        <td><strong><?php echo esc_html( $activation['license_key'] ); ?></strong></td>
                        <td>
                            <span class="license-type license-type-<?php echo esc_attr( strtolower( $activation['license_type'] ) ); ?>">
                                <?php echo esc_html( $activation['license_type'] ); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ( $activation['user_id'] ) : ?>
                                <a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $activation['user_id'] ) ); ?>">
                                    <?php echo esc_html( $activation['user_login'] ); ?>
                                </a>
                                <br>
                                <small><?php echo esc_html( $activation['user_email'] ); ?></small>
                            <?php else : ?>
                                &mdash;
                            <?php endif; ?>
                        </td>
                        <td><code><?php echo esc_html( substr( $activation['site_id'], 0, 16 ) . '...' ); ?></code></td>
                        <td>
                            <a href="<?php echo esc_url( $activation['site_url'] ); ?>" target="_blank">
                                <?php echo esc_html( $activation['site_url'] ); ?>
                            </a>
                        </td>
                        <td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $activation['activated_at'] ) ) ); ?></td>
                        <td>
                            <?php if ( $activation['deactivated_at'] ) : ?>
                                <?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $activation['deactivated_at'] ) ) ); ?>
                            <?php else : ?>
                                &mdash;
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( $activation['is_active'] ) : ?>
                                <span class="license-status license-status-active"><?php esc_html_e( '활성', 'jj-license-manager' ); ?></span>
                            <?php else : ?>
                                <span class="license-status license-status-inactive"><?php esc_html_e( '비활성', 'jj-license-manager' ); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- 페이지네이션 -->
    <?php
    $total_pages = ceil( $total_items / $per_page );
    if ( $total_pages > 1 ) {
        echo '<div class="tablenav bottom">';
        echo '<div class="tablenav-pages">';
        echo paginate_links( array(
            'base' => add_query_arg( 'paged', '%#%' ),
            'format' => '',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'total' => $total_pages,
            'current' => $current_page,
        ) );
        echo '</div>';
        echo '</div>';
    }
    ?>
</div>

