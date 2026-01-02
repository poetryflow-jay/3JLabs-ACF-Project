#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
[Phase 20] PO 파일을 MO 파일로 컴파일하는 스크립트

WP-CLI 또는 msgfmt를 사용하여 PO 파일을 MO 파일로 컴파일합니다.
"""

import os
import subprocess
from pathlib import Path
from typing import List, Tuple

def find_msgfmt() -> str:
    """msgfmt 실행 파일 경로 찾기"""
    # Windows에서 msgfmt 찾기
    possible_paths = [
        r'C:\Program Files\Git\usr\bin\msgfmt.exe',
        r'C:\msys64\usr\bin\msgfmt.exe',
        r'C:\msys\usr\bin\msgfmt.exe',
    ]
    
    for path in possible_paths:
        if os.path.exists(path):
            return path
    
    # PATH에서 찾기
    try:
        result = subprocess.run(['where', 'msgfmt'], capture_output=True, text=True, shell=True)
        if result.returncode == 0 and result.stdout.strip():
            return result.stdout.strip().split('\n')[0]
    except:
        pass
    
    return None

def compile_po_to_mo(po_path: Path, mo_path: Path, msgfmt_path: str = None) -> Tuple[bool, str]:
    """PO 파일을 MO 파일로 컴파일"""
    if not po_path.exists():
        return False, f"PO 파일이 없습니다: {po_path}"
    
    # msgfmt 경로 찾기
    if msgfmt_path is None:
        msgfmt_path = find_msgfmt()
    
    if msgfmt_path and os.path.exists(msgfmt_path):
        # msgfmt 사용
        try:
            cmd = [msgfmt_path, '-o', str(mo_path), str(po_path)]
            result = subprocess.run(cmd, capture_output=True, text=True, cwd=po_path.parent)
            
            if result.returncode == 0:
                return True, "msgfmt로 컴파일 완료"
            else:
                return False, f"msgfmt 오류: {result.stderr}"
        except Exception as e:
            return False, f"msgfmt 실행 오류: {e}"
    else:
        # Python으로 직접 컴파일 (간단한 버전)
        try:
            return compile_po_to_mo_python(po_path, mo_path)
        except Exception as e:
            return False, f"Python 컴파일 오류: {e}"

def compile_po_to_mo_python(po_path: Path, mo_path: Path) -> Tuple[bool, str]:
    """Python으로 PO 파일을 MO 파일로 컴파일 (간단한 버전)"""
    try:
        # polib 라이브러리 사용 (있는 경우)
        try:
            import polib
            po = polib.pofile(str(po_path))
            po.save_as_mofile(str(mo_path))
            return True, "polib로 컴파일 완료"
        except ImportError:
            # polib가 없으면 기본 파싱 (제한적)
            return compile_po_to_mo_basic(po_path, mo_path)
    except Exception as e:
        return False, f"Python 컴파일 오류: {e}"

def compile_po_to_mo_basic(po_path: Path, mo_path: Path) -> Tuple[bool, str]:
    """기본 PO → MO 변환 (간단한 버전)"""
    # 실제로는 polib나 msgfmt를 사용하는 것이 좋지만,
    # 여기서는 경고만 표시
    return False, "msgfmt 또는 polib가 필요합니다. 'pip install polib' 또는 msgfmt를 설치하세요."

def main():
    """메인 함수"""
    base_path = Path(__file__).parent
    plugin_path = base_path / 'acf-css-really-simple-style-management-center-master'
    languages_path = plugin_path / 'languages'
    
    if not languages_path.exists():
        print(f"❌ languages 폴더가 없습니다: {languages_path}")
        return
    
    print("=" * 60)
    print("Phase 20: PO 파일을 MO 파일로 컴파일")
    print("=" * 60)
    print()
    
    # msgfmt 경로 찾기
    msgfmt_path = find_msgfmt()
    if msgfmt_path:
        print(f"✅ msgfmt 발견: {msgfmt_path}")
    else:
        print("⚠️ msgfmt를 찾을 수 없습니다. Python polib를 시도합니다.")
        try:
            import polib
            print("✅ polib 라이브러리 사용 가능")
        except ImportError:
            print("❌ polib가 설치되어 있지 않습니다.")
            print("   설치 방법: pip install polib")
            return
    
    print()
    
    # PO 파일 찾기
    po_files = list(languages_path.glob('*.po'))
    
    if not po_files:
        print("❌ PO 파일을 찾을 수 없습니다.")
        return
    
    success_count = 0
    fail_count = 0
    
    for po_file in po_files:
        mo_file = po_file.with_suffix('.mo')
        lang_code = po_file.stem.replace('acf-css-really-simple-style-management-center-', '')
        
        print(f"📝 [{lang_code}] 컴파일 중...")
        
        success, message = compile_po_to_mo(po_file, mo_file, msgfmt_path)
        
        if success:
            print(f"   ✅ 완료: {mo_file.name}")
            success_count += 1
        else:
            print(f"   ❌ 실패: {message}")
            fail_count += 1
    
    print()
    print("=" * 60)
    print(f"✅ 완료: {success_count}개")
    if fail_count > 0:
        print(f"❌ 실패: {fail_count}개")
    print("=" * 60)
    
    if fail_count > 0:
        print()
        print("💡 해결 방법:")
        print("   1. msgfmt 설치 (Git for Windows에 포함됨)")
        print("   2. 또는 Python polib 설치: pip install polib")
        print("   3. 또는 WP-CLI 사용: wp i18n make-mo languages/")

if __name__ == '__main__':
    main()
