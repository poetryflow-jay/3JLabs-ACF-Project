<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="jj-admin-center-tab-content active" data-tab="general">
    <div class="jj-admin-center-general-form">
        <h3><?php esc_html_e( '일반 설정', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <p class="description">
            <?php esc_html_e( '관리자 인터페이스의 기본 표시 옵션을 설정합니다.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>
        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row"><?php esc_html_e( '관리자 메뉴 표시', 'acf-css-really-simple-style-management-center' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="jj_admin_show_menu" value="1" checked />
                        <?php esc_html_e( '좌측 관리자 메뉴 표시', 'acf-css-really-simple-style-management-center' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( '관리자 툴바 표시', 'acf-css-really-simple-style-management-center' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="jj_admin_show_toolbar" value="1" checked />
                        <?php esc_html_e( '상단 관리자 툴바 표시', 'acf-css-really-simple-style-management-center' ); ?>
                    </label>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

