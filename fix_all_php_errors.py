#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
3J Labs PHP 오류 자동 수정 도구
===================================
발견된 PHP 문법 오류를 자동으로 수정 시도
"""

import subprocess
import re
import sys
from pathlib import Path
from code_quality_checker import CodeQualityChecker

def fix_php_error(file_path, error_msg, line_num):
    """PHP 오류 수정 시도"""
    file_path = Path(file_path)
    
    with open(file_path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    if line_num and line_num <= len(lines):
        original_line = lines[line_num - 1]
        
        # 일반적인 오류 패턴 수정
        fixes = [
            # 괄호 닫기 오류
            (r'\) \),', r'),'),
            (r'array\(\) ,', r'array() ),'),
            (r'microtime\( true \),', r'microtime( true ) ),'),
            # 함수 호출 괄호 오류
            (r'call_user_func\( array\( \$([^\)]+)\), \'([^\']+)\' \)', 
             r'call_user_func( array( $\1 ) ), \'\2\''),
        ]
        
        fixed_line = original_line
        for pattern, replacement in fixes:
            fixed_line = re.sub(pattern, replacement, fixed_line)
        
        if fixed_line != original_line:
            lines[line_num - 1] = fixed_line
            
            with open(file_path, 'w', encoding='utf-8') as f:
                f.writelines(lines)
            
            print(f"  ✓ 라인 {line_num} 수정: {file_path.name}")
            return True
    
    return False

def main():
    print("="*70)
    print("PHP 오류 자동 수정")
    print("="*70)
    print()
    
    checker = CodeQualityChecker(quick_mode=False)
    checker.scan_directory()
    
    fixed_count = 0
    
    for error in checker.errors:
        if error['type'] == 'php_syntax':
            file_path = error['file']
            error_msg = error['message']
            line_num = error.get('line')
            
            print(f"수정 시도: {Path(file_path).name}")
            if fix_php_error(file_path, error_msg, line_num):
                fixed_count += 1
                
                # 수정 후 재검사
                if checker.check_php_syntax(Path(file_path)):
                    print(f"  ✅ 수정 완료 및 검증 통과")
                else:
                    print(f"  ⚠️  수정 후에도 오류 남아있음")
            else:
                print(f"  ❌ 자동 수정 불가능 (수동 수정 필요)")
            print()
    
    print(f"총 {fixed_count}개 오류 수정 시도 완료")
    
    # 최종 검사
    print("\n최종 검사 실행...")
    final_checker = CodeQualityChecker(quick_mode=False)
    final_ok = final_checker.scan_directory()
    final_checker.print_report()
    
    sys.exit(0 if final_ok else 1)

if __name__ == '__main__':
    main()
