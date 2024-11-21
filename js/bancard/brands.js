(function($) {
    $(document).ready(function() {
        // mostrar barra de herramientas
        $('#brand-loading').hide();
        $('.show-on-document-ready').show();
        var filterizd = $('.filtr-container').filterizr();
    });

    $('.filtr-container').on('click', '.filtr-item', function() {
        var brand = JSON.parse(atob($(this).data('brand'))),
            modal = $('#brand-modal'),
            services = brand.services,
            servicesHtml = '';

        $('#brand-name', modal).text('Servicios disponibles para '+brand.name);
        $('#brand-logo', modal).html('<img src="https://www.bancard.com.py/s4/public/billing_brands_logos/'+brand.logo_resource_id+'.normal.png" alt="'+brand.name+' logo">');

        for (var i = services.length - 1; i >= 0; i--) {
            servicesHtml += '<a href="'+services[i].url+'" class="btn btn-primary">'+services[i].name+'<a>';
        }

        $('#brand-services', modal).html(servicesHtml);
    });
}) (jQuery);
