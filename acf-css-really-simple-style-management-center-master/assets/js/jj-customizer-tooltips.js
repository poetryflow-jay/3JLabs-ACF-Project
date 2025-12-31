/**
 * J&J Customizer Tooltips
 * 
 * 컬러, 폰트, 버튼 설정에 적용 위치 정보를 알려주는 툴팁 시스템
 * 
 * @since v3.8.0
 */
(function( $ ) {
    'use strict';

    $( function() {
        
        // Customizer가 로드된 후 실행
        wp.customize.bind( 'ready', function() {
            
            // 모든 컨트롤에 툴팁 추가
            wp.customize.control.each( function( control ) {
                
                // 툴팁 데이터가 있는 경우에만 툴팁 추가
                if ( control.params && control.params.tooltip ) {
                    addTooltipToControl( control );
                } else if ( control.extended( wp.customize.Control ) && control.params && control.params.description ) {
                    // description이 있으면 툴팁으로 사용
                    addTooltipToControl( control, control.params.description );
                }
            } );
            
            // 새 컨트롤이 추가될 때도 툴팁 추가
            wp.customize.control.bind( 'add', function( control ) {
                if ( control.params && control.params.tooltip ) {
                    addTooltipToControl( control );
                } else if ( control.params && control.params.description ) {
                    addTooltipToControl( control, control.params.description );
                }
            } );
        } );
        
        /**
         * 컨트롤에 툴팁 추가
         */
        function addTooltipToControl( control, tooltipText ) {
            tooltipText = tooltipText || ( control.params && control.params.tooltip ) || '';
            
            if ( ! tooltipText ) {
                return;
            }
            
            // 컨트롤이 렌더링될 때 툴팁 추가
            control.deferred.embedded.done( function() {
                var $control = $( control.container );
                var $label = $control.find( 'label, .customize-control-title' );
                
                if ( $label.length === 0 ) {
                    return;
                }
                
                // 툴팁 아이콘 추가
                if ( ! $label.find( '.jj-tooltip-icon' ).length ) {
                    var $icon = $( '<span class="dashicons dashicons-editor-help jj-tooltip-icon" title="' + escapeHtml( tooltipText ) + '"></span>' );
                    $label.append( $icon );
                    
                    // 툴팁 표시 이벤트
                    $icon.on( 'mouseenter', function( e ) {
                        showTooltip( $( this ), tooltipText, e );
                    } );
                    
                    $icon.on( 'mouseleave', function() {
                        hideTooltip();
                    } );
                }
            } );
        }
        
        /**
         * 툴팁 표시
         */
        function showTooltip( $trigger, text, event ) {
            // 기존 툴팁 제거
            $( '.jj-tooltip' ).remove();
            
            var $tooltip = $( '<div class="jj-tooltip">' + escapeHtml( text ) + '</div>' );
            $( 'body' ).append( $tooltip );
            
            // 위치 계산
            var offset = $trigger.offset();
            var tooltipWidth = $tooltip.outerWidth();
            var tooltipHeight = $tooltip.outerHeight();
            var windowWidth = $( window ).width();
            var windowHeight = $( window ).height();
            var scrollTop = $( window ).scrollTop();
            
            var left = offset.left + $trigger.outerWidth() + 10;
            var top = offset.top - scrollTop;
            
            // 화면 밖으로 나가지 않도록 조정
            if ( left + tooltipWidth > windowWidth ) {
                left = offset.left - tooltipWidth - 10;
            }
            
            if ( top + tooltipHeight > windowHeight ) {
                top = windowHeight - tooltipHeight - 10;
            }
            
            $tooltip.css( {
                left: left + 'px',
                top: top + 'px',
            } );
            
            // 애니메이션
            setTimeout( function() {
                $tooltip.addClass( 'jj-tooltip-visible' );
            }, 10 );
        }
        
        /**
         * 툴팁 숨기기
         */
        function hideTooltip() {
            $( '.jj-tooltip' ).removeClass( 'jj-tooltip-visible' );
            setTimeout( function() {
                $( '.jj-tooltip' ).remove();
            }, 200 );
        }
        
        /**
         * HTML 이스케이프
         */
        function escapeHtml( text ) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace( /[&<>"']/g, function( m ) { return map[m]; } );
        }
    } );

})( jQuery );
