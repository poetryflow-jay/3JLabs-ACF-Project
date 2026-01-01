#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
코드 품질 검사 헤더 추가 도구
새로 생성되거나 수정된 파일에 품질 검사 헤더를 추가합니다.
"""

import os
from pathlib import Path

HEADERS = {
    '.php': 'code_quality_header_php.txt',
    '.js': 'code_quality_header_js.txt',
    '.jsx': 'code_quality_header_js.txt',
}

def has_quality_header(file_path):
    """파일에 품질 검사 헤더가 있는지 확인"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read(500)  # 처음 500자만 확인
            return '3J Labs Code Quality Assurance' in content
    except:
        return False

def add_header(file_path, header_file):
    """파일에 헤더 추가"""
    header_path = Path(__file__).parent / header_file
    
    if not header_path.exists():
        print(f"⚠️  헤더 파일을 찾을 수 없습니다: {header_file}")
        return False
    
    with open(header_path, 'r', encoding='utf-8') as f:
        header_content = f.read()
    
    with open(file_path, 'r', encoding='utf-8') as f:
        original_content = f.read()
    
    # 이미 헤더가 있으면 스킵
    if has_quality_header(file_path):
        return False
    
    # PHP 파일인 경우 <?php 다음에 추가
    if file_path.suffix == '.php':
        if original_content.startswith('<?php'):
            # <?php 다음 줄에 추가
            lines = original_content.split('\n', 1)
            new_content = lines[0] + '\n' + header_content + '\n' + (lines[1] if len(lines) > 1 else '')
        else:
            new_content = header_content + '\n' + original_content
    else:
        new_content = header_content + '\n' + original_content
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    
    return True

def main():
    import sys
    
    if len(sys.argv) > 1:
        target_files = [Path(f) for f in sys.argv[1:]]
    else:
        # 주요 파일에만 자동 추가
        target_files = [
            Path('acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php'),
            Path('jj_deployment_system.py'),
        ]
    
    base_dir = Path(__file__).parent
    
    added_count = 0
    for file_path in target_files:
        full_path = base_dir / file_path if not file_path.is_absolute() else file_path
        
        if not full_path.exists():
            print(f"⚠️  파일을 찾을 수 없습니다: {file_path}")
            continue
        
        suffix = full_path.suffix
        if suffix in HEADERS:
            if add_header(full_path, HEADERS[suffix]):
                print(f"✅ 헤더 추가됨: {file_path.name}")
                added_count += 1
            else:
                print(f"⏭️  이미 헤더 있음: {file_path.name}")
    
    print(f"\n총 {added_count}개 파일에 헤더 추가 완료")

if __name__ == '__main__':
    main()
