(function($){

    $.fn.secureKeypad = function( options ) {
        var opts = $.extend({}, $.fn.secureKeypad.defaults, options);

        return this.each(function(i, textElement){
            var input = $(this);
            input.attr('readonly', true);

            var box = $('<div class="sk-keypad">').append(renderButtons(opts.bootstrapClass))
            .on('mousemove', 'button.sk-btn-num', function(e) {
                toggleButtonsLabel($(this), 'hide');
            })
            .on('mouseleave', 'button.sk-btn-num', function(e) {
                toggleButtonsLabel($(this), 'show');
            })
            .on('click', 'button', function(e) {
                var btnVal = $(this).val();
                var textVal = $(textElement).val();

                input.data('hashed', false);

                if(btnVal == 'del') { // delete button
                    $(textElement).val('');
                } else { // alphanumeric values
                    $(textElement).val(textVal+btnVal);
                }

                $(this).parent().parent().html(renderButtons(opts.bootstrapClass));
                toggleButtonsLabel($(this), 'show');
            });

            input.after(box);

            input.closest('form').submit(function() {
                if ( !input.data('hashed') ) {
                    if ( opts.validate && !validatePass(input) ) {
                        return false;
                    }

                    var hashedVal = sha256(md5( input.val() ).toLowerCase());
                    input.val( hashedVal ).data('hashed', true);
                }
            });
        });
    };

    $.fn.secureKeypad.defaults = {
        bootstrapClass : 'default',
        validate: false
    };

    function toggleButtonsLabel (btn, state) {
        var box = btn.parent().parent();

        box.find('button.sk-btn-num').each(function(i, el) {
            $(el).text(state === 'show' ? $(el).val() : '*');
        });
    }

    function renderButtons(bc) {
        var n = shuffle([0, 1, 2, 3, 4, 5, 6, 7, 8, 9]);
        var c = shuffle(['A', 'B', 'C', 'D']);

        return  '<div>'+createBtn(c[0], bc)+createBtn(c[1], bc)+createBtn(c[2], bc)+createBtn(c[3], bc)+'</div>'+
                '<div>'+createBtn(n[0], bc)+createBtn(n[1], bc)+createBtn(n[2], bc)+createBtn(n[3], bc)+'</div>'+
                '<div>'+createBtn(n[4], bc)+createBtn(n[5], bc)+createBtn(n[6], bc)+createBtn(n[7], bc)+'</div>'+
                '<div>'+createBtn(n[8], bc)+createBtn(n[9], bc)+createDelBtn(bc)+'</div>';
    }

    function createBtn(value, bc) {
        var btnClass = 'sk-btn-num '+(bc ? 'btn btn-'+bc : '');
        var styles = "width: 44px; height: 44px";
        return '<button class="'+btnClass+'" type="button" value="'+value+'" style="'+styles+'">'+
            value+
        '</button>';
    }

    function createDelBtn(bc) {
        var btnClass = 'sk-btn-del '+(bc ? 'btn btn-'+bc : '');
        var styles = "width: 88px; height: 44px";
        return '<button class="'+btnClass+'" type="button" value="del" style="'+styles+'">Borrar</button>';
    }

    //+ Jonas Raoni Soares Silva
    //@ http://jsfromhell.com/array/shuffle [v1.0]
    function shuffle(o){ //v1.0
        for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
        return o;
    }

    function validatePass(input) {
        var numCheck = /(?=.*[0-9])/;
        var charCheck = /(?=.*[A-D])/;

        if ( input.val().length < 6 ) {
            attachError(input, 'La clave debe tener por lo menos 6 dígitos.');
            return false;
        }
        if ( (m = numCheck.exec(input.val())) === null ) {
            attachError(input, 'La clave debe tener por lo menos un número.');
            return false;
        }
        if ( (m = charCheck.exec(input.val())) === null ) {
            attachError(input, 'La clave debe tener por lo menos una letra.');
            return false;
        }

        if ( hasSequential(input.val()) ) {
            attachError(input, 'La clave no debe poseer números o letras consecutivas.');
            return false
        }

        return true;
    }

    function attachError(input, msg) {
        var id = input.prop('id')+'-error';
        var msgElement = '<div id="'+id+'" class="help-block ">'+msg+'</label>';

        $('#'+id).remove();
        input.parent().append(msgElement).parent().addClass('has-error');
    }

    function hasSequential(s) {
        // Check susesive chars
        for(var i in s)
            if(s[+i] == s[+i+1]) return true;
        // Check for sequential numerical characters
        for(var i in s)
            if (+s[+i+1] == +s[i]+1) return true;
        // Check for sequential alphabetical characters
        for(var i in s)
            if (String.fromCharCode(s.charCodeAt(i)+1) == s[+i+1]) return true;
        return false;
    }
})(jQuery);
