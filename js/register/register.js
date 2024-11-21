jQuery(document).ready(function($) {

    $('#RegisterForm_cityCode').val(1);
    initDistrict();

    $('#RegisterForm_cityCode').change(function(event) {
      var valor=$(this).val();
      $.ajax({
        url: distURL,
        dataType: 'json',
        data: {id: valor}
      })
      .done(function(data) {
        $('#RegisterForm_districtCode').html('');
        $('#RegisterForm_districtCode').append($('<option>').text('Selecciona un barrio').attr('value',0));
        $.each(data.barrios, function(i, value) {
            $('#RegisterForm_districtCode').append($('<option>').text(value).attr('value', i));
        });
      }).fail(function() {
        //
      });
    });


});

function initDistrict(){

  var valor='1';
  $.ajax({
    url: distURL,
    dataType: 'json',
    data: {id: valor}
  })
  .done(function(data) {
    $('#RegisterForm_districtCode').html('');
    $('#RegisterForm_districtCode').append($('<option>').text('Selecciona un barrio').attr('value',0));
    $.each(data.barrios, function(i, value) {
        $('#RegisterForm_districtCode').append($('<option>').text(value).attr('value', i));
    });
  }).fail(function() {
    //
  });

}
