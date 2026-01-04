<?php
/**
 * WP Bulk SEO - Real-time Monitor
 *
 * 실시간 SEO 모니터링 및 알림 시스템
 * - 점수 변화 추적
 * - 급격한 점수 하락 알림
 * - 새로운 이슈 감지
 * - 자동 재분석 스케줄링
 *
 * @package WP_Bulk_SEO
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Realtime_Monitor {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Alert thresholds
     */
    private $thresholds = [
        'score_drop' => 10,      // 10점 이상 하락 시 알림
        'critical_issue' => 1,    // Critical 이슈 발생 시 즉시 알림
        'new_issue' => 5,        // 5개 이상 새 이슈 발생 시 알림
    ];

    /**
     * Get singleton instance
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->load_thresholds();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // 점수 저장 시 모니터링
        add_action('wp_bulk_seo_score_saved', [$this, 'on_score_saved'], 10, 2);

        // 이슈 저장 시 모니터링
        add_action('wp_bulk_seo_issue_detected', [$this, 'on_issue_detected'], 10, 2);

        // 스케줄된 모니터링
        add_action('wp_bulk_seo_daily_monitor', [$this, 'run_daily_monitor']);

        // Cron 등록
        if (!wp_next_scheduled('wp_bulk_seo_daily_monitor')) {
            wp_schedule_event(time(), 'daily', 'wp_bulk_seo_daily_monitor');
        }
    }

    /**
     * Load thresholds from settings
     */
    private function load_thresholds() {
        $settings = get_option('wp_bulk_seo_monitor_settings', []);
        $this->thresholds = wp_parse_args($settings, $this->thresholds);
    }

    /**
     * Handle score saved event
     *
     * @param int $post_id Post ID
     * @param array $score_data Score data
     */
    public function on_score_saved($post_id, $score_data) {
        $current_score = $score_data['overall_score'] ?? 0;

        // Get previous score
        $previous_score = $this->get_previous_score($post_id);

        if ($previous_score !== null) {
            $score_change = $current_score - $previous_score;

            // 급격한 점수 하락 감지
            if ($score_change < -$this->thresholds['score_drop']) {
                $this->trigger_alert('score_drop', [
                    'post_id' => $post_id,
                    'previous_score' => $previous_score,
                    'current_score' => $current_score,
                    'change' => $score_change,
                ]);
            }

            // 점수 개선 추적
            if ($score_change > 5) {
                $this->log_improvement($post_id, $previous_score, $current_score);
            }
        }

        // 점수 히스토리 저장
        $this->save_score_history($post_id, $current_score, $score_data);
    }

    /**
     * Handle issue detected event
     *
     * @param int $post_id Post ID
     * @param array $issue Issue data
     */
    public function on_issue_detected($post_id, $issue) {
        // Critical 이슈 즉시 알림
        if (isset($issue['priority']) && $issue['priority'] === 'P0') {
            $this->trigger_alert('critical_issue', [
                'post_id' => $post_id,
                'issue' => $issue,
            ]);
        }

        // 이슈 카운트 확인
        $issue_count = $this->get_recent_issue_count($post_id, 24); // Last 24 hours
        if ($issue_count >= $this->thresholds['new_issue']) {
            $this->trigger_alert('multiple_issues', [
                'post_id' => $post_id,
                'count' => $issue_count,
            ]);
        }
    }

    /**
     * Get previous score
     */
    private function get_previous_score($post_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_score_history';

        $previous = $wpdb->get_var($wpdb->prepare(
            "SELECT score FROM $table WHERE post_id = %d ORDER BY recorded_at DESC LIMIT 1",
            $post_id
        ));

        return $previous !== null ? (float) $previous : null;
    }

    /**
     * Save score history
     */
    private function save_score_history($post_id, $score, $score_data) {
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_score_history';

        $wpdb->insert($table, [
            'post_id' => $post_id,
            'score' => $score,
            'grade' => $score_data['grade'] ?? '',
            'modules' => wp_json_encode($score_data['modules'] ?? []),
            'recorded_at' => current_time('mysql'),
        ]);
    }

    /**
     * Get recent issue count
     */
    private function get_recent_issue_count($post_id, $hours = 24) {
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_issues';
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE post_id = %d AND detected_at >= %s AND is_resolved = 0",
            $post_id,
            $cutoff
        ));
    }

    /**
     * Trigger alert
     */
    private function trigger_alert($alert_type, $data) {
        $alert = [
            'type' => $alert_type,
            'data' => $data,
            'timestamp' => current_time('mysql'),
            'severity' => $this->get_alert_severity($alert_type),
        ];

        // Save alert
        $this->save_alert($alert);

        // Send notification if enabled
        if ($this->should_send_notification($alert_type)) {
            $this->send_notification($alert);
        }
    }

    /**
     * Get alert severity
     */
    private function get_alert_severity($alert_type) {
        $severities = [
            'score_drop' => 'high',
            'critical_issue' => 'critical',
            'multiple_issues' => 'medium',
        ];

        return $severities[$alert_type] ?? 'low';
    }

    /**
     * Check if notification should be sent
     */
    private function should_send_notification($alert_type) {
        $settings = get_option('wp_bulk_seo_monitor_settings', []);
        $notifications = $settings['notifications'] ?? [];

        return isset($notifications[$alert_type]) && $notifications[$alert_type] === true;
    }

    /**
     * Send notification
     */
    private function send_notification($alert) {
        $settings = get_option('wp_bulk_seo_monitor_settings', []);
        $notification_methods = $settings['notification_methods'] ?? ['email'];

        foreach ($notification_methods as $method) {
            switch ($method) {
                case 'email':
                    $this->send_email_notification($alert);
                    break;
                case 'dashboard':
                    $this->create_dashboard_notification($alert);
                    break;
                case 'webhook':
                    $this->send_webhook_notification($alert);
                    break;
            }
        }
    }

    /**
     * Send email notification
     */
    private function send_email_notification($alert) {
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');

        $subject = sprintf(
            '[%s] SEO Alert: %s',
            $site_name,
            $this->get_alert_title($alert)
        );

        $message = $this->format_alert_message($alert);

        wp_mail($admin_email, $subject, $message);
    }

    /**
     * Create dashboard notification
     */
    private function create_dashboard_notification($alert) {
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_notifications';

        $wpdb->insert($table, [
            'alert_type' => $alert['type'],
            'severity' => $alert['severity'],
            'data' => wp_json_encode($alert['data']),
            'message' => $this->get_alert_message($alert),
            'is_read' => 0,
            'created_at' => current_time('mysql'),
        ]);
    }

    /**
     * Send webhook notification
     */
    private function send_webhook_notification($alert) {
        $webhook_url = get_option('wp_bulk_seo_webhook_url', '');
        if (empty($webhook_url)) return;

        wp_remote_post($webhook_url, [
            'body' => wp_json_encode($alert),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout' => 10,
        ]);
    }

    /**
     * Get alert title
     */
    private function get_alert_title($alert) {
        $titles = [
            'score_drop' => '점수 급락 감지',
            'critical_issue' => 'Critical 이슈 발생',
            'multiple_issues' => '다중 이슈 발생',
        ];

        return $titles[$alert['type']] ?? 'SEO 알림';
    }

    /**
     * Get alert message
     */
    private function get_alert_message($alert) {
        $data = $alert['data'];
        $post_id = $data['post_id'] ?? 0;
        $post_title = $post_id ? get_the_title($post_id) : 'Unknown';

        switch ($alert['type']) {
            case 'score_drop':
                return sprintf(
                    '포스트 "%s"의 SEO 점수가 %d점에서 %d점으로 %d점 하락했습니다.',
                    $post_title,
                    $data['previous_score'],
                    $data['current_score'],
                    abs($data['change'])
                );

            case 'critical_issue':
                return sprintf(
                    '포스트 "%s"에 Critical 이슈가 감지되었습니다: %s',
                    $post_title,
                    $data['issue']['label_kr'] ?? $data['issue']['label'] ?? 'Unknown'
                );

            case 'multiple_issues':
                return sprintf(
                    '포스트 "%s"에 최근 24시간 내 %d개의 새 이슈가 발생했습니다.',
                    $post_title,
                    $data['count']
                );

            default:
                return 'SEO 알림이 발생했습니다.';
        }
    }

    /**
     * Format alert message for email
     */
    private function format_alert_message($alert) {
        $message = $this->get_alert_message($alert);
        $data = $alert['data'];
        $post_id = $data['post_id'] ?? 0;

        $html = '<html><body>';
        $html .= '<h2>' . esc_html($this->get_alert_title($alert)) . '</h2>';
        $html .= '<p>' . esc_html($message) . '</p>';

        if ($post_id) {
            $edit_link = admin_url('post.php?post=' . $post_id . '&action=edit');
            $html .= '<p><a href="' . esc_url($edit_link) . '">포스트 편집하기</a></p>';
        }

        $html .= '<hr>';
        $html .= '<p><small>이 알림은 WP Bulk SEO 플러그인에서 자동으로 전송되었습니다.</small></p>';
        $html .= '</body></html>';

        return $html;
    }

    /**
     * Save alert
     */
    private function save_alert($alert) {
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_alerts';

        $wpdb->insert($table, [
            'alert_type' => $alert['type'],
            'severity' => $alert['severity'],
            'data' => wp_json_encode($alert['data']),
            'created_at' => $alert['timestamp'],
        ]);
    }

    /**
     * Log improvement
     */
    private function log_improvement($post_id, $previous_score, $current_score) {
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_improvements';

        $wpdb->insert($table, [
            'post_id' => $post_id,
            'previous_score' => $previous_score,
            'new_score' => $current_score,
            'improvement' => $current_score - $previous_score,
            'recorded_at' => current_time('mysql'),
        ]);
    }

    /**
     * Run daily monitor
     */
    public function run_daily_monitor() {
        // Check for stale analyses
        $this->check_stale_analyses();

        // Check for trending issues
        $this->check_trending_issues();

        // Generate daily report
        $this->generate_daily_report();
    }

    /**
     * Check for stale analyses
     */
    private function check_stale_analyses() {
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_scores';
        $cutoff = date('Y-m-d H:i:s', strtotime('-30 days'));

        $stale_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE analyzed_at < %s",
            $cutoff
        ));

        if ($stale_count > 0) {
            $this->trigger_alert('stale_analyses', [
                'count' => $stale_count,
                'message' => sprintf('%d개의 포스트가 30일 이상 분석되지 않았습니다.', $stale_count),
            ]);
        }
    }

    /**
     * Check trending issues
     */
    private function check_trending_issues() {
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_issues';
        $cutoff = date('Y-m-d H:i:s', strtotime('-7 days'));

        $trending = $wpdb->get_results($wpdb->prepare(
            "SELECT issue_type, COUNT(*) as count 
             FROM $table 
             WHERE detected_at >= %s AND is_resolved = 0 
             GROUP BY issue_type 
             ORDER BY count DESC 
             LIMIT 5",
            $cutoff
        ), ARRAY_A);

        if (!empty($trending)) {
            $this->trigger_alert('trending_issues', [
                'trending' => $trending,
            ]);
        }
    }

    /**
     * Generate daily report
     */
    private function generate_daily_report() {
        $stats = $this->get_daily_stats();
        $report = [
            'date' => current_time('Y-m-d'),
            'stats' => $stats,
            'summary' => $this->generate_summary($stats),
        ];

        // Save report
        update_option('wp_bulk_seo_daily_report_' . current_time('Y-m-d'), $report);

        // Send if enabled
        if (get_option('wp_bulk_seo_send_daily_report', false)) {
            $this->send_daily_report($report);
        }
    }

    /**
     * Get daily stats
     */
    private function get_daily_stats() {
        global $wpdb;
        $scores_table = $wpdb->prefix . 'wp_bulk_seo_scores';
        $issues_table = $wpdb->prefix . 'wp_bulk_seo_issues';
        $today = current_time('Y-m-d');

        return [
            'analyzed_today' => (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $scores_table WHERE DATE(analyzed_at) = %s",
                $today
            )),
            'new_issues' => (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $issues_table WHERE DATE(detected_at) = %s",
                $today
            )),
            'resolved_issues' => (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $issues_table WHERE DATE(resolved_at) = %s",
                $today
            )),
            'avg_score' => (float) $wpdb->get_var("SELECT AVG(overall_score) FROM $scores_table"),
        ];
    }

    /**
     * Generate summary
     */
    private function generate_summary($stats) {
        $summary = [];

        if ($stats['analyzed_today'] > 0) {
            $summary[] = sprintf('오늘 %d개의 콘텐츠가 분석되었습니다.', $stats['analyzed_today']);
        }

        if ($stats['new_issues'] > 0) {
            $summary[] = sprintf('%d개의 새 이슈가 감지되었습니다.', $stats['new_issues']);
        }

        if ($stats['resolved_issues'] > 0) {
            $summary[] = sprintf('%d개의 이슈가 해결되었습니다.', $stats['resolved_issues']);
        }

        $summary[] = sprintf('현재 평균 SEO 점수: %.1f점', $stats['avg_score']);

        return implode(' ', $summary);
    }

    /**
     * Send daily report
     */
    private function send_daily_report($report) {
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');

        $subject = sprintf('[%s] SEO 일일 리포트 - %s', $site_name, $report['date']);

        $message = '<html><body>';
        $message .= '<h2>SEO 일일 리포트</h2>';
        $message .= '<p>' . esc_html($report['summary']) . '</p>';
        $message .= '<h3>상세 통계</h3>';
        $message .= '<ul>';
        $message .= '<li>분석된 콘텐츠: ' . $report['stats']['analyzed_today'] . '개</li>';
        $message .= '<li>새 이슈: ' . $report['stats']['new_issues'] . '개</li>';
        $message .= '<li>해결된 이슈: ' . $report['stats']['resolved_issues'] . '개</li>';
        $message .= '<li>평균 점수: ' . round($report['stats']['avg_score'], 1) . '점</li>';
        $message .= '</ul>';
        $message .= '<p><a href="' . admin_url('admin.php?page=wp-bulk-seo') . '">대시보드 보기</a></p>';
        $message .= '</body></html>';

        wp_mail($admin_email, $subject, $message, ['Content-Type: text/html; charset=UTF-8']);
    }

    /**
     * Get notifications for current user
     */
    public function get_notifications($limit = 10) {
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_notifications';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE is_read = 0 ORDER BY created_at DESC LIMIT %d",
            $limit
        ), ARRAY_A);
    }

    /**
     * Mark notification as read
     */
    public function mark_notification_read($notification_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_notifications';

        $wpdb->update($table, ['is_read' => 1], ['id' => $notification_id]);
    }
}
