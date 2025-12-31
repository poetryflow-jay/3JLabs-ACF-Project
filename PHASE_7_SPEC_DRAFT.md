# Phase 7 Spec Draft: Real AI Intelligence (확장 플러그인 방식 권장)

## 목표(한 줄)
사용자가 “원하는 분위기/브랜드/업종”을 말하면, **ACF CSS Manager 설정을 자동 생성·적용**하고, 템플릿으로 저장/공유까지 이어지는 AI 워크플로우를 만든다.

---

## 핵심 원칙(Phase 6의 안정성 위에)
- **AI는 코어가 아니라 확장(Extension)로**: `JJ_Extension_Manager` 위에 `acf-css-ai` 확장 플러그인 형태로 제공
- **비용/보안 분리**: API Key, 과금, 요청 제한은 확장에서 관리(코어는 “훅 + 데이터 구조”만 제공)
- **실패해도 안전**: AI 실패 시 설정 변경 0, 혹은 “미리보기 모드”만 제공
- **감사 가능**: “AI가 바꾼 값”을 diff 형태로 보여주고 적용 버튼을 누를 때만 저장

---

## UX 플로우(최소 2단계)
1. **프롬프트 입력**: “이 사이트를 고급스러운 블랙&골드로, 법률사무소 느낌”
2. **미리보기/차이보기**:
   - Before/After 팔레트/폰트/버튼/기본 규칙 diff
   - “적용(저장)” / “임시 적용” / “취소”
3. **템플릿 저장(선택)**:
   - 내 템플릿 / Cloud / Market 게시(권한/요금 기반)

---

## API/확장 설계(코어가 제공할 것)
### 1) AI 확장 등록
- filter: `jj_style_guide_extensions`
- interface: `JJ_Style_Guide_Extension_Interface`
- capability: 예) `ai_styling` (Edition Controller에 단계적으로 추가 가능)

### 2) 코어 훅(새로 추가 제안)
- `jj_style_guide_ai_prepare_context` (현재 설정/테마/플러그인 환경 제공)
- `jj_style_guide_ai_apply_suggestion` (AI가 반환한 설정을 적용하기 전 sanitize/검증)
- `jj_style_guide_ai_applied` (적용 후 이벤트)

### 3) 데이터 계약(Contract)
AI 확장은 아래 형태로 결과를 반환:
```json
{
  "settings_patch": { "palettes": { "brand": { "primary_color": "#..." } } },
  "explanation": "변경 이유 요약",
  "confidence": 0.78
}
```

---

## 보안/요금(운영 관점)
- **API Key 저장소**: WP 옵션 + `manage_options` 권한 + 암호화(가능하면 Sodium/OpenSSL)
- **요청 제한**:
  - 1) 일일/월간 토큰 한도
  - 2) 요청 속도 제한(rate limit)
- **PII 방지**: 사이트 콘텐츠/사용자 데이터는 기본적으로 전송 금지(옵션으로만 허용)

---

## Phase 7 마일스톤(추천 순서)
1. **7.1 AI Extension Skeleton**: UI 1페이지 + mock provider
2. **7.2 Provider 1개 연동**: OpenAI/Anthropic 중 1개 선택, 키 설정 UI 포함
3. **7.3 Diff/Preview**: 적용 전 변경점 시각화
4. **7.4 Cloud/Market 연결**: 생성된 스타일을 Cloud로 저장/마켓 게시(권한/과금)


