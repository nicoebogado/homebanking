// se cambió el plugin datepicker y ya no funciona el evento changeDate y la opción onRender
(function($) {
    $(document).ready(function() {
            var nowTemp = new Date();
            var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

            var dateFrom = $('#MovementForm_dateFrom')
            .datepicker({
              format: 'dd-mm-yyyy',
              orientation: 'bottom left',
              autoclose: true,
              language: 'es',
              // onRender: function(date) {
              //   return date.valueOf() > now.valueOf() ? 'disabled' : '';
              // }
            })
            .on('changeDate', function(ev) {
              dateFrom.hide();
              if (ev.date.valueOf() > dateTo.date.valueOf()) {
                var newDate = new Date(ev.date);
                
                newDate.setDate(newDate.getDate() + 1);
                dateTo.setValue(newDate);
              }
              $('#MovementForm_dateTo')[0].focus();
            })
            .data('datepicker');

            var dateTo = $('#MovementForm_dateTo')
            .datepicker({
              format: 'dd-mm-yyyy',
              orientation: 'bottom left',
              autoclose: true,
              language: 'es',
              // onRender: function(date) {
              //   var res = date.valueOf() <= dateFrom.date.valueOf() ? 'disabled' : '';
              //   res = date.valueOf() > now.valueOf() ? 'disabled' : res;
              // }
            })
            .on('changeDate', function(ev) {
              dateTo.hide();
            })
            .data('datepicker');
    });
}) (jQuery);
