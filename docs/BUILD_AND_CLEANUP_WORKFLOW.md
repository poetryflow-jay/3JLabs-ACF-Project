# 빌드 및 정리 워크플로우

## 개요

3J Labs ACF CSS 플러그인 패밀리의 빌드 및 정리 프로세스를 자동화하여 효율적으로 관리합니다.

## 자동화된 워크플로우

### 1. 전체 플러그인 빌드

```bash
python build_all_plugins_all_editions.py
```

**자동 실행되는 작업:**
1. 모든 플러그인 버전 확인
2. 에디션별 빌드 (Free, Basic, Premium, Unlimited × Standard, Partner, Master)
3. 마스터 올인원 플러그인 빌드
4. 번들 패키지 생성
5. **대시보드 자동 업데이트**
6. **dist 폴더 자동 정리** ✨

### 2. 개별 플러그인 빌드 (GUI)

```bash
python 3j_dev_toolkit.py
```

GUI에서 개별 플러그인을 선택하여 빌드할 수 있으며, 빌드 완료 시 자동으로 정리가 실행됩니다.

### 3. PowerShell 빌드 스크립트

```powershell
.\build_v13_4_7.ps1
```

빌드 완료 후 자동으로:
- 대시보드 업데이트
- dist 폴더 정리

## 자동 정리 시스템

### 보존 대상

다음 파일들은 자동으로 보존됩니다:

1. **최신 버전 ZIP 파일**
   - 각 플러그인의 최신 버전 에디션별 ZIP
   - 마스터 올인원 플러그인
   - 완전한 번들 패키지

2. **마스터 전용 플러그인**
   - `acf-css-really-simple-style-management-center-master-v13.4.7.zip`
   - `acf-css-neural-link-v*.zip`
   - `marketing-wordpress-plugins-acf-css-woo-license-v*.zip`

3. **문서 파일**
   - 모든 `.md`, `.txt`, `.html`, `.json`, `.yaml`, `.yml`, `.pdf`, `.doc`, `.docx` 파일
   - README, CHANGELOG, RELEASE, ROADMAP, GUIDE, MEMORY, CONTEXT, REFERENCE 포함 파일명

### 이동 대상

다음 파일들은 `dist/old/` 폴더로 자동 이동됩니다:

1. **구 버전 ZIP 파일**
   - v13.3.0, v13.4.0, v13.4.2 등 이전 버전
   - 핫픽스 파일 (`-hotfix` 포함)

2. **빌드 중간 폴더**
   - 에디션별 빌드 중 생성된 임시 폴더

## 복구 시스템

### 아카이브 목록 보기

```bash
python restore_from_archive.py --list
```

### 특정 파일 복구

```bash
python restore_from_archive.py --restore "acf-css-really-simple-style-management-center-master-v13.4.2.zip"
```

### 특정 버전 복구

```bash
python restore_from_archive.py --version "v13.4.2"
```

### 모든 파일 복구

```bash
python restore_from_archive.py --restore-all
```

### 특정 아카이브에서 복구

```bash
python restore_from_archive.py --restore-all --archive "20260102-194952"
```

## 수동 정리

필요 시 수동으로 정리를 실행할 수 있습니다:

```bash
python cleanup_dist_folder.py
```

## Git 연동

모든 버전의 파일은 Git에 커밋되어 있으므로, 필요 시 Git에서 복구할 수 있습니다:

```bash
# 특정 커밋의 파일 확인
git show <commit-hash>:dist/<filename>

# 특정 커밋으로 파일 복구
git checkout <commit-hash> -- dist/<filename>
```

## 권장 워크플로우

### 일상적인 빌드

1. **빌드 실행**
   ```bash
   python build_all_plugins_all_editions.py
   ```

2. **자동 정리 실행됨** (빌드 스크립트에 통합)
   - 구 버전 파일이 `dist/old/`로 자동 이동
   - 최신 버전만 `dist/`에 유지

3. **Git 커밋**
   ```bash
   git add dist/
   git commit -m "빌드 완료: v13.4.7"
   ```

### 구 버전 복구가 필요한 경우

1. **아카이브 확인**
   ```bash
   python restore_from_archive.py --list
   ```

2. **파일 복구**
   ```bash
   python restore_from_archive.py --restore "filename.zip"
   ```

3. **Git에서 복구** (필요 시)
   ```bash
   git checkout <commit-hash> -- dist/<filename>
   ```

## 문제 해결

### 파일이 잘못 이동된 경우

1. 아카이브에서 파일 찾기
2. 복구 스크립트로 복구
3. 정리 스크립트 개선 (필요 시)

### 정리 스크립트 오류

정리 스크립트는 실패해도 빌드 프로세스를 중단하지 않습니다. 수동으로 실행할 수 있습니다:

```bash
python cleanup_dist_folder.py
```

### 보존 패턴 추가

`cleanup_dist_folder.py`의 `get_preserved_patterns()` 함수에 패턴을 추가하면 됩니다.

---

**작성일**: 2026-01-02  
**작성자**: Jason (CTO, 3J Labs)  
**버전**: 1.0.0
