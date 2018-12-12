jQuery(document).ready(function($) {
    $('.wfm-favorites-del').click(function(e) {
        e.preventDefault();
        if (!confirm('Подтвердите удаление')) return false;
        var post = $(this).data('post'),
            parent = $(this).parent(),
            loader = parent.next(),
            li = $(this).closest('li');
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                security: wfmFavorites.nonce,
                action: 'wfm_del',
                postID: post
            },
            beforeSend: function() {
                parent.fadeOut(300, function() {
                    loader.fadeIn();
                });
            },
            success: function(res) {
                loader.fadeOut(300, function() {
                    li.html(res);
                });
            },
            error: function() {
                console.log('Ошибка!');
            }
        });
    });

    $('#wfm-favorites-del-all').click(function(e) {
       e.preventDefault();
       if (!confirm('Подтвердите удаление')) return false;
       var $this = $(this),
           loader = $this.next(),
           parent = $this.parent(),
           list = parent.prev();

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                security: wfmFavorites.nonce,
                action: 'wfm_del_all'
            },
            beforeSend: function() {
                $this.fadeOut(300, function() {
                    loader.fadeIn();
                });
            },
            success: function(res) {
                loader.fadeOut(300, function() {
                    if (res === 'Список очищен') {
                        parent.html(res);
                        list.fadeOut();
                    } else {
                        $this.fadeIn();
                        alert(res);
                    }
                });
            },
            error: function() {
                alert('Ошибка!');
            }
        });
    });
});