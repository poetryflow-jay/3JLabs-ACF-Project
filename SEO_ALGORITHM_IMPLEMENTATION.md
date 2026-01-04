# SEO 알고리즘 구현 가이드

**작성일**: 2026-01-03  
**버전**: 1.0.0

---

## 📋 구현 체크리스트

### Phase 1: 데이터베이스 및 기본 구조

- [ ] 플러그인 메인 파일 생성 (`wp-bulk-seo-aeo.php`)
- [ ] 데이터베이스 테이블 생성 스크립트
- [ ] SEO 요소 데이터 CSV → 데이터베이스 로드
- [ ] 기본 클래스 구조 생성
- [ ] 관리자 메뉴 추가

### Phase 2: SEO 알고리즘 구현

- [ ] `WP_Bulk_SEO_Algorithm` 클래스 구현
- [ ] `WP_Bulk_SEO_Scorer` 클래스 구현
- [ ] 각 요소별 점수 계산 함수 구현
- [ ] 종합 점수 계산 로직 구현
- [ ] 등급 계산 (A+, A, B, C, D, F)

### Phase 3: 모듈 개발

- [ ] Tier 1 모듈 (5개)
  - [ ] Domain Authority 모듈
  - [ ] Mobile SEO 모듈
  - [ ] Core Web Vitals 모듈
  - [ ] User Engagement 모듈
  - [ ] Brand Recognition 모듈

- [ ] Tier 2 모듈 (5개)
  - [ ] Content Quality 모듈
  - [ ] Content Relevance 모듈
  - [ ] Page Speed 모듈
  - [ ] HTTPS Security 모듈
  - [ ] Backlink Quality 모듈

- [ ] Tier 3 모듈 (7개)
  - [ ] Structured Data 모듈
  - [ ] Title Optimization 모듈
  - [ ] Content Freshness 모듈
  - [ ] Keyword Usage 모듈
  - [ ] Author Authority 모듈
  - [ ] Internal Linking 모듈
  - [ ] Image Optimization 모듈

### Phase 4: 최적화 엔진

- [ ] 최적화 제안 생성 로직
- [ ] 가중치 기반 우선순위 정렬
- [ ] 자동 최적화 기능
- [ ] 최적화 적용 추적

### Phase 5: 관리자 인터페이스

- [ ] 대시보드 페이지
- [ ] SEO 점수 표시
- [ ] 최적화 제안 목록
- [ ] 요소별 상세 분석
- [ ] 설정 페이지

---

## 🔧 구현 예제 코드

### 1. 플러그인 활성화 시 데이터베이스 생성

```php
function wp_bulk_seo_activate() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // SEO 요소 테이블
    $table_factors = $wpdb->prefix . 'wp_bulk_seo_factors';
    $sql_factors = "CREATE TABLE $table_factors (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        module_name VARCHAR(255),
        category VARCHAR(50),
        seo_factor VARCHAR(255),
        estimated_weight TINYINT UNSIGNED DEFAULT 5,
        impact VARCHAR(50),
        explanation TEXT,
        google_element VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_category (category),
        INDEX idx_weight (estimated_weight)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_factors);
    
    // CSV 데이터 로드
    wp_bulk_seo_load_factors_from_csv();
}

function wp_bulk_seo_load_factors_from_csv() {
    global $wpdb;
    $table = $wpdb->prefix . 'wp_bulk_seo_factors';
    
    $csv_file = plugin_dir_path(__FILE__) . 'seo_factors_sample_data.csv';
    
    if (!file_exists($csv_file)) {
        return;
    }
    
    $handle = fopen($csv_file, 'r');
    $header = fgetcsv($handle); // 헤더 스킵
    
    while (($data = fgetcsv($handle)) !== FALSE) {
        $wpdb->insert($table, [
            'name' => $data[0],
            'module_name' => $data[1],
            'category' => $data[2],
            'seo_factor' => $data[3],
            'estimated_weight' => (int)$data[4],
            'impact' => $data[5],
            'explanation' => $data[6],
            'google_element' => $data[7]
        ]);
    }
    
    fclose($handle);
}
```

### 2. SEO 점수 계산 예제

```php
function wp_bulk_seo_calculate_score($post_id = null) {
    $core = WP_Bulk_SEO_Core::instance();
    return $core->calculate_seo_score($post_id);
}

// 사용 예제
$score = wp_bulk_seo_calculate_score();
echo "종합 SEO 점수: " . $score['overall_score'] . " (" . $score['grade'] . ")";
```

---

**다음 단계**: 실제 플러그인 코드 작성 시작
