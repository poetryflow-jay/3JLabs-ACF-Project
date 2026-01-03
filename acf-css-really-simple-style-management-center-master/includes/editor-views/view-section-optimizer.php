<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AI CSS Optimizer Section
 * 
 * @package ACF_CSS_Manager
 * @version 22.1.4
 */

if ( ! class_exists( 'JJ_CSS_Optimizer_AI' ) ) {
    echo '<div class="notice notice-error"><p>' . esc_html__( 'CSS Optimizer AI가 로드되지 않았습니다.', 'acf-css-really-simple-style-management-center' ) . '</p></div>';
    return;
}

$optimizer = JJ_CSS_Optimizer_AI::instance();

// Get current CSS
$settings = get_option( 'jj_style_guide_settings', array() );
$current_css = isset( $settings['custom_css'] ) ? $settings['custom_css'] : '';

// Analyze if CSS exists
$stats = null;
$suggestions = array();

if ( ! empty( $current_css ) ) {
    $stats = $optimizer->get_optimization_stats( $current_css );
    $suggestions = $stats['suggestions'];
}
?>

<div class="jj-optimizer-section">
    <h2>🚀 <?php esc_html_e( 'AI CSS Performance Optimizer', 'acf-css-really-simple-style-management-center' ); ?></h2>
    <p class="description">
        <?php esc_html_e( 'CSS를 분석하여 중복 제거, 최적화 제안, 압축을 자동으로 수행합니다.', 'acf-css-really-simple-style-management-center' ); ?>
    </p>

    <style>
        .jj-optimizer-section {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .jj-optimizer-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin: 20px 0;
        }
        .jj-stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        .jj-stat-box h3 {
            margin: 0 0 8px 0;
            font-size: 32px;
            font-weight: 700;
        }
        .jj-stat-box p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .jj-suggestions-list {
            margin: 20px 0;
        }
        .jj-suggestion-item {
            background: #f9fafb;
            border-left: 4px solid #3b82f6;
            padding: 16px;
            margin-bottom: 12px;
            border-radius: 4px;
        }
        .jj-suggestion-item.severity-high {
            border-left-color: #ef4444;
            background: #fef2f2;
        }
        .jj-suggestion-item.severity-medium {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }
        .jj-suggestion-item.severity-low {
            border-left-color: #10b981;
            background: #f0fdf4;
        }
        .jj-suggestion-item h4 {
            margin: 0 0 8px 0;
            font-size: 16px;
            font-weight: 600;
        }
        .jj-suggestion-item p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }
        .jj-optimizer-actions {
            margin-top: 20px;
        }
    </style>

    <?php if ( empty( $current_css ) ) : ?>
        <div class="notice notice-info inline">
            <p><?php esc_html_e( 'CSS가 없습니다. 스타일을 추가한 후 최적화를 실행하세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
        </div>
    <?php else : ?>
        
        <div class="jj-optimizer-stats">
            <div class="jj-stat-box">
                <h3><?php echo esc_html( number_format( $stats['original_size'] ) ); ?></h3>
                <p><?php esc_html_e( '원본 크기 (bytes)', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>
            
            <div class="jj-stat-box">
                <h3><?php echo esc_html( number_format( $stats['optimized_size'] ) ); ?></h3>
                <p><?php esc_html_e( '최적화 후 크기', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>
            
            <div class="jj-stat-box">
                <h3><?php echo esc_html( $stats['savings_percent'] ); ?>%</h3>
                <p><?php esc_html_e( '압축률', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>
            
            <div class="jj-stat-box">
                <h3><?php echo esc_html( $stats['suggestions_count'] ); ?></h3>
                <p><?php esc_html_e( '최적화 제안', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>
        </div>

        <?php if ( ! empty( $suggestions ) ) : ?>
            <div class="jj-suggestions-list">
                <h3><?php esc_html_e( '💡 최적화 제안', 'acf-css-really-simple-style-management-center' ); ?></h3>
                
                <?php foreach ( $suggestions as $suggestion ) : ?>
                    <div class="jj-suggestion-item severity-<?php echo esc_attr( $suggestion['severity'] ); ?>">
                        <h4><?php echo esc_html( $suggestion['message'] ); ?></h4>
                        
                        <?php if ( isset( $suggestion['details'] ) && ! empty( $suggestion['details'] ) ) : ?>
                            <p><?php echo esc_html( sprintf( __( '세부사항: %d개 항목 발견', 'acf-css-really-simple-style-management-center' ), count( $suggestion['details'] ) ) ); ?></p>
                        <?php endif; ?>
                        
                        <?php if ( isset( $suggestion['savings'] ) ) : ?>
                            <p><strong><?php echo esc_html( sprintf( __( '절약 가능: %s KB', 'acf-css-really-simple-style-management-center' ), $suggestion['savings']['kb'] ) ); ?></strong></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="notice notice-success inline">
                <p>✓ <?php esc_html_e( 'CSS가 이미 최적화되어 있습니다!', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>
        <?php endif; ?>

        <div class="jj-optimizer-actions">
            <button type="button" class="button button-primary" id="jj-apply-optimization">
                <?php esc_html_e( '자동 최적화 적용', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
            <button type="button" class="button" id="jj-download-optimized">
                <?php esc_html_e( '최적화된 CSS 다운로드', 'acf-css-really-simple-style-management-center' ); ?>
            </button>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#jj-apply-optimization').on('click', function() {
                if (!confirm('<?php esc_html_e( '현재 CSS를 최적화된 버전으로 교체하시겠습니까?', 'acf-css-really-simple-style-management-center' ); ?>')) {
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).text('<?php esc_html_e( '최적화 중...', 'acf-css-really-simple-style-management-center' ); ?>');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'jj_apply_css_optimization',
                        security: '<?php echo wp_create_nonce( 'jj_optimizer_nonce' ); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            location.reload();
                        } else {
                            alert(response.data.message);
                            $btn.prop('disabled', false).text('<?php esc_html_e( '자동 최적화 적용', 'acf-css-really-simple-style-management-center' ); ?>');
                        }
                    }
                });
            });

            $('#jj-download-optimized').on('click', function() {
                var optimizedCSS = <?php echo json_encode( $optimizer->optimize( $current_css ) ); ?>;
                var blob = new Blob([optimizedCSS], { type: 'text/css' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'optimized-styles.css';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });
        });
        </script>

    <?php endif; ?>
</div>
