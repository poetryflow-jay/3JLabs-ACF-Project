#!/usr/bin/env python3
"""
Phase 8.1: 성능 최적화 분석 도구
파일 크기, 의존성, 중복 코드 분석
"""
import os
import re
from pathlib import Path
from collections import defaultdict

BASE_DIR = Path('acf-css-really-simple-style-management-center-master')
ASSETS_DIR = BASE_DIR / 'assets'

def analyze_js_files():
    """JavaScript 파일 분석"""
    js_files = {}
    total_size = 0
    
    for js_file in (ASSETS_DIR / 'js').glob('*.js'):
        size = js_file.stat().st_size
        total_size += size
        
        with open(js_file, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
            
        # 간단한 분석
        lines = len(content.splitlines())
        functions = len(re.findall(r'function\s+\w+', content))
        jquery_uses = content.count('$(')
        
        js_files[js_file.name] = {
            'size': size,
            'size_kb': round(size / 1024, 2),
            'lines': lines,
            'functions': functions,
            'jquery_uses': jquery_uses,
        }
    
    return js_files, total_size

def analyze_css_files():
    """CSS 파일 분석"""
    css_files = {}
    total_size = 0
    
    for css_file in (ASSETS_DIR / 'css').glob('*.css'):
        size = css_file.stat().st_size
        total_size += size
        
        with open(css_file, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        lines = len(content.splitlines())
        selectors = len(re.findall(r'[^{}]+{', content))
        
        css_files[css_file.name] = {
            'size': size,
            'size_kb': round(size / 1024, 2),
            'lines': lines,
            'selectors': selectors,
        }
    
    return css_files, total_size

def find_duplicate_code():
    """중복 코드 패턴 찾기 (간단한 버전)"""
    js_dir = ASSETS_DIR / 'js'
    patterns = defaultdict(list)
    
    # 공통 패턴 찾기
    common_patterns = [
        r'jQuery\(document\)\.ready',
        r'\$\(document\)\.ready',
        r'wp\.ajax\.url',
        r'admin-ajax\.php',
        r'wp_create_nonce',
    ]
    
    for js_file in js_dir.glob('*.js'):
        with open(js_file, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
            for pattern in common_patterns:
                if re.search(pattern, content):
                    patterns[pattern].append(js_file.name)
    
    return patterns

def analyze_dependencies():
    """의존성 분석"""
    js_dir = ASSETS_DIR / 'js'
    dependencies = defaultdict(set)
    
    # 간단한 require/import 패턴 찾기
    for js_file in js_dir.glob('*.js'):
        with open(js_file, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
            
        # jQuery 사용 확인
        if 'jQuery' in content or '$(' in content:
            dependencies[js_file.name].add('jquery')
        
        # WordPress 의존성
        if 'wp.' in content or 'wp_ajax' in content:
            dependencies[js_file.name].add('wp-api')
    
    return dependencies

def main():
    print("=" * 60)
    print("Phase 8.1: 성능 최적화 분석")
    print("=" * 60)
    
    # JavaScript 분석
    print("\n[1] JavaScript 파일 분석")
    print("-" * 60)
    js_files, js_total = analyze_js_files()
    
    print(f"총 파일 수: {len(js_files)}")
    print(f"총 크기: {round(js_total / 1024, 2)} KB ({round(js_total / 1024 / 1024, 2)} MB)")
    print("\n파일별 상세:")
    for name, info in sorted(js_files.items(), key=lambda x: x[1]['size'], reverse=True):
        print(f"  {name:35} {info['size_kb']:6.2f} KB  {info['lines']:5} lines  {info['functions']:3} functions")
    
    # CSS 분석
    print("\n[2] CSS 파일 분석")
    print("-" * 60)
    css_files, css_total = analyze_css_files()
    
    print(f"총 파일 수: {len(css_files)}")
    print(f"총 크기: {round(css_total / 1024, 2)} KB ({round(css_total / 1024 / 1024, 2)} MB)")
    print("\n파일별 상세:")
    for name, info in sorted(css_files.items(), key=lambda x: x[1]['size'], reverse=True):
        print(f"  {name:35} {info['size_kb']:6.2f} KB  {info['lines']:5} lines  {info['selectors']:4} selectors")
    
    # 중복 패턴
    print("\n[3] 공통 패턴 분석")
    print("-" * 60)
    patterns = find_duplicate_code()
    for pattern, files in patterns.items():
        if len(files) > 1:
            print(f"  패턴: {pattern[:50]}")
            print(f"    사용 파일: {len(files)}개 - {', '.join(files[:5])}")
    
    # 의존성
    print("\n[4] 의존성 분석")
    print("-" * 60)
    deps = analyze_dependencies()
    jquery_files = [f for f, d in deps.items() if 'jquery' in d]
    print(f"jQuery 의존 파일: {len(jquery_files)}개")
    
    # 최적화 제안
    print("\n[5] 최적화 제안")
    print("-" * 60)
    print("✓ JavaScript 번들링 필요")
    print("✓ CSS 최적화 및 미사용 코드 제거")
    print("✓ 공통 코드 통합 필요")
    print("✓ 지연 로딩 (Lazy Loading) 적용")
    
    print("\n" + "=" * 60)

if __name__ == '__main__':
    main()
