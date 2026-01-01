#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
[Phase 9.1] 번역 파일 자동 업데이트 시스템

번역 파일을 자동으로 업데이트하고 동기화합니다.
- POT 파일 재생성
- PO 파일 업데이트
- 번역 누락 확인 및 알림
"""

import os
import subprocess
from pathlib import Path
from datetime import datetime

class TranslationUpdater:
    def __init__(self, plugin_path: str):
        self.plugin_path = Path(plugin_path)
        self.languages_path = self.plugin_path / 'languages'
        self.pot_file = self.languages_path / 'acf-css-really-simple-style-management-center.pot'
        
    def run_generate_translations(self):
        """번역 파일 생성 스크립트 실행"""
        print("📝 POT 파일 재생성 중...")
        script_path = self.plugin_path.parent / 'generate_translations.py'
        
        if not script_path.exists():
            print(f"❌ 번역 생성 스크립트를 찾을 수 없습니다: {script_path}")
            return False
        
        try:
            result = subprocess.run(
                ['python', str(script_path)],
                cwd=str(self.plugin_path.parent),
                capture_output=True,
                text=True,
                encoding='utf-8'
            )
            
            if result.returncode == 0:
                print("✅ POT 파일 재생성 완료")
                print(result.stdout)
                return True
            else:
                print(f"❌ POT 파일 재생성 실패:")
                print(result.stderr)
                return False
        except Exception as e:
            print(f"❌ 오류 발생: {e}")
            return False
    
    def update_po_files(self):
        """PO 파일 업데이트 (msgmerge)"""
        print("\n🔄 PO 파일 업데이트 중...")
        
        if not self.pot_file.exists():
            print(f"❌ POT 파일을 찾을 수 없습니다: {self.pot_file}")
            return False
        
        po_files = list(self.languages_path.glob('*.po'))
        
        if not po_files:
            print("ℹ️  PO 파일이 없습니다. 새로 생성해야 합니다.")
            return True
        
        updated_count = 0
        for po_file in po_files:
            print(f"   업데이트 중: {po_file.name}")
            try:
                # msgmerge 실행 (gettext 도구 필요)
                result = subprocess.run(
                    ['msgmerge', '--update', '--backup=numbered', str(po_file), str(self.pot_file)],
                    capture_output=True,
                    text=True,
                    encoding='utf-8'
                )
                
                if result.returncode == 0:
                    updated_count += 1
                    print(f"      ✅ 완료")
                else:
                    print(f"      ⚠️  경고: {result.stderr}")
            except FileNotFoundError:
                print(f"      ⚠️  msgmerge를 찾을 수 없습니다. gettext 도구를 설치하세요.")
                print(f"      (Windows: https://mlocati.github.io/articles/gettext-iconv-windows.html)")
                print(f"      (macOS: brew install gettext)")
                print(f"      (Linux: apt-get install gettext)")
            except Exception as e:
                print(f"      ❌ 오류: {e}")
        
        print(f"\n✅ {updated_count}/{len(po_files)}개 PO 파일 업데이트 완료")
        return True
    
    def check_missing_translations(self):
        """번역 누락 확인"""
        print("\n🔍 번역 누락 확인 중...")
        
        script_path = self.plugin_path.parent / 'validate_translations.py'
        
        if not script_path.exists():
            print(f"⚠️  검증 스크립트를 찾을 수 없습니다: {script_path}")
            return
        
        try:
            result = subprocess.run(
                ['python', str(script_path)],
                cwd=str(self.plugin_path.parent),
                capture_output=True,
                text=True,
                encoding='utf-8'
            )
            
            print(result.stdout)
            if result.stderr:
                print(result.stderr)
        except Exception as e:
            print(f"❌ 오류 발생: {e}")
    
    def generate_report(self):
        """업데이트 보고서 생성"""
        print("\n" + "=" * 60)
        print("📊 번역 파일 업데이트 보고서")
        print("=" * 60)
        print(f"작업 시간: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print(f"POT 파일: {self.pot_file}")
        print(f"PO 파일 경로: {self.languages_path}")
        
        if self.pot_file.exists():
            pot_size = self.pot_file.stat().st_size
            print(f"POT 파일 크기: {pot_size:,} bytes")
        
        po_files = list(self.languages_path.glob('*.po'))
        print(f"PO 파일 수: {len(po_files)}개")
        
        for po_file in po_files:
            po_size = po_file.stat().st_size
            print(f"  - {po_file.name}: {po_size:,} bytes")
        
        print("=" * 60)
    
    def run(self):
        """메인 실행 함수"""
        print("=" * 60)
        print("🌍 번역 파일 자동 업데이트 시스템")
        print("=" * 60)
        print()
        
        # 1. POT 파일 재생성
        if not self.run_generate_translations():
            print("\n❌ POT 파일 재생성 실패. 작업을 중단합니다.")
            return
        
        # 2. PO 파일 업데이트
        self.update_po_files()
        
        # 3. 번역 누락 확인
        self.check_missing_translations()
        
        # 4. 보고서 생성
        self.generate_report()
        
        print("\n✅ 모든 작업 완료!")
        print("=" * 60)


def main():
    # 플러그인 경로 설정
    script_dir = Path(__file__).parent
    plugin_path = script_dir / 'acf-css-really-simple-style-management-center-master'
    
    if not plugin_path.exists():
        print(f"❌ 플러그인 경로를 찾을 수 없습니다: {plugin_path}")
        return
    
    updater = TranslationUpdater(str(plugin_path))
    updater.run()


if __name__ == '__main__':
    main()
