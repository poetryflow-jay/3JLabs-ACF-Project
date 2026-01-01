<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="jj-admin-center-tab-content" data-tab="license">
    <?php
    $license_manager = null;
    $current_license_key = get_option( 'jj_style_guide_license_key', '' );
    if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
        require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $license_manager = JJ_License_Manager::instance();
        }
    }
    
    // 라이센스 매니저가 없으면 안내 메시지
    if ( ! $license_manager ) {
        echo '<div class="notice notice-error inline"><p>' . esc_html__( '라이센스 관리 모듈을 로드할 수 없습니다.', 'acf-css-really-simple-style-management-center' ) . '</p></div>';
    } else {
    
    // 현재 상태 조회
    $license_status = $license_manager->get_license_status();
    $license_type = $license_manager->get_current_license_type();
    
    // 결제/연장 링크 결정
    $is_expired = false;
    $action_text = __( '업그레이드', 'acf-css-really-simple-style-management-center' );
    
    if ( isset( $license_status['valid_until'] ) && $license_status['valid_until'] < time() ) {
        $is_expired = true;
        $action_text = __( '기한 연장', 'acf-css-really-simple-style-management-center' );
    } elseif ( ! $license_status['valid'] && ! empty( $current_license_key ) ) {
        if ( in_array( $license_type, array( 'BASIC', 'PREM', 'UNLIM' ) ) ) {
            $is_expired = true;
            $action_text = __( '기한 연장', 'acf-css-really-simple-style-management-center' );
        }
    }
    $purchase_url = $license_manager->get_purchase_url( $is_expired ? 'renew' : 'upgrade' );
    
    // Partner/Master 판별:
    // - Partner/Master: 업그레이드 유도 UI 금지
    // - Master: 라이센스 발급 도구 노출
    $is_partner_or_higher = false;
    $is_master = false;
    if ( class_exists( 'JJ_Edition_Controller' ) ) {
        try {
            $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            $is_master = JJ_Edition_Controller::instance()->is_at_least( 'master' );
        } catch ( Exception $e ) {
            // ignore
        } catch ( Error $e ) {
            // ignore
        }
    } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
        $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        $is_master = ( 'MASTER' === JJ_STYLE_GUIDE_LICENSE_TYPE );
    } elseif ( is_string( $license_type ) ) {
        $is_partner_or_higher = in_array( $license_type, array( 'PARTNER', 'MASTER' ), true );
        $is_master = ( 'MASTER' === $license_type );
    }
    ?>
    
    <div class="jj-license-settings">
        <h3><?php esc_html_e( '라이센스 관리 (License Management)', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <p class="description">
            <?php esc_html_e( '제품 정품 인증을 통해 모든 기능을 활성화하고 자동 업데이트를 받으세요.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>

        <!-- [마스터 버전 전용 도구] -->
        <?php if ( $is_master ) : ?>
        <div class="jj-master-license-management" style="margin-bottom: 30px; padding: 20px; background: #f0f6fc; border: 1px solid #c3c4c7; border-radius: 4px;">
            <h4 style="margin-top: 0; display: flex; align-items: center; gap: 8px;">
                <span class="dashicons dashicons-shield-alt"></span>
                <?php esc_html_e( '마스터 권한: 라이센스 발급 도구', 'acf-css-really-simple-style-management-center' ); ?>
            </h4>
            <p class="description">
                <?php esc_html_e( '라이센스 키를 생성, 검증, 관리할 수 있는 발급자(Issuer) 패널로 이동합니다.', 'acf-css-really-simple-style-management-center' ); ?>
            </p>
            <p class="submit" style="margin-bottom: 0;">
                <a href="<?php echo esc_url( admin_url( 'options-general.php?page=jj-license-issuer' ) ); ?>" class="button button-primary">
                    <?php esc_html_e( '라이센스 발급 관리자 이동', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
            </p>
        </div>
        <?php endif; ?>

        <!-- [현재 라이센스 상태 카드] -->
        <div class="jj-license-status-card" style="margin-bottom: 30px; padding: 25px; background: #fff; border: 1px solid #dcdcde; border-left: 4px solid <?php echo $license_status['valid'] ? '#00a32a' : '#d63638'; ?>; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                <div style="flex: 1;">
                    <h4 style="margin-top: 0; margin-bottom: 15px; font-size: 16px;">
                        <?php esc_html_e( '현재 라이센스 상태', 'acf-css-really-simple-style-management-center' ); ?>
                    </h4>
                    
                    <div style="display: grid; grid-template-columns: auto 1fr; gap: 15px; align-items: center;">
                        <!-- 상태 -->
                        <div style="font-weight: 600; color: #646970;"><?php esc_html_e( '활성화 상태:', 'acf-css-really-simple-style-management-center' ); ?></div>
                        <div>
                            <?php if ( $license_status['valid'] ) : ?>
                                <span class="jj-status-badge active" style="background: #00a32a; color: #fff; padding: 4px 10px; border-radius: 4px; font-weight: 600; font-size: 12px;">
                                    <span class="dashicons dashicons-yes" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span>
                                    <?php esc_html_e( '정품 인증됨', 'acf-css-really-simple-style-management-center' ); ?>
                                </span>
                            <?php else : ?>
                                <span class="jj-status-badge inactive" style="background: #d63638; color: #fff; padding: 4px 10px; border-radius: 4px; font-weight: 600; font-size: 12px;">
                                    <span class="dashicons dashicons-warning" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span>
                                    <?php esc_html_e( '인증되지 않음', 'acf-css-really-simple-style-management-center' ); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- 에디션 -->
                        <div style="font-weight: 600; color: #646970;"><?php esc_html_e( '에디션:', 'acf-css-really-simple-style-management-center' ); ?></div>
                        <div>
                            <?php
                            $type_bg = '#e5e5e5'; // Free
                            $type_color = '#50575e';
                            if ( $license_type === 'BASIC' ) { $type_bg = '#2271b1'; $type_color = '#fff'; }
                            elseif ( $license_type === 'PREM' ) { $type_bg = '#0073aa'; $type_color = '#fff'; }
                            elseif ( $license_type === 'UNLIM' ) { $type_bg = '#8b5cf6'; $type_color = '#fff'; }
                            elseif ( $license_type === 'PARTNER' ) { $type_bg = '#0ea5e9'; $type_color = '#fff'; }
                            elseif ( $license_type === 'MASTER' ) { $type_bg = '#c0392b'; $type_color = '#fff'; }
                            ?>
                            <span class="jj-license-type-badge" style="background: <?php echo esc_attr( $type_bg ); ?>; color: <?php echo esc_attr( $type_color ); ?>; padding: 4px 10px; border-radius: 4px; font-weight: 600; font-size: 12px;">
                                <?php echo esc_html( $license_type ); ?>
                            </span>
                        </div>

                        <!-- 만료일 -->
                        <?php if ( isset( $license_status['valid_until'] ) && $license_status['valid_until'] > 0 ) : ?>
                        <div style="font-weight: 600; color: #646970;"><?php esc_html_e( '만료일:', 'acf-css-really-simple-style-management-center' ); ?></div>
                        <div>
                            <?php 
                            $days_left = ceil( ( $license_status['valid_until'] - time() ) / ( 60 * 60 * 24 ) );
                            echo date_i18n( 'Y년 m월 d일', $license_status['valid_until'] );
                            
                            if ( $days_left <= 30 ) {
                                echo ' <span style="color: #d63638; font-weight: bold;">(' . sprintf( __( '%d일 남음', 'acf-css-really-simple-style-management-center' ), $days_left ) . ')</span>';
                            }
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 업그레이드 버튼 -->
                <?php if ( ! $is_partner_or_higher ) : ?>
                <div style="text-align: right;">
                    <a href="<?php echo esc_url( $purchase_url ); ?>" target="_blank" class="button button-primary" style="height: 36px; line-height: 34px; font-size: 14px; padding: 0 20px;">
                        <?php echo esc_html( $action_text ); ?>
                        <span class="dashicons dashicons-external" style="font-size: 16px; margin-left: 4px;"></span>
                    </a>
                    <p class="description" style="margin-top: 8px; font-size: 12px;">
                        <?php esc_html_e( '더 많은 기능을 잠금 해제하세요.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- [라이센스 키 입력 폼] -->
        <div class="jj-license-input-box" style="padding: 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px;">
            <h4 style="margin-top: 0; margin-bottom: 15px; font-size: 15px;"><?php esc_html_e( '라이센스 키 입력', 'acf-css-really-simple-style-management-center' ); ?></h4>
            
            <form id="jj-license-key-form" method="post">
                <div style="display: flex; gap: 10px; max-width: 600px;">
                    <input type="text" 
                           id="jj-license-key-input" 
                           name="license_key" 
                           class="large-text" 
                           value="<?php echo esc_attr( $current_license_key ); ?>" 
                           placeholder="JJ-XXXX-XXXX-XXXX-XXXX"
                           style="font-family: monospace; letter-spacing: 1px; font-size: 14px; padding: 8px 12px; height: 40px;">
                    
                    <button type="submit" class="button button-primary button-large" id="jj-save-license-btn" style="height: 40px; padding: 0 20px;">
                        <?php esc_html_e( '저장 및 검증', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                </div>
                <p class="description" style="margin-top: 8px;">
                    <?php esc_html_e( '구매 확인 이메일에 포함된 라이센스 키를 입력하세요.', 'acf-css-really-simple-style-management-center' ); ?>
                </p>
                
                <!-- 검증 결과 메시지 -->
                <div id="jj-license-message" style="margin-top: 15px; padding: 12px; border-radius: 4px; display: none;"></div>
            </form>
        </div>

        <?php if ( ! empty( $current_license_key ) ) : ?>
        <!-- [초기화 버튼] -->
        <div style="margin-top: 20px; text-align: right;">
            <button type="button" class="button button-link-delete" id="jj-remove-license-btn">
                <?php esc_html_e( '라이센스 키 제거 및 초기화', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
        </div>
        <?php endif; ?>
    </div>
    <?php } ?>
</div>
