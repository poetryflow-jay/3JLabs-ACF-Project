# WPCODEBOX2 코드 스니펫 컬렉션

> 3J Labs에서 수집 및 정리한 유용한 WooCommerce/WordPress 코드 스니펫

**정리일**: 2026-01-02
**버전**: 1.0.0
**담당자**: Jason (CTO)

---

## 📁 카테고리

1. [WooCommerce 가격 관리](#woocommerce-가격-관리)
2. [WooCommerce UI/UX](#woocommerce-uiux)
3. [WordPress 유틸리티](#wordpress-유틸리티)
4. [검색 기능](#검색-기능)
5. [디버깅/로깅](#디버깅로깅)

---

## WooCommerce 가격 관리

### 1. 상품 할인율 자동 계산기 (PHP + JS)

상품 편집 화면에서 퍼센트 또는 금액 기반으로 할인가를 자동 계산합니다.

**원본**: WPCODEBOX2 - "상품 할인율 자동 계산기"
**활용**: ACF CSS WooCommerce Toolkit에 통합됨

```php
<?php
// 상품 편집 메타박스에 할인 계산기 추가
add_action('woocommerce_product_options_pricing', 'add_advanced_pricing_fields');
function add_advanced_pricing_fields() {
    ?>
    <div class="options_group pricing show_if_simple">
        <?php
        woocommerce_wp_select([
            'id' => '_installment_months',
            'label' => __('할부 개월 수', 'textdomain'),
            'options' => [
                '1' => '일시불',
                '3' => '3개월',
                '6' => '6개월',
                '12' => '12개월',
                '24' => '24개월'
            ],
            'desc_tip' => true,
            'description' => __('정가와 할인가 모두에 적용됩니다', 'textdomain')
        ]);
        ?>
        <!-- 할인 계산기 UI는 JavaScript로 구현 -->
    </div>
    <?php
}
```

---

### 2. 가격 계산 엔진 & 숏코드 (PHP)

할인율, 절약금액, 할부가격을 계산하는 핵심 엔진과 모듈형 숏코드를 제공합니다.

**원본**: WPCODEBOX2 - "RealDeal Platform: 가격 시스템 최종판 v11.0"
**활용**: 
- ACF CSS WooCommerce Toolkit에 `class-price-engine.php`로 통합
- ACF Code Snippets Box 프리셋에 추가

**핵심 함수**:
```php
function get_price_data( $product ) {
    $data = [
        'regular_price'       => (float) $product->get_regular_price(),
        'sale_price'          => (float) $product->get_sale_price(),
        'is_on_sale'          => $product->is_on_sale(),
        'saved_amount'        => 0,
        'discount_percentage' => 0,
        'installment_months'  => (int) get_post_meta( $product->get_id(), 'installment_months', true ),
        'installment_price'   => 0,
    ];
    // 계산 로직...
    return $data;
}
```

**숏코드**:
- `[realdeal_price]` - 통합 가격 표시
- `[rd_badge]` - 할인 배지
- `[rd_summary]` - 절약 금액
- `[rd_installments]` - 할부 정보

---

### 3. 빠른 편집 필드 확장 (PHP)

상품 목록의 빠른 편집에 할인가격과 할부 개월 수 필드를 추가합니다.

**원본**: WPCODEBOX2 - "RealDeal Platform: 빠른 편집 기능 강화"
**활용**: ACF CSS WooCommerce Toolkit에 `class-quick-edit-fields.php`로 통합

```php
add_action( 'woocommerce_product_quick_edit_end', 'add_quick_edit_fields' );
add_action( 'woocommerce_product_quick_edit_save', 'save_quick_edit_fields' );
```

---

## WooCommerce UI/UX

### 4. 할부 가격 표시 스타일 (CSS)

상품 목록에서 할부 가격과 할인 배지를 스타일링합니다.

**원본**: WPCODEBOX2 - "우커머스 상품 가격 할부 표기 스타일"
**활용**: ACF CSS WooCommerce Toolkit의 `assets/css/price-styles.css`에 통합

```css
.realdeal-price-wrapper {
  line-height: 1.5;
}

.realdeal-discount-badge {
    display: inline-block !important;
    padding: 6px 12px !important;
    background-color: var(--accent-red, #FF0033) !important;
    color: white !important;
    font-size: 0.9em !important;
    font-weight: 700 !important;
    border-radius: 4px !important;
}

.realdeal-installment-price {
  display: block;
  font-size: 15px;
  font-weight: 400;
  margin-top: 4px;
}
```

---

### 5. WooCommerce 버튼 스타일 (CSS)

미니카트, 체크아웃, 장바구니 버튼 스타일을 통일합니다.

**원본**: WPCODEBOX2 - "우커머스 버튼"
**활용**: ACF CSS WooCommerce Toolkit의 `assets/css/button-styles.css`에 통합

**주요 선택자**:
- `.wc-block-mini-cart__footer-cart`
- `.wc-block-mini-cart__footer-checkout`
- `.wc-block-cart__submit-button`

---

### 6. 장바구니 UI 정리 (PHP)

장바구니/미니카트의 상품명 영역에서 불필요한 요소를 제거합니다.

**원본**: WPCODEBOX2 - "RealDeal Platform: 장바구니 UI 외과수술 v11.2"
**활용**: ACF CSS WooCommerce Toolkit에 `class-cart-enhancer.php`로 통합

```php
add_filter( 'woocommerce_cart_item_name', 'cleanup_cart_item_name', 100, 3 );
```

---

### 7. 번역 오류 수정 (PHP)

"Saved"가 "저장"으로 잘못 번역된 것을 "절약"으로 교정합니다.

**원본**: WPCODEBOX2 - "RealDeal Platform: 번역어 교체 v11.1"
**활용**: ACF Code Snippets Box 프리셋 및 ACF CSS WooCommerce Toolkit에 통합

```php
add_filter( 'gettext', 'fix_translation_error', 20, 3 );
function fix_translation_error( $translated_text, $text, $domain ) {
    if ( 'Saved' === $text && '저장' === $translated_text ) {
        $translated_text = '절약';
    }
    return $translated_text;
}
```

---

## WordPress 유틸리티

### 8. 검색 폼 URL 커스터마이저 (PHP + JS)

검색 폼의 action URL과 파라미터명을 커스터마이즈합니다.

**원본**: WPCODEBOX2 - "검색 블록 - 검색 결과 URL 등 변경"
**활용**: ACF Code Snippets Box 프리셋에 추가

```php
// GenerateBlocks 쿼리 루프 인수 필터링
add_filter( 'generateblocks_query_loop_args', function( $query_args, $block ){
    // 검색어 추가 로직
    return $query_args;
}, 10, 2 );

// 검색 폼 커스터마이징
add_action( 'wp_footer', function(){
    // 검색 폼 action URL 변경 JavaScript
} );
```

---

## 디버깅/로깅

### 9. 플러그인 비활성화 로그 기록기 (PHP)

플러그인이 비활성화될 때 상세 로그를 기록합니다 (디버깅용).

**원본**: WPCODEBOX2 - "플러그인 비활성화 로그 기록기"
**활용**: ACF Code Snippets Box 프리셋에 추가

```php
function log_plugin_deactivation($plugin_name, $is_network_wide) {
    $log_file = WP_CONTENT_DIR . '/plugin_deactivation.log';
    $timestamp = current_time('mysql');
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    // 로그 기록...
}
add_action('deactivated_plugin', 'log_plugin_deactivation', 10, 2);
```

---

## 📦 활용 현황

| 스니펫 | ACF Code Snippets Box | ACF CSS WooCommerce Toolkit | ACF CSS 메인 |
|--------|:---------------------:|:---------------------------:|:------------:|
| 할인 계산기 | ✅ 프리셋 | ✅ 핵심 기능 | - |
| 가격 엔진 | ✅ 프리셋 | ✅ 핵심 기능 | - |
| 빠른 편집 | ✅ 프리셋 | ✅ 핵심 기능 | - |
| 가격 스타일 | ✅ 프리셋 | ✅ CSS | ✅ 연동 가능 |
| 버튼 스타일 | ✅ 프리셋 | ✅ CSS | ✅ 연동 가능 |
| 장바구니 정리 | ✅ 프리셋 | ✅ 핵심 기능 | - |
| 번역 수정 | ✅ 프리셋 | ✅ 포함 | - |
| 검색 커스터마이저 | ✅ 프리셋 | - | - |
| 비활성화 로그 | ✅ 프리셋 | - | - |

---

## 📝 참고 사항

1. **Pro 전용 기능**: WooCommerce 관련 프리셋은 대부분 Pro 버전 이상 사용자에게만 제공됩니다.
2. **ACF CSS 연동**: 스타일 관련 기능은 ACF CSS 메인 플러그인의 CSS 변수와 연동됩니다.
3. **호환성**: 모든 코드는 WordPress 6.0+ 및 WooCommerce 7.0+와 호환됩니다.

---

*이 문서는 3J Labs의 내부 개발 문서입니다.*
*© 2026 3J Labs (제이x제니x제이슨 연구소)*
