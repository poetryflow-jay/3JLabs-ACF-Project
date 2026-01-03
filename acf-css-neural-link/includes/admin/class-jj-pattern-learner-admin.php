<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Pattern Learner Admin UI
 * 
 * @package ACF_CSS_Neural_Link
 * @version 6.1.0
 */
class JJ_Pattern_Learner_Admin {
    private static $instance = null;
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu' ), 100 );
        add_action( 'wp_ajax_jj_get_pattern_suggestions', array( $this, 'ajax_get_suggestions' ) );
        add_action( 'wp_ajax_jj_reset_patterns', array( $this, 'ajax_reset_patterns' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function add_menu() {
        add_submenu_page(
            'jj-admin-center',
            __( 'AI Pattern Learning', 'acf-css-neural-link' ),
            __( '🧠 AI Learning', 'acf-css-neural-link' ),
            'manage_options',
            'jj-pattern-learner',
            array( $this, 'render_page' )
        );
    }

    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'jj-pattern-learner' ) === false ) {
            return;
        }

        wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', array(), '4.4.0', true );
    }

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-css-neural-link' ) );
        }

        if ( ! class_exists( 'JJ_Pattern_Learner' ) ) {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'Pattern Learner가 로드되지 않았습니다.', 'acf-css-neural-link' ) . '</p></div>';
            return;
        }

        $learner = JJ_Pattern_Learner::instance();
        $stats = $learner->get_stats();
        ?>
        <div class="wrap jj-pattern-learner-admin">
            <h1><?php esc_html_e( 'AI Pattern Learning Dashboard', 'acf-css-neural-link' ); ?></h1>
            <p class="description">
                <?php esc_html_e( '여러분의 스타일 수정 패턴을 자동으로 학습하고 최적화 제안을 제공합니다.', 'acf-css-neural-link' ); ?>
            </p>

            <style>
                .jj-stats-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 20px;
                    margin: 24px 0;
                }
                .jj-stat-card {
                    background: #fff;
                    border: 2px solid #e5e7eb;
                    border-radius: 12px;
                    padding: 24px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                }
                .jj-stat-card h3 {
                    margin: 0 0 8px 0;
                    font-size: 14px;
                    color: #6b7280;
                    font-weight: 600;
                    text-transform: uppercase;
                }
                .jj-stat-card .stat-value {
                    font-size: 36px;
                    font-weight: 700;
                    color: #111827;
                    margin: 8px 0;
                }
                .jj-stat-card .stat-label {
                    font-size: 14px;
                    color: #9ca3af;
                }
                .jj-chart-container {
                    background: #fff;
                    border: 2px solid #e5e7eb;
                    border-radius: 12px;
                    padding: 24px;
                    margin: 24px 0;
                }
                .jj-suggestions-box {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: #fff;
                    border-radius: 12px;
                    padding: 24px;
                    margin: 24px 0;
                }
                .jj-suggestions-box h3 {
                    margin: 0 0 16px 0;
                    font-size: 20px;
                }
                .jj-suggestion-item {
                    background: rgba(255,255,255,0.15);
                    border-radius: 8px;
                    padding: 16px;
                    margin-bottom: 12px;
                    backdrop-filter: blur(10px);
                }
                .jj-suggestion-item strong {
                    display: block;
                    margin-bottom: 8px;
                    font-size: 16px;
                }
                .jj-confidence-badge {
                    display: inline-block;
                    padding: 4px 12px;
                    border-radius: 6px;
                    font-size: 12px;
                    font-weight: 700;
                    margin-left: 8px;
                }
                .jj-confidence-badge.high {
                    background: #10b981;
                }
                .jj-confidence-badge.medium {
                    background: #f59e0b;
                }
                .jj-confidence-badge.low {
                    background: #6b7280;
                }
            </style>

            <div class="jj-stats-grid">
                <div class="jj-stat-card">
                    <h3><?php esc_html_e( 'Total Changes', 'acf-css-neural-link' ); ?></h3>
                    <div class="stat-value"><?php echo esc_html( number_format( $stats['total_changes'] ) ); ?></div>
                    <div class="stat-label"><?php esc_html_e( '전체 수정 횟수', 'acf-css-neural-link' ); ?></div>
                </div>

                <div class="jj-stat-card">
                    <h3><?php esc_html_e( 'Patterns Learned', 'acf-css-neural-link' ); ?></h3>
                    <div class="stat-value"><?php echo esc_html( number_format( $stats['patterns_learned'] ) ); ?></div>
                    <div class="stat-label"><?php esc_html_e( '학습된 패턴', 'acf-css-neural-link' ); ?></div>
                </div>

                <div class="jj-stat-card">
                    <h3><?php esc_html_e( 'Change Types', 'acf-css-neural-link' ); ?></h3>
                    <div class="stat-value"><?php echo esc_html( count( $stats['change_types'] ) ); ?></div>
                    <div class="stat-label"><?php esc_html_e( '수정 유형 수', 'acf-css-neural-link' ); ?></div>
                </div>
            </div>

            <div class="jj-chart-container">
                <h2><?php esc_html_e( 'Most Frequent Change Types', 'acf-css-neural-link' ); ?></h2>
                <canvas id="jj-change-types-chart" width="400" height="200"></canvas>
            </div>

            <div class="jj-suggestions-box">
                <h3>💡 <?php esc_html_e( 'AI 추천 사항', 'acf-css-neural-link' ); ?></h3>
                <div id="jj-suggestions-container">
                    <p><?php esc_html_e( '스타일을 수정하면 AI가 다음 단계를 제안합니다.', 'acf-css-neural-link' ); ?></p>
                </div>
            </div>

            <div style="margin-top: 24px;">
                <button class="button button-secondary" id="jj-reset-patterns">
                    <?php esc_html_e( '학습 데이터 초기화', 'acf-css-neural-link' ); ?>
                </button>
            </div>

            <script>
            jQuery(document).ready(function($) {
                // Chart.js - Change Types
                var ctx = document.getElementById('jj-change-types-chart').getContext('2d');
                var changeTypes = <?php echo json_encode( $stats['most_frequent'] ); ?>;
                
                var labels = Object.keys(changeTypes).map(function(key) {
                    return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                });
                var data = Object.values(changeTypes);
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '<?php esc_html_e( 'Frequency', 'acf-css-neural-link' ); ?>',
                            data: data,
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });

                // Reset patterns
                $('#jj-reset-patterns').on('click', function() {
                    if (!confirm('<?php esc_html_e( '정말 모든 학습 데이터를 초기화하시겠습니까?', 'acf-css-neural-link' ); ?>')) {
                        return;
                    }

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'jj_reset_patterns',
                            security: '<?php echo wp_create_nonce( 'jj_pattern_learner_nonce' ); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert(response.data.message);
                            }
                        }
                    });
                });
            });
            </script>
        </div>
        <?php
    }

    public function ajax_get_suggestions() {
        check_ajax_referer( 'jj_pattern_learner_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-neural-link' ) ) );
        }

        $current_change = isset( $_POST['change_type'] ) ? sanitize_text_field( $_POST['change_type'] ) : '';

        if ( empty( $current_change ) ) {
            wp_send_json_error( array( 'message' => __( '변경 유형이 필요합니다.', 'acf-css-neural-link' ) ) );
        }

        if ( class_exists( 'JJ_Pattern_Learner' ) ) {
            $learner = JJ_Pattern_Learner::instance();
            $suggestions = $learner->get_suggestions( $current_change );

            $formatted = array();
            foreach ( $suggestions as $suggestion ) {
                $formatted[] = $learner->format_suggestion( $suggestion );
            }

            wp_send_json_success( array( 'suggestions' => $formatted ) );
        }

        wp_send_json_error( array( 'message' => __( 'Pattern Learner를 사용할 수 없습니다.', 'acf-css-neural-link' ) ) );
    }

    public function ajax_reset_patterns() {
        check_ajax_referer( 'jj_pattern_learner_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-neural-link' ) ) );
        }

        if ( class_exists( 'JJ_Pattern_Learner' ) ) {
            $learner = JJ_Pattern_Learner::instance();
            $learner->reset_patterns();
            wp_send_json_success( array( 'message' => __( '학습 데이터가 초기화되었습니다.', 'acf-css-neural-link' ) ) );
        }

        wp_send_json_error( array( 'message' => __( 'Pattern Learner를 사용할 수 없습니다.', 'acf-css-neural-link' ) ) );
    }
}
