<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Neural Link Version Manager
 * 
 * 관리자가 새 버전을 등록하고 에디션별 ZIP 파일을 업로드하는 관리자 UI입니다.
 * 
 * @since v3.0.0
 */
class JJ_Neural_Link_Version_Manager {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    public function add_menu_page() {
        add_submenu_page(
            'edit.php?post_type=jj_license', // 라이센스 관리자 메뉴 하위 (임시)
            '버전 관리',
            '버전 관리',
            'manage_options',
            'jj-neural-link-versions',
            array( $this, 'render_page' )
        );
    }

    public function register_settings() {
        register_setting( 'jj_neural_link_versions', 'jj_neural_link_latest_version' );
        
        // 에디션별 파일 URL 저장 (Free, Basic, Premium, Unlimited, Partner, Master)
        $editions = array( 'free', 'basic', 'premium', 'unlimited', 'partner', 'master' );
        foreach ( $editions as $edition ) {
            register_setting( 'jj_neural_link_versions', "jj_neural_link_file_{$edition}" );
        }
    }

    public function enqueue_scripts( $hook ) {
        if ( 'jj_license_page_jj-neural-link-versions' !== $hook ) {
            return;
        }
        wp_enqueue_media();
    }

    public function render_page() {
        $editions = array( 'free', 'basic', 'premium', 'unlimited', 'partner', 'master' );
        ?>
        <div class="wrap">
            <h1>📦 Neural Link: 버전 및 배포 관리</h1>
            <p>새로운 플러그인 버전을 등록하고 배포 파일을 업로드하세요.</p>
            
            <form method="post" action="options.php">
                <?php settings_fields( 'jj_neural_link_versions' ); ?>
                <?php do_settings_sections( 'jj_neural_link_versions' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">최신 버전 번호</th>
                        <td>
                            <input type="text" name="jj_neural_link_latest_version" value="<?php echo esc_attr( get_option( 'jj_neural_link_latest_version' ) ); ?>" class="regular-text" placeholder="예: 5.5.0" />
                            <p class="description">이 버전보다 낮은 버전을 사용하는 클라이언트에게 업데이트 알림이 전송됩니다.</p>
                        </td>
                    </tr>
                    
                    <?php foreach ( $editions as $edition ) : 
                        $option_name = "jj_neural_link_file_{$edition}";
                        $file_url = get_option( $option_name );
                    ?>
                    <tr>
                        <th scope="row"><?php echo ucfirst( $edition ); ?> Edition 파일</th>
                        <td>
                            <input type="text" name="<?php echo $option_name; ?>" id="<?php echo $option_name; ?>" value="<?php echo esc_attr( $file_url ); ?>" class="regular-text" />
                            <button type="button" class="button jj-upload-btn" data-target="<?php echo $option_name; ?>">파일 선택/업로드</button>
                            <?php if ( $file_url ) : ?>
                                <br><a href="<?php echo esc_url( $file_url ); ?>" target="_blank">다운로드 확인</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <?php submit_button( '버전 정보 저장 및 배포 준비' ); ?>
            </form>
            
            <script>
            jQuery(document).ready(function($){
                $('.jj-upload-btn').click(function(e) {
                    e.preventDefault();
                    var target = '#' + $(this).data('target');
                    var image_frame;
                    if(image_frame){
                        image_frame.open();
                    }
                    image_frame = wp.media({
                        title: '배포 파일 선택',
                        multiple : false,
                        library : {
                            type : 'application/zip'
                        }
                    });
                    image_frame.on('close',function(){
                        var selection =  image_frame.state().get('selection');
                        var gallery_attachment = selection.first();
                        var gallery_img_url = selection.first().toJSON().url;
                        $(target).val(gallery_img_url);
                    });
                    image_frame.open();
                });
            });
            </script>
        </div>
        <?php
    }
}

