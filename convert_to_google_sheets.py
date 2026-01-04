#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
CSV를 Google 스프레드시트로 변환하는 스크립트
"""

import csv
import json

def csv_to_google_sheets_json(csv_file, output_file='google_sheets_data.json'):
    """CSV를 Google 스프레드시트 JSON 형식으로 변환"""
    
    data = []
    with open(csv_file, 'r', encoding='utf-8-sig') as f:
        reader = csv.DictReader(f)
        for row in reader:
            data.append(row)
    
    # Google Sheets JSON 형식
    sheets_data = {
        'values': [
            list(data[0].keys())  # 헤더
        ]
    }
    
    # 데이터 행 추가
    for row in data:
        sheets_data['values'].append([
            row.get('name', ''),
            row.get('module_name', ''),
            row.get('category', ''),
            row.get('seo_factor', ''),
            row.get('estimated_weight', ''),
            row.get('impact', ''),
            row.get('explanation', ''),
            row.get('google_element', '')
        ])
    
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(sheets_data, f, ensure_ascii=False, indent=2)
    
    print(f"Google Sheets JSON 생성 완료: {output_file}")
    print(f"총 {len(data)}개 행 + 1개 헤더 = {len(sheets_data['values'])}개 행")
    
    return sheets_data

def generate_google_sheets_import_instructions():
    """Google 스프레드시트 가져오기 지침 생성"""
    
    instructions = """
# Google 스프레드시트로 데이터 가져오기

## 방법 1: CSV 파일 직접 가져오기

1. Google 스프레드시트 열기 (https://sheets.google.com)
2. 새 스프레드시트 생성
3. 파일 > 가져오기 > 업로드
4. `seo_factors_sample_data.csv` 파일 선택
5. 가져오기 설정:
   - 가져오기 위치: 새 시트로 바꾸기
   - 구분 기호: 쉼표
   - 문자 인코딩: UTF-8

## 방법 2: 수동 복사/붙여넣기

1. CSV 파일을 텍스트 에디터로 열기
2. 전체 내용 복사 (Ctrl+A, Ctrl+C)
3. Google 스프레드시트에서 A1 셀 선택
4. 붙여넣기 (Ctrl+V)
5. 데이터 > 텍스트를 열로 분할 > 쉼표

## 방법 3: Google Apps Script 사용

```javascript
function importCSV() {
  var file = DriveApp.getFilesByName('seo_factors_sample_data.csv').next();
  var csvData = file.getBlob().getDataAsString();
  var csvValues = Utilities.parseCsv(csvData);
  var sheet = SpreadsheetApp.getActiveSheet();
  sheet.getRange(1, 1, csvValues.length, csvValues[0].length).setValues(csvValues);
}
```

## 필터 설정

가져온 후 다음 필터를 설정하세요:

1. **카테고리별 필터**: 
   - 데이터 > 필터 만들기
   - Category 컬럼에서 필터링

2. **가중치별 정렬**:
   - Estimated Weight 컬럼 클릭
   - 데이터 > Z→A 정렬 (높은 가중치부터)

3. **Impact별 필터**:
   - Impact 컬럼에서 High/Medium/Low 필터링

## 권장 시트 구조

- **시트 1**: 전체 데이터 (필터 적용)
- **시트 2**: Tier 1 요소 (가중치 9-10)
- **시트 3**: Tier 2 요소 (가중치 8)
- **시트 4**: Tier 3 요소 (가중치 6-7)
- **시트 5**: 카테고리별 요약
"""
    
    with open('google_sheets_import_instructions.md', 'w', encoding='utf-8') as f:
        f.write(instructions)
    
    print("Google Sheets 가져오기 지침 생성 완료: google_sheets_import_instructions.md")

if __name__ == "__main__":
    csv_to_google_sheets_json('seo_factors_sample_data.csv')
    generate_google_sheets_import_instructions()
    print("\n✅ 모든 파일 생성 완료!")
    print("\n다음 단계:")
    print("1. seo_factors_sample_data.csv를 Google 스프레드시트로 가져오기")
    print("2. google_sheets_import_instructions.md 파일 참고")
