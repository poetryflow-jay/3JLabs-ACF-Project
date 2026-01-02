# dist 폴더 관리 가이드

## 개요

`dist` 폴더는 빌드된 플러그인 ZIP 파일을 저장하는 디렉토리입니다. 자동 정리, 복구, 보존 기능을 통해 효율적으로 관리할 수 있습니다.

## 자동 정리 시스템

### cleanup_dist_folder.py

빌드 후 자동으로 구 버전 파일을 `dist/old/` 폴더로 이동시킵니다.

**보존 대상:**
- 최신 버전 ZIP 파일 (v13.4.7 기준)
- 마스터 올인원 플러그인
- Neural Link, WooCommerce License Bridge (마스터 전용)
- 문서 파일 (README, 작업 원칙, 로드맵, 릴리즈 노트, 메모리, 콘텍스트, 레퍼런스)

**이동 대상:**
- 구 버전 ZIP 파일 (v13.3.0, v13.4.0, v13.4.2 등)
- 핫픽스 파일
- 빌드 중간 폴더

**사용법:**
```bash
python cleanup_dist_folder.py
```

**자동 실행:**
- `build_all_plugins_all_editions.py` 실행 시 자동 실행
- `3j_dev_toolkit.py` 빌드 완료 시 자동 실행
- `build_v13_4_7.ps1` 실행 시 자동 실행

## 복구 시스템

### restore_from_archive.py

아카이브에서 파일을 복구할 수 있습니다.

**기능:**
1. 아카이브 목록 보기
2. 특정 파일 복구
3. 특정 버전 복구
4. 모든 파일 복구

**사용법:**

```bash
# 아카이브 목록 보기
python restore_from_archive.py --list

# 특정 파일 복구
python restore_from_archive.py --restore "acf-css-really-simple-style-management-center-master-v13.4.2.zip"

# 특정 버전 복구
python restore_from_archive.py --version "v13.4.2"

# 모든 파일 복구 (가장 최근 아카이브)
python restore_from_archive.py --restore-all

# 특정 아카이브에서 복구
python restore_from_archive.py --restore-all --archive "20260102-194952"
```

## 보존 기능

### 자동 보존 대상

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
   - `.md`, `.txt`, `.html`, `.json`, `.yaml`, `.yml`, `.pdf`, `.doc`, `.docx`
   - README, CHANGELOG, RELEASE, ROADMAP, GUIDE, MEMORY, CONTEXT, REFERENCE 포함 파일명

### 보존 패턴

정리 스크립트는 다음 패턴의 파일을 자동으로 보존합니다:

```python
- .*master-v13\.4\.7\.zip$  # 마스터 올인원
- .*neural-link-v[\d.]+\.zip$  # Neural Link
- .*woo-license-v[\d.]+\.zip$  # WooCommerce License Bridge
- .*Complete-Bundle-v13\.4\.7\.zip$  # 최신 번들
```

## 아카이브 구조

```
dist/
├── old/
│   └── 20260102-194952/  # 타임스탬프별 아카이브
│       ├── *.zip         # 이동된 ZIP 파일
│       └── */            # 이동된 폴더
└── *.zip                 # 최신 버전 ZIP 파일
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

1. **빌드 실행**
   ```bash
   python build_all_plugins_all_editions.py
   ```

2. **자동 정리 실행** (빌드 스크립트에 통합됨)
   - 구 버전 파일이 `dist/old/`로 자동 이동
   - 최신 버전만 `dist/`에 유지

3. **필요 시 복구**
   ```bash
   python restore_from_archive.py --list
   python restore_from_archive.py --restore "filename.zip"
   ```

4. **Git 커밋**
   ```bash
   git add dist/
   git commit -m "빌드 완료: v13.4.7"
   ```

## 주의사항

1. **덮어쓰기 확인**: 복구 시 기존 파일이 있으면 덮어쓰기 확인을 받습니다.
2. **아카이브 보존**: `dist/old/` 폴더는 Git에 커밋하지 않아도 됩니다 (`.gitignore`에 추가 권장).
3. **문서 파일**: 문서 파일은 항상 보존되므로 수동으로 관리할 필요가 없습니다.

## 문제 해결

### 파일이 잘못 이동된 경우

```bash
# 아카이브에서 파일 찾기
python restore_from_archive.py --list

# 파일 복구
python restore_from_archive.py --restore "filename.zip"
```

### 정리 스크립트 오류

정리 스크립트는 실패해도 빌드 프로세스를 중단하지 않습니다. 수동으로 실행할 수 있습니다:

```bash
python cleanup_dist_folder.py
```

---

**작성일**: 2026-01-02  
**작성자**: Jason (CTO, 3J Labs)  
**버전**: 1.0.0
