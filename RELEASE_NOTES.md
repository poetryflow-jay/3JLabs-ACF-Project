# 3J Labs ACF CSS 플러그인 패밀리 - 릴리즈 노트

## 릴리즈 개요

**릴리즈 날짜**: 2026년 1월 3일  
**릴리즈 버전**: Phase 24 진행 중 (v20.2.2)  
**개발팀**: 3J Labs (제이x제니x제이슨 연구소)

---

## 📦 플러그인 버전 업데이트

| 플러그인 | 이전 버전 | 새 버전 | 변경 유형 |
|----------|-----------|---------|-----------|
| ACF CSS Manager (Master) | 13.4.0 | **20.2.2** | Major |
| ACF CSS Woo License Bridge | 2.0.0 | **20.2.2** | Major |
| WP Bulk Manager | 2.3.4 | **2.5.1** | Minor |
| ACF CSS Neural Link | 4.1.0 | **4.3.0** | Minor |

---

## 🚀 주요 변경사항 (Phase 24)

### ACF CSS Manager v20.2.2
- ✅ **WordPress 6.7.0+ 번역 로딩 최적화**: `_load_textdomain_just_in_time` 경고 해결을 위해 클래스 초기화 시점을 `init` 훅으로 연기.
- ✅ **메뉴 시스템 강화**: '알림판' 바로 아래 '벌크 매니저', 'ACF 스타일 센터' 순서 강제 및 강조 배경색 적용.

### ACF CSS Woo License Bridge v20.2.2
- ✅ **Master/Partner 에디션 시스템 도입**: 빌드 시 에디션에 따라 기능 및 대시보드 접근 제어.
- ✅ **판매 및 정산 대시보드**: 파트너 전용 판매 내역 확인 및 정산 현황 뷰 추가.
- ✅ **Neural Link 서버 연동**: WooCommerce 결제와 Neural Link 라이센스 발행 API 연동 강화.

### WP Bulk Manager v2.5.1
- ✅ **메뉴 가시성 문제 해결**: `custom_menu_order` 필터를 사용하여 최상위 메뉴 위치 고정.
- ✅ **선택 활성화 기능**: 플러그인 목록에서 여러 플러그인을 한 번에 활성화하는 기능 추가.
- ✅ **업로드 오류 핸들링 개선**: 서버측 PHP 업로드 오류 및 클라이언트측 AJAX 오류 메시지 상세화.

### ACF CSS Neural Link v4.3.0
- ✅ **업데이트 채널 관리**: Stable, Beta, Dev 채널별 배포 로직 통합.
- ✅ **순차 배포(Rolling Updates)**: 사이트별 그룹 할당(A/B/C) 및 점진적 업데이트 배포 기능.

---

## 🚀 주요 변경사항

### ACF CSS Manager v13.4.0

**Phase 18: 전체 로드맵 구현 및 에디션 빌드 시스템**

- ✅ 조건 빌더 UI 완전 구현 (AND/OR 논리 조합, 13가지 조건 타입)
- ✅ 넛지 마케팅 시스템 (Toast, Banner, Modal, Tooltip, Spotlight, Walkthrough)
- ✅ 내보내기/가져오기 기능 (JSON, ZIP 지원)
- ✅ 클라우드 동기화 API 기반 구조
- ✅ 서드파티 연동 프레임워크 (ACF, FacetWP, Perfmatters, Elementor, Gutenberg)
- ✅ 22개 언어 다국어 지원 완료
- ✅ Python 개발 툴킷 CLI 모드 추가

### ACF CSS AI Extension v2.1.0

- 🔄 버전 동기화 및 안정성 개선
- 📦 빌드 시스템 호환성 향상

### ACF CSS Neural Link v4.1.0

- 🔄 라이선스 API 버전 동기화
- 📦 에디션 빌드 시스템 통합

### ACF Code Snippets Box v1.1.0

**첫 마이너 업데이트**

- ✅ 조건 빌더 UI JavaScript 완전 구현
- ✅ 넛지 시스템 JavaScript 완전 구현
- ✅ 내보내기/가져오기 JavaScript 완전 구현
- ✅ 라이선스 기반 기능 접근 제어
- ✅ WooCommerce 연동 프리셋 추가
- ✅ 22개 언어 번역 파일 완료

### ACF CSS WooCommerce Toolkit v1.1.0

**첫 마이너 업데이트**

- ✅ 상품 Q&A 시스템 완전 구현
- ✅ Q&A JavaScript UI 구현
- ✅ 가격 엔진 안정성 개선
- ✅ 22개 언어 번역 파일 완료

---

## 🛠️ 개발 도구 업데이트

### Python Development Toolkit v2.0.0

**CLI 모드 추가**

```bash
# 플러그인 목록 확인
python 3j_dev_toolkit.py --list

# 간단 빌드
python 3j_dev_toolkit.py --simple

# 에디션별 빌드
python 3j_dev_toolkit.py --version 13.4.0 --edition all --user-type all --bundle
```

**지원 에디션**:
- Free (무료)
- Pro Basic
- Pro Premium
- Pro Unlimited

**지원 사용자 타입**:
- Standard (일반 사용자)
- Partner (파트너)
- Master (개발용)

---

## 🌐 지원 언어 (22개)

1. 🇰🇷 한국어 (ko_KR)
2. 🇺🇸 영어 (en_US)
3. 🇬🇧 영어 - 영국 (en_GB)
4. 🇨🇳 중국어 - 간체 (zh_CN)
5. 🇹🇼 중국어 - 번체 (zh_TW)
6. 🇭🇰 중국어 - 홍콩 (zh_HK)
7. 🇯🇵 일본어 (ja)
8. 🇪🇸 스페인어 (es_ES)
9. 🇧🇷 포르투갈어 - 브라질 (pt_BR)
10. 🇫🇷 프랑스어 (fr_FR)
11. 🇨🇦 프랑스어 - 캐나다 (fr_CA)
12. 🇩🇪 독일어 (de_DE)
13. 🇨🇭 독일어 - 스위스 (de_CH)
14. 🇳🇱 네덜란드어 (nl_NL)
15. 🇧🇪 네덜란드어 - 벨기에 (nl_BE)
16. 🇮🇹 이탈리아어 (it_IT)
17. 🇮🇳 힌디어 (hi_IN)
18. 🇻🇳 베트남어 (vi)
19. 🇹🇭 태국어 (th)
20. 🇹🇷 튀르키예어 (tr_TR)
21. 🇷🇺 러시아어 (ru_RU)
22. 🇺🇦 우크라이나어 (uk)

---

## 📁 생성된 빌드 패키지

### 기본 빌드 (5개)
- `acf-css-manager-master-v13.4.0.zip`
- `acf-css-ai-extension-v2.1.0.zip`
- `acf-css-neural-link-v4.1.0.zip`
- `acf-code-snippets-box-v1.1.0.zip`
- `acf-css-woocommerce-toolkit-v1.1.0.zip`

### 에디션 빌드 (12개)
- Free × (Standard, Partner, Master) = 3개
- Pro Basic × (Standard, Partner, Master) = 3개
- Pro Premium × (Standard, Partner, Master) = 3개
- Pro Unlimited × (Standard, Partner, Master) = 3개

### 번들 패키지
- `3J-Labs-ACF-CSS-Bundle-v13.4.0.zip`

---

## 🔗 GitHub 저장소

**Repository**: [poetryflow-jay/3JLabs-ACF-Project](https://github.com/poetryflow-jay/3JLabs-ACF-Project)

---

## 📋 다음 버전 예고 (Phase 19)

- 🎯 Figma 통합 심화 (W Design Kit 스타일)
- 🎯 AI 폰트 추천 기능 고도화
- 🎯 로컬 WordPress 테스트 환경 자동화
- 🎯 성능 최적화 및 코드 리팩토링

---

## 📞 지원 및 문의

- **웹사이트**: https://3j-labs.com
- **GitHub**: https://github.com/poetryflow-jay
- **이메일**: support@3j-labs.com

---

**© 2026 3J Labs (제이x제니x제이슨 연구소). All rights reserved.**
