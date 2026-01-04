<?php
/**
 * Submissions View
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$plugin = ACF_Mail_SMTP::get_instance();
$submission = new ACF_Mail_SMTP_Form_Submission();

$form_id = isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0;
$submission_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

if ( $submission_id > 0 ) {
    // Single submission view
    $submission_data = $submission->get_submission( $submission_id );
    $form = $plugin->forms->get_form( $submission_data['form_id'] );
    ?>
    <div class="wrap acf-mail-smtp-submissions-v25">
        <h1 class="wp-bulk-seo-header-v25">
            <?php esc_html_e( '제출 상세', 'acf-mail-smtp' ); ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-submissions' . ( $form_id ? '&form_id=' . $form_id : '' ) ) ); ?>" class="wp-bulk-seo-btn-v25 wp-bulk-seo-btn-v25-secondary" style="margin-left: 15px;">
                <?php esc_html_e( '← 목록으로', 'acf-mail-smtp' ); ?>
            </a>
        </h1>

        <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 30px;">
            <div class="wp-bulk-seo-suggestion-card-v25">
                <h2><?php esc_html_e( '제출 정보', 'acf-mail-smtp' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( '폼', 'acf-mail-smtp' ); ?></th>
                        <td><?php echo esc_html( $form['name'] ); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( '제출 시간', 'acf-mail-smtp' ); ?></th>
                        <td><?php echo esc_html( $submission_data['created_at'] ); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'IP 주소', 'acf-mail-smtp' ); ?></th>
                        <td><?php echo esc_html( $submission_data['ip_address'] ); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( '상태', 'acf-mail-smtp' ); ?></th>
                        <td>
                            <span class="status-<?php echo esc_attr( $submission_data['status'] ); ?>">
                                <?php echo esc_html( ucfirst( $submission_data['status'] ) ); ?>
                            </span>
                        </td>
                    </tr>
                </table>

                <h2><?php esc_html_e( '제출 데이터', 'acf-mail-smtp' ); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( '필드', 'acf-mail-smtp' ); ?></th>
                            <th><?php esc_html_e( '값', 'acf-mail-smtp' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $submission_data['data'] ) && is_array( $submission_data['data'] ) ) : ?>
                            <?php foreach ( $submission_data['data'] as $field_id => $value ) : ?>
                                <?php
                                $field_label = $field_id;
                                if ( isset( $form['fields'] ) && is_array( $form['fields'] ) ) {
                                    foreach ( $form['fields'] as $field ) {
                                        if ( isset( $field['id'] ) && $field['id'] === $field_id ) {
                                            $field_label = isset( $field['label'] ) ? $field['label'] : $field_id;
                                            break;
                                        }
                                    }
                                }
                                if ( is_array( $value ) ) {
                                    $value = implode( ', ', $value );
                                }
                                ?>
                                <tr>
                                    <td><strong><?php echo esc_html( $field_label ); ?></strong></td>
                                    <td><?php echo esc_html( $value ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="2"><?php esc_html_e( '데이터가 없습니다.', 'acf-mail-smtp' ); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
} else {
    // Submissions list view
    $submissions = $submission->get_submissions( array(
        'form_id' => $form_id,
        'limit' => 50,
    ) );
    ?>
    <div class="wrap acf-mail-smtp-submissions-v25">
        <h1 class="wp-bulk-seo-header-v25">
            <?php esc_html_e( '제출 관리', 'acf-mail-smtp' ); ?>
        </h1>

        <div class="wp-bulk-seo-suggestions-grid-v25" style="margin-top: 30px;">
            <div class="wp-bulk-seo-suggestion-card-v25">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'ID', 'acf-mail-smtp' ); ?></th>
                            <th><?php esc_html_e( '폼', 'acf-mail-smtp' ); ?></th>
                            <th><?php esc_html_e( '제출 시간', 'acf-mail-smtp' ); ?></th>
                            <th><?php esc_html_e( 'IP 주소', 'acf-mail-smtp' ); ?></th>
                            <th><?php esc_html_e( '상태', 'acf-mail-smtp' ); ?></th>
                            <th><?php esc_html_e( '작업', 'acf-mail-smtp' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $submissions ) ) : ?>
                            <?php foreach ( $submissions as $sub ) : ?>
                                <?php $form = $plugin->forms->get_form( $sub['form_id'] ); ?>
                                <tr>
                                    <td><?php echo esc_html( $sub['id'] ); ?></td>
                                    <td><?php echo esc_html( $form ? $form['name'] : 'N/A' ); ?></td>
                                    <td><?php echo esc_html( $sub['created_at'] ); ?></td>
                                    <td><?php echo esc_html( $sub['ip_address'] ); ?></td>
                                    <td>
                                        <span class="status-<?php echo esc_attr( $sub['status'] ); ?>">
                                            <?php echo esc_html( ucfirst( $sub['status'] ) ); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=acf-mail-smtp-submissions&id=' . $sub['id'] ) ); ?>">
                                            <?php esc_html_e( '보기', 'acf-mail-smtp' ); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6"><?php esc_html_e( '제출 내역이 없습니다.', 'acf-mail-smtp' ); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}
?>
