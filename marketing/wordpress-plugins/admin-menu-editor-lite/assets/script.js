jQuery(document).ready(function($) {
    // Sortable
    $('#jj-menu-list').sortable({
        handle: '.jj-menu-handle',
        placeholder: 'ui-state-highlight',
        axis: 'y'
    });

    // Toggle Visibility
    $(document).on('click', '.jj-toggle-visibility', function() {
        var item = $(this).closest('.jj-menu-item');
        var icon = $(this).find('.dashicons');
        
        item.toggleClass('hidden-menu');
        
        if (item.hasClass('hidden-menu')) {
            icon.removeClass('dashicons-visibility').addClass('dashicons-hidden');
        } else {
            icon.removeClass('dashicons-hidden').addClass('dashicons-visibility');
        }
    });

    // Save
    $('#jj-save-menu').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true).text('저장 중...');
        
        var layout = {};
        $('#jj-menu-list .jj-menu-item').each(function(index) {
            var slug = $(this).data('slug');
            var label = $(this).find('.jj-menu-label').val();
            var hidden = $(this).hasClass('hidden-menu');
            
            layout[slug] = {
                order: index,
                label: label,
                hidden: hidden
            };
        });

        $.ajax({
            url: jjAME.ajax_url,
            type: 'POST',
            data: {
                action: 'jj_ame_save',
                nonce: jjAME.nonce,
                layout: layout
            },
            success: function(res) {
                alert(res.data);
                btn.prop('disabled', false).text('설정 저장');
            },
            error: function() {
                alert('저장 실패');
                btn.prop('disabled', false).text('설정 저장');
            }
        });
    });

    // Pro Feature Lock
    $('.jj-edit-advanced').on('click', function() {
        alert('🔒 이 기능은 Pro 버전에서만 사용할 수 있습니다.\n\n서브메뉴 편집, 권한 설정, 아이콘 변경 등 강력한 기능을 만나보세요!');
    });
});

