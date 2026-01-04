<?php
/**
 * Form Template
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<form class="acf-mail-smtp-form" data-form-id="<?php echo esc_attr( $form['id'] ); ?>" method="post">
    <?php if ( ! empty( $form['title'] ) ) : ?>
        <h2><?php echo esc_html( $form['title'] ); ?></h2>
    <?php endif; ?>

    <?php if ( ! empty( $form['description'] ) ) : ?>
        <p><?php echo esc_html( $form['description'] ); ?></p>
    <?php endif; ?>

    <?php if ( ! empty( $form['fields'] ) && is_array( $form['fields'] ) ) : ?>
        <?php foreach ( $form['fields'] as $field ) : ?>
            <?php
            $field_id = isset( $field['id'] ) ? $field['id'] : '';
            $field_type = isset( $field['type'] ) ? $field['type'] : 'text';
            $field_label = isset( $field['label'] ) ? $field['label'] : '';
            $field_required = isset( $field['required'] ) && $field['required'];
            ?>
            <div class="form-field">
                <label for="<?php echo esc_attr( $field_id ); ?>">
                    <?php echo esc_html( $field_label ); ?>
                    <?php if ( $field_required ) : ?>
                        <span class="required">*</span>
                    <?php endif; ?>
                </label>

                <?php if ( $field_type === 'textarea' ) : ?>
                    <textarea 
                        id="<?php echo esc_attr( $field_id ); ?>" 
                        name="<?php echo esc_attr( $field_id ); ?>" 
                        <?php echo $field_required ? 'required' : ''; ?>
                        rows="5"
                    ></textarea>
                <?php elseif ( $field_type === 'select' ) : ?>
                    <select 
                        id="<?php echo esc_attr( $field_id ); ?>" 
                        name="<?php echo esc_attr( $field_id ); ?>" 
                        <?php echo $field_required ? 'required' : ''; ?>
                    >
                        <option value=""><?php esc_html_e( '선택하세요', 'acf-mail-smtp' ); ?></option>
                        <?php if ( isset( $field['options'] ) && is_array( $field['options'] ) ) : ?>
                            <?php foreach ( $field['options'] as $option ) : ?>
                                <option value="<?php echo esc_attr( $option ); ?>">
                                    <?php echo esc_html( $option ); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                <?php elseif ( $field_type === 'checkbox' || $field_type === 'radio' ) : ?>
                    <div class="checkbox-group">
                        <?php if ( isset( $field['options'] ) && is_array( $field['options'] ) ) : ?>
                            <?php foreach ( $field['options'] as $option ) : ?>
                                <label>
                                    <input 
                                        type="<?php echo esc_attr( $field_type ); ?>" 
                                        name="<?php echo esc_attr( $field_id ); ?>" 
                                        value="<?php echo esc_attr( $option ); ?>"
                                        <?php echo $field_required ? 'required' : ''; ?>
                                    />
                                    <?php echo esc_html( $option ); ?>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                    <input 
                        type="<?php echo esc_attr( $field_type ); ?>" 
                        id="<?php echo esc_attr( $field_id ); ?>" 
                        name="<?php echo esc_attr( $field_id ); ?>" 
                        <?php echo $field_required ? 'required' : ''; ?>
                    />
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="form-field">
        <button type="submit" class="submit-button">
            <?php esc_html_e( '제출', 'acf-mail-smtp' ); ?>
        </button>
    </div>
</form>
