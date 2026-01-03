# 3J Labs ACF CSS 플러그인 패밀리 - 릴리즈 노트

## 릴리즈 개요

**릴리즈 날짜**: 2026년 1월 3일  
**릴리즈 버전**: Phase 26 긴급 재정비 (v21.0.1)  
**개발팀**: 3J Labs (제이x제니x제이슨 연구소)

---

## 📦 플러그인 버전 업데이트

| 플러그인 | 이전 버전 | 새 버전 | 변경 유형 |
|----------|-----------|---------|-----------|
| ACF CSS Manager (Master) | 20.2.4 | **21.0.1** | Emergency |
| ACF CSS Woo License Bridge | 20.2.2 | **21.0.0** | Major |
| WP Bulk Manager | 2.5.1 | **3.0.0** | Major |
| ACF CSS Neural Link | 4.3.0 | **5.0.0** | Major |

---

## 🚀 주요 변경사항 (Phase 26 Rollback - Clean Master)

### ACF CSS Manager v21.0.1
- ✅ **Clean Master Rollback**: Nudge Flow, Bulk Manager 등 타 패밀리 플러그인의 강제 통합 로직을 전면 제거하여 코드 경량화 및 안정성 확보.
- ✅ **마스터 키 권한 집중**: 타 플러그인의 기능을 직접 수행하는 대신, 패밀리 플러그인 전체의 라이센스 잠금을 해제하고 모든 프리미엄 기능을 활성화하는 '마스터 통합 제어기' 역할로 정체성 재정립.
- ✅ **자체 기능 무제한**: 마스터 에디션 내 모든 고유 기능(Style Center, Style Guide 등)의 제약을 해제하고 개발 중인 내부 확장 모듈(AI 어시스턴트 등) 로딩 구조 마련.
- ✅ **시스템 오작동 수정**: 무리한 통합으로 인한 메뉴 충돌 및 "권한 없음" 오류 방지 조치.

---

## 🚀 주요 변경사항 (Phase 26 - 전략적 프리셋 & 템플릿 마켓)

### ACF MBA Nudge Flow v20.2.4
- ✅ **전략적 프리셋 6종 탑재**: '개인화 마케팅 타당성 보고서'의 시나리오를 바탕으로 첫 방문 큐레이션, 가입 유도, 장바구니 회수, 무료배송 유도 등 6종의 고효율 템플릿 기본 제공.
- ✅ **템플릿 센터(Marketplace) 고도화**: 보고서 기반 3J Labs 추천 전략 탭 추가 및 유/무료 템플릿의 시각적 구분 강화.
- ✅ **원클릭 프리셋 설치**: 프리셋 설치 시 워크플로우 메뉴에 '초기 비활성화(Draft)' 상태로 자동 생성 및 메타 설정 완료.
- ✅ **수익화 시스템 강화**: 템플릿 판매자 신청 및 월간 정산액 노출을 통해 사장님의 노하우 자산화 유도.
- ✅ **UI/UX 현지화**: 좌측 메뉴바 '넛지 플로우' 명칭 및 주요 버튼/설명 한글화 완료.

## 🚀 주요 변경사항 (Phase 25 - Nudge Flow 개편)

### ACF MBA Nudge Flow v20.2.2 (v20.2.2)
- ✅ **메뉴 배치 최적화**: WooCommerce '마케팅' 메뉴 하단(Position 58)으로 이동하여 커머스 운영자의 접근성 및 워크플로우 효율성 증대.
- ✅ **서브메뉴 구조 개편**: 대시보드(통계), 워크플로우(관리), 분석(데이터), 템플릿 센터(공유)로 메뉴 체계화.
- ✅ **수익화 모델 UI 설계**: 템플릿 센터 내 유/무료 템플릿 구분 및 판매자 등록/정산 프로세스 UI 기반 구축.
- ✅ **공유 템플릿 생태계**: 사용자 템플릿 공유 및 판매 허용 옵션을 위한 데이터 구조 마련.

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
