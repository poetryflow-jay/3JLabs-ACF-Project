#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
3J Labs - 아카이브에서 파일 복구 스크립트
dist/old 폴더의 아카이브에서 구 버전 파일을 복구합니다.

사용법:
    python restore_from_archive.py [옵션]
    
옵션:
    --list          : 아카이브 목록 보기
    --restore-all   : 모든 파일 복구
    --restore FILE  : 특정 파일 복구
    --version VER   : 특정 버전 복구
"""

import os
import shutil
import argparse
from pathlib import Path
from datetime import datetime
import re

def list_archives():
    """아카이브 목록 보기"""
    base_path = Path(__file__).parent
    old_path = base_path / 'dist' / 'old'
    
    if not old_path.exists():
        print("❌ old 폴더가 없습니다.")
        return
    
    archives = [d for d in old_path.iterdir() if d.is_dir()]
    
    if not archives:
        print("📦 아카이브가 없습니다.")
        return
    
    print("📦 아카이브 목록:")
    print("=" * 60)
    
    for archive in sorted(archives, reverse=True):
        zip_count = len(list(archive.rglob('*.zip')))
        folder_count = len([d for d in archive.rglob('*') if d.is_dir()])
        
        print(f"\n📁 {archive.name}")
        print(f"   - ZIP 파일: {zip_count}개")
        print(f"   - 폴더: {folder_count}개")
        
        # 주요 파일 목록 (처음 5개)
        zip_files = list(archive.rglob('*.zip'))[:5]
        if zip_files:
            print(f"   - 주요 파일:")
            for zf in zip_files:
                print(f"     • {zf.name}")

def restore_file(filename, archive_name=None):
    """특정 파일 복구"""
    base_path = Path(__file__).parent
    dist_path = base_path / 'dist'
    old_path = base_path / 'dist' / 'old'
    
    if not old_path.exists():
        print("❌ old 폴더가 없습니다.")
        return False
    
    # 아카이브 찾기
    if archive_name:
        archive_path = old_path / archive_name
        if not archive_path.exists():
            print(f"❌ 아카이브를 찾을 수 없습니다: {archive_name}")
            return False
        archives = [archive_path]
    else:
        archives = sorted([d for d in old_path.iterdir() if d.is_dir()], reverse=True)
    
    # 파일 찾기
    found = False
    for archive in archives:
        zip_files = list(archive.rglob(filename))
        if zip_files:
            for zip_file in zip_files:
                dest = dist_path / zip_file.name
                if dest.exists():
                    response = input(f"⚠️ 파일이 이미 존재합니다: {dest.name}\n덮어쓰시겠습니까? (y/N): ")
                    if response.lower() != 'y':
                        print("  ⏭️ 건너뜀")
                        continue
                
                shutil.copy2(str(zip_file), str(dest))
                print(f"✅ 복구 완료: {zip_file.name}")
                found = True
    
    if not found:
        print(f"❌ 파일을 찾을 수 없습니다: {filename}")
        return False
    
    return True

def restore_version(version_pattern):
    """특정 버전의 파일 복구"""
    base_path = Path(__file__).parent
    dist_path = base_path / 'dist'
    old_path = base_path / 'dist' / 'old'
    
    if not old_path.exists():
        print("❌ old 폴더가 없습니다.")
        return
    
    # 모든 아카이브에서 버전 패턴 검색
    archives = sorted([d for d in old_path.iterdir() if d.is_dir()], reverse=True)
    
    restored = []
    for archive in archives:
        zip_files = list(archive.rglob('*.zip'))
        for zip_file in zip_files:
            if version_pattern in zip_file.name:
                dest = dist_path / zip_file.name
                if not dest.exists():
                    shutil.copy2(str(zip_file), str(dest))
                    restored.append(zip_file.name)
                    print(f"✅ 복구: {zip_file.name}")
    
    if restored:
        print(f"\n✅ 총 {len(restored)}개 파일 복구 완료")
    else:
        print(f"❌ 버전 '{version_pattern}'에 해당하는 파일을 찾을 수 없습니다.")

def restore_all(archive_name=None):
    """모든 파일 복구 (주의: 덮어쓰기 가능)"""
    base_path = Path(__file__).parent
    dist_path = base_path / 'dist'
    old_path = base_path / 'dist' / 'old'
    
    if not old_path.exists():
        print("❌ old 폴더가 없습니다.")
        return
    
    # 아카이브 선택
    if archive_name:
        archive_path = old_path / archive_name
        if not archive_path.exists():
            print(f"❌ 아카이브를 찾을 수 없습니다: {archive_name}")
            return
        archives = [archive_path]
    else:
        # 가장 최근 아카이브 사용
        archives = sorted([d for d in old_path.iterdir() if d.is_dir()], reverse=True)
        if not archives:
            print("❌ 아카이브가 없습니다.")
            return
        archive = archives[0]
        print(f"⚠️ 가장 최근 아카이브 사용: {archive.name}")
        response = input("모든 파일을 복구하시겠습니까? (y/N): ")
        if response.lower() != 'y':
            print("❌ 취소되었습니다.")
            return
        archives = [archive]
    
    restored = []
    for archive in archives:
        zip_files = list(archive.rglob('*.zip'))
        for zip_file in zip_files:
            dest = dist_path / zip_file.name
            if dest.exists():
                print(f"⚠️ 건너뜀 (이미 존재): {zip_file.name}")
                continue
            
            shutil.copy2(str(zip_file), str(dest))
            restored.append(zip_file.name)
    
    if restored:
        print(f"\n✅ 총 {len(restored)}개 파일 복구 완료")
    else:
        print("❌ 복구할 파일이 없습니다.")

def main():
    parser = argparse.ArgumentParser(description='아카이브에서 파일 복구')
    parser.add_argument('--list', action='store_true', help='아카이브 목록 보기')
    parser.add_argument('--restore-all', action='store_true', help='모든 파일 복구')
    parser.add_argument('--restore', type=str, help='특정 파일 복구')
    parser.add_argument('--version', type=str, help='특정 버전 복구 (예: v13.4.2)')
    parser.add_argument('--archive', type=str, help='아카이브 이름 지정')
    
    args = parser.parse_args()
    
    if args.list:
        list_archives()
    elif args.restore_all:
        restore_all(args.archive)
    elif args.restore:
        restore_file(args.restore, args.archive)
    elif args.version:
        restore_version(args.version)
    else:
        parser.print_help()

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n❌ 사용자에 의해 중단되었습니다")
    except Exception as e:
        print(f"\n\n❌ 오류 발생: {e}")
        import traceback
        traceback.print_exc()
