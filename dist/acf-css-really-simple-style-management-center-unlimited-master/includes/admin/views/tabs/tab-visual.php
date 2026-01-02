<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$visual_options = get_option( 'jj_style_guide_visual_options', array() );

// 기본값 설정
$login_logo_url = isset( $visual_options['login_logo_url'] ) ? $visual_options['login_logo_url'] : '';
$login_bg_color = isset( $visual_options['login_bg_color'] ) ? $visual_options['login_bg_color'] : '#f0f0f1';
$login_form_bg_color = isset( $visual_options['login_form_bg_color'] ) ? $visual_options['login_form_bg_color'] : '#ffffff';
$login_button_color = isset( $visual_options['login_button_color'] ) ? $visual_options['login_button_color'] : '#2271b1';

$admin_theme_mode = isset( $visual_options['admin_theme_mode'] ) ? $visual_options['admin_theme_mode'] : 'default';
$admin_accent_color = isset( $visual_options['admin_accent_color'] ) ? $visual_options['admin_accent_color'] : '#2271b1';
?>
<div class="jj-admin-center-tab-content" data-tab="visual">
    <div class="jj-admin-center-general-form">
        <h3><?php esc_html_e( '비주얼 커맨드 센터', 'acf-css-really-simple-style-management-center' ); ?></h3>
        <p class="description">
            <?php esc_html_e( '로그인 화면과 관리자 테마를 시각적으로 커스터마이징합니다.', 'acf-css-really-simple-style-management-center' ); ?>
        </p>

        <div class="jj-visual-section" style="margin-top: 30px;">
            <h4><?php esc_html_e( '🔐 로그인 화면 커스터마이징', 'acf-css-really-simple-style-management-center' ); ?></h4>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="visual_options_login_logo_url"><?php esc_html_e( '커스텀 로고 URL', 'acf-css-really-simple-style-management-center' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="visual_options_login_logo_url" name="visual_options[login_logo_url]" value="<?php echo esc_url( $login_logo_url ); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e( '로그인 화면의 워드프레스 로고를 대체할 이미지 URL을 입력하세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php esc_html_e( '배경 및 폼 색상', 'acf-css-really-simple-style-management-center' ); ?></label>
                        </th>
                        <td>
                            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                                <div class="jj-admin-center-color-input">
                                    <span style="font-size: 12px;"><?php esc_html_e( '배경색', 'acf-css-really-simple-style-management-center' ); ?></span>
                                    <input type="text" name="visual_options[login_bg_color]" value="<?php echo esc_attr( $login_bg_color ); ?>" class="jj-color-picker">
                                </div>
                                <div class="jj-admin-center-color-input">
                                    <span style="font-size: 12px;"><?php esc_html_e( '폼 배경색', 'acf-css-really-simple-style-management-center' ); ?></span>
                                    <input type="text" name="visual_options[login_form_bg_color]" value="<?php echo esc_attr( $login_form_bg_color ); ?>" class="jj-color-picker">
                                </div>
                                <div class="jj-admin-center-color-input">
                                    <span style="font-size: 12px;"><?php esc_html_e( '버튼색', 'acf-css-really-simple-style-management-center' ); ?></span>
                                    <input type="text" name="visual_options[login_button_color]" value="<?php echo esc_attr( $login_button_color ); ?>" class="jj-color-picker">
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <hr style="margin: 30px 0;">

        <div class="jj-visual-section">
            <h4><?php esc_html_e( '🎨 관리자 테마 설정', 'acf-css-really-simple-style-management-center' ); ?></h4>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label><?php esc_html_e( '테마 모드', 'acf-css-really-simple-style-management-center' ); ?></label>
                        </th>
                        <td>
                            <label style="margin-right: 15px;">
                                <input type="radio" name="visual_options[admin_theme_mode]" value="default" <?php checked( $admin_theme_mode, 'default' ); ?>>
                                <?php esc_html_e( '기본 (Light)', 'acf-css-really-simple-style-management-center' ); ?>
                            </label>
                            <label>
                                <input type="radio" name="visual_options[admin_theme_mode]" value="dark" <?php checked( $admin_theme_mode, 'dark' ); ?>>
                                <?php esc_html_e( '다크 모드 (Dark)', 'acf-css-really-simple-style-management-center' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php esc_html_e( '강조 색상 (Accent)', 'acf-css-really-simple-style-management-center' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="visual_options[admin_accent_color]" value="<?php echo esc_attr( $admin_accent_color ); ?>" class="jj-color-picker">
                            <p class="description"><?php esc_html_e( '관리자 메뉴 및 주요 버튼의 강조 색상을 변경합니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
