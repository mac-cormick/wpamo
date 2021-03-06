jQuery(document).ready(function($) {
   $('.wfm-favorites-link a').click(function(e) {
       var action = $(this).data('action');
       $.ajax({
           type: 'POST',
           url: wfmFavorites.url,
           data: {
               security: wfmFavorites.nonce,
               action: 'wfm_' + action,
               postID: wfmFavorites.postID
           },
           beforeSend: function() {
               $('.wfm-favorites-link a').fadeOut(300, function() {
                    $('.wfm-favorites-link .wfm-favorites-hidden').fadeIn();
               });
           },
           success: function(res) {
               $('.wfm-favorites-hidden').fadeOut(300, function() {
                   $('.wfm-favorites-link .wfm-favorites-link').html(res);
                   if (action === 'del') {
                       $('.widget_wfm-favorites-widget').find('li.cat-item-' + wfmFavorites.postID).remove();
                   }
               });
           },
           error: function() {
               console.log('Ошибка!');
           }
       });
       e.preventDefault();
    });
});