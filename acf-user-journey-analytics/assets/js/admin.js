/**
 * ACF User Journey Analytics - Admin Scripts
 */

(function($) {
    'use strict';

    // 실시간 데이터 업데이트
    function updateRealtimeData() {
        $.ajax({
            url: acf_uja_admin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'acf_uja_realtime_data',
                nonce: acf_uja_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    var data = response.data;

                    // 실시간 사용자 수 업데이트
                    $('.realtime-item strong').first().text(data.realtime_users.toLocaleString());
                }
            }
        });
    }

    // 30초마다 실시간 데이터 업데이트
    if ($('.acf-uja-realtime-bar').length) {
        setInterval(updateRealtimeData, 30000);
    }

    // 프로모션 배너 닫기
    $('.promo-dismiss').on('click', function() {
        $(this).closest('.acf-uja-promo-banner').slideUp(300);
        localStorage.setItem('acf_uja_promo_dismissed', '1');
    });

    // 로컬 스토리지에서 프로모션 상태 확인
    if (localStorage.getItem('acf_uja_promo_dismissed') === '1') {
        $('.acf-uja-promo-banner').hide();
    }

    // 날짜 범위 선택기 향상
    $('.acf-uja-period-selector a').on('click', function(e) {
        // 페이지 로딩 인디케이터 표시
        $('body').css('cursor', 'wait');
    });

    // 테이블 정렬 (선택적)
    $('.acf-uja-section table th').css('cursor', 'pointer').on('click', function() {
        var table = $(this).closest('table');
        var index = $(this).index();
        var rows = table.find('tbody tr').toArray();
        var asc = $(this).hasClass('sorted-asc');

        rows.sort(function(a, b) {
            var aVal = $(a).find('td').eq(index).text();
            var bVal = $(b).find('td').eq(index).text();

            // 숫자 비교
            var aNum = parseFloat(aVal.replace(/[^0-9.-]/g, ''));
            var bNum = parseFloat(bVal.replace(/[^0-9.-]/g, ''));

            if (!isNaN(aNum) && !isNaN(bNum)) {
                return asc ? aNum - bNum : bNum - aNum;
            }

            // 문자열 비교
            return asc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
        });

        table.find('th').removeClass('sorted-asc sorted-desc');
        $(this).addClass(asc ? 'sorted-desc' : 'sorted-asc');

        table.find('tbody').empty().append(rows);
    });

    // 숫자 애니메이션
    function animateNumber(element, target) {
        var current = 0;
        var increment = target / 30;
        var timer = setInterval(function() {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.text(Math.floor(current).toLocaleString());
        }, 30);
    }

    // 카드 숫자 애니메이션 (페이지 로드 시)
    $('.summary-card .card-content h3').each(function() {
        var $this = $(this);
        var text = $this.text();
        var num = parseFloat(text.replace(/[^0-9.-]/g, ''));

        if (!isNaN(num) && num > 0) {
            $this.text('0');
            setTimeout(function() {
                animateNumber($this, num);
            }, 200);
        }
    });

    // CSV 내보내기
    $('[data-export-csv]').on('click', function(e) {
        e.preventDefault();
        var period = $(this).data('period') || '7days';

        $.ajax({
            url: acf_uja_admin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'acf_uja_export_csv',
                nonce: acf_uja_admin.nonce,
                period: period
            },
            success: function(response) {
                if (response.success && response.data.csv) {
                    // CSV 다운로드
                    var blob = new Blob([response.data.csv], { type: 'text/csv;charset=utf-8;' });
                    var link = document.createElement('a');
                    var url = URL.createObjectURL(blob);

                    link.setAttribute('href', url);
                    link.setAttribute('download', 'user-journey-analytics-' + period + '.csv');
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    alert('내보내기에 실패했습니다.');
                }
            },
            error: function() {
                alert('서버 오류가 발생했습니다.');
            }
        });
    });

    // 툴팁 초기화
    $('[data-tooltip]').each(function() {
        var $this = $(this);
        var tooltip = $('<div class="acf-uja-tooltip">' + $this.data('tooltip') + '</div>');

        $this.on('mouseenter', function() {
            $('body').append(tooltip);
            var pos = $this.offset();
            tooltip.css({
                top: pos.top - tooltip.outerHeight() - 10,
                left: pos.left + ($this.outerWidth() / 2) - (tooltip.outerWidth() / 2)
            }).fadeIn(200);
        }).on('mouseleave', function() {
            tooltip.remove();
        });
    });

})(jQuery);
