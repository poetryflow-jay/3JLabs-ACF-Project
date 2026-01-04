# 릴리즈 노트 v23.0.2 - 플러그인 목록 페이지 대폭 개선

**릴리즈 날짜**: 2026년 1월 3일  
**버전**: 23.0.2  
**주요 변경사항**: 플러그인 목록 페이지 UI/UX 대폭 개선

---

## 🎯 주요 개선사항

### ✨ 모든 플러그인에 자동 업데이트 버튼 추가

- **전역 적용**: 마스터 버전이든 일반 버전이든 상관없이 **모든 플러그인**에 자동 업데이트 토글 버튼이 표시됩니다
- **명확한 텍스트**: "자동 업데이트 활성화" / "자동 업데이트 비활성화"로 상태를 명확하게 표시
- **시각적 강조**: 버튼 스타일, 상태별 색상(초록/빨강), 아이콘(✅/⚪) 적용
- **AJAX 기반**: 페이지 새로고침 없이 즉시 토글 가능

### 🎨 플러그인 액션 링크 대폭 강화

#### 제목 아래 링크 (플러그인별 기능)
- **설정 링크**: 볼드체, 아이콘(⚙️), 파란색, 테두리 강조
- **롤백 링크**: 그라데이션 텍스트, 볼드체, 아이콘(🔄), 노란색 강조
- **자동 업데이트 버튼**: 버튼 스타일, 상태별 색상, 배경색 적용

#### 비활성화 링크 강조
- WordPress 기본 비활성화 링크에 자동으로 스타일 적용
- 빨간색, 볼드체, 테두리로 시각적 강조

### 📋 플러그인 설명 아래 메타 링크 추가

모든 플러그인에 다음 링크들이 자동으로 추가됩니다:

- **자동 업데이트 버튼**: 버튼 스타일로 강조 표시
- **공식 사이트**: 파란색, 볼드체, 테두리, 아이콘(🌐)
- **작성자**: 진한 파란색, 볼드체, 테두리, 아이콘(👤)
- **버전 정보**: 회색, 볼드체, 아이콘(📦)

### 🔧 기술적 개선사항

#### 전역 플러그인 목록 향상기 (`JJ_Global_Plugin_List_Enhancer`)
- 모든 플러그인에 자동으로 적용되는 전역 필터 시스템
- WordPress 코어 플러그인 제외 로직
- AJAX 기반 자동 업데이트 토글

#### 플러그인별 향상기 (`JJ_Plugin_List_Enhancer`)
- 각 플러그인별 맞춤 기능 링크 제공
- 플러그인별 설정 페이지, 대시보드 등 바로가기
- 업그레이드 유도 링크 (마스터/파트너/언리미티드 제외)

---

## 📦 적용된 플러그인

다음 플러그인들에 `JJ_Plugin_List_Enhancer`가 초기화되어 플러그인별 맞춤 링크가 제공됩니다:

1. **ACF CSS 설정 관리자** (메인 플러그인)
   - 스타일 센터 바로가기
   - 설정 관리자 바로가기

2. **넛지 플로우**
   - 워크플로우 관리 바로가기
   - 템플릿 센터 바로가기

3. **ACF CSS Neural Link**
   - 라이센스 관리 바로가기

4. **ACF CSS WooCommerce Toolkit**
   - WooCommerce 설정 바로가기

5. **ACF CSS AI Extension**
   - AI 대시보드 바로가기

6. **ACF CSS License Bridge for WooCommerce**
   - 라이센스 설정 바로가기

7. **코드 박스**
   - 코드 박스 관리 바로가기
   - 프리셋 라이브러리 바로가기

---

## 🎨 스타일 개선사항

### CSS 강화
- 모든 링크에 **볼드체(font-weight: 800)** 적용
- **큰 글꼴(font-size: 14-15px)** 적용
- **색상 강조**: 기능별 고유 색상 적용
- **아이콘 추가**: 각 링크에 의미있는 이모지 아이콘
- **테두리/배경**: 중요한 링크에 테두리 및 배경색 적용
- **호버 효과**: 마우스 오버 시 시각적 피드백

### JavaScript 개선
- 전역 자동 업데이트 토글 핸들러
- 비활성화 링크 자동 스타일 적용
- AJAX 기반 실시간 업데이트

---

## 🔄 하위 호환성

- 기존 플러그인과 완전 호환
- WordPress 기본 기능과 충돌 없음
- 점진적 향상(Progressive Enhancement) 방식 적용

---

## 📝 개발자 가이드

### 새로운 플러그인에 적용하기

```php
// 플러그인 메인 파일에 추가
add_action( 'plugins_loaded', function () {
    $plugin_list_enhancer_file = dirname( dirname( __FILE__ ) ) . '/acf-css-really-simple-style-management-center-master/includes/class-jj-plugin-list-enhancer.php';
    if ( file_exists( $plugin_list_enhancer_file ) ) {
        require_once $plugin_list_enhancer_file;
    }
    
    if ( class_exists( 'JJ_Plugin_List_Enhancer' ) ) {
        $enhancer = new JJ_Plugin_List_Enhancer();
        $enhancer->init( array(
            'plugin_file' => __FILE__,
            'plugin_name' => 'Your Plugin Name',
            'settings_url' => admin_url( 'admin.php?page=your-settings-page' ),
            'text_domain' => 'your-text-domain',
            'version_constant' => 'YOUR_PLUGIN_VERSION',
            'license_constant' => 'YOUR_PLUGIN_LICENSE_TYPE',
            'upgrade_url' => 'https://3j-labs.com/',
            'docs_url' => admin_url( 'admin.php?page=your-docs-page' ),
            'support_url' => 'https://3j-labs.com/support',
        ) );
    }
}, 25 );
```

### 플러그인별 기능 링크 추가

`class-jj-plugin-list-enhancer.php`의 `get_feature_links()` 메서드에 플러그인별 링크를 추가하세요:

```php
elseif ( strpos( $this->plugin_basename, 'your-plugin-slug' ) !== false ) {
    $links['feature1'] = array(
        'url' => admin_url( 'admin.php?page=your-feature-page' ),
        'label' => __( '기능 이름', $text_domain ),
        'icon' => '🎯',
        'color' => '#your-color',
        'tooltip' => __( '기능 설명', $text_domain ),
    );
}
```

---

## 🐛 버그 수정

- 자동 업데이트 버튼이 일부 플러그인에만 표시되던 문제 해결
- 플러그인 메타 링크가 중복 표시되던 문제 해결
- 비활성화 링크 스타일이 적용되지 않던 문제 해결

---

## 🔮 향후 계획

- [ ] 롤백 기능 완전 구현
- [ ] 플러그인별 통계 대시보드 추가
- [ ] 일괄 자동 업데이트 설정 기능
- [ ] 플러그인 의존성 시각화

---

## 📚 참고 자료

- [WordPress Plugin API - plugin_action_links](https://developer.wordpress.org/reference/hooks/plugin_action_links/)
- [WordPress Plugin API - plugin_row_meta](https://developer.wordpress.org/reference/hooks/plugin_row_meta/)
- [WordPress Auto Updates](https://wordpress.org/support/article/configuring-automatic-background-updates/)

---

**개발팀**: 3J Labs (제이x제니x제이슨 연구소)  
**문의**: https://3j-labs.com/support
