(function($) {
    $('table.items').on('click', 'button.pay-bill', function() {
        var bill = $(this).data('bill');
        $('tr.bill-description>td', '#table-bill-detail').text(bill.description ? bill.description : '');
        $('tr.bill-amount>td', '#table-bill-detail').text(bill.f_amount);
        $('tr.bill-minpay>td', '#table-bill-detail').text(bill.f_minimum_payment);
        $('tr.bill-duedate>td', '#table-bill-detail').text(bill.f_due_date);
        $('input#BancardServiceForm_amount', '#form-pay-bill').val(bill.amount);
        $('input#BancardServiceForm_billJSON', '#form-pay-bill').val(JSON.stringify(bill));
    });
}) (jQuery);
