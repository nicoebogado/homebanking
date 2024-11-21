jQuery(document).ready(function($) {

    $('#UpdateProfileForm').submit(function(e) {
      elements=$(this).find(':input');
      elements.prop('disabled', false);
    });

    $('#address1 #UpdateProfileForm_city').val(0);
    $('#address2 #UpdateProfileForm_city').val(0);

    $.ajax({
      url: initURL,
      dataType: 'json',
    })
    .done(function(data) {

      $('#UpdateProfileForm_email').val(data.cabecera.email);
      $('#UpdateProfileForm_hold').val(data.cabecera.esretener);
      $('#UpdateProfileForm_hold').val(data.cabecera.indoficinacurrier);
      $('#UpdateProfileForm_summaryType').val(data.cabecera.tipoextracto);
      $('#UpdateProfileForm_clientOffice').val(data.cabecera.oficinacliente);

      $('#address1 #UpdateProfileForm_district').append($('<option>').text('Selecciona un barrio').attr('value',0));
      if(data.barrios[0]){
        $.each(data.barrios[0], function(i, value) {
            $('#address1 #UpdateProfileForm_district').append($('<option>').text(value).attr('value', i));
        });
      }

      $('#address2 #UpdateProfileForm_district').append($('<option>').text('Selecciona un barrio').attr('value',0));
      if(data.barrios[1]){
        $.each(data.barrios[1], function(i, value) {
            $('#address2 #UpdateProfileForm_district').append($('<option>').text(value).attr('value', i));
        });
      }

      direcciones = data.direcciones;
      if(Array.isArray(direcciones)){
        for (var i = 0; i < direcciones.length; i++) {
          $('#address'+(i+1)+' #UpdateProfileForm_city').val(direcciones[i].codigociudad);
          $('#address'+(i+1)+' #UpdateProfileForm_address').val(direcciones[i].direccion);
          $('#address'+(i+1)+' #UpdateProfileForm_isDefaultAddres').val(direcciones[i].principal);
          $('#address'+(i+1)+' #UpdateProfileForm_shippingType').val(direcciones[i].tipoenvio);
          $('#address'+(i+1)+' #UpdateProfileForm_addressType').val(direcciones[i].tipo);
          $('#address'+(i+1)+' #UpdateProfileForm_district').val(direcciones[i].codigobarrio);
          $('#address'+(i+1)+' #UpdateProfileForm_addressCode').val(direcciones[i].codigodireccion);
          if(direcciones[i].principal=='S'){
              $('#address'+(i+1)+' :input').attr('readonly', 'readonly');
              $('#address'+(i+1)+' :input').prop('disabled', true);
          }
        }
      }

      telefonos = data.telefonos;
      if(Array.isArray(telefonos)){
        for (var i = 0; i < telefonos.length; i++) {
          $('#telephone'+(i+1)+' #UpdateProfileForm_areaCode').val(telefonos[i].area);
          $('#telephone'+(i+1)+' #UpdateProfileForm_extensionNumber').val(telefonos[i].interno);
          $('#telephone'+(i+1)+' #UpdateProfileForm_isDefaultTelephone').val(telefonos[i].principal);
          $('#telephone'+(i+1)+' #UpdateProfileForm_telephoneNumber').val(telefonos[i].telefono);
          $('#telephone'+(i+1)+' #UpdateProfileForm_telephoneType').val(telefonos[i].tipo);
          $('#telephone'+(i+1)+' #UpdateProfileForm_lineType').val(telefonos[i].tipolinea);
          $('#telephone'+(i+1)+' #UpdateProfileForm_telephoneCode').val(telefonos[i].codigotelefono);
          if(telefonos[i].principal=='S'){
              $('#telephone'+(i+1)+' :input').attr('readonly', 'readonly');
              $('#telephone'+(i+1)+' :input').prop('disabled', true);
          }
        }
      }

    });

    $('#address1 #UpdateProfileForm_city').change(function(event) {
      var valor=$(this).val()+'-B';
      $.ajax({
        url: distURL,
        dataType: 'json',
        data: {id: valor}
      })
      .done(function(data) {
        $('#address1 #UpdateProfileForm_district').html('');
        $('#address1 #UpdateProfileForm_district').append($('<option>').text('Selecciona un barrio').attr('value',0));
        $.each(data.barrios, function(i, value) {
            $('#address1 #UpdateProfileForm_district').append($('<option>').text(value).attr('value', i));
        });
      }).fail(function() {
        $('#address1 #UpdateProfileForm_district').html('');
        $('#address1 #UpdateProfileForm_district').append($('<option>').text('Selecciona un barrio').attr('value',0));
      });
    });

    $('#address2 #UpdateProfileForm_city').change(function(event) {
      var valor=$(this).val()+'-B';
      $.ajax({
        url: distURL,
        dataType: 'json',
        data: {id: valor}
      })
      .done(function(data) {
        $('#address2 #UpdateProfileForm_district').html('');
        $('#address2 #UpdateProfileForm_district').append($('<option>').text('Selecciona un barrio').attr('value',0));
        $.each(data.barrios, function(i, value) {
            $('#address2 #UpdateProfileForm_district').append($('<option>').text(value).attr('value', i));
        });
      }).fail(function() {
        $('#address2 #UpdateProfileForm_district').html('');
        $('#address2 #UpdateProfileForm_district').append($('<option>').text('Selecciona un barrio').attr('value',0));
      });
    });


});
