<?php
/**
 * Email Template
 * 
 * 이메일 템플릿을 관리하는 클래스
 * 
 * @package ACF_Mail_SMTP
 * @version 1.0.0
 * @author 3J Labs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Mail_SMTP_Email_Template {

    /**
     * 기본 이메일 템플릿
     */
    public static function get_default_template() {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">{form_title}</h1>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                새로운 폼 제출이 있습니다.
                            </p>
                            {form_data}
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e9ecef;">
                            <p style="color: #6c757d; font-size: 12px; margin: 0;">
                                이 이메일은 {site_name}에서 자동으로 발송되었습니다.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    /**
     * 템플릿 변수 치환
     */
    public static function replace_variables( $template, $data ) {
        $replacements = array(
            '{form_title}' => isset( $data['form_title'] ) ? $data['form_title'] : '',
            '{form_data}' => isset( $data['form_data'] ) ? $data['form_data'] : '',
            '{site_name}' => get_option( 'blogname' ),
            '{site_url}' => home_url(),
            '{date}' => current_time( 'Y-m-d H:i:s' ),
        );

        foreach ( $replacements as $key => $value ) {
            $template = str_replace( $key, $value, $template );
        }

        return $template;
    }

    /**
     * 폼 데이터를 HTML 테이블로 변환
     */
    public static function format_form_data( $form_data, $fields = array() ) {
        $html = '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">';

        foreach ( $form_data as $field_id => $value ) {
            $field_label = $field_id;

            // Find field label
            if ( ! empty( $fields ) && is_array( $fields ) ) {
                foreach ( $fields as $field ) {
                    if ( isset( $field['id'] ) && $field['id'] === $field_id ) {
                        $field_label = isset( $field['label'] ) ? $field['label'] : $field_id;
                        break;
                    }
                }
            }

            if ( is_array( $value ) ) {
                $value = implode( ', ', $value );
            }

            $html .= '<tr>';
            $html .= '<td style="padding: 12px; border: 1px solid #e9ecef; background-color: #f8f9fa; font-weight: bold; width: 30%;">' . esc_html( $field_label ) . '</td>';
            $html .= '<td style="padding: 12px; border: 1px solid #e9ecef;">' . esc_html( $value ) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }
}
