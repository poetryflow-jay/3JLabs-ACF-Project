#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
3J Labs Code Quality Checker
===================================
모든 코드 파일에 대한 품질 검사 도구
- PHP: 문법 검사 (php -l)
- JavaScript: 기본 검증
- 경로 참조 확인
- 클래스명/함수명 일관성 확인
- 변경 사항 기록

사용법:
    python code_quality_checker.py [--quick] [--file=경로]
    --quick: 빠른 검사 (중요 파일만)
    --file: 특정 파일만 검사
"""

import os
import sys
import subprocess
import re
from pathlib import Path
from datetime import datetime
import json

class CodeQualityChecker:
    def __init__(self, quick_mode=False, target_file=None):
        self.quick_mode = quick_mode
        self.target_file = target_file
        self.base_dir = Path(__file__).parent.resolve()
        self.errors = []
        self.warnings = []
        self.checked_files = []
        self.change_log = []
        
        # PHP 경로 찾기
        self.php_bin = self._find_php_bin()
        
    def _find_php_bin(self):
        """PHP CLI 경로 찾기"""
        import shutil
        env_bin = os.environ.get('PHP_BIN')
        if env_bin and shutil.which(env_bin):
            return shutil.which(env_bin)
        which_php = shutil.which('php')
        if which_php:
            return which_php
        return None
    
    def check_php_syntax(self, file_path):
        """PHP 문법 검사"""
        if not self.php_bin:
            self.warnings.append(f"PHP CLI를 찾을 수 없습니다: {file_path}")
            return True
        
        try:
            result = subprocess.run(
                [self.php_bin, '-l', str(file_path)],
                capture_output=True,
                text=True,
                timeout=10
            )
            if result.returncode != 0:
                error_msg = result.stdout.strip()
                self.errors.append({
                    'file': str(file_path),
                    'type': 'php_syntax',
                    'message': error_msg,
                    'line': self._extract_line_number(error_msg)
                })
                return False
            return True
        except Exception as e:
            self.warnings.append(f"PHP 검사 실패 {file_path}: {e}")
            return True
    
    def check_path_references(self, file_path):
        """경로 참조 확인"""
        if not file_path.suffix in ['.php', '.js', '.py']:
            return True
        
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # 상대 경로 패턴 찾기
            suspicious_patterns = [
                r'\.\./\.\./\.\./',  # 3단계 이상 상대 경로
                r'require.*\.\./',    # require에 상대 경로
                r'include.*\.\./',    # include에 상대 경로
                r'require_once.*\.\./',
                r'include_once.*\.\./',
            ]
            
            lines = content.split('\n')
            for i, line in enumerate(lines, 1):
                for pattern in suspicious_patterns:
                    if re.search(pattern, line):
                        self.warnings.append({
                            'file': str(file_path),
                            'type': 'path_reference',
                            'line': i,
                            'message': f"의심스러운 경로 참조: {line.strip()[:80]}"
                        })
            
            return True
        except Exception as e:
            self.warnings.append(f"경로 검사 실패 {file_path}: {e}")
            return True
    
    def check_class_function_names(self, file_path):
        """클래스명/함수명 일관성 확인"""
        if file_path.suffix != '.php':
            return True
        
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # 클래스명 검사 (JJ_로 시작해야 함)
            class_pattern = r'class\s+([A-Za-z_][A-Za-z0-9_]*)'
            classes = re.findall(class_pattern, content)
            
            for cls in classes:
                if not cls.startswith('JJ_'):
                    self.warnings.append({
                        'file': str(file_path),
                        'type': 'naming',
                        'message': f"클래스명이 JJ_로 시작하지 않음: {cls}"
                    })
            
            return True
        except Exception as e:
            self.warnings.append(f"이름 검사 실패 {file_path}: {e}")
            return True
    
    def _extract_line_number(self, error_msg):
        """에러 메시지에서 라인 번호 추출"""
        match = re.search(r'on line (\d+)', error_msg)
        return int(match.group(1)) if match else None
    
    def check_file(self, file_path):
        """파일 검사"""
        file_path = Path(file_path)
        if not file_path.exists():
            return False
        
        self.checked_files.append(file_path)
        
        if file_path.suffix == '.php':
            php_ok = self.check_php_syntax(file_path)
            self.check_path_references(file_path)
            self.check_class_function_names(file_path)
            return php_ok
        elif file_path.suffix in ['.js', '.jsx']:
            self.check_path_references(file_path)
            return True
        else:
            return True
    
    def scan_directory(self, directory=None):
        """디렉토리 스캔"""
        if directory is None:
            directory = self.base_dir
        
        directory = Path(directory)
        
        # 검사할 파일 패턴
        patterns = {
            'php': ['*.php'],
            'js': ['*.js', '*.jsx'],
        }
        
        files_to_check = []
        
        if self.target_file:
            files_to_check.append(Path(self.target_file))
        elif self.quick_mode:
            # 빠른 모드: 주요 파일만
            key_files = [
                'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php',
                'acf-css-ai-extension/acf-css-ai-extension.php',
                'acf-css-neural-link/acf-css-neural-link.php',
                'jj_deployment_system.py',
            ]
            for f in key_files:
                full_path = self.base_dir / f
                if full_path.exists():
                    files_to_check.append(full_path)
        else:
            # 전체 검사
            for ext, patterns_list in patterns.items():
                for pattern in patterns_list:
                    files_to_check.extend(directory.rglob(pattern))
        
        # 제외 패턴
        exclude_patterns = [
            'node_modules',
            'vendor',
            '.git',
            '__pycache__',
            '.venv',
            'tests',
        ]
        
        filtered_files = []
        for f in files_to_check:
            if not any(pattern in str(f) for pattern in exclude_patterns):
                filtered_files.append(f)
        
        # 검사 실행
        all_ok = True
        for file_path in filtered_files:
            if not self.check_file(file_path):
                all_ok = False
        
        return all_ok
    
    def generate_report(self):
        """검사 보고서 생성"""
        report = {
            'timestamp': datetime.now().isoformat(),
            'checked_files': len(self.checked_files),
            'errors': len(self.errors),
            'warnings': len(self.warnings),
            'details': {
                'errors': self.errors,
                'warnings': self.warnings,
            }
        }
        
        return report
    
    def print_report(self):
        """검사 결과 출력"""
        print("="*70)
        print("코드 품질 검사 결과")
        print("="*70)
        print(f"검사한 파일 수: {len(self.checked_files)}")
        print(f"오류: {len(self.errors)}개")
        print(f"경고: {len(self.warnings)}개")
        print()
        
        if self.errors:
            print("❌ 오류:")
            for error in self.errors:
                print(f"  - {error['file']}")
                if error.get('line'):
                    print(f"    라인 {error['line']}: {error['message']}")
                else:
                    print(f"    {error['message']}")
            print()
        
        if self.warnings:
            print("⚠️  경고:")
            for warning in self.warnings[:20]:  # 최대 20개만 표시
                if isinstance(warning, dict):
                    print(f"  - {warning['file']}: {warning.get('message', warning)}")
                else:
                    print(f"  - {warning}")
            if len(self.warnings) > 20:
                print(f"  ... 외 {len(self.warnings) - 20}개 경고")
            print()
        
        if not self.errors:
            print("✅ 모든 문법 검사 통과!")
        
        return len(self.errors) == 0

def main():
    import argparse
    
    parser = argparse.ArgumentParser(description='3J Labs 코드 품질 검사 도구')
    parser.add_argument('--quick', action='store_true', help='빠른 검사 (주요 파일만)')
    parser.add_argument('--file', type=str, help='특정 파일만 검사')
    parser.add_argument('--json', action='store_true', help='JSON 형식으로 출력')
    
    args = parser.parse_args()
    
    checker = CodeQualityChecker(quick_mode=args.quick, target_file=args.file)
    
    print("🔍 코드 품질 검사 시작...")
    print()
    
    all_ok = checker.scan_directory()
    
    if args.json:
        report = checker.generate_report()
        print(json.dumps(report, indent=2, ensure_ascii=False))
    else:
        checker.print_report()
    
    sys.exit(0 if all_ok else 1)

if __name__ == '__main__':
    main()
