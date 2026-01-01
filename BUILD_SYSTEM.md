# 빌드 시스템 가이드

**Phase 8.1.6**: Webpack 기반 빌드 시스템 구축

## 설치

```bash
npm install
```

## 빌드 명령어

### 프로덕션 빌드
```bash
npm run build
```
- 모든 JavaScript 파일을 번들링하고 minify
- 출력: `assets/js/bundled/*.min.js`

### 개발 빌드
```bash
npm run build:dev
```
- 소스맵 포함
- 최적화 없음

### 개발 모드 (Watch)
```bash
npm run watch
```
- 파일 변경 시 자동 재빌드

### 번들 분석
```bash
npm run analyze
```
- 번들 크기 분석
- 웹 브라우저에서 시각화

## 번들 구조

### admin-center-bundle
- `jj-common-utils.js` - 공통 유틸리티
- `jj-admin-center.js` - Admin Center 메인
- `jj-admin-center-keyboard.js` - 키보드 단축키
- `jj-form-enhancer.js` - 폼 UX 개선
- `jj-undosystem.js` - Undo 시스템

### style-guide-presets
- `jj-style-guide-presets.js` - 프리셋 관리

### 기타
- `style-guide-editor.js` - 스타일 가이드 에디터
- `labs-center.js` - Labs Center
- `keyboard-shortcuts.js` - 키보드 단축키 (레거시)
- `tooltips.js` - 툴팁
- `live-preview.js` - 라이브 프리뷰

## WordPress 통합

번들된 파일은 `JJ_Asset_Optimizer` 클래스에서 자동으로 로드됩니다.

개발 모드에서는 원본 파일을, 프로덕션에서는 번들 파일을 로드하도록 설정 가능합니다.

## 최적화 기능

1. **코드 스플리팅**: 공통 코드를 별도 청크로 분리
2. **Tree Shaking**: 미사용 코드 제거
3. **Minification**: Terser를 사용한 코드 압축
4. **소스맵**: 개발 모드에서만 생성

## 향후 개선

- [ ] Vite 마이그레이션 고려
- [ ] CSS 번들링 추가
- [ ] 이미지 최적화 파이프라인
- [ ] TypeScript 지원
