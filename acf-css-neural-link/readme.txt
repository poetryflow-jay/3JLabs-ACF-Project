=== JJ License Manager ===
Contributors: jay-jenny-labs
Tags: license, woocommerce, ecommerce, subscription
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 2.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

JJ's Center of Style Setting 플러그인 전용 라이센스 관리 시스템입니다. WooCommerce와 연동하여 자동으로 라이센스를 생성하고 관리합니다.

== Description ==

JJ License Manager는 WordPress 기반 플러그인 판매 웹사이트를 위한 완전한 라이센스 관리 솔루션입니다.

**주요 기능:**

* **자동 라이센스 생성**: WooCommerce 주문 완료 시 자동으로 라이센스 키 생성
* **라이센스 검증 API**: REST API를 통한 라이센스 검증 및 활성화 관리
* **활성화 관리**: 사이트별 라이센스 활성화 추적 및 제한
* **만료 관리**: 구독 기간에 따른 자동 만료 및 비활성화
* **히스토리 추적**: 모든 라이센스 활동의 상세 기록
* **회원 연동**: WordPress 사용자 및 WooCommerce 주문과 완전 연동

**WooCommerce 통합:**

* 주문 완료 시 자동 라이센스 생성
* 상품 메타를 통한 라이센스 타입 설정
* 구독 기간 자동 계산
* 이메일 알림 (선택사항)

**결제 시스템 지원:**

* WooCommerce를 통한 모든 결제 게이트웨이 지원
* Stripe, PayPal 등 모든 WooCommerce 결제 플러그인과 호환

**라이센스 타입:**

* FREE: 무료 버전
* BASIC: 기본 버전 (1개 사이트)
* PREM: 프리미엄 버전 (1개 사이트)
* UNLIM: 언리미티드 버전 (무제한 사이트)

== Installation ==

1. 플러그인 파일을 `wp-content/plugins/` 디렉터리에 업로드
2. WordPress 관리자에서 플러그인 활성화
3. WooCommerce가 설치 및 활성화되어 있어야 합니다
4. WooCommerce 상품에 라이센스 메타 필드 설정

== Frequently Asked Questions ==

= WooCommerce가 필요합니까? =

네, 이 플러그인은 WooCommerce가 필요합니다.

= 어떤 결제 시스템을 지원합니까? =

WooCommerce를 통한 모든 결제 게이트웨이를 지원합니다 (Stripe, PayPal, 기타 등).

= 라이센스는 어떻게 생성됩니까? =

WooCommerce 주문이 완료되면 자동으로 라이센스가 생성됩니다.

= 라이센스 키 형식은 무엇입니까? =

JJ-[VERSION]-[TYPE]-[RANDOM]-[CHECKSUM] 형식입니다.

== Changelog ==

= 1.0.0 =
* 초기 릴리스
* WooCommerce 통합
* 자동 라이센스 생성
* REST API 엔드포인트
* 관리자 대시보드
* 활성화 관리
* 만료 관리
* 히스토리 추적

== Upgrade Notice ==

= 1.0.0 =
초기 릴리스입니다.

