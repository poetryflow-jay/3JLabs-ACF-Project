<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$stored = (array) get_option( $this->option_key );
?>
<div class="jj-admin-center-tab-content" data-tab="texts">
    <h3><?php esc_html_e( '스타일 센터 텍스트 설정', 'acf-css-really-simple-style-management-center' ); ?></h3>
    <p class="description">
        <?php esc_html_e( '스타일 센터 UI에 사용되는 텍스트 일부를 여기에서 수정할 수 있습니다.', 'acf-css-really-simple-style-management-center' ); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
        <?php foreach ( $this->config as $key => $meta ) :
            $label   = isset( $meta['label'] ) ? $meta['label'] : $key;
            $default = isset( $meta['default'] ) ? $meta['default'] : '';
            $value   = isset( $stored[ $key ] ) ? $stored[ $key ] : $default;
            ?>
            <tr>
                <th scope="row">
                    <label for="jj_admin_texts_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
                    <div style="font-size:11px;color:#666;margin-top:4px;">
                        <code><?php echo esc_html( $key ); ?></code>
                    </div>
                </th>
                <td>
                    <textarea
                        id="jj_admin_texts_<?php echo esc_attr( $key ); ?>"
                        name="jj_admin_texts[<?php echo esc_attr( $key ); ?>]"
                        rows="3"
                        style="width: 100%;"
                    ><?php echo esc_textarea( $value ); ?></textarea>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

