<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="jj-admin-center-tab-content" data-tab="cloud">
    <div class="jj-admin-center-general-form">
        <h3><?php esc_html_e( 'JJ Cloud Ecosystem', 'jj-style-guide' ); ?></h3>
        <p class="description">
            <?php esc_html_e( '스타일 설정을 클라우드에 저장하고, 다른 사이트에서 코드로 불러오세요.', 'jj-style-guide' ); ?>
        </p>

        <?php
        $has_cloud_access = class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ); // Premium 이상
        ?>

        <?php if ( $has_cloud_access ) : ?>
            <div class="jj-cloud-box" style="background: #f0f6fc; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                <h4><?php esc_html_e( '☁️ 클라우드에 저장 (Export)', 'jj-style-guide' ); ?></h4>
                <p><?php esc_html_e( '현재 설정을 클라우드에 안전하게 백업합니다.', 'jj-style-guide' ); ?></p>
                <button type="button" class="button button-primary" id="jj-btn-cloud-export">
                    <?php esc_html_e( '지금 저장하기', 'jj-style-guide' ); ?>
                </button>
                <div id="jj-cloud-export-result" style="margin-top: 10px; display: none;">
                    <p><strong><?php esc_html_e( '공유 코드:', 'jj-style-guide' ); ?></strong> <span class="jj-code-box" style="background: #fff; padding: 5px; border: 1px solid #ddd; font-family: monospace; font-size: 1.2em;"></span></p>
                    <p class="description"><?php esc_html_e( '이 코드를 복사하여 다른 사이트에서 사용하세요.', 'jj-style-guide' ); ?></p>
                </div>
            </div>

            <div class="jj-cloud-box" style="background: #fff; border: 1px solid #ddd; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                <h4><?php esc_html_e( '📥 클라우드에서 불러오기 (Import)', 'jj-style-guide' ); ?></h4>
                <p><?php esc_html_e( '공유 코드를 입력하여 설정을 즉시 적용합니다.', 'jj-style-guide' ); ?></p>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="jj-cloud-share-code" class="regular-text" placeholder="CODE" style="text-transform: uppercase;" />
                    <button type="button" class="button button-secondary" id="jj-btn-cloud-import">
                        <?php esc_html_e( '불러오기', 'jj-style-guide' ); ?>
                    </button>
                </div>
            </div>
            
            <hr style="margin: 30px 0;">
            
            <!-- [Phase 3] 스타일 템플릿 마켓 -->
            <div class="jj-template-market-section" style="margin-top: 30px;">
                <h3><?php esc_html_e( '🎨 스타일 템플릿 마켓', 'jj-style-guide' ); ?></h3>
                <p class="description">
                    <?php esc_html_e( '전문가가 디자인한 스타일 템플릿을 둘러보고 내 사이트에 즉시 적용해보세요.', 'jj-style-guide' ); ?>
                </p>
                
                <div style="margin: 20px 0;">
                    <input type="text" id="jj-template-search" class="regular-text" placeholder="<?php esc_attr_e( '템플릿 검색...', 'jj-style-guide' ); ?>" style="max-width: 300px;" />
                    <select id="jj-template-category" style="margin-left: 10px;">
                        <option value="all"><?php esc_html_e( '전체 카테고리', 'jj-style-guide' ); ?></option>
                        <option value="business"><?php esc_html_e( '비즈니스', 'jj-style-guide' ); ?></option>
                        <option value="cafe"><?php esc_html_e( '카페/레스토랑', 'jj-style-guide' ); ?></option>
                        <option value="tech"><?php esc_html_e( '기술/개발', 'jj-style-guide' ); ?></option>
                    </select>
                    <button type="button" class="button button-secondary" id="jj-template-refresh">
                        <?php esc_html_e( '새로고침', 'jj-style-guide' ); ?>
                    </button>
                </div>
                
                <div id="jj-template-market-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 20px;">
                    <div class="jj-loading" style="text-align: center; padding: 40px; grid-column: 1 / -1;">
                        <span class="spinner is-active"></span>
                        <p><?php esc_html_e( '템플릿을 불러오는 중...', 'jj-style-guide' ); ?></p>
                    </div>
                </div>
                
                <div style="margin-top: 30px; text-align: center; padding: 20px; background: #f0f6fc; border-radius: 4px;">
                    <p style="margin: 0;">
                        <strong><?php esc_html_e( '나만의 스타일을 마켓에 등록하고 수익을 창출해보세요!', 'jj-style-guide' ); ?></strong><br>
                        <button type="button" class="button button-primary" id="jj-template-publish-btn" style="margin-top: 10px;">
                            <?php esc_html_e( '내 스타일 판매하기', 'jj-style-guide' ); ?>
                        </button>
                    </p>
                </div>
            </div>
        <?php else : ?>
            <div class="jj-feature-locked">
                <div class="jj-lock-icon"><span class="dashicons dashicons-cloud"></span></div>
                <h3><?php esc_html_e( 'Premium 기능입니다.', 'jj-style-guide' ); ?></h3>
                <p><?php esc_html_e( '클라우드 동기화 기능을 사용하려면 업그레이드하세요.', 'jj-style-guide' ); ?></p>
                <a href="https://j-j-labs.com" target="_blank" class="button button-primary"><?php esc_html_e( '업그레이드하기', 'jj-style-guide' ); ?></a>
            </div>
        <?php endif; ?>
    </div>
</div>

