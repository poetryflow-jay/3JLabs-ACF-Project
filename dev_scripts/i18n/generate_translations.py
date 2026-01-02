#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
[Phase 9.1] 번역 파일 자동 생성 스크립트

WordPress 플러그인의 번역 파일(.pot)을 자동으로 생성합니다.
- PHP 파일에서 번역 가능한 문자열 추출
- JavaScript 파일에서 번역 가능한 문자열 추출
- .pot 파일 자동 생성 및 업데이트
"""

import os
import re
import subprocess
from pathlib import Path
from datetime import datetime
from typing import List, Dict, Set

class TranslationGenerator:
    def __init__(self, plugin_path: str):
        self.plugin_path = Path(plugin_path)
        self.languages_path = self.plugin_path / 'languages'
        self.pot_file = self.languages_path / 'acf-css-really-simple-style-management-center.pot'
        self.text_domain = 'acf-css-really-simple-style-management-center'
        
        # 번역 함수 패턴 (텍스트 도메인 포함)
        # __( '텍스트', 'domain' ) 또는 __( "텍스트", 'domain' ) 형식 지원
        self.php_patterns = [
            (r'__\(\s*(["\'])([^\'"]+)\1\s*,\s*["\']([^\'"]+)["\']', '__'),
            (r'_e\(\s*(["\'])([^\'"]+)\1\s*,\s*["\']([^\'"]+)["\']', '_e'),
            (r'esc_html__\(\s*(["\'])([^\'"]+)\1\s*,\s*["\']([^\'"]+)["\']', 'esc_html__'),
            (r'esc_attr__\(\s*(["\'])([^\'"]+)\1\s*,\s*["\']([^\'"]+)["\']', 'esc_attr__'),
            (r'esc_html_e\(\s*(["\'])([^\'"]+)\1\s*,\s*["\']([^\'"]+)["\']', 'esc_html_e'),
            (r'esc_attr_e\(\s*(["\'])([^\'"]+)\1\s*,\s*["\']([^\'"]+)["\']', 'esc_attr_e'),
            (r'_n\(\s*(["\'])([^\'"]+)\1\s*,\s*(["\'])([^\'"]+)\3\s*,\s*\d+\s*,\s*["\']([^\'"]+)["\']', '_n'),
            (r'_x\(\s*(["\'])([^\'"]+)\1\s*,\s*["\']([^\'"]+)["\']\s*,\s*["\']([^\'"]+)["\']', '_x'),
            (r'_nx\(\s*(["\'])([^\'"]+)\1\s*,\s*(["\'])([^\'"]+)\3\s*,\s*\d+\s*,\s*["\']([^\'"]+)["\']\s*,\s*["\']([^\'"]+)["\']', '_nx'),
        ]
        
        self.js_patterns = [
            (r'__\([\'"]([^\'"]+)[\'"]', '__'),
            (r'_e\([\'"]([^\'"]+)[\'"]', '_e'),
            (r'wp\.i18n\.__\([\'"]([^\'"]+)[\'"]', 'wp.i18n.__'),
            (r'wp\.i18n\._e\([\'"]([^\'"]+)[\'"]', 'wp.i18n._e'),
        ]
        
        self.translations: Dict[str, Dict] = {}
    
    def find_php_files(self) -> List[Path]:
        """PHP 파일 찾기"""
        php_files = []
        exclude_dirs = {'node_modules', '.git', 'vendor', 'tests', '__pycache__'}
        
        for root, dirs, files in os.walk(self.plugin_path):
            # 제외 디렉토리 필터링
            dirs[:] = [d for d in dirs if d not in exclude_dirs]
            
            for file in files:
                if file.endswith('.php'):
                    php_files.append(Path(root) / file)
        
        return php_files
    
    def find_js_files(self) -> List[Path]:
        """JavaScript 파일 찾기"""
        js_files = []
        exclude_dirs = {'node_modules', '.git', 'vendor', 'tests', '__pycache__'}
        
        for root, dirs, files in os.walk(self.plugin_path):
            # 제외 디렉토리 필터링
            dirs[:] = [d for d in dirs if d not in exclude_dirs]
            
            for file in files:
                if file.endswith('.js') and 'min' not in file.lower():
                    js_files.append(Path(root) / file)
        
        return js_files
    
    def extract_strings_from_file(self, file_path: Path, patterns: List[tuple]) -> List[Dict]:
        """파일에서 번역 가능한 문자열 추출"""
        strings = []
        
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
                lines = content.split('\n')
                
                for line_num, line in enumerate(lines, 1):
                    for pattern, func_name in patterns:
                        matches = re.finditer(pattern, line)
                        for match in matches:
                            # 그룹에서 실제 번역 문자열 추출 (일반적으로 두 번째 그룹)
                            if len(match.groups()) >= 2:
                                string = match.group(2)  # 두 번째 그룹이 번역 문자열
                            else:
                                string = match.group(1)  # 폴백
                            
                            # 이스케이프 문자 처리
                            string = string.replace('\\"', '"').replace("\\'", "'")
                            string = string.replace('\\n', '\n').replace('\\t', '\t')
                            
                            # 빈 문자열 제외
                            if string.strip():
                                strings.append({
                                    'string': string,
                                    'file': str(file_path.relative_to(self.plugin_path)),
                                    'line': line_num,
                                    'function': func_name,
                                })
        except Exception as e:
            print(f"Error reading {file_path}: {e}")
        
        return strings
    
    def extract_all_strings(self):
        """모든 파일에서 번역 가능한 문자열 추출"""
        print("🔍 PHP 파일에서 문자열 추출 중...")
        php_files = self.find_php_files()
        print(f"   발견된 PHP 파일: {len(php_files)}개")
        
        for php_file in php_files:
            strings = self.extract_strings_from_file(php_file, self.php_patterns)
            for s in strings:
                key = s['string']
                if key not in self.translations:
                    self.translations[key] = {
                        'string': key,
                        'references': [],
                    }
                self.translations[key]['references'].append({
                    'file': s['file'],
                    'line': s['line'],
                    'function': s['function'],
                })
        
        print("🔍 JavaScript 파일에서 문자열 추출 중...")
        js_files = self.find_js_files()
        print(f"   발견된 JavaScript 파일: {len(js_files)}개")
        
        for js_file in js_files:
            strings = self.extract_strings_from_file(js_file, self.js_patterns)
            for s in strings:
                key = s['string']
                if key not in self.translations:
                    self.translations[key] = {
                        'string': key,
                        'references': [],
                    }
                self.translations[key]['references'].append({
                    'file': s['file'],
                    'line': s['line'],
                    'function': s['function'],
                })
        
        print(f"✅ 총 {len(self.translations)}개의 고유한 번역 문자열 발견")
    
    def generate_pot_file(self):
        """POT 파일 생성"""
        if not self.languages_path.exists():
            self.languages_path.mkdir(parents=True)
        
        # 플러그인 메인 파일에서 버전 정보 추출
        main_file = self.plugin_path / 'acf-css-really-simple-style-guide.php'
        version = '8.5.0'
        if main_file.exists():
            with open(main_file, 'r', encoding='utf-8') as f:
                content = f.read()
                version_match = re.search(r'Version:\s*(\d+\.\d+\.\d+)', content)
                if version_match:
                    version = version_match.group(1)
        
        pot_content = []
        pot_content.append('# Copyright (C) 2026 3J Labs')
        pot_content.append('# This file is distributed under the GPLv2 or later.')
        pot_content.append('msgid ""')
        pot_content.append('msgstr ""')
        pot_content.append(f'"Project-Id-Version: ACF CSS Manager {version}\\n"')
        pot_content.append('"Report-Msgid-Bugs-To: https://3j-labs.com/support\\n"')
        pot_content.append(f'"POT-Creation-Date: {datetime.now().strftime("%Y-%m-%d %H:%M:%S%z")}\\n"')
        pot_content.append('"MIME-Version: 1.0\\n"')
        pot_content.append('"Content-Type: text/plain; charset=UTF-8\\n"')
        pot_content.append('"Content-Transfer-Encoding: 8bit\\n"')
        pot_content.append('"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n"')
        pot_content.append('"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n"')
        pot_content.append('"Language-Team: LANGUAGE <LL@li.org>\\n"')
        pot_content.append('"X-Generator: Translation Generator Script\\n"')
        pot_content.append(f'"X-Domain: {self.text_domain}\\n"')
        pot_content.append('')
        
        # 번역 문자열 정렬 (파일명, 라인 번호 순)
        sorted_strings = sorted(
            self.translations.items(),
            key=lambda x: (x[1]['references'][0]['file'], x[1]['references'][0]['line'])
        )
        
        for string, data in sorted_strings:
            # 참조 정보 추가
            for ref in data['references']:
                pot_content.append(f"#: {ref['file']}:{ref['line']}")
            
            # msgid 및 msgstr
            pot_content.append('msgid ' + self.escape_string(string))
            pot_content.append('msgstr ""')
            pot_content.append('')
        
        # 파일 쓰기
        with open(self.pot_file, 'w', encoding='utf-8') as f:
            f.write('\n'.join(pot_content))
        
        print(f"✅ POT 파일 생성 완료: {self.pot_file}")
        print(f"   총 {len(self.translations)}개의 번역 항목")
    
    def escape_string(self, s: str) -> str:
        """문자열을 POT 형식으로 이스케이프"""
        s = s.replace('\\', '\\\\')
        s = s.replace('"', '\\"')
        s = s.replace('\n', '\\n')
        s = s.replace('\t', '\\t')
        return f'"{s}"'
    
    def validate_translations(self):
        """번역 파일 검증"""
        print("\n🔍 번역 파일 검증 중...")
        
        issues = []
        
        # 중복 문자열 확인
        duplicates = {}
        for string, data in self.translations.items():
            if len(data['references']) > 1:
                duplicates[string] = len(data['references'])
        
        if duplicates:
            print(f"   ⚠️  중복 문자열 발견: {len(duplicates)}개")
            for string, count in list(duplicates.items())[:5]:
                print(f"      - \"{string[:50]}...\" ({count}회 사용)")
        
        # 빈 문자열 확인
        empty_strings = [s for s in self.translations.keys() if not s.strip()]
        if empty_strings:
            issues.append(f"빈 문자열 발견: {len(empty_strings)}개")
        
        # 특수 문자 확인
        special_chars = []
        for string in self.translations.keys():
            if re.search(r'[^\x20-\x7E\u00A0-\uFFFF]', string):
                special_chars.append(string)
        
        if special_chars:
            print(f"   ℹ️  특수 문자 포함 문자열: {len(special_chars)}개")
        
        print("✅ 검증 완료")
        return len(issues) == 0
    
    def run(self):
        """메인 실행 함수"""
        print("=" * 60)
        print("🌍 번역 파일 자동 생성 스크립트")
        print("=" * 60)
        print(f"플러그인 경로: {self.plugin_path}")
        print()
        
        # 문자열 추출
        self.extract_all_strings()
        print()
        
        # POT 파일 생성
        self.generate_pot_file()
        print()
        
        # 검증
        self.validate_translations()
        print()
        
        print("=" * 60)
        print("✅ 작업 완료!")
        print("=" * 60)


def main():
    # 플러그인 경로 설정
    script_dir = Path(__file__).parent
    plugin_path = script_dir / 'acf-css-really-simple-style-management-center-master'
    
    if not plugin_path.exists():
        print(f"❌ 플러그인 경로를 찾을 수 없습니다: {plugin_path}")
        return
    
    generator = TranslationGenerator(str(plugin_path))
    generator.run()


if __name__ == '__main__':
    main()
