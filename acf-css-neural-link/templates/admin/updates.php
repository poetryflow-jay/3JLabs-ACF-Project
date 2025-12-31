<?php
/**
 * 플러그인 업데이트 관리 페이지 템플릿
 * 
 * @package JJ_License_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$admin = JJ_License_Admin::instance();
$uploaded_plugins = $admin->get_uploaded_plugins();
?>

<div class="wrap">
    <h1><?php esc_html_e( '플러그인 업데이트 관리', 'acf-css-really-simple-style-management-center' ); ?></h1>
    
    <div class="jj-license-updates">
        <div class="jj-license-section">
            <h2><?php esc_html_e( '플러그인 파일 업로드', 'acf-css-really-simple-style-management-center' ); ?></h2>
            <p><?php esc_html_e( '새 버전의 플러그인 ZIP 파일을 업로드하여 자동 업데이트를 제공할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
            
            <form method="post" enctype="multipart/form-data" class="jj-upload-form">
                <?php wp_nonce_field( 'jj_upload_plugin' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="plugin_slug"><?php esc_html_e( '플러그인 슬러그', 'acf-css-really-simple-style-management-center' ); ?></label>
                        </th>
                        <td>
                            <select name="plugin_slug" id="plugin_slug" required>
                                <option value=""><?php esc_html_e( '선택하세요', 'acf-css-really-simple-style-management-center' ); ?></option>
                                <option value="acf-css-really-simple-style-management-center"><?php esc_html_e( 'ACF CSS: Advanced Custom Fonts&Colors&Styles Setting - Really Simple WordPress Style Management Center (Free)', 'acf-css-really-simple-style-management-center' ); ?></option>
                                <option value="acf-css-really-simple-style-management-center-pro"><?php esc_html_e( 'ACF CSS: Advanced Custom Fonts&Colors&Styles Setting - Really Simple WordPress Style Management Center - Pro (Basic/Premium/Unlimited)', 'acf-css-really-simple-style-management-center' ); ?></option>
                                <option value="acf-css-really-simple-style-management-center-developer-partner"><?php esc_html_e( 'ACF CSS: Advanced Custom Fonts&Colors&Styles Setting - Really Simple WordPress Style Management Center - Developer Partner Version', 'acf-css-really-simple-style-management-center' ); ?></option>
                                <option value="acf-css-really-simple-style-management-center-master"><?php esc_html_e( 'ACF CSS: Advanced Custom Fonts&Colors&Styles Setting - Really Simple WordPress Style Management Center - Master Version', 'acf-css-really-simple-style-management-center' ); ?></option>
                            </select>
                            <p class="description"><?php esc_html_e( '업데이트할 플러그인을 선택하세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="version"><?php esc_html_e( '버전', 'acf-css-really-simple-style-management-center' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="version" id="version" class="regular-text" placeholder="예: 5.1.7" required pattern="\d+\.\d+\.\d+">
                            <p class="description"><?php esc_html_e( '버전 번호를 입력하세요 (예: 5.1.7).', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="plugin_file"><?php esc_html_e( '플러그인 ZIP 파일', 'acf-css-really-simple-style-management-center' ); ?></label>
                        </th>
                        <td>
                            <input type="file" name="plugin_file" id="plugin_file" accept=".zip" required>
                            <p class="description"><?php esc_html_e( '플러그인 ZIP 파일을 선택하세요. 파일명은 자동으로 생성됩니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="jj_upload_plugin" class="button button-primary" value="<?php esc_attr_e( '업로드', 'acf-css-really-simple-style-management-center' ); ?>">
                </p>
            </form>
        </div>
        
        <div class="jj-license-section">
            <h2><?php esc_html_e( '업로드된 플러그인 파일', 'acf-css-really-simple-style-management-center' ); ?></h2>
            
            <?php if ( empty( $uploaded_plugins ) ) : ?>
                <p><?php esc_html_e( '업로드된 플러그인 파일이 없습니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
            <?php else : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th scope="col"><?php esc_html_e( '플러그인', 'acf-css-really-simple-style-management-center' ); ?></th>
                            <th scope="col"><?php esc_html_e( '버전', 'acf-css-really-simple-style-management-center' ); ?></th>
                            <th scope="col"><?php esc_html_e( '파일 크기', 'acf-css-really-simple-style-management-center' ); ?></th>
                            <th scope="col"><?php esc_html_e( '업로드 날짜', 'acf-css-really-simple-style-management-center' ); ?></th>
                            <th scope="col"><?php esc_html_e( '작업', 'acf-css-really-simple-style-management-center' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $uploaded_plugins as $plugin ) : ?>
                            <tr>
                                <td><?php echo esc_html( $plugin['plugin_slug'] ); ?></td>
                                <td><strong><?php echo esc_html( $plugin['version'] ); ?></strong></td>
                                <td><?php echo esc_html( size_format( $plugin['file_size'] ) ); ?></td>
                                <td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $plugin['upload_date'] ) ); ?></td>
                                <td>
                                    <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=jj-license-updates&action=delete_plugin&plugin_file=' . urlencode( $plugin['file_name'] ) ), 'delete_plugin_' . $plugin['file_name'] ) ); ?>" class="button button-small" onclick="return confirm('<?php esc_attr_e( '정말로 이 파일을 삭제하시겠습니까?', 'acf-css-really-simple-style-management-center' ); ?>');">
                                        <?php esc_html_e( '삭제', 'acf-css-really-simple-style-management-center' ); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="jj-license-section">
            <h2><?php esc_html_e( '플러그인 버전별 제어', 'acf-css-really-simple-style-management-center' ); ?></h2>
            <p><?php esc_html_e( '각 플러그인 버전의 자동 업데이트 활성화/비활성화를 제어할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
            
            <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th scope="col"><?php esc_html_e( '플러그인 버전', 'acf-css-really-simple-style-management-center' ); ?></th>
                        <th scope="col"><?php esc_html_e( '자동 업데이트 상태', 'acf-css-really-simple-style-management-center' ); ?></th>
                        <th scope="col"><?php esc_html_e( '작업', 'acf-css-really-simple-style-management-center' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $plugin_versions = array(
                        'free' => array(
                            'slug' => 'acf-css-really-simple-style-management-center',
                            'name' => __( 'Free Version', 'acf-css-really-simple-style-management-center' )
                        ),
                        'pro' => array(
                            'slug' => 'acf-css-really-simple-style-management-center-pro',
                            'name' => __( 'Pro Version (Basic/Premium/Unlimited)', 'acf-css-really-simple-style-management-center' )
                        ),
                        'live' => array(
                            'slug' => 'acf-css-really-simple-style-management-center-developer-partner',
                            'name' => __( 'Developer Partner Version', 'acf-css-really-simple-style-management-center' )
                        ),
                        'dev' => array(
                            'slug' => 'acf-css-really-simple-style-management-center-master',
                            'name' => __( 'Master Version', 'acf-css-really-simple-style-management-center' )
                        )
                    );
                    
                    foreach ( $plugin_versions as $key => $version ) :
                        // Pro 버전은 다른 파일명 사용
                        if ( $key === 'pro' ) {
                            $plugin_file = $version['slug'] . '/acf-css-really-simple-style-guide-pro.php';
                        } else {
                            $plugin_file = $version['slug'] . '/acf-css-really-simple-style-guide.php';
                        }
                        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
                        $is_auto_update_enabled = in_array( $plugin_file, $auto_updates, true );
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $version['name'] ); ?></strong></td>
                        <td>
                            <span class="jj-auto-update-status <?php echo $is_auto_update_enabled ? 'enabled' : 'disabled'; ?>">
                                <?php echo $is_auto_update_enabled ? esc_html__( '활성화됨', 'acf-css-really-simple-style-management-center' ) : esc_html__( '비활성화됨', 'acf-css-really-simple-style-management-center' ); ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" 
                                    class="button button-small jj-toggle-plugin-auto-update" 
                                    data-plugin-file="<?php echo esc_attr( $plugin_file ); ?>"
                                    data-plugin-key="<?php echo esc_attr( $key ); ?>"
                                    data-current-state="<?php echo $is_auto_update_enabled ? '1' : '0'; ?>">
                                <?php echo $is_auto_update_enabled ? esc_html__( '비활성화', 'acf-css-really-simple-style-management-center' ) : esc_html__( '활성화', 'acf-css-really-simple-style-management-center' ); ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="jj-license-section">
            <h2><?php esc_html_e( '업데이트 정보', 'acf-css-really-simple-style-management-center' ); ?></h2>
            <p><?php esc_html_e( '플러그인 파일을 업로드하면, 해당 플러그인이 설치된 사이트에서 자동으로 업데이트 알림을 받게 됩니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
            <ul>
                <li><?php esc_html_e( '플러그인 파일명 형식: {plugin-slug}-{version}.zip (예: acf-css-really-simple-style-management-center-basic-5.3.3.zip)', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php esc_html_e( '업로드된 파일은 wp-content/uploads/jj-plugin-updates/ 디렉토리에 저장됩니다.', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php esc_html_e( '라이센스가 유효한 사이트에서만 업데이트를 다운로드할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?></li>
            </ul>
        </div>
    </div>
</div>

