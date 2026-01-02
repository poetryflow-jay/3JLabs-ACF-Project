<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="jj-admin-center-tab-content" data-tab="backup">
    <h3><?php esc_html_e( '백업 및 복원', 'acf-css-really-simple-style-management-center' ); ?></h3>
    <p class="description">
        <?php esc_html_e( '플러그인 설정을 백업하고 복원할 수 있습니다. 자동 백업은 매일, 주간, 월간으로 자동 생성됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
    </p>

    <div class="jj-backup-controls" style="margin-bottom: 25px;">
        <h4><?php esc_html_e( '백업 생성', 'acf-css-really-simple-style-management-center' ); ?></h4>
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" 
                   id="jj-backup-label-input" 
                   placeholder="<?php esc_attr_e( '백업 레이블 (선택사항)', 'acf-css-really-simple-style-management-center' ); ?>"
                   class="regular-text" 
                   style="width: 300px;" />
            <button type="button" class="button button-primary" id="jj-create-backup-btn">
                <?php esc_html_e( '수동 백업 생성', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <span class="spinner jj-backup-spinner" style="float: none; margin-left: 5px; display: none;"></span>
        </div>
        <p class="description" style="margin-top: 10px;">
            <?php esc_html_e( '수동으로 백업을 생성할 수 있습니다. 레이블을 입력하지 않으면 "수동 백업"으로 저장됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>
    </div>

    <div class="jj-backup-settings" style="margin-bottom: 25px; padding: 15px; background: #f9f9f9; border: 1px solid #c3c4c7; border-radius: 4px;">
        <h4><?php esc_html_e( '자동 백업 설정', 'acf-css-really-simple-style-management-center' ); ?></h4>
        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row"><?php esc_html_e( '설정 변경 시 자동 백업', 'acf-css-really-simple-style-management-center' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" 
                               name="jj_auto_backup_on_change" 
                               id="jj-auto-backup-on-change"
                               value="1"
                            <?php checked( get_option( 'jj_style_guide_auto_backup_on_change', false ) ); ?> />
                        <?php esc_html_e( '설정이 변경될 때마다 자동으로 백업 생성', 'acf-css-really-simple-style-management-center' ); ?>
                    </label>
                    <p class="description">
                        <?php esc_html_e( '이 옵션을 활성화하면 스타일 센터나 Admin Center에서 설정을 변경할 때마다 자동으로 백업이 생성됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="jj-backup-list-container">
        <h4><?php esc_html_e( '백업 목록', 'acf-css-really-simple-style-management-center' ); ?></h4>
        <div id="jj-backup-list" style="margin-top: 15px;">
            <div class="jj-backup-list-loading" style="padding: 20px; text-align: center;">
                <span class="spinner is-active"></span>
                <p><?php esc_html_e( '백업 목록을 불러오는 중...', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>
        </div>
    </div>

    <div class="jj-backup-history-container" style="margin-top: 30px;">
        <h4><?php esc_html_e( '변경 이력', 'acf-css-really-simple-style-management-center' ); ?></h4>
        <div id="jj-backup-history" style="margin-top: 15px;">
            <div class="jj-backup-history-loading" style="padding: 20px; text-align: center;">
                <span class="spinner is-active"></span>
                <p><?php esc_html_e( '변경 이력을 불러오는 중...', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>
        </div>
    </div>
</div>

