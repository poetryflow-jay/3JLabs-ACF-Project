<?php
/**
 * 라이센스 목록 템플릿
 * 
 * @package JJ_LicenseManagerTemplates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( '라이센스 관리', 'jj-license-manager' ); ?></h1>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=jj-license-settings' ) ); ?>" class="page-title-action"><?php esc_html_e( '설정', 'jj-license-manager' ); ?></a>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=jj-license-manager&action=create' ) ); ?>" class="page-title-action"><?php esc_html_e( '라이센스 생성', 'jj-license-manager' ); ?></a>
    
    <hr class="wp-header-end">
    
    <?php
    // 통계 표시
    require_once JJ_NEURAL_LINK_PATH . 'includes/admin/class-jj-license-stats.php';
    $stats = JJ_License_Stats::get_stats();
    ?>
    <div class="jj-license-stats">
        <div class="jj-license-stat-box">
            <h3><?php esc_html_e( '전체 라이센스', 'jj-license-manager' ); ?></h3>
            <div class="stat-number"><?php echo esc_html( number_format( $stats['total_licenses'] ) ); ?></div>
            <div class="stat-label"><?php esc_html_e( '총 라이센스 수', 'jj-license-manager' ); ?></div>
        </div>
        <div class="jj-license-stat-box">
            <h3><?php esc_html_e( '활성 라이센스', 'jj-license-manager' ); ?></h3>
            <div class="stat-number" style="color: #00a32a;"><?php echo esc_html( number_format( $stats['active_licenses'] ) ); ?></div>
            <div class="stat-label"><?php esc_html_e( '활성화된 라이센스', 'jj-license-manager' ); ?></div>
        </div>
        <div class="jj-license-stat-box">
            <h3><?php esc_html_e( '활성 사이트', 'jj-license-manager' ); ?></h3>
            <div class="stat-number" style="color: #2271b1;"><?php echo esc_html( number_format( $stats['active_sites'] ) ); ?></div>
            <div class="stat-label"><?php esc_html_e( '활성화된 사이트 수', 'jj-license-manager' ); ?></div>
        </div>
        <div class="jj-license-stat-box">
            <h3><?php esc_html_e( '만료 임박', 'jj-license-manager' ); ?></h3>
            <div class="stat-number" style="color: <?php echo $stats['expiring_soon'] > 0 ? '#d63638' : '#646970'; ?>;">
                <?php echo esc_html( number_format( $stats['expiring_soon'] ) ); ?>
            </div>
            <div class="stat-label"><?php esc_html_e( '14일 이내 만료', 'jj-license-manager' ); ?></div>
        </div>
    </div>
    
    <?php if ( isset( $_GET['deleted'] ) ) : ?>
        <div class="notice notice-success"><p><?php esc_html_e( '라이센스가 삭제되었습니다.', 'jj-license-manager' ); ?></p></div>
    <?php endif; ?>
    
    <?php if ( isset( $_GET['deactivated'] ) ) : ?>
        <div class="notice notice-success"><p><?php esc_html_e( '라이센스가 비활성화되었습니다.', 'jj-license-manager' ); ?></p></div>
    <?php endif; ?>
    
    <?php if ( isset( $_GET['activated'] ) ) : ?>
        <div class="notice notice-success"><p><?php esc_html_e( '라이센스가 활성화되었습니다.', 'jj-license-manager' ); ?></p></div>
    <?php endif; ?>
    
    <?php if ( isset( $_GET['renewed'] ) ) : ?>
        <div class="notice notice-success"><p><?php esc_html_e( '라이센스가 갱신되었습니다.', 'jj-license-manager' ); ?></p></div>
    <?php endif; ?>
    
    <?php if ( isset( $_GET['renew_error'] ) && isset( $_GET['message'] ) ) : ?>
        <div class="notice notice-error"><p><?php echo esc_html( urldecode( $_GET['message'] ) ); ?></p></div>
    <?php endif; ?>
    
    <?php if ( isset( $_GET['action'] ) && $_GET['action'] === 'renew' && isset( $_GET['license_id'] ) ) : ?>
        <?php include JJ_NEURAL_LINK_PATH . 'templates/admin/renew-license.php'; ?>
        <?php return; ?>
    <?php endif; ?>
    
    <!-- 검색 및 필터 -->
    <div class="tablenav top">
        <form method="get" action="">
            <input type="hidden" name="page" value="jj-license-manager">
            
            <p class="search-box">
                <label class="screen-reader-text" for="search-input"><?php esc_html_e( '검색:', 'jj-license-manager' ); ?></label>
                <input type="search" id="search-input" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( '라이센스 키, 사용자 검색...', 'jj-license-manager' ); ?>">
                <input type="submit" id="search-submit" class="button" value="<?php esc_attr_e( '검색', 'jj-license-manager' ); ?>">
            </p>
            
            <div class="alignleft actions">
                <select name="status">
                    <option value=""><?php esc_html_e( '모든 상태', 'jj-license-manager' ); ?></option>
                    <option value="active" <?php selected( $status_filter, 'active' ); ?>><?php esc_html_e( '활성', 'jj-license-manager' ); ?></option>
                    <option value="inactive" <?php selected( $status_filter, 'inactive' ); ?>><?php esc_html_e( '비활성', 'jj-license-manager' ); ?></option>
                    <option value="expired" <?php selected( $status_filter, 'expired' ); ?>><?php esc_html_e( '만료', 'jj-license-manager' ); ?></option>
                </select>
                
                <select name="type">
                    <option value=""><?php esc_html_e( '모든 타입', 'jj-license-manager' ); ?></option>
                    <option value="FREE" <?php selected( $type_filter, 'FREE' ); ?>>Free</option>
                    <option value="BASIC" <?php selected( $type_filter, 'BASIC' ); ?>>Basic</option>
                    <option value="PREM" <?php selected( $type_filter, 'PREM' ); ?>>Premium</option>
                    <option value="UNLIM" <?php selected( $type_filter, 'UNLIM' ); ?>>Unlimited</option>
                </select>
                
                <input type="submit" class="button" value="<?php esc_attr_e( '필터', 'jj-license-manager' ); ?>">
            </div>
        </form>
    </div>
    
    <!-- 라이센스 테이블 -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-license-key"><?php esc_html_e( '라이센스 키', 'jj-license-manager' ); ?></th>
                <th scope="col" class="manage-column column-type"><?php esc_html_e( '타입', 'jj-license-manager' ); ?></th>
                <th scope="col" class="manage-column column-user"><?php esc_html_e( '사용자', 'jj-license-manager' ); ?></th>
                <th scope="col" class="manage-column column-product"><?php esc_html_e( '상품', 'jj-license-manager' ); ?></th>
                <th scope="col" class="manage-column column-status"><?php esc_html_e( '상태', 'jj-license-manager' ); ?></th>
                <th scope="col" class="manage-column column-activations"><?php esc_html_e( '활성화', 'jj-license-manager' ); ?></th>
                <th scope="col" class="manage-column column-expires"><?php esc_html_e( '만료일', 'jj-license-manager' ); ?></th>
                <th scope="col" class="manage-column column-actions"><?php esc_html_e( '작업', 'jj-license-manager' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $licenses ) ) : ?>
                <tr>
                    <td colspan="8" class="no-items"><?php esc_html_e( '라이센스가 없습니다.', 'jj-license-manager' ); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ( $licenses as $license ) : ?>
                    <tr>
                        <td class="column-license-key">
                            <strong><?php echo esc_html( $license['license_key'] ); ?></strong>
                            <div class="row-actions">
                                <span class="view">
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=jj-license-activations&license_id=' . $license['id'] ) ); ?>">
                                        <?php esc_html_e( '활성화 보기', 'jj-license-manager' ); ?>
                                    </a>
                                </span>
                            </div>
                        </td>
                        <td class="column-type">
                            <span class="license-type license-type-<?php echo esc_attr( strtolower( $license['license_type'] ) ); ?>">
                                <?php echo esc_html( $license['license_type'] ); ?>
                            </span>
                        </td>
                        <td class="column-user">
                            <?php if ( $license['user_id'] ) : ?>
                                <a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $license['user_id'] ) ); ?>">
                                    <?php echo esc_html( $license['display_name'] ?: $license['user_login'] ); ?>
                                </a>
                                <br>
                                <small><?php echo esc_html( $license['user_email'] ); ?></small>
                            <?php else : ?>
                                <?php esc_html_e( '게스트', 'jj-license-manager' ); ?>
                            <?php endif; ?>
                        </td>
                        <td class="column-product">
                            <?php if ( $license['product_id'] ) : ?>
                                <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $license['product_id'] . '&action=edit' ) ); ?>">
                                    <?php echo esc_html( $license['product_name'] ?: '#' . $license['product_id'] ); ?>
                                </a>
                                <?php if ( $license['order_id'] ) : ?>
                                    <br>
                                    <small>
                                        <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $license['order_id'] . '&action=edit' ) ); ?>">
                                            <?php printf( esc_html__( '주문 #%d', 'jj-license-manager' ), $license['order_id'] ); ?>
                                        </a>
                                    </small>
                                <?php endif; ?>
                            <?php else : ?>
                                &mdash;
                            <?php endif; ?>
                        </td>
                        <td class="column-status">
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
                        <td class="column-activations">
                            <?php
                            printf(
                                esc_html__( '%d / %d', 'jj-license-manager' ),
                                intval( $license['active_count'] ),
                                intval( $license['max_activations'] )
                            );
                            ?>
                        </td>
                        <td class="column-expires">
                            <?php if ( $license['expires_at'] ) : ?>
                                <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $license['expires_at'] ) ) ); ?>
                                <?php
                                $days_until_expiry = ( strtotime( $license['expires_at'] ) - time() ) / ( 24 * 60 * 60 );
                                if ( $days_until_expiry <= 14 && $days_until_expiry > 0 ) {
                                    echo '<br><span style="color: #d63638;">(' . sprintf( esc_html__( '%d일 후 만료', 'jj-license-manager' ), ceil( $days_until_expiry ) ) . ')</span>';
                                } elseif ( $days_until_expiry <= 0 ) {
                                    echo '<br><span style="color: #d63638;">(' . esc_html__( '만료됨', 'jj-license-manager' ) . ')</span>';
                                }
                                ?>
                            <?php else : ?>
                                <span style="color: #2271b1;"><?php esc_html_e( '평생', 'jj-license-manager' ); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="column-actions">
                            <?php if ( $license['status'] === 'active' ) : ?>
                                <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=jj-license-manager&action=deactivate&license_id=' . $license['id'] ), 'deactivate_license_' . $license['id'] ) ); ?>" class="button button-small">
                                    <?php esc_html_e( '비활성화', 'jj-license-manager' ); ?>
                                </a>
                                <?php if ( $license['expires_at'] ) : ?>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=jj-license-manager&action=renew&license_id=' . $license['id'] ) ); ?>" class="button button-small">
                                        <?php esc_html_e( '갱신', 'jj-license-manager' ); ?>
                                    </a>
                                <?php endif; ?>
                            <?php else : ?>
                                <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=jj-license-manager&action=activate&license_id=' . $license['id'] ), 'activate_license_' . $license['id'] ) ); ?>" class="button button-small">
                                    <?php esc_html_e( '활성화', 'jj-license-manager' ); ?>
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=jj-license-manager&action=delete&license_id=' . $license['id'] ), 'delete_license_' . $license['id'] ) ); ?>" class="button button-small delete" onclick="return confirm('<?php esc_attr_e( '정말로 이 라이센스를 삭제하시겠습니까?', 'jj-license-manager' ); ?>');">
                                <?php esc_html_e( '삭제', 'jj-license-manager' ); ?>
                            </a>
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

<style>
.license-type {
    padding: 3px 8px;
    border-radius: 3px;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
}
.license-type-free { background: #f0f0f1; color: #50575e; }
.license-type-basic { background: #2271b1; color: #fff; }
.license-type-prem { background: #8c8f94; color: #fff; }
.license-type-unlim { background: #d63638; color: #fff; }

.license-status {
    padding: 3px 8px;
    border-radius: 3px;
    font-weight: 600;
    font-size: 11px;
}
.license-status-active { background: #00a32a; color: #fff; }
.license-status-inactive { background: #d63638; color: #fff; }
.license-status-expired { background: #f0b849; color: #fff; }
</style>

