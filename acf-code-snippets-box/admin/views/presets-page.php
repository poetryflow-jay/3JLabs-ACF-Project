<?php
/**
 * ACF Code Snippets Box - 프리셋 라이브러리 페이지
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$css_presets = ACF_CSB_Presets::get_css_presets();
$js_presets = ACF_CSB_Presets::get_js_presets();
$php_presets = ACF_CSB_Presets::get_php_presets();
?>
<div class="wrap acf-csb-presets-page">
    <h1>
        <span class="dashicons dashicons-welcome-widgets-menus" style="font-size: 30px; width: 30px; height: 30px; margin-right: 10px;"></span>
        <?php esc_html_e( '프리셋 라이브러리', 'acf-code-snippets-box' ); ?>
    </h1>
    <p class="description">
        <?php esc_html_e( '자주 사용되는 유용한 코드 스니펫을 원클릭으로 추가하세요.', 'acf-code-snippets-box' ); ?>
    </p>

    <!-- [v4.2.0] 검색 및 필터 바 -->
    <div class="acf-csb-preset-search-bar" style="
        margin: 20px 0;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    ">
        <div style="flex: 1; min-width: 250px;">
            <input type="text" 
                   id="preset-search-input" 
                   placeholder="<?php esc_attr_e( '프리셋 검색... (이름, 설명, 태그)', 'acf-code-snippets-box' ); ?>" 
                   style="width: 100%; padding: 10px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;">
        </div>
        <div>
            <select id="preset-category-filter" style="padding: 10px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;">
                <option value=""><?php esc_html_e( '모든 카테고리', 'acf-code-snippets-box' ); ?></option>
                <?php
                $categories = ACF_CSB_Presets::get_all_categories();
                $counts = ACF_CSB_Presets::get_category_counts();
                foreach ( $categories as $category ) :
                    $category_name = ACF_CSB_Presets::get_category_name( $category );
                    $count = isset( $counts[ $category ] ) ? $counts[ $category ] : 0;
                ?>
                    <option value="<?php echo esc_attr( $category ); ?>">
                        <?php echo esc_html( $category_name ); ?> (<?php echo esc_html( $count ); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="button" class="button button-primary" id="preset-search-btn">
                <?php esc_html_e( '🔍 검색', 'acf-code-snippets-box' ); ?>
            </button>
            <button type="button" class="button" id="preset-reset-btn">
                <?php esc_html_e( '초기화', 'acf-code-snippets-box' ); ?>
            </button>
        </div>
    </div>

    <!-- 검색 결과 표시 영역 -->
    <div id="preset-search-results" style="display: none; margin: 20px 0;">
        <div class="search-results-header" style="padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="margin: 0;">
                <?php esc_html_e( '검색 결과', 'acf-code-snippets-box' ); ?>
                <span id="search-results-count" style="color: #666; font-weight: normal;"></span>
            </h3>
            <button type="button" class="button button-small" id="close-search-results" style="margin-top: 10px;">
                <?php esc_html_e( '검색 결과 닫기', 'acf-code-snippets-box' ); ?>
            </button>
        </div>
        <div id="search-results-content"></div>
    </div>

    <!-- 탭 네비게이션 -->
    <nav class="nav-tab-wrapper" style="margin-top: 20px;">
        <a href="#css-presets" class="nav-tab nav-tab-active" data-tab="css"><?php esc_html_e( 'CSS 프리셋', 'acf-code-snippets-box' ); ?></a>
        <a href="#js-presets" class="nav-tab" data-tab="js"><?php esc_html_e( 'JavaScript 프리셋', 'acf-code-snippets-box' ); ?></a>
        <a href="#php-presets" class="nav-tab" data-tab="php"><?php esc_html_e( 'PHP 프리셋', 'acf-code-snippets-box' ); ?></a>
    </nav>

    <!-- CSS 프리셋 -->
    <div id="css-presets" class="acf-csb-preset-tab" style="margin-top: 20px;">
        <div class="acf-csb-preset-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php foreach ( $css_presets as $id => $preset ) : ?>
            <div class="acf-csb-preset-card" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); color: #fff; padding: 15px 20px;">
                    <h3 style="margin: 0; font-size: 16px;"><?php echo esc_html( $preset['name'] ); ?></h3>
                </div>
                <div style="padding: 20px;">
                    <p style="color: #646970; margin: 0 0 15px;"><?php echo esc_html( $preset['description'] ); ?></p>
                    <pre style="background: #f6f7f7; padding: 15px; border-radius: 6px; font-size: 12px; overflow-x: auto; max-height: 150px;"><code><?php echo esc_html( $preset['code'] ); ?></code></pre>
                    <div style="margin-top: 15px; display: flex; gap: 10px; align-items: center;">
                        <?php
                        // 기존 스니펫이 있는지 확인
                        $existing_snippet = get_posts( array(
                            'post_type'      => 'acf_code_snippet',
                            'meta_key'       => '_acf_csb_preset_id',
                            'meta_value'     => $id,
                            'posts_per_page' => 1,
                            'post_status'    => 'any',
                        ) );
                        $has_snippet = ! empty( $existing_snippet );
                        $snippet_id = $has_snippet ? $existing_snippet[0]->ID : 0;
                        $is_enabled = $has_snippet ? ( get_post_meta( $snippet_id, '_acf_csb_enabled', true ) === '1' ) : false;
                        ?>
                        <?php if ( $has_snippet ) : ?>
                            <button type="button" 
                                    class="button <?php echo $is_enabled ? 'button-secondary' : 'button-primary'; ?> acf-csb-toggle-preset" 
                                    data-type="css" 
                                    data-id="<?php echo esc_attr( $id ); ?>"
                                    data-post-id="<?php echo esc_attr( $snippet_id ); ?>"
                                    data-enabled="<?php echo $is_enabled ? '1' : '0'; ?>">
                                <?php echo $is_enabled ? '🔴 비활성화' : '🟢 활성화'; ?>
                            </button>
                            <a href="<?php echo admin_url( 'post.php?post=' . $snippet_id . '&action=edit' ); ?>" class="button">
                                <?php esc_html_e( '✏️ 수정', 'acf-code-snippets-box' ); ?>
                            </a>
                        <?php else : ?>
                            <button type="button" class="button button-primary acf-csb-use-preset" data-type="css" data-id="<?php echo esc_attr( $id ); ?>">
                                <?php esc_html_e( '스니펫으로 추가', 'acf-code-snippets-box' ); ?>
                            </button>
                        <?php endif; ?>
                        <button type="button" class="button acf-csb-copy-code" data-code="<?php echo esc_attr( $preset['code'] ); ?>">
                            <?php esc_html_e( '코드 복사', 'acf-code-snippets-box' ); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- JavaScript 프리셋 -->
    <div id="js-presets" class="acf-csb-preset-tab" style="margin-top: 20px; display: none;">
        <div class="acf-csb-preset-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php foreach ( $js_presets as $id => $preset ) : ?>
            <div class="acf-csb-preset-card" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="background: linear-gradient(135deg, #f0ad4e 0%, #ffc107 100%); color: #fff; padding: 15px 20px;">
                    <h3 style="margin: 0; font-size: 16px;"><?php echo esc_html( $preset['name'] ); ?></h3>
                </div>
                <div style="padding: 20px;">
                    <p style="color: #646970; margin: 0 0 15px;"><?php echo esc_html( $preset['description'] ); ?></p>
                    <pre style="background: #f6f7f7; padding: 15px; border-radius: 6px; font-size: 12px; overflow-x: auto; max-height: 150px;"><code><?php echo esc_html( $preset['code'] ); ?></code></pre>
                    <div style="margin-top: 15px; display: flex; gap: 10px; align-items: center;">
                        <?php
                        $existing_snippet = get_posts( array(
                            'post_type'      => 'acf_code_snippet',
                            'meta_key'       => '_acf_csb_preset_id',
                            'meta_value'     => $id,
                            'posts_per_page' => 1,
                            'post_status'    => 'any',
                        ) );
                        $has_snippet = ! empty( $existing_snippet );
                        $snippet_id = $has_snippet ? $existing_snippet[0]->ID : 0;
                        $is_enabled = $has_snippet ? ( get_post_meta( $snippet_id, '_acf_csb_enabled', true ) === '1' ) : false;
                        ?>
                        <?php if ( $has_snippet ) : ?>
                            <button type="button" 
                                    class="button <?php echo $is_enabled ? 'button-secondary' : 'button-primary'; ?> acf-csb-toggle-preset" 
                                    data-type="js" 
                                    data-id="<?php echo esc_attr( $id ); ?>"
                                    data-post-id="<?php echo esc_attr( $snippet_id ); ?>"
                                    data-enabled="<?php echo $is_enabled ? '1' : '0'; ?>">
                                <?php echo $is_enabled ? '🔴 비활성화' : '🟢 활성화'; ?>
                            </button>
                            <a href="<?php echo admin_url( 'post.php?post=' . $snippet_id . '&action=edit' ); ?>" class="button">
                                <?php esc_html_e( '✏️ 수정', 'acf-code-snippets-box' ); ?>
                            </a>
                        <?php else : ?>
                            <button type="button" class="button button-primary acf-csb-use-preset" data-type="js" data-id="<?php echo esc_attr( $id ); ?>">
                                <?php esc_html_e( '스니펫으로 추가', 'acf-code-snippets-box' ); ?>
                            </button>
                        <?php endif; ?>
                        <button type="button" class="button acf-csb-copy-code" data-code="<?php echo esc_attr( $preset['code'] ); ?>">
                            <?php esc_html_e( '코드 복사', 'acf-code-snippets-box' ); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- PHP 프리셋 -->
    <div id="php-presets" class="acf-csb-preset-tab" style="margin-top: 20px; display: none;">
        <div class="notice notice-warning" style="margin-bottom: 20px;">
            <p>
                <strong><?php esc_html_e( '⚠️ 주의:', 'acf-code-snippets-box' ); ?></strong>
                <?php esc_html_e( 'PHP 코드를 실행하려면 설정에서 "PHP 코드 실행"을 활성화해야 합니다.', 'acf-code-snippets-box' ); ?>
            </p>
        </div>
        <div class="acf-csb-preset-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php foreach ( $php_presets as $id => $preset ) : ?>
            <div class="acf-csb-preset-card" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="background: linear-gradient(135deg, #8892bf 0%, #a29bfe 100%); color: #fff; padding: 15px 20px;">
                    <h3 style="margin: 0; font-size: 16px;"><?php echo esc_html( $preset['name'] ); ?></h3>
                    <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 10px;">
                        <?php echo esc_html( $preset['category'] ); ?>
                    </span>
                </div>
                <div style="padding: 20px;">
                    <p style="color: #646970; margin: 0 0 15px;"><?php echo esc_html( $preset['description'] ); ?></p>
                    <pre style="background: #f6f7f7; padding: 15px; border-radius: 6px; font-size: 12px; overflow-x: auto; max-height: 150px;"><code><?php echo esc_html( $preset['code'] ); ?></code></pre>
                    <div style="margin-top: 15px; display: flex; gap: 10px; align-items: center;">
                        <?php
                        $existing_snippet = get_posts( array(
                            'post_type'      => 'acf_code_snippet',
                            'meta_key'       => '_acf_csb_preset_id',
                            'meta_value'     => $id,
                            'posts_per_page' => 1,
                            'post_status'    => 'any',
                        ) );
                        $has_snippet = ! empty( $existing_snippet );
                        $snippet_id = $has_snippet ? $existing_snippet[0]->ID : 0;
                        $is_enabled = $has_snippet ? ( get_post_meta( $snippet_id, '_acf_csb_enabled', true ) === '1' ) : false;
                        ?>
                        <?php if ( $has_snippet ) : ?>
                            <button type="button" 
                                    class="button <?php echo $is_enabled ? 'button-secondary' : 'button-primary'; ?> acf-csb-toggle-preset" 
                                    data-type="php" 
                                    data-id="<?php echo esc_attr( $id ); ?>"
                                    data-post-id="<?php echo esc_attr( $snippet_id ); ?>"
                                    data-enabled="<?php echo $is_enabled ? '1' : '0'; ?>">
                                <?php echo $is_enabled ? '🔴 비활성화' : '🟢 활성화'; ?>
                            </button>
                            <a href="<?php echo admin_url( 'post.php?post=' . $snippet_id . '&action=edit' ); ?>" class="button">
                                <?php esc_html_e( '✏️ 수정', 'acf-code-snippets-box' ); ?>
                            </a>
                        <?php else : ?>
                            <button type="button" class="button button-primary acf-csb-use-preset" data-type="php" data-id="<?php echo esc_attr( $id ); ?>">
                                <?php esc_html_e( '스니펫으로 추가', 'acf-code-snippets-box' ); ?>
                            </button>
                        <?php endif; ?>
                        <button type="button" class="button acf-csb-copy-code" data-code="<?php echo esc_attr( $preset['code'] ); ?>">
                            <?php esc_html_e( '코드 복사', 'acf-code-snippets-box' ); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // [v4.2.0] 프리셋 검색 기능
    let searchTimeout = null;
    
    $('#preset-search-input').on('input', function() {
        const query = $(this).val().trim();
        
        if (searchTimeout) clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(function() {
                performSearch(query);
            }, 500);
        } else if (query.length === 0) {
            $('#preset-search-results').hide();
            $('.acf-csb-preset-tab').show();
        }
    });
    
    $('#preset-search-btn').on('click', function() {
        const query = $('#preset-search-input').val().trim();
        if (query.length >= 2) {
            performSearch(query);
        }
    });
    
    $('#preset-reset-btn').on('click', function() {
        $('#preset-search-input').val('');
        $('#preset-category-filter').val('');
        $('#preset-search-results').hide();
        $('.acf-csb-preset-tab').show();
    });
    
    $('#close-search-results').on('click', function() {
        $('#preset-search-results').hide();
        $('.acf-csb-preset-tab').show();
    });
    
    function performSearch(query) {
        const category = $('#preset-category-filter').val();
        const currentTab = $('.nav-tab-active').data('tab') || 'all';
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'acf_csb_search_presets',
                nonce: '<?php echo wp_create_nonce( "acf_csb_nonce" ); ?>',
                query: query,
                type: currentTab,
                category: category
            },
            success: function(response) {
                if (response.success) {
                    displaySearchResults(response.data.results, query, response.data.count);
                } else {
                    alert('검색 중 오류가 발생했습니다.');
                }
            },
            error: function() {
                alert('검색 요청 실패');
            }
        });
    }
    
    function displaySearchResults(results, query, count) {
        const $resultsContainer = $('#preset-search-results');
        const $resultsContent = $('#search-results-content');
        const $countSpan = $('#search-results-count');
        
        $countSpan.text('(' + count + '개 결과)');
        
        let html = '';
        
        if (count === 0) {
            html = '<div style="text-align: center; padding: 40px; color: #666;">검색 결과가 없습니다.</div>';
        } else {
            // 타입별로 그룹화하여 표시
            const typeLabels = {
                'css': 'CSS 프리셋',
                'js': 'JavaScript 프리셋',
                'php': 'PHP 프리셋',
                'woocommerce_php': 'WooCommerce PHP 프리셋',
                'woocommerce_css': 'WooCommerce CSS 프리셋',
                'utility': '유틸리티 프리셋'
            };
            
            Object.keys(results).forEach(function(type) {
                if (results[type].length === 0) return;
                
                html += '<div style="margin-bottom: 30px;">';
                html += '<h3 style="margin: 0 0 20px; padding-bottom: 10px; border-bottom: 2px solid #ddd;">' + (typeLabels[type] || type) + '</h3>';
                html += '<div class="acf-csb-preset-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">';
                
                Object.keys(results[type]).forEach(function(id) {
                    const preset = results[type][id];
                    html += buildPresetCardHTML(preset, type, id);
                });
                
                html += '</div></div>';
            });
        }
        
        $resultsContent.html(html);
        $resultsContainer.show();
        $('.acf-csb-preset-tab').hide();
        
        // 검색 결과에서도 프리셋 사용 버튼 이벤트 바인딩
        bindPresetEvents();
    }
    
    function buildPresetCardHTML(preset, type, id) {
        const typeColors = {
            'css': 'linear-gradient(135deg, #f5576c 0%, #f093fb 100%)',
            'js': 'linear-gradient(135deg, #f0ad4e 0%, #ffc107 100%)',
            'php': 'linear-gradient(135deg, #8892bf 0%, #a29bfe 100%)',
            'woocommerce_php': 'linear-gradient(135deg, #7f54b3 0%, #9b59b6 100%)',
            'woocommerce_css': 'linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)',
            'utility': 'linear-gradient(135deg, #34495e 0%, #2c3e50 100%)'
        };
        
        const bgColor = typeColors[type] || typeColors['css'];
        const categoryName = preset.category ? ACF_CSB_Presets.get_category_name(preset.category) : '';
        
        let html = '<div class="acf-csb-preset-card" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">';
        html += '<div style="background: ' + bgColor + '; color: #fff; padding: 15px 20px;">';
        html += '<h3 style="margin: 0; font-size: 16px;">' + escapeHtml(preset.name) + '</h3>';
        if (categoryName) {
            html += '<span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 10px;">' + escapeHtml(categoryName) + '</span>';
        }
        html += '</div>';
        html += '<div style="padding: 20px;">';
        html += '<p style="color: #646970; margin: 0 0 15px;">' + escapeHtml(preset.description) + '</p>';
        html += '<pre style="background: #f6f7f7; padding: 15px; border-radius: 6px; font-size: 12px; overflow-x: auto; max-height: 150px;"><code>' + escapeHtml(preset.code) + '</code></pre>';
        html += '<div style="margin-top: 15px; display: flex; gap: 10px; align-items: center;">';
        html += '<button type="button" class="button button-primary acf-csb-use-preset" data-type="' + type + '" data-id="' + id + '">스니펫으로 추가</button>';
        html += '<button type="button" class="button acf-csb-copy-code" data-code="' + escapeHtml(preset.code) + '">코드 복사</button>';
        html += '</div></div></div>';
        
        return html;
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function bindPresetEvents() {
        // 코드 복사
        $('.acf-csb-copy-code').off('click').on('click', function() {
            const code = $(this).data('code');
            navigator.clipboard.writeText(code).then(() => {
                const originalText = $(this).text();
                $(this).text('복사됨!');
                setTimeout(() => $(this).text(originalText), 2000);
            });
        });
        
        // 프리셋 사용
        $('.acf-csb-use-preset').off('click').on('click', function() {
            const type = $(this).data('type');
            const id = $(this).data('id');
            const $btn = $(this);
            
            $btn.prop('disabled', true).text('추가 중...');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'acf_csb_create_preset_snippet',
                    nonce: '<?php echo wp_create_nonce( "acf_csb_nonce" ); ?>',
                    preset_type: type,
                    preset_id: id
                },
                success: function(response) {
                    if (response.success) {
                        $btn.text('추가됨!').addClass('button-secondary');
                        setTimeout(() => {
                            $btn.text('스니펫으로 추가').removeClass('button-secondary').prop('disabled', false);
                        }, 2000);
                    } else {
                        alert('오류: ' + (response.data || '스니펫 생성 실패'));
                        $btn.prop('disabled', false).text('스니펫으로 추가');
                    }
                },
                error: function() {
                    alert('서버 통신 오류가 발생했습니다.');
                    $btn.prop('disabled', false).text('스니펫으로 추가');
                }
            });
        });
    }

    // 탭 전환
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        $('.acf-csb-preset-tab').hide();
        $('#' + $(this).data('tab') + '-presets').show();
    });

    // 코드 복사
    $('.acf-csb-copy-code').on('click', function() {
        const code = $(this).data('code');
        navigator.clipboard.writeText(code).then(() => {
            const originalText = $(this).text();
            $(this).text('<?php echo esc_js( __( '복사됨!', 'acf-code-snippets-box' ) ); ?>');
            setTimeout(() => $(this).text(originalText), 2000);
        });
    });

    // 프리셋 사용 - 원클릭 추가 (바로 활성화)
    $('.acf-csb-use-preset').on('click', function() {
        const type = $(this).data('type');
        const id = $(this).data('id');
        const $btn = $(this);
        const $card = $btn.closest('.acf-csb-preset-card');
        
        $btn.prop('disabled', true).text('<?php echo esc_js( __( "추가 중...", "acf-code-snippets-box" ) ); ?>');
        
        // 새 스니펫 생성 및 바로 활성화
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'acf_csb_create_preset_snippet',
                nonce: '<?php echo wp_create_nonce( "acf_csb_nonce" ); ?>',
                preset_type: type,
                preset_id: id
            },
            success: function(response) {
                if (response.success) {
                    // 성공 메시지 표시
                    const $successMsg = $('<div class="notice notice-success inline" style="margin: 10px 0; padding: 10px;"><p>✅ ' + (response.data.message || '<?php echo esc_js( __( "프리셋이 활성화되었습니다.", "acf-code-snippets-box" ) ); ?>') + '</p></div>');
                    $card.find('.acf-csb-preset-card > div:last-child').prepend($successMsg);
                    
                    // 3초 후 메시지 제거
                    setTimeout(function() {
                        $successMsg.fadeOut(function() {
                            $(this).remove();
                        });
                    }, 3000);
                    
                    // 버튼을 토글 버튼으로 변경
                    const postId = response.data.post_id;
                    $btn.removeClass('acf-csb-use-preset button-primary')
                        .addClass('acf-csb-toggle-preset button-secondary')
                        .data('type', type)
                        .data('id', id)
                        .data('post-id', postId)
                        .data('enabled', '1')
                        .html('🔴 비활성화')
                        .prop('disabled', false);
                    
                    // 수정 버튼 추가
                    if (!$card.find('a[href*="post.php"]').length) {
                        const $editBtn = $('<a href="<?php echo esc_url( admin_url( "post.php" ) ); ?>?post=' + postId + '&action=edit" class="button"><?php echo esc_js( __( "✏️ 수정", "acf-code-snippets-box" ) ); ?></a>');
                        $btn.after($editBtn);
                    }
                } else {
                    alert('오류: ' + (response.data || '<?php echo esc_js( __( "스니펫 생성 실패", "acf-code-snippets-box" ) ); ?>'));
                    $btn.prop('disabled', false).text('<?php echo esc_js( __( "스니펫으로 추가", "acf-code-snippets-box" ) ); ?>');
                }
            },
            error: function() {
                alert('<?php echo esc_js( __( "서버 통신 오류가 발생했습니다.", "acf-code-snippets-box" ) ); ?>');
                $btn.prop('disabled', false).text('<?php echo esc_js( __( "스니펫으로 추가", "acf-code-snippets-box" ) ); ?>');
            }
        });
    });
    
    // 프리셋 토글 (활성화/비활성화)
    $('.acf-csb-toggle-preset').on('click', function() {
        const type = $(this).data('type');
        const id = $(this).data('id');
        const postId = $(this).data('post-id');
        const currentEnabled = $(this).data('enabled') === '1';
        const $btn = $(this);
        
        $btn.prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'acf_csb_toggle_preset',
                nonce: '<?php echo wp_create_nonce( "acf_csb_nonce" ); ?>',
                preset_type: type,
                preset_id: id,
                action_type: 'toggle'
            },
            success: function(response) {
                if (response.success) {
                    // 버튼 상태 업데이트
                    if (response.data.enabled) {
                        $btn.removeClass('button-primary').addClass('button-secondary')
                            .data('enabled', '1')
                            .html('🔴 비활성화');
                    } else {
                        $btn.removeClass('button-secondary').addClass('button-primary')
                            .data('enabled', '0')
                            .html('🟢 활성화');
                    }
                    $btn.prop('disabled', false);
                } else {
                    alert('오류: ' + (response.data || '<?php echo esc_js( __( "토글 실패", "acf-code-snippets-box" ) ); ?>'));
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                alert('<?php echo esc_js( __( "서버 통신 오류가 발생했습니다.", "acf-code-snippets-box" ) ); ?>');
                $btn.prop('disabled', false);
            }
        });
    });
});
</script>
