<?php
/**
 * ACF CSS Manager - Welcome Dashboard (2026 Edition)
 * Modern, marketing-ready first impression
 * 
 * @version 22.2.0
 * @author Jenny (Lead UX Designer) x Jason (Lead Engineer)
 * @created 2026-01-03
 * 
 * Design Philosophy:
 * - "첫 눈에 쓰고 싶어지는 플러그인" (Jenny's Vision)
 * - Showcase value immediately
 * - Provide clear next steps
 * - Make users feel confident and excited
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Get plugin data
$plugin_version = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '22.0.0';
$is_pro = class_exists( 'JJ_Pro_Feature_Manager' );

// Get current user
$current_user = wp_get_current_user();
$user_name = $current_user->display_name ?: $current_user->user_login;

// Get stats
$options = get_option( 'jj_style_guide_options', array() );
$colors_count = 0;
$fonts_count = 0;

if ( isset( $options['palettes'] ) && is_array( $options['palettes'] ) ) {
    foreach ( $options['palettes'] as $palette_type => $palette_colors ) {
        if ( is_array( $palette_colors ) ) {
            $colors_count += count( $palette_colors );
        }
    }
}

if ( isset( $options['fonts'] ) && is_array( $options['fonts'] ) ) {
    $fonts_count = count( $options['fonts'] );
}

// Check if first time user
$is_first_time = ! get_user_meta( get_current_user_id(), 'jj_css_seen_welcome', true );
?>

<div class="wrap jj-ui-system">
    <div class="jj-welcome-header" style="background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%); color: white; padding: 48px 32px; margin: 0 -20px 32px -20px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h1 style="color: white; font-size: 36px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; gap: 16px;">
                <span class="dashicons dashicons-art" style="font-size: 42px;"></span>
                <?php echo $is_first_time ? __( 'Welcome to ACF CSS Manager!', 'acf-css-really-simple-style-management-center' ) : __( 'ACF CSS Manager', 'acf-css-really-simple-style-management-center' ); ?>
            </h1>
            <p style="color: rgba(255,255,255,0.9); font-size: 18px; margin: 0 0 24px 0;">
                <?php 
                if ( $is_first_time ) {
                    printf( 
                        __( 'Hey %s! You\'re about to take control of your website\'s design system. Let\'s get started! 🚀', 'acf-css-really-simple-style-management-center' ),
                        esc_html( $user_name )
                    );
                } else {
                    _e( 'Your centralized design system management hub', 'acf-css-really-simple-style-management-center' );
                }
                ?>
            </p>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="<?php echo admin_url( 'admin.php?page=jj-style-guide-cockpit&section=colors' ); ?>" class="jj-btn jj-btn-lg" style="background: white; color: #FF6B35; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                    <span class="dashicons dashicons-admin-customizer"></span>
                    <?php _e( 'Manage Colors', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=jj-style-guide-cockpit&section=typography' ); ?>" class="jj-btn jj-btn-lg jj-btn-ghost" style="border-color: white; color: white;">
                    <span class="dashicons dashicons-editor-textcolor"></span>
                    <?php _e( 'Typography', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
                <?php if ( $is_first_time ) : ?>
                <button onclick="jQuery('#jj-welcome-tour-modal').fadeIn(200);" class="jj-btn jj-btn-lg jj-btn-ghost" style="border-color: white; color: white;">
                    <span class="dashicons dashicons-welcome-learn-more"></span>
                    <?php _e( 'Take a Tour', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Stats Cards -->
        <div class="jj-card-grid">
            <div class="jj-card" style="background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); color: white;">
                <div class="jj-stat-card">
                    <div class="jj-stat-value"><?php echo intval( $colors_count ); ?></div>
                    <div class="jj-stat-label"><?php _e( 'Colors Defined', 'acf-css-really-simple-style-management-center' ); ?></div>
                    <div class="jj-stat-change">
                        <span class="dashicons dashicons-chart-line"></span>
                        <?php _e( 'Active in your palette', 'acf-css-really-simple-style-management-center' ); ?>
                    </div>
                </div>
            </div>

            <div class="jj-card" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white;">
                <div class="jj-stat-card">
                    <div class="jj-stat-value"><?php echo intval( $fonts_count ); ?></div>
                    <div class="jj-stat-label"><?php _e( 'Font Families', 'acf-css-really-simple-style-management-center' ); ?></div>
                    <div class="jj-stat-change">
                        <span class="dashicons dashicons-editor-textcolor"></span>
                        <?php _e( 'Configured typography', 'acf-css-really-simple-style-management-center' ); ?>
                    </div>
                </div>
            </div>

            <div class="jj-card" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); color: white;">
                <div class="jj-stat-card">
                    <div class="jj-stat-value"><?php echo $is_pro ? __( 'PRO', 'acf-css-really-simple-style-management-center' ) : __( 'FREE', 'acf-css-really-simple-style-management-center' ); ?></div>
                    <div class="jj-stat-label"><?php _e( 'Current Plan', 'acf-css-really-simple-style-management-center' ); ?></div>
                    <?php if ( ! $is_pro ) : ?>
                    <a href="https://3j-labs.com/upgrade" target="_blank" class="jj-stat-change" style="text-decoration: none; color: inherit;">
                        <span class="dashicons dashicons-star-filled"></span>
                        <?php _e( 'Upgrade to PRO', 'acf-css-really-simple-style-management-center' ); ?>
                    </a>
                    <?php else : ?>
                    <div class="jj-stat-change">
                        <span class="dashicons dashicons-yes-alt"></span>
                        <?php _e( 'All features unlocked', 'acf-css-really-simple-style-management-center' ); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="jj-card jj-mt-lg">
            <div class="jj-card-header">
                <h2 class="jj-card-title">
                    <span class="dashicons dashicons-admin-generic"></span>
                    <?php _e( 'Quick Actions', 'acf-css-really-simple-style-management-center' ); ?>
                </h2>
            </div>
            <div class="jj-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                    <a href="<?php echo admin_url( 'admin.php?page=jj-style-guide-cockpit&section=presets' ); ?>" class="jj-quick-action-card" style="display: flex; align-items: center; gap: 16px; padding: 20px; background: #F9FAFB; border-radius: 8px; text-decoration: none; transition: all 0.2s; border: 2px solid transparent;">
                        <div style="width: 48px; height: 48px; background: #DBEAFE; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span class="dashicons dashicons-admin-appearance" style="color: #3B82F6; font-size: 24px;"></span>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #262626;"><?php _e( 'Design Presets', 'acf-css-really-simple-style-management-center' ); ?></h3>
                            <p style="margin: 0; font-size: 13px; color: #737373;"><?php _e( 'Apply professional themes instantly', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </div>
                    </a>

                    <a href="<?php echo admin_url( 'admin.php?page=jj-style-guide-cockpit&section=optimizer' ); ?>" class="jj-quick-action-card" style="display: flex; align-items: center; gap: 16px; padding: 20px; background: #F9FAFB; border-radius: 8px; text-decoration: none; transition: all 0.2s; border: 2px solid transparent;">
                        <div style="width: 48px; height: 48px; background: #D1FAE5; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span class="dashicons dashicons-performance" style="color: #10B981; font-size: 24px;"></span>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #262626;"><?php _e( 'CSS Optimizer', 'acf-css-really-simple-style-management-center' ); ?></h3>
                            <p style="margin: 0; font-size: 13px; color: #737373;"><?php _e( 'Optimize performance automatically', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </div>
                    </a>

                    <a href="<?php echo admin_url( 'admin.php?page=jj-style-guide-cockpit&section=team-sync' ); ?>" class="jj-quick-action-card" style="display: flex; align-items: center; gap: 16px; padding: 20px; background: #F9FAFB; border-radius: 8px; text-decoration: none; transition: all 0.2s; border: 2px solid transparent;">
                        <div style="width: 48px; height: 48px; background: #FEF3C7; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span class="dashicons dashicons-groups" style="color: #F59E0B; font-size: 24px;"></span>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #262626;"><?php _e( 'Team Sync', 'acf-css-really-simple-style-management-center' ); ?></h3>
                            <p style="margin: 0; font-size: 13px; color: #737373;"><?php _e( 'Share settings with your team', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </div>
                    </a>

                    <a href="<?php echo admin_url( 'admin.php?page=jj-style-guide-cockpit&section=stats' ); ?>" class="jj-quick-action-card" style="display: flex; align-items: center; gap: 16px; padding: 20px; background: #F9FAFB; border-radius: 8px; text-decoration: none; transition: all 0.2s; border: 2px solid transparent;">
                        <div style="width: 48px; height: 48px; background: #F3E8FF; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span class="dashicons dashicons-chart-area" style="color: #8B5CF6; font-size: 24px;"></span>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #262626;"><?php _e( 'Design Insights', 'acf-css-really-simple-style-management-center' ); ?></h3>
                            <p style="margin: 0; font-size: 13px; color: #737373;"><?php _e( 'Analyze your design system', 'acf-css-really-simple-style-management-center' ); ?></p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Updates -->
        <div class="jj-card-grid" style="grid-template-columns: 2fr 1fr; margin-top: 24px;">
            <div class="jj-card">
                <div class="jj-card-header">
                    <h2 class="jj-card-title">
                        <span class="dashicons dashicons-megaphone"></span>
                        <?php _e( "What's New in v22.2", 'acf-css-really-simple-style-management-center' ); ?>
                    </h2>
                </div>
                <div class="jj-card-body">
                    <ul style="list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 12px;">
                        <li style="display: flex; gap: 12px;">
                            <span class="jj-badge jj-badge-success"><?php _e( 'NEW', 'acf-css-really-simple-style-management-center' ); ?></span>
                            <span><?php _e( 'Team Collaboration: Export/Import settings with version control', 'acf-css-really-simple-style-management-center' ); ?></span>
                        </li>
                        <li style="display: flex; gap: 12px;">
                            <span class="jj-badge jj-badge-success"><?php _e( 'NEW', 'acf-css-really-simple-style-management-center' ); ?></span>
                            <span><?php _e( 'AI CSS Optimizer: Automatic minification and suggestions', 'acf-css-really-simple-style-management-center' ); ?></span>
                        </li>
                        <li style="display: flex; gap: 12px;">
                            <span class="jj-badge jj-badge-info"><?php _e( 'IMPROVED', 'acf-css-really-simple-style-management-center' ); ?></span>
                            <span><?php _e( 'Modern UI System 2026: Card-based layouts and smooth animations', 'acf-css-really-simple-style-management-center' ); ?></span>
                        </li>
                        <li style="display: flex; gap: 12px;">
                            <span class="jj-badge jj-badge-info"><?php _e( 'IMPROVED', 'acf-css-really-simple-style-management-center' ); ?></span>
                            <span><?php _e( '5 new industry-specific design templates (E-commerce, Blog, Portfolio, Agency, SaaS)', 'acf-css-really-simple-style-management-center' ); ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="jj-card">
                <div class="jj-card-header">
                    <h2 class="jj-card-title">
                        <span class="dashicons dashicons-sos"></span>
                        <?php _e( 'Need Help?', 'acf-css-really-simple-style-management-center' ); ?>
                    </h2>
                </div>
                <div class="jj-card-body">
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <a href="https://3j-labs.com/docs" target="_blank" class="jj-btn jj-btn-ghost jj-btn-sm">
                            <span class="dashicons dashicons-book"></span>
                            <?php _e( 'Documentation', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                        <a href="https://3j-labs.com/support" target="_blank" class="jj-btn jj-btn-ghost jj-btn-sm">
                            <span class="dashicons dashicons-format-chat"></span>
                            <?php _e( 'Support Forum', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                        <a href="https://3j-labs.com/tutorials" target="_blank" class="jj-btn jj-btn-ghost jj-btn-sm">
                            <span class="dashicons dashicons-video-alt3"></span>
                            <?php _e( 'Video Tutorials', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interactive hover effect for quick action cards -->
<style>
.jj-quick-action-card:hover {
    background: #FFFFFF !important;
    border-color: var(--jj-primary) !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    transform: translateY(-2px);
}
</style>

<?php
// Mark as seen
if ( $is_first_time ) {
    update_user_meta( get_current_user_id(), 'jj_css_seen_welcome', time() );
}
?>
