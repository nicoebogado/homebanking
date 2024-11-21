(function ($) {
    $('form')
        .on('submit', function (e) {
            window.history.replaceState({}, "", document.referrer)
        });
})(jQuery);
