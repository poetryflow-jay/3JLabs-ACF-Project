<?php
/**
 * Dashboard View with Chart.js Statistics
 *
 * @package ACF_Mail_SMTP
 * @version 2.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$plugin = ACF_Mail_SMTP::get_instance();

// Get statistics
global $wpdb;
$forms_table = $wpdb->prefix . 'acf_mail_smtp_forms';
$submissions_table = $wpdb->prefix . 'acf_mail_smtp_submissions';
$emails_table = $wpdb->prefix . 'acf_mail_smtp_emails';

$total_forms = $wpdb->get_var( "SELECT COUNT(*) FROM $forms_table WHERE status = 'active'" );
$total_submissions = $wpdb->get_var( "SELECT COUNT(*) FROM $submissions_table" );
$total_emails_sent = $wpdb->get_var( "SELECT COUNT(*) FROM $emails_table WHERE status = 'sent'" );
$total_emails_failed = $wpdb->get_var( "SELECT COUNT(*) FROM $emails_table WHERE status = 'failed'" );

// Get submissions per day for the last 30 days
$submissions_by_day = $wpdb->get_results(
    "SELECT DATE(created_at) as date, COUNT(*) as count
    FROM $submissions_table
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date ASC",
    ARRAY_A
);

// Get emails by status for pie chart
$emails_by_status = $wpdb->get_results(
    "SELECT status, COUNT(*) as count
    FROM $emails_table
    GROUP BY status",
    ARRAY_A
);

// Get submissions by form for bar chart
$submissions_by_form = $wpdb->get_results(
    "SELECT f.name, COUNT(s.id) as count
    FROM $forms_table f
    LEFT JOIN $submissions_table s ON f.id = s.form_id
    WHERE f.status = 'active'
    GROUP BY f.id, f.name
    ORDER BY count DESC
    LIMIT 10",
    ARRAY_A
);

// Get hourly distribution for submissions (last 7 days)
$submissions_by_hour = $wpdb->get_results(
    "SELECT HOUR(created_at) as hour, COUNT(*) as count
    FROM $submissions_table
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY HOUR(created_at)
    ORDER BY hour ASC",
    ARRAY_A
);

// Prepare chart data
$chart_labels_30_days = array();
$chart_data_30_days = array();
$date_counts = array();

foreach ( $submissions_by_day as $row ) {
    $date_counts[ $row['date'] ] = (int) $row['count'];
}

for ( $i = 29; $i >= 0; $i-- ) {
    $date = date( 'Y-m-d', strtotime( "-$i days" ) );
    $chart_labels_30_days[] = date( 'm/d', strtotime( $date ) );
    $chart_data_30_days[] = isset( $date_counts[ $date ] ) ? $date_counts[ $date ] : 0;
}

// Email status data
$email_status_labels = array();
$email_status_data = array();
$email_status_colors = array();

$status_color_map = array(
    'sent' => '#10b981',
    'failed' => '#ef4444',
    'pending' => '#f59e0b',
    'queued' => '#6366f1',
);

foreach ( $emails_by_status as $row ) {
    $email_status_labels[] = ucfirst( $row['status'] );
    $email_status_data[] = (int) $row['count'];
    $email_status_colors[] = isset( $status_color_map[ $row['status'] ] ) ? $status_color_map[ $row['status'] ] : '#9ca3af';
}

// Form submission data
$form_labels = array();
$form_data = array();

foreach ( $submissions_by_form as $row ) {
    $form_labels[] = mb_strimwidth( $row['name'], 0, 15, '...' );
    $form_data[] = (int) $row['count'];
}

// Hourly data
$hourly_data = array_fill( 0, 24, 0 );
foreach ( $submissions_by_hour as $row ) {
    $hourly_data[ (int) $row['hour'] ] = (int) $row['count'];
}
$hourly_labels = array();
for ( $h = 0; $h < 24; $h++ ) {
    $hourly_labels[] = sprintf( '%02d:00', $h );
}

// Recent submissions
$recent_submissions = $wpdb->get_results(
    "SELECT s.*, f.name as form_name
    FROM $submissions_table s
    LEFT JOIN $forms_table f ON s.form_id = f.id
    ORDER BY s.created_at DESC
    LIMIT 10",
    ARRAY_A
);

// Recent emails
$recent_emails = $wpdb->get_results(
    "SELECT * FROM $emails_table
    ORDER BY created_at DESC
    LIMIT 10",
    ARRAY_A
);

// Calculate growth (compared to previous 30 days)
$prev_submissions = $wpdb->get_var(
    "SELECT COUNT(*) FROM $submissions_table
    WHERE created_at BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND DATE_SUB(NOW(), INTERVAL 30 DAY)"
);
$curr_submissions = $wpdb->get_var(
    "SELECT COUNT(*) FROM $submissions_table
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
);

$growth_rate = $prev_submissions > 0 ? round( ( ( $curr_submissions - $prev_submissions ) / $prev_submissions ) * 100, 1 ) : 0;
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<div class="wrap acf-mail-smtp-dashboard-v25">
    <h1 class="wp-bulk-seo-header-v25">
        📊 <?php esc_html_e( 'ACF Mail SMTP 대시보드', 'acf-mail-smtp' ); ?>
    </h1>

    <!-- Statistics Cards -->
    <div class="acf-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-top: 30px;">
        <div class="acf-stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 24px; color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; font-size: 80px; opacity: 0.15;">📋</div>
            <div style="font-size: 42px; font-weight: 700; margin-bottom: 8px;"><?php echo esc_html( $total_forms ); ?></div>
            <div style="font-size: 14px; opacity: 0.9;"><?php esc_html_e( '활성 폼', 'acf-mail-smtp' ); ?></div>
        </div>
        <div class="acf-stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 16px; padding: 24px; color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; font-size: 80px; opacity: 0.15;">📝</div>
            <div style="font-size: 42px; font-weight: 700; margin-bottom: 8px;"><?php echo esc_html( $total_submissions ); ?></div>
            <div style="font-size: 14px; opacity: 0.9;">
                <?php esc_html_e( '총 제출', 'acf-mail-smtp' ); ?>
                <?php if ( $growth_rate != 0 ) : ?>
                    <span style="margin-left: 8px; padding: 2px 8px; background: rgba(255,255,255,0.2); border-radius: 12px; font-size: 12px;">
                        <?php echo $growth_rate > 0 ? '↑' : '↓'; ?> <?php echo abs( $growth_rate ); ?>%
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="acf-stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 16px; padding: 24px; color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; font-size: 80px; opacity: 0.15;">✉️</div>
            <div style="font-size: 42px; font-weight: 700; margin-bottom: 8px;"><?php echo esc_html( $total_emails_sent ); ?></div>
            <div style="font-size: 14px; opacity: 0.9;"><?php esc_html_e( '발송된 이메일', 'acf-mail-smtp' ); ?></div>
        </div>
        <div class="acf-stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 16px; padding: 24px; color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; font-size: 80px; opacity: 0.15;">⚠️</div>
            <div style="font-size: 42px; font-weight: 700; margin-bottom: 8px;"><?php echo esc_html( $total_emails_failed ); ?></div>
            <div style="font-size: 14px; opacity: 0.9;"><?php esc_html_e( '실패한 이메일', 'acf-mail-smtp' ); ?></div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="acf-charts-row" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-top: 30px;">
        <!-- 30 Day Submissions Chart -->
        <div class="acf-chart-card" style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 18px; color: #374151;">📈 <?php esc_html_e( '최근 30일 제출 추이', 'acf-mail-smtp' ); ?></h3>
                <span style="font-size: 13px; color: #6b7280;"><?php esc_html_e( '일별 통계', 'acf-mail-smtp' ); ?></span>
            </div>
            <div style="height: 280px;">
                <canvas id="submissions-line-chart"></canvas>
            </div>
        </div>

        <!-- Email Status Pie Chart -->
        <div class="acf-chart-card" style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
            <h3 style="margin: 0 0 20px 0; font-size: 18px; color: #374151;">📧 <?php esc_html_e( '이메일 발송 현황', 'acf-mail-smtp' ); ?></h3>
            <div style="height: 280px; display: flex; align-items: center; justify-content: center;">
                <?php if ( ! empty( $email_status_data ) ) : ?>
                    <canvas id="email-status-chart"></canvas>
                <?php else : ?>
                    <div style="text-align: center; color: #9ca3af;">
                        <div style="font-size: 48px; margin-bottom: 12px;">📭</div>
                        <p><?php esc_html_e( '아직 이메일 데이터가 없습니다', 'acf-mail-smtp' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="acf-charts-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px;">
        <!-- Submissions by Form Bar Chart -->
        <div class="acf-chart-card" style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
            <h3 style="margin: 0 0 20px 0; font-size: 18px; color: #374151;">📊 <?php esc_html_e( '폼별 제출 수', 'acf-mail-smtp' ); ?></h3>
            <div style="height: 260px;">
                <?php if ( ! empty( $form_data ) ) : ?>
                    <canvas id="form-submissions-chart"></canvas>
                <?php else : ?>
                    <div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                        <div style="text-align: center;">
                            <div style="font-size: 48px; margin-bottom: 12px;">📋</div>
                            <p><?php esc_html_e( '폼을 만들고 제출을 받아보세요', 'acf-mail-smtp' ); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Hourly Distribution Chart -->
        <div class="acf-chart-card" style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 18px; color: #374151;">⏰ <?php esc_html_e( '시간대별 제출', 'acf-mail-smtp' ); ?></h3>
                <span style="font-size: 13px; color: #6b7280;"><?php esc_html_e( '최근 7일', 'acf-mail-smtp' ); ?></span>
            </div>
            <div style="height: 260px;">
                <canvas id="hourly-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 30px;">
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-forms&action=new' ) ); ?>"
           class="acf-action-card" style="display: flex; align-items: center; gap: 12px; padding: 20px; background: white; border-radius: 12px; text-decoration: none; color: #374151; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.2s;">
            <span style="font-size: 28px;">✨</span>
            <span style="font-weight: 600;"><?php esc_html_e( '새 폼 만들기', 'acf-mail-smtp' ); ?></span>
        </a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-smtp' ) ); ?>"
           class="acf-action-card" style="display: flex; align-items: center; gap: 12px; padding: 20px; background: white; border-radius: 12px; text-decoration: none; color: #374151; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.2s;">
            <span style="font-size: 28px;">⚙️</span>
            <span style="font-weight: 600;"><?php esc_html_e( 'SMTP 설정', 'acf-mail-smtp' ); ?></span>
        </a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-submissions' ) ); ?>"
           class="acf-action-card" style="display: flex; align-items: center; gap: 12px; padding: 20px; background: white; border-radius: 12px; text-decoration: none; color: #374151; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.2s;">
            <span style="font-size: 28px;">📥</span>
            <span style="font-weight: 600;"><?php esc_html_e( '제출 내역', 'acf-mail-smtp' ); ?></span>
        </a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-logs' ) ); ?>"
           class="acf-action-card" style="display: flex; align-items: center; gap: 12px; padding: 20px; background: white; border-radius: 12px; text-decoration: none; color: #374151; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.2s;">
            <span style="font-size: 28px;">📋</span>
            <span style="font-weight: 600;"><?php esc_html_e( '이메일 로그', 'acf-mail-smtp' ); ?></span>
        </a>
    </div>

    <!-- Recent Data Tables -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 30px;">
        <!-- Recent Submissions -->
        <div class="acf-chart-card" style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; font-size: 18px; color: #374151;">📝 <?php esc_html_e( '최근 제출', 'acf-mail-smtp' ); ?></h3>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-submissions' ) ); ?>" style="font-size: 13px; color: #667eea; text-decoration: none;">
                    <?php esc_html_e( '전체보기 →', 'acf-mail-smtp' ); ?>
                </a>
            </div>
            <div style="overflow-x: auto;">
                <table class="acf-data-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e5e7eb;">
                            <th style="text-align: left; padding: 12px 8px; font-size: 13px; font-weight: 600; color: #6b7280;"><?php esc_html_e( '폼', 'acf-mail-smtp' ); ?></th>
                            <th style="text-align: left; padding: 12px 8px; font-size: 13px; font-weight: 600; color: #6b7280;"><?php esc_html_e( '시간', 'acf-mail-smtp' ); ?></th>
                            <th style="text-align: center; padding: 12px 8px; font-size: 13px; font-weight: 600; color: #6b7280;"><?php esc_html_e( '상태', 'acf-mail-smtp' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $recent_submissions ) ) : ?>
                            <?php foreach ( array_slice( $recent_submissions, 0, 5 ) as $submission ) : ?>
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 12px 8px;">
                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-submissions&id=' . $submission['id'] ) ); ?>" style="color: #374151; text-decoration: none; font-weight: 500;">
                                            <?php echo esc_html( $submission['form_name'] ?: 'N/A' ); ?>
                                        </a>
                                    </td>
                                    <td style="padding: 12px 8px; font-size: 13px; color: #6b7280;">
                                        <?php echo esc_html( human_time_diff( strtotime( $submission['created_at'] ), current_time( 'timestamp' ) ) ); ?> <?php esc_html_e( '전', 'acf-mail-smtp' ); ?>
                                    </td>
                                    <td style="padding: 12px 8px; text-align: center;">
                                        <span class="acf-status-badge status-<?php echo esc_attr( $submission['status'] ); ?>">
                                            <?php echo esc_html( ucfirst( $submission['status'] ) ); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3" style="padding: 24px; text-align: center; color: #9ca3af;">
                                    <?php esc_html_e( '제출 내역이 없습니다.', 'acf-mail-smtp' ); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Emails -->
        <div class="acf-chart-card" style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; font-size: 18px; color: #374151;">✉️ <?php esc_html_e( '최근 이메일', 'acf-mail-smtp' ); ?></h3>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-logs' ) ); ?>" style="font-size: 13px; color: #667eea; text-decoration: none;">
                    <?php esc_html_e( '전체보기 →', 'acf-mail-smtp' ); ?>
                </a>
            </div>
            <div style="overflow-x: auto;">
                <table class="acf-data-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e5e7eb;">
                            <th style="text-align: left; padding: 12px 8px; font-size: 13px; font-weight: 600; color: #6b7280;"><?php esc_html_e( '받는 사람', 'acf-mail-smtp' ); ?></th>
                            <th style="text-align: left; padding: 12px 8px; font-size: 13px; font-weight: 600; color: #6b7280;"><?php esc_html_e( '제목', 'acf-mail-smtp' ); ?></th>
                            <th style="text-align: center; padding: 12px 8px; font-size: 13px; font-weight: 600; color: #6b7280;"><?php esc_html_e( '상태', 'acf-mail-smtp' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $recent_emails ) ) : ?>
                            <?php foreach ( array_slice( $recent_emails, 0, 5 ) as $email ) : ?>
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 12px 8px; font-size: 13px; color: #374151;">
                                        <?php echo esc_html( mb_strimwidth( $email['to_email'], 0, 25, '...' ) ); ?>
                                    </td>
                                    <td style="padding: 12px 8px; font-size: 13px; color: #6b7280;">
                                        <?php echo esc_html( mb_strimwidth( $email['subject'], 0, 30, '...' ) ); ?>
                                    </td>
                                    <td style="padding: 12px 8px; text-align: center;">
                                        <span class="acf-status-badge status-<?php echo esc_attr( $email['status'] ); ?>">
                                            <?php echo esc_html( ucfirst( $email['status'] ) ); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3" style="padding: 24px; text-align: center; color: #9ca3af;">
                                    <?php esc_html_e( '이메일 로그가 없습니다.', 'acf-mail-smtp' ); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.acf-mail-smtp-dashboard-v25 {
    max-width: 1600px;
}

.acf-action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12) !important;
}

.acf-status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.acf-status-badge.status-new {
    background: #dbeafe;
    color: #1e40af;
}

.acf-status-badge.status-read {
    background: #d1fae5;
    color: #065f46;
}

.acf-status-badge.status-sent {
    background: #d1fae5;
    color: #065f46;
}

.acf-status-badge.status-failed {
    background: #fee2e2;
    color: #991b1b;
}

.acf-status-badge.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.acf-status-badge.status-active {
    background: #d1fae5;
    color: #065f46;
}

@media (max-width: 1200px) {
    .acf-charts-row {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 768px) {
    .acf-stats-grid {
        grid-template-columns: 1fr 1fr !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Common chart options
    var commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    };

    // 30 Day Submissions Line Chart
    var submissionsCtx = document.getElementById('submissions-line-chart');
    if (submissionsCtx) {
        new Chart(submissionsCtx, {
            type: 'line',
            data: {
                labels: <?php echo wp_json_encode( $chart_labels_30_days ); ?>,
                datasets: [{
                    label: '<?php echo esc_js( __( '제출 수', 'acf-mail-smtp' ) ); ?>',
                    data: <?php echo wp_json_encode( $chart_data_30_days ); ?>,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#667eea',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { maxTicksLimit: 10, font: { size: 11 }, color: '#9ca3af' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: { font: { size: 11 }, color: '#9ca3af', precision: 0 }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // Email Status Doughnut Chart
    var emailStatusCtx = document.getElementById('email-status-chart');
    if (emailStatusCtx) {
        new Chart(emailStatusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo wp_json_encode( $email_status_labels ); ?>,
                datasets: [{
                    data: <?php echo wp_json_encode( $email_status_data ); ?>,
                    backgroundColor: <?php echo wp_json_encode( $email_status_colors ); ?>,
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 12 }
                        }
                    }
                }
            }
        });
    }

    // Form Submissions Bar Chart
    var formSubmissionsCtx = document.getElementById('form-submissions-chart');
    if (formSubmissionsCtx && <?php echo ! empty( $form_data ) ? 'true' : 'false'; ?>) {
        new Chart(formSubmissionsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo wp_json_encode( $form_labels ); ?>,
                datasets: [{
                    label: '<?php echo esc_js( __( '제출 수', 'acf-mail-smtp' ) ); ?>',
                    data: <?php echo wp_json_encode( $form_data ); ?>,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(240, 147, 251, 0.8)',
                        'rgba(79, 172, 254, 0.8)',
                        'rgba(250, 112, 154, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(245, 87, 108, 0.8)',
                        'rgba(0, 242, 254, 0.8)',
                        'rgba(254, 225, 64, 0.8)',
                        'rgba(156, 163, 175, 0.8)'
                    ],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: { font: { size: 11 }, color: '#9ca3af', precision: 0 }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#374151' }
                    }
                }
            }
        });
    }

    // Hourly Distribution Bar Chart
    var hourlyCtx = document.getElementById('hourly-chart');
    if (hourlyCtx) {
        new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo wp_json_encode( $hourly_labels ); ?>,
                datasets: [{
                    label: '<?php echo esc_js( __( '제출 수', 'acf-mail-smtp' ) ); ?>',
                    data: <?php echo wp_json_encode( $hourly_data ); ?>,
                    backgroundColor: 'rgba(102, 126, 234, 0.7)',
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxTicksLimit: 12,
                            font: { size: 10 },
                            color: '#9ca3af'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: { font: { size: 11 }, color: '#9ca3af', precision: 0 }
                    }
                }
            }
        });
    }
});
</script>
