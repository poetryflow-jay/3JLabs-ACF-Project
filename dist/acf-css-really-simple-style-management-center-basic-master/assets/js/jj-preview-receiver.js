/**
 * J&J Preview Page Receiver - v5.0.3
 * 
 * 프리뷰 페이지에서 실시간 CSS 업데이트를 수신하는 스크립트
 * - postMessage로 CSS 변수 수신
 * - 새로고침 없이 실시간 스타일 적용
 * 
 * @since 5.0.3
 * @version 5.0.3
 */
(function() {
    'use strict';

    /**
     * 프리뷰 수신기
     */
    const PreviewReceiver = {
        // CSS 스타일 요소
        styleElement: null,
        
        /**
         * 초기화
         */
        init: function() {
            // CSS 스타일 요소 생성
            this.createStyleElement();
            
            // postMessage 리스너 등록
            this.setupMessageListener();
            
            // 부모 창에 준비 완료 신호 전송
            this.notifyReady();
        },
        
        /**
         * CSS 스타일 요소 생성
         */
        createStyleElement: function() {
            // 기존 스타일 요소 확인
            this.styleElement = document.getElementById('jj-live-preview-css');
            
            if (!this.styleElement) {
                // 새 스타일 요소 생성
                this.styleElement = document.createElement('style');
                this.styleElement.id = 'jj-live-preview-css';
                document.head.appendChild(this.styleElement);
            }
        },
        
        /**
         * postMessage 리스너 설정
         */
        setupMessageListener: function() {
            const self = this;
            
            window.addEventListener('message', function(e) {
                // 보안: 같은 도메인에서만 메시지 수신
                // 참고: 개발 환경에서는 '*' 허용 (프로덕션에서는 더 엄격하게)
                if (e.origin !== window.location.origin && 
                    !window.location.hostname.includes('localhost') && 
                    !window.location.hostname.includes('127.0.0.1')) {
                    return;
                }
                
                // CSS 업데이트 메시지 처리
                if (e.data && e.data.type === 'jj-preview-css-update') {
                    self.updateCSS(e.data.css, e.data.cssVars);
                }
                
                // 뷰포트 변경 메시지 처리
                if (e.data && e.data.type === 'jj-preview-viewport-change') {
                    self.changeViewport(e.data.viewport);
                }
            });
        },
        
        /**
         * CSS 업데이트
         */
        updateCSS: function(cssText, cssVars) {
            if (!this.styleElement) {
                this.createStyleElement();
            }
            
            // CSS 텍스트 업데이트
            if (cssText) {
                this.styleElement.textContent = cssText;
            } else if (cssVars) {
                // CSS 변수만 업데이트
                let css = ':root {\n';
                for (const key in cssVars) {
                    if (cssVars[key]) {
                        css += '  ' + key + ': ' + cssVars[key] + ';\n';
                    }
                }
                css += '}';
                this.styleElement.textContent = css;
            }
            
            // 애니메이션 효과 (선택적)
            document.body.style.transition = 'all 0.3s ease';
            setTimeout(function() {
                document.body.style.transition = '';
            }, 300);
        },
        
        /**
         * 뷰포트 변경
         */
        changeViewport: function(viewport) {
            const viewportSizes = {
                desktop: { width: 1200 },
                tablet: { width: 768 },
                mobile: { width: 375 }
            };
            
            const size = viewportSizes[viewport];
            if (!size) {
                return;
            }
            
            // viewport 메타 태그 업데이트
            let viewportMeta = document.querySelector('meta[name="viewport"]');
            if (viewportMeta) {
                viewportMeta.content = 'width=' + size.width + ', initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
            } else {
                viewportMeta = document.createElement('meta');
                viewportMeta.name = 'viewport';
                viewportMeta.content = 'width=' + size.width + ', initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
                document.head.appendChild(viewportMeta);
            }
            
            // body 최대 너비 조정
            let style = document.getElementById('jj-preview-viewport-style');
            if (!style) {
                style = document.createElement('style');
                style.id = 'jj-preview-viewport-style';
                document.head.appendChild(style);
            }
            style.textContent = 'body { max-width: ' + size.width + 'px; margin: 0 auto; box-shadow: 0 0 20px rgba(0,0,0,0.3); }';
        },
        
        /**
         * 부모 창에 준비 완료 신호 전송
         */
        notifyReady: function() {
            // 부모 창이 있으면 준비 완료 신호 전송
            if (window.opener || window.parent !== window) {
                const message = {
                    type: 'jj-preview-ready',
                    timestamp: Date.now()
                };
                
                try {
                    if (window.opener) {
                        window.opener.postMessage(message, '*');
                    } else if (window.parent) {
                        window.parent.postMessage(message, '*');
                    }
                } catch (e) {
                    console.warn('준비 완료 신호 전송 실패:', e);
                }
            }
        }
    };
    
    // DOM 준비 시 초기화
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            PreviewReceiver.init();
        });
    } else {
        PreviewReceiver.init();
    }
    
    // 전역으로 노출
    window.JJPreviewReceiver = PreviewReceiver;
    
})();

