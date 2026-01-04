// Airtable 데이터 추출 스크립트
// 브라우저 콘솔에서 실행

(function() {
    console.log('Airtable 데이터 추출 시작...');
    
    // Airtable의 데이터는 보통 window 객체나 DOM에 저장되어 있음
    // 여러 방법으로 시도
    
    let data = null;
    
    // 방법 1: window 객체에서 데이터 찾기
    if (window.__INITIAL_STATE__) {
        data = window.__INITIAL_STATE__;
        console.log('방법 1: __INITIAL_STATE__ 발견');
    }
    
    // 방법 2: Airtable의 내부 상태 찾기
    if (window.airtable) {
        data = window.airtable;
        console.log('방법 2: window.airtable 발견');
    }
    
    // 방법 3: DOM에서 데이터 추출
    const cells = document.querySelectorAll('[role="gridcell"], [data-testid*="cell"], .cell');
    if (cells.length > 0) {
        console.log(`방법 3: ${cells.length}개의 셀 발견`);
        
        const rows = [];
        let currentRow = {};
        let currentCol = 0;
        const columns = ['Name', 'ModuleName', 'Impact', 'Weight', 'SEO Factor', 'Explanation'];
        
        cells.forEach((cell, index) => {
            const text = cell.innerText || cell.textContent || '';
            if (text.trim()) {
                const colIndex = index % columns.length;
                currentRow[columns[colIndex]] = text.trim();
                
                if (colIndex === columns.length - 1) {
                    rows.push({...currentRow});
                    currentRow = {};
                }
            }
        });
        
        if (rows.length > 0) {
            data = rows;
            console.log(`추출된 행 수: ${rows.length}`);
        }
    }
    
    // 방법 4: 테이블 구조에서 직접 추출
    const tableRows = document.querySelectorAll('tr, [role="row"]');
    if (tableRows.length > 0) {
        console.log(`방법 4: ${tableRows.length}개의 행 발견`);
        
        const extractedRows = [];
        tableRows.forEach((row, rowIndex) => {
            const cells = row.querySelectorAll('td, th, [role="gridcell"], [role="cell"]');
            if (cells.length >= 4) {
                const rowData = {};
                cells.forEach((cell, cellIndex) => {
                    const text = cell.innerText || cell.textContent || '';
                    if (text.trim()) {
                        rowData[`col${cellIndex}`] = text.trim();
                    }
                });
                if (Object.keys(rowData).length > 0) {
                    extractedRows.push(rowData);
                }
            }
        });
        
        if (extractedRows.length > 0) {
            data = extractedRows;
            console.log(`추출된 행 수: ${extractedRows.length}`);
        }
    }
    
    // 방법 5: 모든 텍스트 노드에서 패턴 찾기
    const allText = document.body.innerText || document.body.textContent || '';
    console.log('전체 텍스트 길이:', allText.length);
    
    // 결과 출력
    if (data) {
        console.log('추출된 데이터:', JSON.stringify(data, null, 2));
        return data;
    } else {
        console.log('데이터를 찾을 수 없습니다. DOM 구조 확인 중...');
        
        // DOM 구조 정보 출력
        const info = {
            bodyTextLength: document.body.innerText.length,
            cells: document.querySelectorAll('[role="gridcell"]').length,
            rows: document.querySelectorAll('[role="row"]').length,
            tables: document.querySelectorAll('table').length,
            divs: document.querySelectorAll('div').length
        };
        console.log('DOM 정보:', info);
        
        // 모든 div의 클래스와 데이터 속성 확인
        const divs = Array.from(document.querySelectorAll('div')).slice(0, 50);
        divs.forEach((div, i) => {
            if (div.className || div.getAttribute('data-testid')) {
                console.log(`Div ${i}:`, {
                    className: div.className,
                    dataTestId: div.getAttribute('data-testid'),
                    text: (div.innerText || '').substring(0, 50)
                });
            }
        });
        
        return null;
    }
})();
