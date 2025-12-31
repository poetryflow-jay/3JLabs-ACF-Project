# 🚀 ACF CSS Manager - 마케팅 사이트 배포 가이드

이 폴더에는 ACF CSS Manager 마케팅 사이트의 모든 파일이 포함되어 있습니다.

## 📁 폴더 구조

```
deploy/
├── index.html          # 메인 랜딩 페이지
├── beta.html           # 베타 테스터 신청 폼
├── netlify.toml        # Netlify 설정
├── _redirects          # URL 리다이렉트
├── demo/               # 데모 사이트 페이지
│   ├── index.html      # Tech Startup Dark
│   ├── law-firm.html   # Elegant Law Firm
│   └── cafe.html       # Cozy Cafe
└── screenshots/        # 마케팅용 스크린샷
    ├── 01-admin-center.png
    ├── 02-ai-generation.png
    └── ... (10개)
```

---

## 🌐 배포 방법

### Option 1: Netlify (권장) ⭐

**드래그 앤 드롭 배포:**

1. https://app.netlify.com 로그인
2. **Sites** → 이 폴더를 드래그 앤 드롭
3. 자동으로 배포 완료!
4. 커스텀 도메인 설정 (선택)

**CLI 배포:**

```bash
npm install -g netlify-cli
netlify login
cd marketing/deploy
netlify deploy --prod
```

**무료 URL 예시:** `https://acf-css-manager.netlify.app`

---

### Option 2: Vercel

```bash
npm install -g vercel
cd marketing/deploy
vercel --prod
```

---

### Option 3: GitHub Pages

1. GitHub 저장소 생성: `acf-css-landing`
2. 이 폴더의 내용을 push
3. Settings → Pages → Source: `main` branch
4. URL: `https://username.github.io/acf-css-landing`

---

## 🔧 Formspree 설정

베타 폼이 작동하려면:

1. https://formspree.io 에서 가입
2. **+ New Form** → Form ID 복사
3. `beta.html` 파일에서 `YOUR_FORM_ID` 교체:

```html
<form action="https://formspree.io/f/YOUR_FORM_ID" method="POST">
```

---

## 🔗 페이지 URL

| 페이지 | 경로 |
|--------|------|
| 랜딩 페이지 | `/` |
| 베타 신청 | `/beta` 또는 `/beta.html` |
| 데모: Tech Startup | `/demo/` |
| 데모: Law Firm | `/demo/law-firm.html` |
| 데모: Cafe | `/demo/cafe.html` |

---

## 📊 배포 후 체크리스트

- [ ] 랜딩 페이지 로딩 확인
- [ ] 베타 폼 제출 테스트
- [ ] Formspree 대시보드에서 제출 확인
- [ ] 데모 페이지 링크 작동 확인
- [ ] 스크린샷 이미지 로딩 확인
- [ ] Google Analytics 연동 (선택)
- [ ] 커스텀 도메인 설정 (선택)

---

## 📧 문의

- **기술 지원**: support@j-j-labs.com
- **베타 관련**: beta@j-j-labs.com
