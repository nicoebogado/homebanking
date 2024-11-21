(function($) {

    $(document).ready(function() {
        $('.panel-body')
        .on('submit', 'form', function(){
            var input = $('#ChangePasswordForm_currentPassword');
            var value = sha256(md5(input.val()).toLowerCase());

            input.val(value);
        });
    });
}) (jQuery);
