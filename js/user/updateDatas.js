(function($) {

    $(document).ready(function() {
        $('.agi-modal')
        .on('change', '#AddressData_codigoCiudad', function(){
            // realizar llamada ajax para recuperar los barrios
            // relacionados a la ciudad seleccionada
            var $this = $(this),
                onchangeurl = $this.data('onchangeurl');
            $.ajax({
                url: onchangeurl,
                data: {'cityCode': $this.val()},
                cache: true,
                success: function(html) {
                    $('#AddressData_codigoBarrio').html(html);
                }
            });
        });
    });
}) (jQuery);
