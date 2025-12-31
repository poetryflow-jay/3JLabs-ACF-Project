# 📝 베타 폼 설정 가이드

이 문서는 `beta-signup.html` 폼을 실제로 작동시키기 위한 설정 방법을 안내합니다.

## Option 1: Formspree (권장)

**무료 티어**: 월 50건 제출 / 무제한 폼

### 설정 단계

1. **계정 생성**
   - https://formspree.io 접속
   - 이메일로 무료 가입

2. **새 폼 생성**
   - Dashboard → "+ New Form"
   - Form 이름: "ACF CSS Beta Signup"
   - 생성 후 Form ID 복사 (예: `xpwzabcd`)

3. **HTML 수정**
   ```html
   <!-- beta-signup.html 파일에서 -->
   <form action="https://formspree.io/f/xpwzabcd" method="POST">
   ```

4. **이메일 알림 설정**
   - Form Settings → Email Notifications
   - 알림 받을 이메일 주소 입력

5. **스팸 방지**
   - Form Settings → Spam Filtering → Enable reCAPTCHA

### 제출 데이터 확인
- Dashboard → 해당 폼 → Submissions

---

## Option 2: Google Forms

**무료 / 무제한**

### 설정 단계

1. **Google Forms 생성**
   - https://forms.google.com
   - 새 폼 만들기

2. **필드 추가**
   - 이름 (단답형, 필수)
   - 이메일 (단답형, 필수, 이메일 검증)
   - 웹사이트 URL (단답형)
   - 역할 (드롭다운)
   - 기대 기능 (드롭다운)
   - 하고 싶은 말 (장문형)

3. **스프레드시트 연동**
   - 응답 탭 → 스프레드시트 연결

4. **HTML에서 연동**
   - Google Forms 직접 임베드 또는
   - 랜딩 페이지에서 Google Forms 링크로 리다이렉트

```html
<a href="https://forms.gle/YOUR_FORM_ID" class="submit-btn">
    ✨ 베타 테스터 신청하기
</a>
```

---

## Option 3: Netlify Forms

**Netlify 호스팅 사용 시**

### 설정 단계

1. **HTML 수정**
   ```html
   <form name="beta-signup" method="POST" data-netlify="true">
       <input type="hidden" name="form-name" value="beta-signup">
       <!-- 기존 필드들 -->
   </form>
   ```

2. **Netlify에 배포**
   - 자동으로 폼 감지 및 설정

3. **알림 설정**
   - Site Settings → Forms → Form notifications

---

## Option 4: 자체 서버 (PHP/Node.js)

### PHP 예시

```php
<?php
// submit.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_URL);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    $interest = filter_input(INPUT_POST, 'interest', FILTER_SANITIZE_STRING);
    $feedback = filter_input(INPUT_POST, 'feedback', FILTER_SANITIZE_STRING);
    
    // 데이터베이스 저장 또는 이메일 전송
    $to = 'beta@j-j-labs.com';
    $subject = 'New Beta Signup: ' . $name;
    $message = "Name: $name\nEmail: $email\nWebsite: $website\nRole: $role\nInterest: $interest\nFeedback: $feedback";
    
    mail($to, $subject, $message);
    
    // 감사 페이지로 리다이렉트
    header('Location: thank-you.html');
    exit;
}
?>
```

---

## 제출 후 페이지

### thank-you.html 생성

```html
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>신청 완료 - ACF CSS Beta</title>
</head>
<body>
    <h1>✅ 베타 테스터 신청 완료!</h1>
    <p>확인 이메일이 곧 발송됩니다.</p>
    <a href="/">홈으로 돌아가기</a>
</body>
</html>
```

---

## 이메일 자동화 (선택)

### Zapier 연동

1. Formspree → Zapier Trigger
2. Action: Gmail/Mailchimp으로 환영 이메일 전송

### 환영 이메일 템플릿

```
제목: ACF CSS 베타 테스터로 선정되셨습니다! 🎉

안녕하세요 {{name}}님,

ACF CSS Manager v6.2.0 베타 프로그램에 참여해 주셔서 감사합니다!

📦 베타 버전 다운로드: [링크]
📖 시작 가이드: [링크]
💬 피드백 채널: [Discord/Slack 링크]

정식 출시 시 50% 할인 코드는 별도로 발송됩니다.

감사합니다,
J&J Labs 팀
```

---

## 데이터 관리

### 수집 데이터 목록

| 필드 | 용도 |
|------|------|
| name | 개인화 이메일 |
| email | 베타 버전 배포, 할인 코드 발송 |
| website | 테스트 환경 파악 |
| role | 사용자 세그먼트 분석 |
| interest | 기능 우선순위 결정 |
| feedback | 제품 개선 아이디어 |

### GDPR 준수

- 개인정보 처리방침 링크 포함 ✓
- 동의 체크박스 필수 ✓
- 데이터 삭제 요청 처리 프로세스 준비

---

*문의: beta@j-j-labs.com*

