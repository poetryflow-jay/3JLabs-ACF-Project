#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Airtable 데이터 추출 스크립트
Selenium을 사용하여 동적 콘텐츠를 로드하고 데이터를 추출
"""

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
import json
import time
import re

def extract_airtable_data():
    """Airtable에서 데이터 추출"""
    
    url = "https://airtable.com/applEPk7fCvm7MghM/shrR2eOWItDCSW76O/tblRX4GHpE79ePSdI?viewControls=on"
    
    # Chrome 옵션 설정
    chrome_options = Options()
    chrome_options.add_argument('--headless')  # 헤드리스 모드
    chrome_options.add_argument('--no-sandbox')
    chrome_options.add_argument('--disable-dev-shm-usage')
    chrome_options.add_argument('--disable-gpu')
    chrome_options.add_argument('user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36')
    
    try:
        driver = webdriver.Chrome(options=chrome_options)
        driver.get(url)
        
        # 페이지 로드 대기
        print("페이지 로드 대기 중...")
        time.sleep(5)
        
        # JavaScript 실행하여 데이터 추출
        extract_script = """
        (function() {
            const data = [];
            
            // 모든 행 찾기
            const rows = document.querySelectorAll('[role="row"]');
            console.log('발견된 행 수:', rows.length);
            
            rows.forEach((row, rowIndex) => {
                const cells = row.querySelectorAll('[role="gridcell"], [role="cell"]');
                if (cells.length >= 4) {
                    const rowData = {};
                    cells.forEach((cell, cellIndex) => {
                        const text = (cell.innerText || cell.textContent || '').trim();
                        if (text && text.length > 0) {
                            rowData[`col${cellIndex}`] = text;
                        }
                    });
                    if (Object.keys(rowData).length > 0) {
                        data.push(rowData);
                    }
                }
            });
            
            // 전체 텍스트도 추출
            const allText = document.body.innerText || '';
            
            return JSON.stringify({
                rows: data,
                text: allText.substring(0, 10000),
                rowCount: rows.length
            });
        })();
        """
        
        result = driver.execute_script(extract_script)
        parsed_result = json.loads(result)
        
        driver.quit()
        
        return parsed_result
        
    except Exception as e:
        print(f"오류 발생: {e}")
        return None

if __name__ == "__main__":
    print("Airtable 데이터 추출 시작...")
    data = extract_airtable_data()
    
    if data:
        # JSON 파일로 저장
        with open('airtable_data.json', 'w', encoding='utf-8') as f:
            json.dump(data, f, ensure_ascii=False, indent=2)
        print(f"데이터 추출 완료! {len(data.get('rows', []))}개 행 발견")
        print(f"저장 위치: airtable_data.json")
    else:
        print("데이터 추출 실패")
