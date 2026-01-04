# WP Bulk SEO & AEO(AIO) 플러그인 설계 문서

**작성일**: 2026-01-03  
**버전**: 1.0.0  
**기반**: SEO 알고리즘 설계 + Airtable 데이터베이스 + Google 알고리즘 유출 문서

---

## 📋 플러그인 개요

### 플러그인 정보

- **이름**: WP Bulk SEO & AEO(AIO)
- **설명**: WordPress Really Simple and Easy Search Engine Optimization and AI Optimization Automation. 1, 2, 3 Click Auto Optimization.
- **버전**: 1.0.0
- **작성자**: 3J Labs
- **라이선스**: GPL v2 or later

### 핵심 기능

1. **1-Click SEO**: 기본 SEO 설정 자동화
2. **2-Click AI**: AI 기반 최적화 제안
3. **3-Click 완전 자동화**: 모든 SEO 작업 자동화
4. **실시간 SEO 점수**: Google 알고리즘 기반 종합 점수
5. **가중치 기반 최적화**: Airtable 데이터베이스 기반 우선순위 제안

---

## 🏗️ 아키텍처 설계

### 디렉토리 구조

```
wp-bulk-seo-aeo/
├── wp-bulk-seo-aeo.php                    # 메인 플러그인 파일
├── uninstall.php                          # 언인스톨 스크립트
├── readme.txt                             # WordPress.org용
├── includes/
│   ├── class-wp-bulk-seo-core.php         # 핵심 클래스
│   ├── class-seo-algorithm.php            # SEO 알고리즘
│   ├── class-seo-scorer.php               # 점수 계산 엔진
│   ├── class-optimization-engine.php      # 최적화 엔진
│   ├── class-airtable-sync.php            # Airtable 동기화
│   ├── modules/
│   │   ├── class-domain-authority.php     # 도메인 권위 모듈
│   │   ├── class-user-engagement.php      # 사용자 참여 모듈
│   │   ├── class-content-quality.php      # 콘텐츠 품질 모듈
│   │   ├── class-core-web-vitals.php      # Core Web Vitals 모듈
│   │   ├── class-mobile-seo.php           # 모바일 SEO 모듈
│   │   ├── class-content-freshness.php    # 콘텐츠 신선도 모듈
│   │   ├── class-backlink-analyzer.php    # 백링크 분석 모듈
│   │   ├── class-page-speed.php           # 페이지 속도 모듈
│   │   ├── class-structured-data.php      # 구조화된 데이터 모듈
│   │   ├── class-title-optimizer.php      # 제목 최적화 모듈
│   │   ├── class-keyword-optimizer.php    # 키워드 최적화 모듈
│   │   ├── class-internal-linking.php     # 내부 링크 모듈
│   │   ├── class-image-optimizer.php     # 이미지 최적화 모듈
│   │   ├── class-author-authority.php     # 작성자 권위 모듈
│   │   ├── class-https-security.php      # HTTPS 보안 모듈
│   │   └── class-social-signals.php      # 소셜 시그널 모듈
│   ├── admin/
│   │   ├── class-admin.php                # 관리자 클래스
│   │   ├── class-admin-dashboard.php     # 대시보드
│   │   ├── class-admin-settings.php      # 설정 페이지
│   │   ├── views/
│   │   │   ├── dashboard.php              # 대시보드 뷰
│   │   │   ├── seo-score.php              # SEO 점수 뷰
│   │   │   ├── optimization-suggestions.php # 최적화 제안 뷰
│   │   │   ├── factors-list.php           # SEO 요소 목록
│   │   │   └── settings.php               # 설정 뷰
│   │   └── assets/
│   │       ├── css/
│   │       │   ├── admin.css
│   │       │   └── dashboard.css
│   │       └── js/
│   │           ├── admin.js
│   │           ├── dashboard.js
│   │           └── seo-calculator.js
│   ├── frontend/
│   │   ├── class-frontend.php             # 프론트엔드 클래스
│   │   ├── class-meta-tags.php            # 메타 태그 출력
│   │   ├── class-schema-markup.php        # Schema 마크업
│   │   └── class-sitemap.php              # 사이트맵 생성
│   ├── api/
│   │   ├── class-google-search-console.php # GSC API
│   │   ├── class-google-analytics.php     # GA API
│   │   ├── class-pagespeed-insights.php   # PageSpeed API
│   │   └── class-mobile-friendly.php     # 모바일 테스트 API
│   ├── ai/
│   │   ├── class-ai-optimizer.php         # AI 최적화
│   │   ├── class-content-ai.php          # 콘텐츠 AI
│   │   └── class-keyword-ai.php          # 키워드 AI
│   └── helpers/
│       ├── class-database.php             # 데이터베이스 헬퍼
│       ├── class-cache.php                # 캐싱 헬퍼
│       └── class-utils.php                # 유틸리티
├── assets/
│   ├── css/
│   │   └── frontend.css
│   ├── js/
│   │   └── frontend.js
│   └── images/
│       └── logo.png
└── languages/
    └── wp-bulk-seo-aeo.pot
```

---

## 🔧 핵심 클래스 설계

### 1. WP_Bulk_SEO_Core (메인 클래스)

```php
<?php
/**
 * WP Bulk SEO & AEO(AIO) - 핵심 클래스
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Core {
    
    private static $instance = null;
    
    private $algorithm;
    private $scorer;
    private $optimization_engine;
    private $airtable_sync;
    
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        require_once plugin_dir_path(__FILE__) . 'includes/class-seo-algorithm.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-seo-scorer.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-optimization-engine.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-airtable-sync.php';
        
        $this->algorithm = new WP_Bulk_SEO_Algorithm();
        $this->scorer = new WP_Bulk_SEO_Scorer($this->algorithm);
        $this->optimization_engine = new WP_Bulk_SEO_Optimization_Engine();
        $this->airtable_sync = new WP_Bulk_SEO_Airtable_Sync();
    }
    
    private function init_hooks() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        
        // AJAX 핸들러
        add_action('wp_ajax_wp_bulk_seo_calculate_score', array($this, 'ajax_calculate_score'));
        add_action('wp_ajax_wp_bulk_seo_get_suggestions', array($this, 'ajax_get_suggestions'));
        add_action('wp_ajax_wp_bulk_seo_apply_optimization', array($this, 'ajax_apply_optimization'));
    }
    
    public function calculate_seo_score($post_id = null) {
        return $this->scorer->calculate($post_id);
    }
    
    public function get_optimization_suggestions($post_id = null) {
        $score = $this->calculate_seo_score($post_id);
        return $this->optimization_engine->generate_suggestions($score);
    }
    
    public function apply_optimization($optimization_id) {
        return $this->optimization_engine->apply($optimization_id);
    }
}
```

### 2. WP_Bulk_SEO_Algorithm (알고리즘 클래스)

```php
class WP_Bulk_SEO_Algorithm {
    
    private $factors;
    private $weights;
    
    public function __construct() {
        $this->load_factors();
        $this->initialize_weights();
    }
    
    private function load_factors() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_bulk_seo_factors';
        
        $this->factors = $wpdb->get_results(
            "SELECT * FROM {$table_name} ORDER BY estimated_weight DESC",
            ARRAY_A
        );
    }
    
    private function initialize_weights() {
        $this->weights = [
            'tier1' => 9.5,  // 가중치 9-10
            'tier2' => 8.0,  // 가중치 8
            'tier3' => 6.5,  // 가중치 6-7
            'tier4' => 5.0   // 가중치 4-5
        ];
    }
    
    public function get_factor_weight($factor_name) {
        foreach ($this->factors as $factor) {
            if ($factor['name'] === $factor_name || 
                $factor['google_element'] === $factor_name) {
                return (int) $factor['estimated_weight'];
            }
        }
        return 5; // 기본값
    }
    
    public function calculate_factor_score($factor_name, $data) {
        // 각 요소별 점수 계산 로직
        $module_class = $this->get_module_class($factor_name);
        if ($module_class && class_exists($module_class)) {
            $module = new $module_class();
            return $module->calculate_score($data);
        }
        return 0;
    }
    
    private function get_module_class($factor_name) {
        $mapping = [
            'siteAuthority' => 'WP_Bulk_SEO_Domain_Authority',
            'PageRank' => 'WP_Bulk_SEO_Backlink_Analyzer',
            'Good Clicks' => 'WP_Bulk_SEO_User_Engagement',
            'Core Web Vitals' => 'WP_Bulk_SEO_Core_Web_Vitals',
            'Mobile Friendliness' => 'WP_Bulk_SEO_Mobile_SEO',
            // ... 더 많은 매핑
        ];
        
        return isset($mapping[$factor_name]) ? $mapping[$factor_name] : null;
    }
}
```

### 3. WP_Bulk_SEO_Scorer (점수 계산 엔진)

```php
class WP_Bulk_SEO_Scorer {
    
    private $algorithm;
    
    public function __construct($algorithm) {
        $this->algorithm = $algorithm;
    }
    
    public function calculate($post_id = null) {
        $site_data = $this->collect_site_data($post_id);
        $factor_scores = [];
        $total_weight = 0;
        $weighted_score = 0;
        
        foreach ($this->algorithm->get_factors() as $factor) {
            $factor_name = $factor['google_element'] ?: $factor['name'];
            $weight = $this->algorithm->get_factor_weight($factor_name);
            
            $score = $this->algorithm->calculate_factor_score($factor_name, $site_data);
            
            $factor_scores[$factor_name] = [
                'score' => $score,
                'weight' => $weight,
                'category' => $factor['category']
            ];
            
            $weighted_score += $score * $weight;
            $total_weight += $weight;
        }
        
        $overall_score = $total_weight > 0 ? $weighted_score / $total_weight : 0;
        
        return [
            'overall_score' => round($overall_score, 2),
            'grade' => $this->get_grade($overall_score),
            'factor_scores' => $factor_scores,
            'calculated_at' => current_time('mysql')
        ];
    }
    
    private function collect_site_data($post_id = null) {
        // 사이트 데이터 수집
        return [
            'domain_authority' => $this->get_domain_authority_data(),
            'user_engagement' => $this->get_user_engagement_data(),
            'content_quality' => $this->get_content_quality_data($post_id),
            'web_vitals' => $this->get_web_vitals_data(),
            // ... 더 많은 데이터
        ];
    }
    
    private function get_grade($score) {
        if ($score >= 90) return 'A+';
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'F';
    }
}
```

---

## 📊 데이터베이스 스키마

### 테이블 생성 SQL

```sql
-- SEO 요소 데이터베이스
CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wp_bulk_seo_factors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    module_name VARCHAR(255),
    category ENUM('Technical SEO', 'Content SEO', 'Link SEO', 'User Experience', 'Social Signals', 'Local SEO', 'International SEO', 'Other') DEFAULT 'Other',
    seo_factor VARCHAR(255),
    estimated_weight TINYINT UNSIGNED DEFAULT 5,
    impact ENUM('High', 'Medium', 'Low') DEFAULT 'Medium',
    explanation TEXT,
    google_element VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_weight (estimated_weight),
    INDEX idx_google_element (google_element)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEO 점수 기록
CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wp_bulk_seo_scores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    site_id BIGINT UNSIGNED DEFAULT 0,
    post_id BIGINT UNSIGNED DEFAULT 0,
    overall_score DECIMAL(5,2),
    grade VARCHAR(2),
    factor_scores JSON,
    calculated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_site (site_id),
    INDEX idx_post (post_id),
    INDEX idx_score (overall_score),
    INDEX idx_calculated (calculated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 최적화 제안
CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wp_bulk_seo_suggestions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    site_id BIGINT UNSIGNED DEFAULT 0,
    post_id BIGINT UNSIGNED DEFAULT 0,
    factor VARCHAR(255),
    priority ENUM('high', 'medium', 'low') DEFAULT 'medium',
    suggestion TEXT,
    estimated_improvement DECIMAL(5,2),
    status ENUM('pending', 'in_progress', 'completed', 'dismissed') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_site (site_id),
    INDEX idx_post (post_id),
    INDEX idx_priority (priority),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🚀 개발 로드맵

### Phase 1: MVP (4주)

**Week 1-2**: 기본 구조
- 플러그인 기본 구조 생성
- 데이터베이스 테이블 생성
- Airtable 데이터베이스에서 SEO 요소 로드
- 기본 관리자 메뉴

**Week 3-4**: 핵심 기능
- SEO 알고리즘 구현
- 점수 계산 엔진
- 기본 대시보드

### Phase 2: 확장 (4주)

**Week 5-6**: 모듈 개발
- Tier 1 요소 모듈 (도메인 권위, 모바일, Core Web Vitals)
- Tier 2 요소 모듈 (콘텐츠 품질, 페이지 속도)

**Week 7-8**: 최적화 엔진
- 최적화 제안 시스템
- 자동 최적화 기능

### Phase 3: 완성 (4주)

**Week 9-10**: AI 통합
- AI 콘텐츠 최적화
- AI 키워드 제안

**Week 11-12**: 고급 기능
- 실시간 모니터링
- 리포트 생성
- API 통합

---

**다음 단계**: 실제 코드 구현 시작
