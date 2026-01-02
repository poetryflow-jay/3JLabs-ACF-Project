<?php
/**
 * ACF CSS WooCommerce Toolkit - Product Q&A System
 *
 * 상품 Q&A (질문과 답변) 시스템
 *
 * @package ACF_CSS_WooCommerce_Toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Product Q&A 클래스
 */
class ACF_WC_Toolkit_Product_QA {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 커스텀 포스트 타입
     */
    const POST_TYPE = 'product_qa';

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
     * 초기화
     */
    public function init() {
        // 커스텀 포스트 타입 등록
        add_action( 'init', array( $this, 'register_post_type' ) );

        // 상품 탭에 Q&A 추가
        add_filter( 'woocommerce_product_tabs', array( $this, 'add_qa_tab' ) );

        // 질문 제출 AJAX
        add_action( 'wp_ajax_acf_wc_submit_question', array( $this, 'ajax_submit_question' ) );
        add_action( 'wp_ajax_nopriv_acf_wc_submit_question', array( $this, 'ajax_submit_question' ) );

        // 답변 AJAX (관리자)
        add_action( 'wp_ajax_acf_wc_submit_answer', array( $this, 'ajax_submit_answer' ) );

        // 좋아요/도움이 됨 AJAX
        add_action( 'wp_ajax_acf_wc_qa_helpful', array( $this, 'ajax_mark_helpful' ) );
        add_action( 'wp_ajax_nopriv_acf_wc_qa_helpful', array( $this, 'ajax_mark_helpful' ) );

        // 관리자 메뉴
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );

        // 메타박스
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_meta_boxes' ) );

        // 스크립트/스타일
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // 이메일 알림
        add_action( 'acf_wc_qa_answered', array( $this, 'send_answer_notification' ), 10, 2 );
    }

    /**
     * 커스텀 포스트 타입 등록
     */
    public function register_post_type() {
        $labels = array(
            'name'               => __( '상품 Q&A', 'acf-css-woocommerce-toolkit' ),
            'singular_name'      => __( '질문', 'acf-css-woocommerce-toolkit' ),
            'add_new'            => __( '새 질문 추가', 'acf-css-woocommerce-toolkit' ),
            'add_new_item'       => __( '새 질문 추가', 'acf-css-woocommerce-toolkit' ),
            'edit_item'          => __( '질문 편집', 'acf-css-woocommerce-toolkit' ),
            'new_item'           => __( '새 질문', 'acf-css-woocommerce-toolkit' ),
            'view_item'          => __( '질문 보기', 'acf-css-woocommerce-toolkit' ),
            'search_items'       => __( '질문 검색', 'acf-css-woocommerce-toolkit' ),
            'not_found'          => __( '질문을 찾을 수 없습니다', 'acf-css-woocommerce-toolkit' ),
            'not_found_in_trash' => __( '휴지통에 질문이 없습니다', 'acf-css-woocommerce-toolkit' ),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => false, // 별도 메뉴에서 표시
            'query_var'           => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array( 'title', 'editor', 'author' ),
            'menu_icon'           => 'dashicons-format-chat',
        );

        register_post_type( self::POST_TYPE, $args );

        // Q&A 상태 등록
        register_post_status( 'answered', array(
            'label'                     => __( '답변완료', 'acf-css-woocommerce-toolkit' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'label_count'               => _n_noop( '답변완료 <span class="count">(%s)</span>', '답변완료 <span class="count">(%s)</span>', 'acf-css-woocommerce-toolkit' ),
        ) );

        register_post_status( 'waiting', array(
            'label'                     => __( '답변대기', 'acf-css-woocommerce-toolkit' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'label_count'               => _n_noop( '답변대기 <span class="count">(%s)</span>', '답변대기 <span class="count">(%s)</span>', 'acf-css-woocommerce-toolkit' ),
        ) );
    }

    /**
     * 상품 탭에 Q&A 추가
     */
    public function add_qa_tab( $tabs ) {
        global $product;

        if ( ! $product ) {
            return $tabs;
        }

        $qa_count = $this->get_qa_count( $product->get_id() );

        $tabs['product_qa'] = array(
            'title'    => sprintf( __( 'Q&A (%d)', 'acf-css-woocommerce-toolkit' ), $qa_count ),
            'priority' => 25,
            'callback' => array( $this, 'render_qa_tab' ),
        );

        return $tabs;
    }

    /**
     * Q&A 탭 내용 렌더링
     */
    public function render_qa_tab() {
        global $product;

        $product_id = $product->get_id();
        $questions = $this->get_questions( $product_id );
        $can_ask = is_user_logged_in() || get_option( 'acf_wc_qa_allow_guest', 'yes' ) === 'yes';
        ?>
        <div class="acf-wc-product-qa">
            <h2><?php esc_html_e( '상품 Q&A', 'acf-css-woocommerce-toolkit' ); ?></h2>

            <!-- 질문 작성 폼 -->
            <?php if ( $can_ask ) : ?>
                <div class="acf-wc-qa-form-wrapper">
                    <h3><?php esc_html_e( '질문하기', 'acf-css-woocommerce-toolkit' ); ?></h3>
                    <form id="acf-wc-qa-form" class="acf-wc-qa-form">
                        <?php wp_nonce_field( 'acf_wc_qa_submit', 'qa_nonce' ); ?>
                        <input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>">

                        <div class="form-row">
                            <label for="qa-question"><?php esc_html_e( '질문 내용', 'acf-css-woocommerce-toolkit' ); ?> <span class="required">*</span></label>
                            <textarea id="qa-question" name="question" rows="4" required 
                                      placeholder="<?php esc_attr_e( '상품에 대해 궁금한 점을 작성해주세요.', 'acf-css-woocommerce-toolkit' ); ?>"></textarea>
                        </div>

                        <?php if ( ! is_user_logged_in() ) : ?>
                            <div class="form-row form-row-wide">
                                <div class="form-row-half">
                                    <label for="qa-name"><?php esc_html_e( '이름', 'acf-css-woocommerce-toolkit' ); ?> <span class="required">*</span></label>
                                    <input type="text" id="qa-name" name="guest_name" required>
                                </div>
                                <div class="form-row-half">
                                    <label for="qa-email"><?php esc_html_e( '이메일', 'acf-css-woocommerce-toolkit' ); ?> <span class="required">*</span></label>
                                    <input type="email" id="qa-email" name="guest_email" required
                                           placeholder="<?php esc_attr_e( '답변 알림을 받을 이메일', 'acf-css-woocommerce-toolkit' ); ?>">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-row">
                            <label>
                                <input type="checkbox" name="is_private" value="1">
                                <?php esc_html_e( '비공개 질문 (판매자만 볼 수 있음)', 'acf-css-woocommerce-toolkit' ); ?>
                            </label>
                        </div>

                        <button type="submit" class="button alt">
                            <?php esc_html_e( '질문 등록', 'acf-css-woocommerce-toolkit' ); ?>
                        </button>
                    </form>
                </div>
            <?php else : ?>
                <div class="acf-wc-qa-login-notice">
                    <p>
                        <?php esc_html_e( '질문을 작성하려면 로그인이 필요합니다.', 'acf-css-woocommerce-toolkit' ); ?>
                        <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">
                            <?php esc_html_e( '로그인', 'acf-css-woocommerce-toolkit' ); ?>
                        </a>
                    </p>
                </div>
            <?php endif; ?>

            <!-- 질문 목록 -->
            <div class="acf-wc-qa-list">
                <?php if ( $questions->have_posts() ) : ?>
                    <?php while ( $questions->have_posts() ) : $questions->the_post(); 
                        $question_id = get_the_ID();
                        $is_private = get_post_meta( $question_id, '_is_private', true );
                        $is_answered = get_post_status() === 'answered';
                        $answer = get_post_meta( $question_id, '_answer', true );
                        $answer_date = get_post_meta( $question_id, '_answer_date', true );
                        $helpful_count = (int) get_post_meta( $question_id, '_helpful_count', true );

                        // 비공개 질문은 작성자 또는 관리자만 볼 수 있음
                        if ( $is_private && ! $this->can_view_private( $question_id ) ) {
                            continue;
                        }
                    ?>
                        <div class="acf-wc-qa-item <?php echo $is_answered ? 'answered' : 'waiting'; ?> <?php echo $is_private ? 'private' : ''; ?>">
                            <div class="qa-question">
                                <div class="qa-header">
                                    <span class="qa-status">
                                        <?php if ( $is_answered ) : ?>
                                            <span class="status-answered">✓ <?php esc_html_e( '답변완료', 'acf-css-woocommerce-toolkit' ); ?></span>
                                        <?php else : ?>
                                            <span class="status-waiting"><?php esc_html_e( '답변대기', 'acf-css-woocommerce-toolkit' ); ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <?php if ( $is_private ) : ?>
                                        <span class="qa-private">🔒 <?php esc_html_e( '비공개', 'acf-css-woocommerce-toolkit' ); ?></span>
                                    <?php endif; ?>
                                    <span class="qa-date"><?php echo esc_html( get_the_date() ); ?></span>
                                </div>

                                <div class="qa-content">
                                    <span class="qa-label">Q</span>
                                    <div class="qa-text">
                                        <?php the_content(); ?>
                                        <span class="qa-author">
                                            - <?php echo esc_html( $this->get_author_display_name( $question_id ) ); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <?php if ( $is_answered && $answer ) : ?>
                                <div class="qa-answer">
                                    <div class="qa-content">
                                        <span class="qa-label">A</span>
                                        <div class="qa-text">
                                            <?php echo wp_kses_post( wpautop( $answer ) ); ?>
                                            <span class="qa-author">
                                                - <?php echo esc_html( get_bloginfo( 'name' ) ); ?> 
                                                (<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $answer_date ) ) ); ?>)
                                            </span>
                                        </div>
                                    </div>

                                    <div class="qa-helpful">
                                        <button class="qa-helpful-btn" data-qa-id="<?php echo esc_attr( $question_id ); ?>">
                                            👍 <?php esc_html_e( '도움이 됐어요', 'acf-css-woocommerce-toolkit' ); ?>
                                            <span class="helpful-count">(<?php echo esc_html( $helpful_count ); ?>)</span>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>

                    <!-- 페이지네이션 -->
                    <div class="acf-wc-qa-pagination">
                        <?php
                        echo paginate_links( array(
                            'total'   => $questions->max_num_pages,
                            'current' => max( 1, get_query_var( 'paged' ) ),
                        ) );
                        ?>
                    </div>
                <?php else : ?>
                    <div class="acf-wc-qa-empty">
                        <p><?php esc_html_e( '아직 등록된 질문이 없습니다.', 'acf-css-woocommerce-toolkit' ); ?></p>
                        <p><?php esc_html_e( '첫 번째 질문을 등록해보세요!', 'acf-css-woocommerce-toolkit' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * 상품별 Q&A 개수
     */
    private function get_qa_count( $product_id ) {
        $args = array(
            'post_type'      => self::POST_TYPE,
            'post_status'    => array( 'publish', 'answered', 'waiting' ),
            'meta_query'     => array(
                array(
                    'key'   => '_product_id',
                    'value' => $product_id,
                ),
            ),
            'posts_per_page' => -1,
            'fields'         => 'ids',
        );

        $query = new WP_Query( $args );
        return $query->found_posts;
    }

    /**
     * 질문 목록 가져오기
     */
    private function get_questions( $product_id, $paged = 1 ) {
        $args = array(
            'post_type'      => self::POST_TYPE,
            'post_status'    => array( 'publish', 'answered', 'waiting' ),
            'meta_query'     => array(
                array(
                    'key'   => '_product_id',
                    'value' => $product_id,
                ),
            ),
            'posts_per_page' => 10,
            'paged'          => $paged,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        return new WP_Query( $args );
    }

    /**
     * 비공개 질문 조회 권한 확인
     */
    private function can_view_private( $question_id ) {
        if ( current_user_can( 'manage_woocommerce' ) ) {
            return true;
        }

        $author_id = get_post_field( 'post_author', $question_id );
        return get_current_user_id() == $author_id;
    }

    /**
     * 작성자 표시 이름
     */
    private function get_author_display_name( $question_id ) {
        $guest_name = get_post_meta( $question_id, '_guest_name', true );
        
        if ( $guest_name ) {
            // 이름 마스킹 (홍*동)
            $name = mb_substr( $guest_name, 0, 1 ) . '*' . mb_substr( $guest_name, -1 );
            return $name;
        }

        $author_id = get_post_field( 'post_author', $question_id );
        $user = get_user_by( 'id', $author_id );
        
        if ( $user ) {
            $display_name = $user->display_name ?: $user->user_login;
            return mb_substr( $display_name, 0, 1 ) . '***' . mb_substr( $display_name, -1 );
        }

        return __( '익명', 'acf-css-woocommerce-toolkit' );
    }

    /**
     * 스크립트/스타일 로드
     */
    public function enqueue_assets() {
        if ( ! is_product() ) {
            return;
        }

        wp_enqueue_style(
            'acf-wc-product-qa',
            ACF_WC_TOOLKIT_URL . 'assets/css/product-qa.css',
            array(),
            ACF_WC_TOOLKIT_VERSION
        );

        wp_enqueue_script(
            'acf-wc-product-qa',
            ACF_WC_TOOLKIT_URL . 'assets/js/product-qa.js',
            array( 'jquery' ),
            ACF_WC_TOOLKIT_VERSION,
            true
        );

        wp_localize_script( 'acf-wc-product-qa', 'acfWcQA', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'acf_wc_qa_nonce' ),
            'i18n'    => array(
                'submitting' => __( '등록 중...', 'acf-css-woocommerce-toolkit' ),
                'success'    => __( '질문이 등록되었습니다. 빠른 시일 내에 답변드리겠습니다.', 'acf-css-woocommerce-toolkit' ),
                'error'      => __( '오류가 발생했습니다. 다시 시도해주세요.', 'acf-css-woocommerce-toolkit' ),
                'thanks'     => __( '감사합니다!', 'acf-css-woocommerce-toolkit' ),
            ),
        ) );
    }

    /**
     * AJAX: 질문 제출
     */
    public function ajax_submit_question() {
        check_ajax_referer( 'acf_wc_qa_submit', 'qa_nonce' );

        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
        $question = isset( $_POST['question'] ) ? sanitize_textarea_field( $_POST['question'] ) : '';
        $is_private = isset( $_POST['is_private'] ) && $_POST['is_private'] === '1';

        if ( ! $product_id || empty( $question ) ) {
            wp_send_json_error( __( '필수 항목을 입력해주세요.', 'acf-css-woocommerce-toolkit' ) );
        }

        // 상품 존재 확인
        $product = wc_get_product( $product_id );
        if ( ! $product ) {
            wp_send_json_error( __( '상품을 찾을 수 없습니다.', 'acf-css-woocommerce-toolkit' ) );
        }

        // 스팸 방지 (같은 IP에서 1분 내 중복 등록 방지)
        $recent = get_transient( 'acf_wc_qa_' . md5( $_SERVER['REMOTE_ADDR'] ) );
        if ( $recent ) {
            wp_send_json_error( __( '잠시 후 다시 시도해주세요.', 'acf-css-woocommerce-toolkit' ) );
        }

        // 질문 저장
        $post_data = array(
            'post_title'   => wp_trim_words( $question, 10 ),
            'post_content' => $question,
            'post_status'  => 'waiting',
            'post_type'    => self::POST_TYPE,
            'post_author'  => get_current_user_id() ?: 0,
        );

        $question_id = wp_insert_post( $post_data );

        if ( is_wp_error( $question_id ) ) {
            wp_send_json_error( $question_id->get_error_message() );
        }

        // 메타 데이터 저장
        update_post_meta( $question_id, '_product_id', $product_id );
        update_post_meta( $question_id, '_is_private', $is_private ? '1' : '' );

        // 게스트 정보
        if ( ! is_user_logged_in() ) {
            $guest_name = isset( $_POST['guest_name'] ) ? sanitize_text_field( $_POST['guest_name'] ) : '';
            $guest_email = isset( $_POST['guest_email'] ) ? sanitize_email( $_POST['guest_email'] ) : '';
            
            update_post_meta( $question_id, '_guest_name', $guest_name );
            update_post_meta( $question_id, '_guest_email', $guest_email );
        }

        // 스팸 방지 트랜시언트 설정
        set_transient( 'acf_wc_qa_' . md5( $_SERVER['REMOTE_ADDR'] ), true, 60 );

        // 관리자 알림
        $this->notify_admin_new_question( $question_id, $product );

        wp_send_json_success( array(
            'message' => __( '질문이 등록되었습니다.', 'acf-css-woocommerce-toolkit' ),
        ) );
    }

    /**
     * AJAX: 답변 제출
     */
    public function ajax_submit_answer() {
        check_ajax_referer( 'acf_wc_qa_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-woocommerce-toolkit' ) );
        }

        $question_id = isset( $_POST['question_id'] ) ? absint( $_POST['question_id'] ) : 0;
        $answer = isset( $_POST['answer'] ) ? wp_kses_post( $_POST['answer'] ) : '';

        if ( ! $question_id || empty( $answer ) ) {
            wp_send_json_error( __( '필수 항목을 입력해주세요.', 'acf-css-woocommerce-toolkit' ) );
        }

        // 답변 저장
        update_post_meta( $question_id, '_answer', $answer );
        update_post_meta( $question_id, '_answer_date', current_time( 'mysql' ) );
        update_post_meta( $question_id, '_answered_by', get_current_user_id() );

        // 상태 변경
        wp_update_post( array(
            'ID'          => $question_id,
            'post_status' => 'answered',
        ) );

        // 답변 알림 이벤트 발생
        do_action( 'acf_wc_qa_answered', $question_id, $answer );

        wp_send_json_success( array(
            'message' => __( '답변이 등록되었습니다.', 'acf-css-woocommerce-toolkit' ),
        ) );
    }

    /**
     * AJAX: 도움이 됨 표시
     */
    public function ajax_mark_helpful() {
        $question_id = isset( $_POST['question_id'] ) ? absint( $_POST['question_id'] ) : 0;

        if ( ! $question_id ) {
            wp_send_json_error();
        }

        // 중복 방지 (쿠키 또는 세션 기반)
        $cookie_key = 'acf_wc_qa_helpful_' . $question_id;
        if ( isset( $_COOKIE[ $cookie_key ] ) ) {
            wp_send_json_error( __( '이미 평가하셨습니다.', 'acf-css-woocommerce-toolkit' ) );
        }

        $count = (int) get_post_meta( $question_id, '_helpful_count', true );
        $count++;
        update_post_meta( $question_id, '_helpful_count', $count );

        // 쿠키 설정 (30일)
        setcookie( $cookie_key, '1', time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );

        wp_send_json_success( array(
            'count' => $count,
        ) );
    }

    /**
     * 관리자에게 새 질문 알림
     */
    private function notify_admin_new_question( $question_id, $product ) {
        $admin_email = get_option( 'admin_email' );
        $subject = sprintf( 
            __( '[%s] 새로운 상품 Q&A 질문', 'acf-css-woocommerce-toolkit' ), 
            get_bloginfo( 'name' ) 
        );

        $message = sprintf(
            __( "새로운 질문이 등록되었습니다.\n\n상품: %s\n질문: %s\n\n답변하기: %s", 'acf-css-woocommerce-toolkit' ),
            $product->get_name(),
            get_the_content( null, false, $question_id ),
            admin_url( 'post.php?post=' . $question_id . '&action=edit' )
        );

        wp_mail( $admin_email, $subject, $message );
    }

    /**
     * 답변 알림 이메일 전송
     */
    public function send_answer_notification( $question_id, $answer ) {
        // 회원인 경우
        $author_id = get_post_field( 'post_author', $question_id );
        if ( $author_id ) {
            $user = get_user_by( 'id', $author_id );
            $email = $user ? $user->user_email : '';
        } else {
            // 게스트인 경우
            $email = get_post_meta( $question_id, '_guest_email', true );
        }

        if ( empty( $email ) || ! is_email( $email ) ) {
            return;
        }

        $product_id = get_post_meta( $question_id, '_product_id', true );
        $product = wc_get_product( $product_id );

        $subject = sprintf(
            __( '[%s] 질문에 답변이 등록되었습니다', 'acf-css-woocommerce-toolkit' ),
            get_bloginfo( 'name' )
        );

        $message = sprintf(
            __( "안녕하세요,\n\n%s 상품에 대한 질문에 답변이 등록되었습니다.\n\n질문: %s\n\n답변: %s\n\n상품 보기: %s", 'acf-css-woocommerce-toolkit' ),
            $product ? $product->get_name() : '',
            get_the_content( null, false, $question_id ),
            $answer,
            $product ? $product->get_permalink() . '#tab-product_qa' : ''
        );

        wp_mail( $email, $subject, $message );
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __( '상품 Q&A', 'acf-css-woocommerce-toolkit' ),
            __( '상품 Q&A', 'acf-css-woocommerce-toolkit' ),
            'manage_woocommerce',
            'edit.php?post_type=' . self::POST_TYPE
        );
    }

    /**
     * 메타박스 추가
     */
    public function add_meta_boxes() {
        add_meta_box(
            'acf_wc_qa_details',
            __( 'Q&A 상세 정보', 'acf-css-woocommerce-toolkit' ),
            array( $this, 'render_details_meta_box' ),
            self::POST_TYPE,
            'side',
            'high'
        );

        add_meta_box(
            'acf_wc_qa_answer',
            __( '답변 작성', 'acf-css-woocommerce-toolkit' ),
            array( $this, 'render_answer_meta_box' ),
            self::POST_TYPE,
            'normal',
            'high'
        );
    }

    /**
     * 상세 정보 메타박스 렌더링
     */
    public function render_details_meta_box( $post ) {
        $product_id = get_post_meta( $post->ID, '_product_id', true );
        $product = wc_get_product( $product_id );
        $is_private = get_post_meta( $post->ID, '_is_private', true );
        ?>
        <p>
            <strong><?php esc_html_e( '상품:', 'acf-css-woocommerce-toolkit' ); ?></strong><br>
            <?php if ( $product ) : ?>
                <a href="<?php echo esc_url( $product->get_permalink() ); ?>" target="_blank">
                    <?php echo esc_html( $product->get_name() ); ?>
                </a>
            <?php else : ?>
                <em><?php esc_html_e( '삭제된 상품', 'acf-css-woocommerce-toolkit' ); ?></em>
            <?php endif; ?>
        </p>
        <p>
            <strong><?php esc_html_e( '공개 설정:', 'acf-css-woocommerce-toolkit' ); ?></strong><br>
            <?php echo $is_private ? __( '🔒 비공개', 'acf-css-woocommerce-toolkit' ) : __( '🌐 공개', 'acf-css-woocommerce-toolkit' ); ?>
        </p>
        <?php
    }

    /**
     * 답변 메타박스 렌더링
     */
    public function render_answer_meta_box( $post ) {
        $answer = get_post_meta( $post->ID, '_answer', true );
        $answer_date = get_post_meta( $post->ID, '_answer_date', true );
        
        wp_nonce_field( 'acf_wc_qa_answer', 'qa_answer_nonce' );
        ?>
        <div class="acf-wc-qa-answer-box">
            <?php
            wp_editor( $answer, 'acf_wc_qa_answer', array(
                'textarea_name' => 'acf_wc_qa_answer',
                'textarea_rows' => 8,
                'media_buttons' => false,
                'teeny'         => true,
            ) );
            ?>
            
            <?php if ( $answer_date ) : ?>
                <p class="description">
                    <?php printf( 
                        esc_html__( '마지막 답변: %s', 'acf-css-woocommerce-toolkit' ), 
                        date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $answer_date ) )
                    ); ?>
                </p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * 메타박스 저장
     */
    public function save_meta_boxes( $post_id ) {
        if ( ! isset( $_POST['qa_answer_nonce'] ) || 
             ! wp_verify_nonce( $_POST['qa_answer_nonce'], 'acf_wc_qa_answer' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( get_post_type( $post_id ) !== self::POST_TYPE ) {
            return;
        }

        if ( isset( $_POST['acf_wc_qa_answer'] ) ) {
            $answer = wp_kses_post( $_POST['acf_wc_qa_answer'] );
            $old_answer = get_post_meta( $post_id, '_answer', true );

            update_post_meta( $post_id, '_answer', $answer );

            // 새로운 답변이 등록된 경우
            if ( ! empty( $answer ) && $answer !== $old_answer ) {
                update_post_meta( $post_id, '_answer_date', current_time( 'mysql' ) );
                update_post_meta( $post_id, '_answered_by', get_current_user_id() );

                // 상태 변경
                wp_update_post( array(
                    'ID'          => $post_id,
                    'post_status' => 'answered',
                ) );

                // 알림 발송
                do_action( 'acf_wc_qa_answered', $post_id, $answer );
            }
        }
    }
}
