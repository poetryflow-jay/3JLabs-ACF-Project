<?php
/**
 * ACF Code Snippets Box - Code Tester
 *
 * 코드 테스트 및 실시간 미리보기
 *
 * @package ACF_Code_Snippets_Box
 * @since 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Code Tester 클래스
 */
class ACF_CSB_Code_Tester {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        add_action( 'wp_ajax_acf_csb_test_code', array( $this, 'ajax_test_code' ) );
        add_action( 'wp_ajax_acf_csb_validate_code', array( $this, 'ajax_validate_code' ) );
        add_action( 'wp_ajax_acf_csb_preview_css', array( $this, 'ajax_preview_css' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_tester_meta_box' ) );
    }

    /**
     * 테스터 메타 박스 추가
     */
    public function add_tester_meta_box() {
        add_meta_box(
            'acf_csb_code_tester',
            '🧪 ' . __( '코드 테스터', 'acf-code-snippets-box' ),
            array( $this, 'render_tester_meta_box' ),
            'acf_code_snippet',
            'normal',
            'high'
        );
    }

    /**
     * 테스터 메타 박스 렌더링
     */
    public function render_tester_meta_box( $post ) {
        $code_type = get_post_meta( $post->ID, '_acf_csb_code_type', true ) ?: 'css';
        ?>
        <div class="acf-csb-code-tester" style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <div style="display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap;">
                <button type="button" class="button button-primary acf-csb-validate-btn" data-post-id="<?php echo esc_attr( $post->ID ); ?>">
                    ✓ 문법 검사
                </button>
                <button type="button" class="button acf-csb-test-btn" data-post-id="<?php echo esc_attr( $post->ID ); ?>">
                    ▶ 테스트 실행
                </button>
                <?php if ( $code_type === 'css' || $code_type === 'js' ) : ?>
                    <button type="button" class="button acf-csb-preview-btn" data-post-id="<?php echo esc_attr( $post->ID ); ?>">
                        👁 실시간 미리보기
                    </button>
                <?php endif; ?>
                <span class="acf-csb-test-status" style="margin-left: auto; padding: 5px 10px; border-radius: 4px; font-size: 13px;"></span>
            </div>

            <!-- 테스트 결과 영역 -->
            <div class="acf-csb-test-result" style="display: none; padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 6px; max-height: 300px; overflow: auto;">
                <div class="result-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <strong>테스트 결과</strong>
                    <button type="button" class="button button-small acf-csb-close-result">✕ 닫기</button>
                </div>
                <div class="result-content">
                    <!-- AJAX로 채워짐 -->
                </div>
            </div>

            <!-- CSS 미리보기 영역 -->
            <div class="acf-csb-preview-panel" style="display: none; margin-top: 15px;">
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <select class="acf-csb-preview-target" style="flex: 1;">
                        <option value="custom"><?php esc_html_e( '커스텀 HTML 입력', 'acf-code-snippets-box' ); ?></option>
                        <option value="buttons"><?php esc_html_e( '버튼 미리보기', 'acf-code-snippets-box' ); ?></option>
                        <option value="typography"><?php esc_html_e( '타이포그래피 미리보기', 'acf-code-snippets-box' ); ?></option>
                        <option value="cards"><?php esc_html_e( '카드 레이아웃', 'acf-code-snippets-box' ); ?></option>
                        <option value="forms"><?php esc_html_e( '폼 요소', 'acf-code-snippets-box' ); ?></option>
                    </select>
                    <button type="button" class="button acf-csb-apply-preview">적용</button>
                </div>
                <div class="acf-csb-preview-html-input" style="margin-bottom: 10px;">
                    <textarea class="acf-csb-custom-html" rows="3" placeholder="미리보기용 HTML을 입력하세요..." style="width: 100%; font-family: monospace; font-size: 12px;"></textarea>
                </div>
                <div class="acf-csb-preview-frame" style="padding: 20px; background: #fff; border: 2px dashed #ddd; border-radius: 8px; min-height: 150px;">
                    <div class="preview-content">
                        <!-- 미리보기 내용 -->
                    </div>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var postId = <?php echo esc_js( $post->ID ); ?>;
            var nonce = '<?php echo wp_create_nonce( 'acf_csb_tester_nonce' ); ?>';

            // 문법 검사
            $('.acf-csb-validate-btn').on('click', function() {
                var $btn = $(this);
                var $status = $('.acf-csb-test-status');
                var code = getEditorCode();
                var codeType = $('#acf_csb_code_type').val();

                $btn.prop('disabled', true).text('검사 중...');
                $status.html('<span style="color: #666;">검사 중...</span>');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'acf_csb_validate_code',
                        post_id: postId,
                        code: code,
                        code_type: codeType,
                        nonce: nonce
                    },
                    success: function(response) {
                        var $result = $('.acf-csb-test-result');
                        $result.show();

                        if (response.success) {
                            if (response.data.errors && response.data.errors.length > 0) {
                                $status.html('<span style="color: #dc3545; font-weight: 600;">⚠ ' + response.data.errors.length + '개 문제 발견</span>');
                                var errorsHtml = '<div style="color: #dc3545;">';
                                response.data.errors.forEach(function(err) {
                                    errorsHtml += '<div style="padding: 5px 0; border-bottom: 1px solid #eee;">' +
                                        '<strong>줄 ' + (err.line || '?') + ':</strong> ' + err.message + '</div>';
                                });
                                errorsHtml += '</div>';
                                $result.find('.result-content').html(errorsHtml);
                            } else {
                                $status.html('<span style="color: #28a745; font-weight: 600;">✓ 문법 오류 없음</span>');
                                $result.find('.result-content').html(
                                    '<div style="color: #28a745; text-align: center; padding: 20px;">' +
                                    '<span style="font-size: 36px;">✓</span><br><strong>문법 오류가 없습니다!</strong><br>' +
                                    '<small style="color: #666;">코드 라인: ' + response.data.lines + ' | 문자 수: ' + response.data.characters + '</small>' +
                                    '</div>'
                                );
                            }
                        } else {
                            $status.html('<span style="color: #dc3545;">검사 실패</span>');
                            $result.find('.result-content').html('<div style="color: #dc3545;">' + response.data + '</div>');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('✓ 문법 검사');
                    }
                });
            });

            // 테스트 실행
            $('.acf-csb-test-btn').on('click', function() {
                var $btn = $(this);
                var $status = $('.acf-csb-test-status');
                var code = getEditorCode();
                var codeType = $('#acf_csb_code_type').val();

                if (codeType === 'php') {
                    if (!confirm('PHP 코드를 테스트 실행하시겠습니까?\n\n주의: 실제 환경에서 실행되며, 오류가 발생할 수 있습니다.')) {
                        return;
                    }
                }

                $btn.prop('disabled', true).text('실행 중...');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'acf_csb_test_code',
                        post_id: postId,
                        code: code,
                        code_type: codeType,
                        nonce: nonce
                    },
                    success: function(response) {
                        var $result = $('.acf-csb-test-result');
                        $result.show();

                        if (response.success) {
                            var html = '<div style="padding: 10px;">';
                            html += '<div style="margin-bottom: 10px;">';
                            html += '<span style="background: ' + (response.data.status === 'success' ? '#28a745' : '#dc3545') + '; color: #fff; padding: 3px 8px; border-radius: 3px; font-size: 11px;">' + response.data.status.toUpperCase() + '</span>';
                            html += ' <span style="color: #666; font-size: 12px;">실행 시간: ' + response.data.execution_time + 'ms</span>';
                            html += '</div>';

                            if (response.data.output) {
                                html += '<div style="background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 6px; font-family: monospace; font-size: 12px; white-space: pre-wrap; overflow-x: auto;">' + escapeHtml(response.data.output) + '</div>';
                            }

                            if (response.data.errors && response.data.errors.length > 0) {
                                html += '<div style="margin-top: 10px; color: #dc3545;">';
                                response.data.errors.forEach(function(err) {
                                    html += '<div style="padding: 5px 0;">⚠ ' + err + '</div>';
                                });
                                html += '</div>';
                            }

                            html += '</div>';
                            $result.find('.result-content').html(html);
                            $status.html('<span style="color: ' + (response.data.status === 'success' ? '#28a745' : '#dc3545') + ';">' + (response.data.status === 'success' ? '✓ 성공' : '⚠ 오류') + '</span>');
                        } else {
                            $result.find('.result-content').html('<div style="color: #dc3545; padding: 10px;">실행 오류: ' + response.data + '</div>');
                            $status.html('<span style="color: #dc3545;">⚠ 오류</span>');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('▶ 테스트 실행');
                    }
                });
            });

            // 실시간 미리보기
            $('.acf-csb-preview-btn').on('click', function() {
                var $panel = $('.acf-csb-preview-panel');
                $panel.toggle();
                if ($panel.is(':visible')) {
                    applyPreview();
                }
            });

            // 미리보기 적용
            $('.acf-csb-apply-preview').on('click', applyPreview);

            function applyPreview() {
                var code = getEditorCode();
                var codeType = $('#acf_csb_code_type').val();
                var target = $('.acf-csb-preview-target').val();
                var customHtml = $('.acf-csb-custom-html').val();
                var $frame = $('.acf-csb-preview-frame .preview-content');

                // 기존 스타일 제거
                $('#acf-csb-preview-style').remove();

                // 프리셋 HTML
                var presetHtml = {
                    buttons: '<button class="btn btn-primary">Primary</button> <button class="btn btn-secondary">Secondary</button> <button class="btn btn-success">Success</button> <button class="btn btn-danger">Danger</button>',
                    typography: '<h1>Heading 1</h1><h2>Heading 2</h2><h3>Heading 3</h3><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt.</p><p><strong>Bold text</strong> and <em>italic text</em> and <a href="#">link example</a>.</p>',
                    cards: '<div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;"><img src="https://via.placeholder.com/300x150" style="width: 100%;"><div style="padding: 15px;"><h3 style="margin: 0 0 10px;">Card Title</h3><p style="margin: 0; color: #666;">Card description text goes here.</p></div></div>',
                    forms: '<form style="max-width: 400px;"><div style="margin-bottom: 15px;"><label style="display: block; margin-bottom: 5px;">Name</label><input type="text" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"></div><div style="margin-bottom: 15px;"><label style="display: block; margin-bottom: 5px;">Email</label><input type="email" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"></div><div style="margin-bottom: 15px;"><label style="display: block; margin-bottom: 5px;">Message</label><textarea style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" rows="3"></textarea></div><button type="button" style="padding: 10px 20px; background: #0073aa; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Submit</button></form>'
                };

                var html = target === 'custom' ? customHtml : (presetHtml[target] || '');
                $frame.html(html);

                if (codeType === 'css') {
                    $('head').append('<style id="acf-csb-preview-style">' + code + '</style>');
                } else if (codeType === 'js') {
                    try {
                        eval(code);
                    } catch (e) {
                        console.error('JS Preview Error:', e);
                    }
                }
            }

            // 결과 닫기
            $('.acf-csb-close-result').on('click', function() {
                $('.acf-csb-test-result').hide();
            });

            // 에디터 코드 가져오기
            function getEditorCode() {
                if (typeof wp !== 'undefined' && wp.codeEditor) {
                    var editorInstance = $('.CodeMirror')[0];
                    if (editorInstance && editorInstance.CodeMirror) {
                        return editorInstance.CodeMirror.getValue();
                    }
                }
                return $('#acf_csb_code').val();
            }

            // HTML 이스케이프
            function escapeHtml(text) {
                var div = document.createElement('div');
                div.appendChild(document.createTextNode(text));
                return div.innerHTML;
            }
        });
        </script>
        <?php
    }

    /**
     * AJAX: 코드 검증
     */
    public function ajax_validate_code() {
        check_ajax_referer( 'acf_csb_tester_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $code = isset( $_POST['code'] ) ? wp_unslash( $_POST['code'] ) : '';
        $code_type = isset( $_POST['code_type'] ) ? sanitize_text_field( $_POST['code_type'] ) : 'css';

        $errors = array();
        $lines = substr_count( $code, "\n" ) + 1;
        $characters = strlen( $code );

        switch ( $code_type ) {
            case 'css':
                $errors = $this->validate_css( $code );
                break;
            case 'js':
                $errors = $this->validate_javascript( $code );
                break;
            case 'php':
                $errors = $this->validate_php( $code );
                break;
            case 'html':
                $errors = $this->validate_html( $code );
                break;
        }

        wp_send_json_success( array(
            'errors'     => $errors,
            'lines'      => $lines,
            'characters' => $characters,
        ) );
    }

    /**
     * CSS 검증
     */
    private function validate_css( $code ) {
        $errors = array();
        $lines = explode( "\n", $code );

        $brace_count = 0;
        $in_string = false;

        foreach ( $lines as $line_num => $line ) {
            $line_number = $line_num + 1;

            // 빈 줄 건너뛰기
            $trimmed = trim( $line );
            if ( empty( $trimmed ) || strpos( $trimmed, '//' ) === 0 ) {
                continue;
            }

            // 중괄호 카운트
            for ( $i = 0; $i < strlen( $line ); $i++ ) {
                $char = $line[ $i ];
                if ( $char === '{' ) $brace_count++;
                if ( $char === '}' ) $brace_count--;
            }

            // 속성:값 형식 확인 (선택자 내부)
            if ( $brace_count > 0 && strpos( $line, ':' ) !== false && strpos( $line, '{' ) === false ) {
                // 세미콜론 누락 체크
                if ( strpos( rtrim( $line ), ';' ) === false && strpos( $line, '{' ) === false && strpos( $line, '}' ) === false ) {
                    // 마지막 속성이 아닌 경우
                    $next_line = isset( $lines[ $line_num + 1 ] ) ? trim( $lines[ $line_num + 1 ] ) : '';
                    if ( $next_line !== '}' && ! empty( $next_line ) ) {
                        $errors[] = array(
                            'line'    => $line_number,
                            'message' => __( '세미콜론(;)이 누락되었을 수 있습니다.', 'acf-code-snippets-box' ),
                        );
                    }
                }
            }
        }

        // 중괄호 불일치
        if ( $brace_count !== 0 ) {
            $errors[] = array(
                'line'    => null,
                'message' => sprintf(
                    __( '중괄호가 일치하지 않습니다 (%s개 %s).', 'acf-code-snippets-box' ),
                    abs( $brace_count ),
                    $brace_count > 0 ? '누락' : '초과'
                ),
            );
        }

        return $errors;
    }

    /**
     * JavaScript 검증
     */
    private function validate_javascript( $code ) {
        $errors = array();

        // 기본적인 문법 체크
        $brace_count = 0;
        $paren_count = 0;
        $bracket_count = 0;

        for ( $i = 0; $i < strlen( $code ); $i++ ) {
            $char = $code[ $i ];
            switch ( $char ) {
                case '{': $brace_count++; break;
                case '}': $brace_count--; break;
                case '(': $paren_count++; break;
                case ')': $paren_count--; break;
                case '[': $bracket_count++; break;
                case ']': $bracket_count--; break;
            }
        }

        if ( $brace_count !== 0 ) {
            $errors[] = array(
                'line'    => null,
                'message' => __( '중괄호 {}가 일치하지 않습니다.', 'acf-code-snippets-box' ),
            );
        }

        if ( $paren_count !== 0 ) {
            $errors[] = array(
                'line'    => null,
                'message' => __( '괄호 ()가 일치하지 않습니다.', 'acf-code-snippets-box' ),
            );
        }

        if ( $bracket_count !== 0 ) {
            $errors[] = array(
                'line'    => null,
                'message' => __( '대괄호 []가 일치하지 않습니다.', 'acf-code-snippets-box' ),
            );
        }

        // console.log 경고
        if ( strpos( $code, 'console.log' ) !== false ) {
            $errors[] = array(
                'line'    => null,
                'message' => __( '⚠ console.log()가 포함되어 있습니다. 프로덕션에서 제거를 권장합니다.', 'acf-code-snippets-box' ),
            );
        }

        // alert 경고
        if ( preg_match( '/\balert\s*\(/', $code ) ) {
            $errors[] = array(
                'line'    => null,
                'message' => __( '⚠ alert()가 포함되어 있습니다. 사용자 경험에 영향을 줄 수 있습니다.', 'acf-code-snippets-box' ),
            );
        }

        return $errors;
    }

    /**
     * PHP 검증
     */
    private function validate_php( $code ) {
        $errors = array();

        // PHP 태그 체크
        if ( strpos( $code, '<?php' ) !== false || strpos( $code, '?>' ) !== false ) {
            $errors[] = array(
                'line'    => null,
                'message' => __( 'PHP 태그(<?php, ?>)를 제거해주세요. 코드만 입력합니다.', 'acf-code-snippets-box' ),
            );
        }

        // 위험한 함수 체크
        $dangerous_functions = array( 'eval', 'exec', 'shell_exec', 'system', 'passthru', 'popen', 'proc_open' );
        foreach ( $dangerous_functions as $func ) {
            if ( preg_match( '/\b' . $func . '\s*\(/', $code ) ) {
                $errors[] = array(
                    'line'    => null,
                    'message' => sprintf( __( '⚠ 위험한 함수 %s()가 감지되었습니다. 보안에 주의하세요.', 'acf-code-snippets-box' ), $func ),
                );
            }
        }

        // 문법 체크 (php -l 사용)
        if ( function_exists( 'proc_open' ) ) {
            $temp_file = wp_tempnam( 'acf_csb_php_check_' );
            file_put_contents( $temp_file, '<?php ' . $code );

            $output = array();
            exec( 'php -l ' . escapeshellarg( $temp_file ) . ' 2>&1', $output, $return_code );

            unlink( $temp_file );

            if ( $return_code !== 0 ) {
                foreach ( $output as $line ) {
                    if ( strpos( $line, 'Parse error' ) !== false || strpos( $line, 'syntax error' ) !== false ) {
                        // 라인 번호 추출
                        preg_match( '/on line (\d+)/', $line, $matches );
                        $line_num = isset( $matches[1] ) ? intval( $matches[1] ) - 1 : null; // <?php 태그 때문에 -1
                        $errors[] = array(
                            'line'    => $line_num,
                            'message' => preg_replace( '/in .+ on line \d+/', '', $line ),
                        );
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * HTML 검증
     */
    private function validate_html( $code ) {
        $errors = array();

        // 태그 짝 맞추기
        preg_match_all( '/<(\/?)([\w]+)[^>]*>/', $code, $matches, PREG_SET_ORDER );

        $stack = array();
        $self_closing = array( 'br', 'hr', 'img', 'input', 'meta', 'link', 'area', 'base', 'col', 'embed', 'param', 'source', 'track', 'wbr' );

        foreach ( $matches as $match ) {
            $is_closing = $match[1] === '/';
            $tag = strtolower( $match[2] );

            if ( in_array( $tag, $self_closing, true ) ) {
                continue;
            }

            if ( $is_closing ) {
                if ( empty( $stack ) || end( $stack ) !== $tag ) {
                    $errors[] = array(
                        'line'    => null,
                        'message' => sprintf( __( '닫는 태그 </%s>가 잘못되었습니다.', 'acf-code-snippets-box' ), $tag ),
                    );
                } else {
                    array_pop( $stack );
                }
            } else {
                $stack[] = $tag;
            }
        }

        foreach ( $stack as $unclosed ) {
            $errors[] = array(
                'line'    => null,
                'message' => sprintf( __( '태그 <%s>가 닫히지 않았습니다.', 'acf-code-snippets-box' ), $unclosed ),
            );
        }

        // 스크립트 태그 경고
        if ( preg_match( '/<script[^>]*>/i', $code ) ) {
            $errors[] = array(
                'line'    => null,
                'message' => __( '⚠ <script> 태그가 포함되어 있습니다. JavaScript 타입 스니펫 사용을 권장합니다.', 'acf-code-snippets-box' ),
            );
        }

        return $errors;
    }

    /**
     * AJAX: 코드 테스트 실행
     */
    public function ajax_test_code() {
        check_ajax_referer( 'acf_csb_tester_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $code = isset( $_POST['code'] ) ? wp_unslash( $_POST['code'] ) : '';
        $code_type = isset( $_POST['code_type'] ) ? sanitize_text_field( $_POST['code_type'] ) : 'css';

        $start_time = microtime( true );
        $output = '';
        $errors = array();
        $status = 'success';

        switch ( $code_type ) {
            case 'php':
                // PHP 실행 설정 확인
                $settings = get_option( 'acf_csb_settings', array() );
                if ( empty( $settings['enable_php_execution'] ) ) {
                    wp_send_json_error( 'PHP 실행이 비활성화되어 있습니다. 설정에서 활성화해주세요.' );
                }

                ob_start();
                try {
                    set_error_handler( function( $errno, $errstr, $errfile, $errline ) use ( &$errors ) {
                        $errors[] = "[$errno] $errstr (line $errline)";
                        return true;
                    } );

                    eval( $code );

                    restore_error_handler();
                } catch ( Exception $e ) {
                    $errors[] = 'Exception: ' . $e->getMessage();
                    $status = 'error';
                } catch ( Error $e ) {
                    $errors[] = 'Error: ' . $e->getMessage();
                    $status = 'error';
                }
                $output = ob_get_clean();

                if ( ! empty( $errors ) ) {
                    $status = 'error';
                }
                break;

            case 'css':
            case 'js':
            case 'html':
                // 프론트엔드에서 처리
                $output = '이 코드 타입은 브라우저에서 미리보기로 테스트하세요.';
                break;
        }

        $execution_time = round( ( microtime( true ) - $start_time ) * 1000, 2 );

        wp_send_json_success( array(
            'status'         => $status,
            'output'         => $output,
            'errors'         => $errors,
            'execution_time' => $execution_time,
        ) );
    }
}
