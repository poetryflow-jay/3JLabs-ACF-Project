<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$integration_manager = new JJ_Integration_Manager();
$integrations = $integration_manager->get_available_integrations();
?>
<div class="wrap jj-marketing-integrations">
    <h1><?php esc_html_e( '🔗 통합 관리', 'jj-marketing-dashboard' ); ?></h1>
    <p class="description">
        <?php esc_html_e( '외부 서비스와의 통합을 관리합니다.', 'jj-marketing-dashboard' ); ?>
    </p>

    <div class="jj-marketing-integrations-container">
        <?php foreach ( $integrations as $id => $integration ) : ?>
            <div class="jj-marketing-integration-card">
                <h3><?php echo esc_html( $integration['name'] ); ?></h3>
                <p>
                    <?php if ( $integration_manager->check_integration_status( $id ) ) : ?>
                        <span class="status-connected"><?php esc_html_e( '연결됨', 'jj-marketing-dashboard' ); ?></span>
                    <?php else : ?>
                        <span class="status-disconnected"><?php esc_html_e( '연결 안 됨', 'jj-marketing-dashboard' ); ?></span>
                    <?php endif; ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
