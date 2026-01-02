# 3J Labs ACF CSS

프로젝트 경로: C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS
버전: 8.4.0
업데이트: 2026-01-02

자세한 내용은 README_WORKSPACE.md를 참조하세요.

---

## 🔧 작업 원칙 (Development Principles)

### 터미널 및 빌드 작업 원칙

1. **Python REPL 상태 감지 및 대응**
   - 터미널 프롬프트가 `>>>`로 표시되면 Python REPL 상태입니다.
   - 이 상태에서는 모든 명령이 Python 코드로 해석되어 `SyntaxError`가 발생할 수 있습니다.
   - **해결 방법**: `exit()` 명령으로 Python을 종료한 후 정상 셸로 복구합니다.

2. **타임아웃 및 재시도 전략**
   - 터미널 작업이 40초 이상 응답이 없거나, 유의미한 진행이 없으면:
     - 작업을 중지하고 다른 방법으로 재시도합니다.
     - 복잡한 한 줄 명령 대신 `.ps1` 스크립트 파일을 생성하여 실행합니다.

3. **PowerShell 빌드 스크립트 원칙**
   - 복잡한 PowerShell 명령은 한 줄로 작성하지 않고, 별도의 `.ps1` 스크립트 파일로 분리합니다.
   - 이렇게 하면 Python 문자열 이스케이프 문제를 피하고, 재사용성과 가독성이 향상됩니다.

4. **ZIP 빌드 주의사항**
   - WordPress 플러그인 ZIP 파일은 **플러그인 폴더가 포함**되어야 합니다.
   - ❌ 잘못된 방법: `Compress-Archive -Path "$folder\*"` (ZIP 루트에 파일이 풀림)
   - ✅ 올바른 방법: `Compress-Archive -Path $folder` (ZIP 안에 폴더가 포함됨)
   - 이렇게 해야 WordPress 업로드 설치 시 올바르게 인식됩니다.

---

*작성일: 2026-01-02*  
*작성자: Jason (CTO, 3J Labs)*
