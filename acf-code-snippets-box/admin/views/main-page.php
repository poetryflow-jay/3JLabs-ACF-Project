<?php
/**
 * ACF Code Snippets Box - 메인 대시보드 페이지
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 통계 가져오기
$total_snippets = wp_count_posts( 'acf_code_snippet' );
$active_count = 0;
$type_counts = array( 'css' => 0, 'js' => 0, 'php' => 0, 'html' => 0 );

$snippets = get_posts( array(
    'post_type'      => 'acf_code_snippet',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
) );

foreach ( $snippets as $snippet ) {
    $is_active = get_post_meta( $snippet->ID, '_acf_csb_active', true );
    $code_type = get_post_meta( $snippet->ID, '_acf_csb_code_type', true ) ?: 'css';
    
    if ( $is_active ) {
        $active_count++;
    }
    
    if ( isset( $type_counts[ $code_type ] ) ) {
        $type_counts[ $code_type ]++;
    }
}
?>
<div class="wrap acf-csb-dashboard">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-editor-code" style="font-size: 30px; width: 30px; height: 30px; margin-right: 10px;"></span>
        <?php esc_html_e( 'ACF Code Snippets Box', 'acf-code-snippets-box' ); ?>
    </h1>
    <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 12px; margin-left: 10px; vertical-align: middle;">
        v<?php echo esc_html( ACF_CSB_VERSION ); ?>
    </span>

    <?php if ( ACF_Code_Snippets_Box::is_acf_css_active() ) : ?>
    <span style="background: #00a32a; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 12px; margin-left: 5px; vertical-align: middle;">
        <?php esc_html_e( 'ACF CSS 연동됨', 'acf-code-snippets-box' ); ?>
    </span>
    <?php endif; ?>

    <hr class="wp-header-end">

    <!-- 통계 카드 -->
    <div class="acf-csb-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0;">
        <div class="acf-csb-stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
            <div style="font-size: 36px; font-weight: bold;"><?php echo esc_html( $total_snippets->publish ); ?></div>
            <div style="opacity: 0.9;"><?php esc_html_e( '총 스니펫', 'acf-code-snippets-box' ); ?></div>
        </div>
        <div class="acf-csb-stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);">
            <div style="font-size: 36px; font-weight: bold;"><?php echo esc_html( $active_count ); ?></div>
            <div style="opacity: 0.9;"><?php esc_html_e( '활성화됨', 'acf-code-snippets-box' ); ?></div>
        </div>
        <div class="acf-csb-stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(240, 147, 251, 0.4);">
            <div style="font-size: 36px; font-weight: bold;"><?php echo esc_html( $type_counts['css'] ); ?></div>
            <div style="opacity: 0.9;"><?php esc_html_e( 'CSS 스니펫', 'acf-code-snippets-box' ); ?></div>
        </div>
        <div class="acf-csb-stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);">
            <div style="font-size: 36px; font-weight: bold;"><?php echo esc_html( $type_counts['js'] ); ?></div>
            <div style="opacity: 0.9;"><?php esc_html_e( 'JS 스니펫', 'acf-code-snippets-box' ); ?></div>
        </div>
        </div>
        
        <div class="acf-csb-stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(240, 87, 118, 0.4);">
            <h3 style="margin: 0;"><?php esc_html_e( '오늘 실행', 'acf-code-snippets-box' ); ?></h3>
            <div style="font-size: 42px; font-weight: 700; margin-bottom: 15px;"><?php echo number_format( get_option( 'acf_csb_executions_today', 0 ) ); ?></div>
            <div class="stat-label" style="opacity: 0.9; font-size: 14px;"><?php esc_html_e( 'PHP 실행 횟수', 'acf-code-snippets-box' ); ?></div>
        </div>
        
        <div class="acf-csb-stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(56, 189, 248, 0.4);">
            <h3 style="margin: 0;"><?php esc_html_e( '가장 많이 사용', 'acf-code-snippets-box' ); ?></h3>
            <div style="font-size: 42px; font-weight: 700; margin-bottom: 15px;">CSS</div>
            <div class="stat-label" style="opacity: 0.9; font-size: 14px;"><?php esc_html_e( '스니펫 유형', 'acf-code-snippets-box' ); ?></div>
        </div>
    </div>
    
    <!-- 빠른 액션 -->
    <div class="acf-csb-quick-actions" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 30px 0;">
        <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=acf_code_snippet' ) ); ?>" class="acf-csb-action-card" style="display: flex; align-items: center; padding: 20px; background: #fff; border-radius: 12px; text-decoration: none; color: #1d2327; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s;">
            <span class="dashicons dashicons-plus-alt2" style="font-size: 40px; width: 40px; height: 40px; color: #667eea; margin-right: 15px;"></span>
            <div>
                <strong style="font-size: 16px;"><?php esc_html_e( '새 스니펫 추가', 'acf-code-snippets-box' ); ?></strong>
                <p style="margin: 5px 0 0; color: #646970; font-size: 13px;"><?php esc_html_e( 'CSS, JS, PHP, HTML 코드를 추가하세요', 'acf-code-snippets-box' ); ?></p>
            </div>
        </a>
        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=acf_code_snippet' ) ); ?>" class="acf-csb-action-card" style="display: flex; align-items: center; padding: 20px; background: #fff; border-radius: 12px; text-decoration: none; color: #1d2327; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s;">
            <span class="dashicons dashicons-list-view" style="font-size: 40px; width: 40px; height: 40px; color: #11998e; margin-right: 15px;"></span>
            <div>
                <strong style="font-size: 16px;"><?php esc_html_e( '모든 스니펫 보기', 'acf-code-snippets-box' ); ?></strong>
                <p style="margin: 5px 0 0; color: #646970; font-size: 13px;"><?php esc_html_e( '스니펫을 관리하고 편집하세요', 'acf-code-snippets-box' ); ?></p>
            </div>
        </a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-code-snippets-presets' ) ); ?>" class="acf-csb-action-card" style="display: flex; align-items: center; padding: 20px; background: #fff; border-radius: 12px; text-decoration: none; color: #1d2327; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s;">
            <span class="dashicons dashicons-welcome-widgets-menus" style="font-size: 40px; width: 40px; height: 40px; color: #f5576c; margin-right: 15px;"></span>
            <div>
                <strong style="font-size: 16px;"><?php esc_html_e( '프리셋 라이브러리', 'acf-code-snippets-box' ); ?></strong>
                <p style="margin: 5px 0 0; color: #646970; font-size: 13px;"><?php esc_html_e( '미리 준비된 유용한 코드 스니펫', 'acf-code-snippets-box' ); ?></p>
            </div>
        </a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-code-snippets-settings' ) ); ?>" class="acf-csb-action-card" style="display: flex; align-items: center; padding: 20px; background: #fff; border-radius: 12px; text-decoration: none; color: #1d2327; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s;">
            <span class="dashicons dashicons-admin-generic" style="font-size: 40px; width: 40px; height: 40px; color: #4facfe; margin-right: 15px;"></span>
            <div>
                <strong style="font-size: 16px;"><?php esc_html_e( '설정', 'acf-code-snippets-box' ); ?></strong>
                <p style="margin: 5px 0 0; color: #646970; font-size: 13px;"><?php esc_html_e( '플러그인 설정을 관리하세요', 'acf-code-snippets-box' ); ?></p>
            </div>
        </a>
    </div>

    <!-- 최근 스니펫 -->
    <div class="acf-csb-recent" style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-top: 30px;">
        <h2 style="margin-top: 0; display: flex; align-items: center;">
            <span class="dashicons dashicons-clock" style="margin-right: 10px;"></span>
            <?php esc_html_e( '최근 스니펫', 'acf-code-snippets-box' ); ?>
        </h2>
        
        <?php
        $recent_snippets = get_posts( array(
            'post_type'      => 'acf_code_snippet',
            'post_status'    => 'publish',
            'posts_per_page' => 5,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        if ( ! empty( $recent_snippets ) ) :
        ?>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( '제목', 'acf-code-snippets-box' ); ?></th>
                    <th style="width: 80px;"><?php esc_html_e( '타입', 'acf-code-snippets-box' ); ?></th>
                    <th style="width: 80px;"><?php esc_html_e( '상태', 'acf-code-snippets-box' ); ?></th>
                    <th style="width: 150px;"><?php esc_html_e( '수정일', 'acf-code-snippets-box' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $recent_snippets as $snippet ) : 
                    $code_type = get_post_meta( $snippet->ID, '_acf_csb_code_type', true ) ?: 'css';
                    $is_active = get_post_meta( $snippet->ID, '_acf_csb_active', true );
                    $type_colors = array(
                        'css'  => '#f5576c',
                        'js'   => '#f0ad4e',
                        'php'  => '#8892bf',
                        'html' => '#e34c26',
                    );
                ?>
                <tr>
                    <td>
                        <a href="<?php echo esc_url( get_edit_post_link( $snippet->ID ) ); ?>">
                            <strong><?php echo esc_html( $snippet->post_title ); ?></strong>
                        </a>
                    </td>
                    <td>
                        <span style="background: <?php echo esc_attr( $type_colors[ $code_type ] ?? '#999' ); ?>; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 11px; text-transform: uppercase;">
                            <?php echo esc_html( strtoupper( $code_type ) ); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ( $is_active ) : ?>
                            <span style="color: #00a32a;">● <?php esc_html_e( '활성', 'acf-code-snippets-box' ); ?></span>
                        <?php else : ?>
                            <span style="color: #999;">○ <?php esc_html_e( '비활성', 'acf-code-snippets-box' ); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo esc_html( get_the_modified_date( 'Y-m-d H:i', $snippet ) ); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else : ?>
        <p style="color: #646970; text-align: center; padding: 40px 0;">
            <?php esc_html_e( '아직 스니펫이 없습니다. 첫 번째 스니펫을 추가해보세요!', 'acf-code-snippets-box' ); ?>
        </p>
        <?php endif; ?>
    </div>

    <!-- 푸터 -->
    <div style="text-align: center; margin-top: 40px; padding: 20px; color: #646970;">
        <p>
            <?php 
            printf(
                esc_html__( 'ACF Code Snippets Box v%s | Made with ❤️ by %s', 'acf-code-snippets-box' ),
                ACF_CSB_VERSION,
                '<a href="https://3j-labs.com" target="_blank">3J Labs (제이x제니x제이슨 연구소)</a>'
            );
            ?>
        </p>
    </div>
</div>

<style>
.acf-csb-action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
}
</style>
