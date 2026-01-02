#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
[Phase 9.1] 번역 파일 검증 도구

번역 파일의 품질을 검증하고 문제점을 보고합니다.
- 번역 누락 확인
- 번역 품질 검사
- 일관성 검증
"""

import os
import re
from pathlib import Path
from typing import List, Dict, Set
from collections import defaultdict

class TranslationValidator:
    def __init__(self, plugin_path: str):
        self.plugin_path = Path(plugin_path)
        self.languages_path = self.plugin_path / 'languages'
        self.pot_file = self.languages_path / 'acf-css-really-simple-style-management-center.pot'
        self.text_domain = 'acf-css-really-simple-style-management-center'
        
        self.issues = []
        self.warnings = []
        self.info = []
    
    def read_pot_file(self) -> Dict[str, Dict]:
        """POT 파일 읽기"""
        translations = {}
        
        if not self.pot_file.exists():
            self.issues.append(f"POT 파일을 찾을 수 없습니다: {self.pot_file}")
            return translations
        
        with open(self.pot_file, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # msgid 추출
        msgid_pattern = r'msgid\s+"([^"]+)"'
        msgid_matches = re.finditer(msgid_pattern, content, re.MULTILINE)
        
        for match in msgid_matches:
            msgid = match.group(1)
            # 이스케이프 문자 처리
            msgid = msgid.replace('\\n', '\n').replace('\\t', '\t').replace('\\"', '"')
            if msgid.strip():
                translations[msgid] = {
                    'references': [],
                    'has_translation': False,
                }
        
        # 참조 정보 추출
        reference_pattern = r'#:\s*([^:]+):(\d+)'
        current_msgid = None
        
        for line in content.split('\n'):
            if line.startswith('#:'):
                ref_match = re.match(reference_pattern, line)
                if ref_match and current_msgid:
                    file_path = ref_match.group(1)
                    line_num = int(ref_match.group(2))
                    translations[current_msgid]['references'].append({
                        'file': file_path,
                        'line': line_num,
                    })
            elif line.startswith('msgid '):
                msgid_match = re.match(r'msgid\s+"([^"]+)"', line)
                if msgid_match:
                    msgid = msgid_match.group(1)
                    msgid = msgid.replace('\\n', '\n').replace('\\t', '\t').replace('\\"', '"')
                    current_msgid = msgid if msgid in translations else None
        
        return translations
    
    def find_po_files(self) -> List[Path]:
        """PO 파일 찾기"""
        po_files = []
        if self.languages_path.exists():
            for file in self.languages_path.glob('*.po'):
                po_files.append(file)
        return po_files
    
    def read_po_file(self, po_file: Path) -> Dict[str, str]:
        """PO 파일 읽기"""
        translations = {}
        
        with open(po_file, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # msgid와 msgstr 쌍 추출
        msgid_pattern = r'msgid\s+"([^"]+)"'
        msgstr_pattern = r'msgstr\s+"([^"]+)"'
        
        lines = content.split('\n')
        current_msgid = None
        current_msgstr = None
        
        for line in lines:
            if line.startswith('msgid '):
                match = re.match(msgid_pattern, line)
                if match:
                    current_msgid = match.group(1)
                    current_msgid = current_msgid.replace('\\n', '\n').replace('\\t', '\t').replace('\\"', '"')
            elif line.startswith('msgstr '):
                match = re.match(msgstr_pattern, line)
                if match:
                    current_msgstr = match.group(1)
                    current_msgstr = current_msgstr.replace('\\n', '\n').replace('\\t', '\t').replace('\\"', '"')
                    if current_msgid and current_msgstr.strip():
                        translations[current_msgid] = current_msgstr
                    current_msgid = None
                    current_msgstr = None
        
        return translations
    
    def check_missing_translations(self, pot_translations: Dict, po_translations: Dict, lang: str):
        """번역 누락 확인"""
        missing = []
        for msgid in pot_translations.keys():
            if msgid not in po_translations or not po_translations[msgid].strip():
                missing.append(msgid)
        
        if missing:
            self.warnings.append(f"[{lang}] 번역 누락: {len(missing)}개")
            if len(missing) <= 10:
                for msgid in missing[:10]:
                    self.warnings.append(f"  - {msgid[:60]}...")
        else:
            self.info.append(f"[{lang}] 모든 번역 완료")
    
    def check_duplicate_strings(self, pot_translations: Dict):
        """중복 문자열 확인"""
        # 같은 문자열이 여러 곳에서 사용되는지 확인
        duplicates = {}
        for msgid, data in pot_translations.items():
            if len(data['references']) > 1:
                duplicates[msgid] = len(data['references'])
        
        if duplicates:
            self.info.append(f"중복 문자열: {len(duplicates)}개 (이는 정상입니다)")
            # 상위 5개만 표시
            sorted_dups = sorted(duplicates.items(), key=lambda x: x[1], reverse=True)[:5]
            for msgid, count in sorted_dups:
                self.info.append(f"  - \"{msgid[:50]}...\" ({count}회 사용)")
    
    def check_empty_strings(self, pot_translations: Dict):
        """빈 문자열 확인"""
        empty = [msgid for msgid in pot_translations.keys() if not msgid.strip()]
        if empty:
            self.issues.append(f"빈 문자열 발견: {len(empty)}개")
    
    def check_special_characters(self, pot_translations: Dict):
        """특수 문자 확인"""
        special = []
        for msgid in pot_translations.keys():
            # 제어 문자나 특수 유니코드 확인
            if re.search(r'[\x00-\x08\x0B-\x1F\x7F]', msgid):
                special.append(msgid)
        
        if special:
            self.info.append(f"특수 문자 포함 문자열: {len(special)}개")
    
    def check_translation_quality(self, pot_translations: Dict, po_translations: Dict, lang: str):
        """번역 품질 검사"""
        # 1. 번역이 원문과 동일한지 확인 (영어의 경우)
        if lang == 'en_US':
            identical = []
            for msgid, msgstr in po_translations.items():
                if msgid == msgstr:
                    identical.append(msgid)
            
            if identical:
                self.info.append(f"[{lang}] 원문과 동일한 번역: {len(identical)}개 (정상)")
        
        # 2. 번역 길이 확인 (너무 짧거나 긴 번역)
        length_issues = []
        for msgid, msgstr in po_translations.items():
            if msgstr:
                # 원문 대비 3배 이상 길거나 1/3 이하인 경우
                if len(msgstr) > len(msgid) * 3 or (len(msgid) > 10 and len(msgstr) < len(msgid) / 3):
                    length_issues.append((msgid[:50], len(msgid), len(msgstr)))
        
        if length_issues:
            self.warnings.append(f"[{lang}] 번역 길이 이상: {len(length_issues)}개")
            for msgid, orig_len, trans_len in length_issues[:5]:
                self.warnings.append(f"  - \"{msgid}...\" ({orig_len} → {trans_len}자)")
    
    def generate_report(self):
        """검증 보고서 생성"""
        print("=" * 60)
        print("🔍 번역 파일 검증 보고서")
        print("=" * 60)
        print()
        
        # POT 파일 읽기
        print("📄 POT 파일 분석 중...")
        pot_translations = self.read_pot_file()
        print(f"   총 번역 항목: {len(pot_translations)}개")
        print()
        
        if not pot_translations:
            print("❌ POT 파일이 비어있거나 읽을 수 없습니다.")
            return
        
        # 기본 검증
        print("🔍 기본 검증 중...")
        self.check_duplicate_strings(pot_translations)
        self.check_empty_strings(pot_translations)
        self.check_special_characters(pot_translations)
        print()
        
        # PO 파일 검증
        print("📄 PO 파일 검증 중...")
        po_files = self.find_po_files()
        
        if not po_files:
            self.warnings.append("PO 파일을 찾을 수 없습니다.")
        else:
            for po_file in po_files:
                lang = po_file.stem.replace('acf-css-really-simple-style-management-center-', '')
                print(f"   [{lang}] 검증 중...")
                
                po_translations = self.read_po_file(po_file)
                print(f"      번역된 항목: {len(po_translations)}개")
                
                self.check_missing_translations(pot_translations, po_translations, lang)
                self.check_translation_quality(pot_translations, po_translations, lang)
                print()
        
        # 보고서 출력
        print("=" * 60)
        print("📊 검증 결과 요약")
        print("=" * 60)
        
        if self.issues:
            print(f"\n❌ 문제점 ({len(self.issues)}개):")
            for issue in self.issues:
                print(f"   - {issue}")
        
        if self.warnings:
            print(f"\n⚠️  경고 ({len(self.warnings)}개):")
            for warning in self.warnings[:20]:  # 최대 20개만 표시
                print(f"   - {warning}")
            if len(self.warnings) > 20:
                print(f"   ... 외 {len(self.warnings) - 20}개")
        
        if self.info:
            print(f"\nℹ️  정보 ({len(self.info)}개):")
            for info in self.info[:10]:  # 최대 10개만 표시
                print(f"   - {info}")
            if len(self.info) > 10:
                print(f"   ... 외 {len(self.info) - 10}개")
        
        if not self.issues and not self.warnings:
            print("\n✅ 모든 검증 통과!")
        
        print()
        print("=" * 60)


def main():
    # 플러그인 경로 설정
    script_dir = Path(__file__).parent
    plugin_path = script_dir / 'acf-css-really-simple-style-management-center-master'
    
    if not plugin_path.exists():
        print(f"❌ 플러그인 경로를 찾을 수 없습니다: {plugin_path}")
        return
    
    validator = TranslationValidator(str(plugin_path))
    validator.generate_report()


if __name__ == '__main__':
    main()
