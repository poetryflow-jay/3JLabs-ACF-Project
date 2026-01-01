# Cursor 워크스페이스 설정 안내

## ⚠️ 중요: Cursor 재시작 필요

프로젝트가 새 경로로 이동되었으므로, Cursor를 재시작하고 새 워크스페이스를 열어야 합니다.

## 새 프로젝트 경로

```
C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS
```

## 설정 단계

### 1단계: 현재 Cursor 종료
- **File > Exit** 또는 **Alt+F4**로 Cursor 완전 종료
- 또는 작업 관리자에서 모든 Cursor 프로세스 종료

### 2단계: Cursor 재시작
- Cursor 실행

### 3단계: 새 워크스페이스 열기
**방법 A: File 메뉴 사용**
1. **File > Open Folder...** (Ctrl+K, Ctrl+O)
2. 다음 경로 입력 또는 선택:
   ```
   C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS
   ```
3. **Select Folder** 클릭

**방법 B: 터미널에서 열기**
```powershell
cd "C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS"
cursor .
```

**방법 C: Windows 탐색기에서**
1. Windows 탐색기에서 폴더 열기:
   ```
   C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS
   ```
2. 폴더 내에서 우클릭
3. **Open with Cursor** 선택 (설치되어 있다면)

## 확인 사항

### 워크스페이스 인식 확인
Cursor 하단 상태바에서 다음을 확인:
- 왼쪽 하단: `3J-ACF-CSS` 또는 프로젝트 폴더명 표시
- 터미널 기본 경로가 새 경로로 설정됨

### 터미널 테스트
새 터미널 열기 (Ctrl+`) 후:
```powershell
pwd
# 출력: C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS
```

### Git 확인
```powershell
git status
# 정상적으로 Git 저장소 인식되어야 함
```

## 생성된 설정 파일

다음 설정 파일이 자동 생성되었습니다:
- `.cursor/settings.json` - Cursor 전용 설정
- `.vscode/settings.json` - VSCode 호환 설정
- `WORKSPACE_PATH.md` - 워크스페이스 경로 정보

## 문제 해결

### 기존 워크스페이스가 계속 열리는 경우
1. **File > Close Folder** (Ctrl+K, F)
2. **File > Open Folder...** 로 새 경로 열기

### 터미널 경로가 잘못된 경우
1. 터미널 창 닫기
2. 새 터미널 열기 (Ctrl+`)
3. 자동으로 새 경로에서 시작되어야 함

### Git이 인식되지 않는 경우
```powershell
cd "C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS"
git status
```

## 작업 디렉토리 정보

- **현재 경로**: `C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS`
- **이전 경로**: `C:\Users\computer\OneDrive\Desktop\클라우드 백업\OneDrive\문서\jj-css-premium` (더 이상 사용 안 함)

---
**업데이트**: 2026-01-01
**버전**: 8.4.0
