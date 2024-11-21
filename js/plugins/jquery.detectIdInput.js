(function ($) {

    $.fn.detectIdInput = function (options) {

        let mobileTokenInput = this.filter('.mobile-token-input');
        let oobSmsInput = this.filter('.oob-sms-input');
        let mobileTokenInputGroup = mobileTokenInput.closest('div.form-group');
        let oobSmsInputGroup = oobSmsInput.closest('div.form-group');

        if (mobileTokenInput.length) {
            if (oobSmsInput.val()) {
                mobileTokenInputGroup.hide();
            } else {
                oobSmsInputGroup.hide();
            }
        }

        mobileTokenInput
            .siblings('span.help-block')
            .children('a')
            .on('click', function (e) {
                e.preventDefault();
                mobileTokenInput.val('');
                mobileTokenInputGroup.hide();
                oobSmsInputGroup.show();
				
                $.ajax({
                    url: 'resendSms',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        'YII_CSRF_TOKEN': csrfToken
                    }
                })
                    .done(function (data) {
                        console.log(data);
                    })
                    .fail(function () {
                        console.log('send sms failed');
                    });
            });

        oobSmsInput
            .siblings('span.help-block')
            .children('a.change-input')
            .on('click', function (e) {
                
                e.preventDefault();
                oobSmsInput.val('');
                oobSmsInputGroup.hide();
                mobileTokenInputGroup.show();
				
            });

        oobSmsInput
            .siblings('span.help-block')
            .children('a.resend-sms')
            .on('click', function (e) {
                
                e.preventDefault();
                oobSmsInput.val('');
                $.ajax({
                    url: 'resendSms',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        'YII_CSRF_TOKEN': csrfToken
                    }
                })
                    .done(function (data) {
                        console.log(data);
                    })
                    .fail(function () {
                        console.log('send sms failed');
                    });
            });
    };
})(jQuery);
