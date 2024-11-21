
<div class="modal fade" id="recuperarAcceso" tabindex="-1" role="dialog" aria-labelledby="recuperarAccesoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="recuperarAccesoLabel">Recuperar Contrase&ntildea
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button></h3>
      </div>
      <div class="modal-body">
        <!--loading-->
        <div class="custom-loader" id="espere"></div>
        <!--Alertst-->
        <div id="completarDatos" class="alert alert-danger" style="display:none;">
          <p id=mensajeerror></p>
        </div>

        <div id="correcto" class="alert alert-success" style="display:none;">
          <p id=mensajecorrecto></p>
        </div>

        <!--Form-->
        <form>
            <!--Datos de sharekey-->
            <input type="hidden" value="" id="sharekey" >
            <div class="form-group">
                <label for="cedula">C&eacutedula de Identidad</label>
                <input type="text" class="form-control" id="cedula" aria-describedby="cedulaHelp" placeholder="Ingrese c&eacute;dula">
            </div>
            <div class="form-group">
                <label for="correo">Correo Electr&oacutenico</label>
                <input type="email" class="form-control" id="correo" placeholder="Ingrese correo">
            </div>
            <div class="form-group">
                <label for="fecNac">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fecNac" placeholder="Ingrese fecha de nacimiento">
            </div>

            <div class="form-group">
                <label for="captCha">Ingrese captcha</label>
                      <input type="text" class="form-control" type="text" id="startTime" name="captcha" placeholder="Ingrese captcha" />
                  <div id="captchaDiv">
                    <img id="imagencaptcha" src="https://secure.fic.com.py/captcha/png/captcha.png" alt="Captcha">
                  </div>
                  
                  <button id="botonactualizar" type="button"><i class="icon-refresh"></i> Actualizar</button>
                  <!--<button type="submit">Verificar</button>-->
              </div>
            <button type="button" id="botonVerificarDatos"  class="btn btn-primary" disabled>Verificar Datos</button>

            <div class="form-group">
                <label for="otp">OTP</label>
                <input type="number" class="form-control" id="otp" aria-describedby="otpHelp" placeholder="Ingrese OTP">
                <small id="otpAyuda" class="form-text text-muted">Para validar los campos solicite la un c&oacute;digo OTP que le llegará al celular por SMS.</small>
            </div>
            
            <button type="button" id="botonOTP"  class="btn btn-primary" disabled>Ingresar OTP <label id="minutoatras"></label></button>
        </form>
        <!--form-->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
        <!--<button type="button" class="btn btn-primary">Save changes</button>-->
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
  //boton captcha
  //al cargar pagina
  $.get('https://secure.fic.com.py/captcha/captcha.php', function(data) {
      $("#imagencaptcha").attr('src','https://secure.fic.com.py/captcha/'+data);
  });
  //al actualizar
  $("#botonactualizar").click(function(){

    
    $.get('https://secure.fic.com.py/captcha/captcha.php', function(data) {
      $("#imagencaptcha").attr('src','https://secure.fic.com.py/captcha/'+data);
    });
    

  });

  //on input captcha
  $('#startTime').on('input', function() {
    if($(this).val().length >= 5){
      document.getElementById("espere").style.display="block";
      
      verificarCaptcha($(this).val());
    }
  });

  $("#botonVerificarDatos").click(function(){
    
    var ci = document.getElementById("cedula").value;
    var mail = document.getElementById("correo").value;
    var fecnac = document.getElementById("fecNac").value;

    //Cambiar url luego.
    var url = "https://secure.fic.com.py/api/public/pass/sharedkey";

    if(ci == '' || mail == '' || fecnac == ''){
      document.getElementById("completarDatos").style.display = "block";
      document.getElementById("mensajeerror").textContent ="";
      document.getElementById("mensajeerror").append("Completar los datos");
      $("#yw0").html("<div class='alert in fade alert-danger'><a href='#' class='close' data-dismiss='alert'>×</a>Complete los campos requeridos</div>");
      $('#recuperarAcceso').modal('hide');
      document.getElementById("cedula").style.borderColor = "red";
      document.getElementById("correo").style.borderColor = "red";
      document.getElementById("fecNac").style.borderColor = "red";
    }else{
      $.ajax({
          type: 'POST',
          url: url,
          data: {
              cedula: ci,
              email: mail,
              fechaNac: fecnac
          },
          beforeSend: function() {
            document.getElementById("espere").style.display="block";
          },
          success: function(data, status) {
            if(status == 'success'){
                
                document.getElementById("espere").style.display="none";
                document.getElementById("completarDatos").style.display = "none";
                document.getElementById("cedula").style.borderColor = "";
                document.getElementById("correo").style.borderColor = "";
                document.getElementById("fecNac").style.borderColor = "";
				
                var resultado = data.split(":");
                if(resultado[0].trim() =='result'){
                  if(resultado[1].trim() == 'Datos incorrectos.'){
                    document.getElementById("completarDatos").style.display = "block";
                    document.getElementById("mensajeerror").textContent ="";
                    document.getElementById("mensajeerror").append("Datos incorrectos");
                    document.getElementById("correcto").style.display = "none";
                    $("#yw0").html("<div class='alert in fade alert-danger'><a href='#' class='close' data-dismiss='alert'>×</a>Datos incorrectos</div>");
                    $('#recuperarAcceso').modal('hide');
					$("#botonVerificarDatos").prop("disabled", true);
                    
                  }else{
                    document.getElementById("correcto").style.display = "block";
                    document.getElementById("mensajecorrecto").textContent ="";
                    document.getElementById("mensajecorrecto").append("Ingrese su código Otp");
                    var datos = data.split("#");
                    $("#sharekey").val(datos[1]);
                    $("#botonOTP").removeAttr('disabled');
                    $("#botonVerificarDatos").prop("disabled", true);
                    cuentaOtp();
                    
                  }
                }else{
                  document.getElementById("completarDatos").style.display = "block";
                  document.getElementById("mensajeerror").textContent ="";
                  document.getElementById("mensajeerror").append("Error al procesar datos");
                  $("#yw0").html("<div class='alert in fade alert-danger'><a href='#' class='close' data-dismiss='alert'>×</a>Error al procesar datos</div>");
                  $('#recuperarAcceso').modal('hide');
                  
                }
                
              }
          },
          error: function(xhr, status, error) {
              // Código a ejecutar en caso de error
              
          }
      });

      $("#botonOTP").click(function(){

        //Cambiar url luego.
        var url = "https://secure.fic.com.py/api/public/pass/verificarotp";
        var otp = document.getElementById("otp").value;
        var sharedkey = document.getElementById("sharekey").value;
        
        

        $.ajax({
          type: 'POST',
          url: url,
          data: {
              otp: otp,
              sharedkey: sharedkey
          },
          beforeSend: function(){
            document.getElementById("espere").style.display="block";
          },
          success: function(data, status){
            if(status == 'success'){
                
                
                document.getElementById("espere").style.display="none";
                document.getElementById("completarDatos").style.display = "none";
                
                document.getElementById("cedula").style.borderColor = "";
                document.getElementById("correo").style.borderColor = "";
                document.getElementById("fecNac").style.borderColor = "";
                var resultado = data.split(":");
                if(resultado[0].trim() =='result'){
                  if(resultado[1].trim() == 'Datos incorrectos.'){
                    document.getElementById("completarDatos").style.display = "block";
                    document.getElementById("mensajeerror").textContent ="";
                    document.getElementById("mensajeerror").append("Datos incorrectos");
                    document.getElementById("correcto").style.display = "none";
                    
                  }else if(resultado[1].trim() == 'OK'){
                    document.getElementById("correcto").style.display = "block";
                    document.getElementById("mensajecorrecto").textContent ="";
                    document.getElementById("mensajecorrecto").append("Hemos enviado un enlace, verifique correo electrónico.");
                    $("#sharekey").val(data);
                    $("#botonOTP").prop('disabled', true);
                    setTimeout(function() {
                        location.reload();
                    }, 10000);
                  }
                }else{
                  document.getElementById("completarDatos").style.display = "block";
                  document.getElementById("mensajeerror").textContent ="";
                  document.getElementById("mensajeerror").append("Error al procesar datos");
                  
                }
                
              }
          },
          error: function(xhr, status, error) {
              // Código a ejecutar en caso de error
             
          }
      });
        

      });

    }
    
  });

});

function verificarCaptcha(valor = ''){
  var captcha = valor;
  var resultado = '';
    //Cambiar url luego.
    var url = "https://secure.fic.com.py/captcha/verificar_captcha.php";
    $.post(url,
            {
              captcha: captcha
            },
            function(data, status){
              
              if(data.trim() == "OK"){
				  
                document.getElementById("espere").style.display="none";
                  $("#botonVerificarDatos").removeAttr('disabled');
                }else if(data.trim() == "ERROR"){
					
                  document.getElementById("startTime").style.borderColor = "red";
                  document.getElementById("espere").style.display="none";
                }   
            });
}

function getOtp(sharekey = ''){
    var sharekey = sharekey;
    
    //Cambiar url luego.
    var url = "https://secure.fic.com.py/api/public/pass/resendsms";
    $.post(url,
            {
              veriPass: sharekey
            },
            function(data, status){
              ////console.log(data);
              ////console.log(status);   
            });
}

function cuentaOtp(){
  // Establecer la duración en segundos (1 minuto = 60 segundos)
  var duracion = 60;

  // Mostrar el tiempo inicial
  actualizarCuentaRegresiva(duracion);

  // Iniciar la cuenta regresiva
  var intervalo = setInterval(function() {
      duracion--;

      // Actualizar el tiempo restante
      actualizarCuentaRegresiva(duracion);

      // Verificar si la cuenta regresiva ha llegado a cero
      if (duracion <= 0) {
          clearInterval(intervalo);
          alert("Volver a solcitar OTP");
          location.reload();
      }
  }, 1000); // Actualizar cada segundo

}

function actualizarCuentaRegresiva(segundos) {
    // Calcular minutos y segundos
    //var minutos = Math.floor(segundos / 60);
    var segundosRestantes = segundos % 60;

    // Formatear y mostrar la cuenta regresiva
    var tiempoRestante =  (segundosRestantes < 10 ? '0' : '') + segundosRestantes;
    $("#minutoatras").text(tiempoRestante);
}
</script>
<style>
  .custom-loader{
    display:none;
    --d:22px;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    color: #F40000;
    position: absolute;
    top: 50%;
    left: 50%;
    box-shadow: 
      calc(1*var(--d))      calc(0*var(--d))     0 0,
      calc(0.707*var(--d))  calc(0.707*var(--d)) 0 1px,
      calc(0*var(--d))      calc(1*var(--d))     0 2px,
      calc(-0.707*var(--d)) calc(0.707*var(--d)) 0 3px,
      calc(-1*var(--d))     calc(0*var(--d))     0 4px,
      calc(-0.707*var(--d)) calc(-0.707*var(--d))0 5px,
      calc(0*var(--d))      calc(-1*var(--d))    0 6px;
    animation: s7 1s infinite steps(8);
  }

  @keyframes s7 {
    100% {transform: rotate(1turn)}
  }

  fieldset.scheduler-border {
      border: 1px groove #ddd !important;
      padding: 0 1.4em 1.4em 1.4em !important;
      margin: 0 0 1.5em 0 !important;
      -webkit-box-shadow:  0px 0px 0px 0px #000;
              box-shadow:  0px 0px 0px 0px #000;
  }

  legend.scheduler-border {
      font-size: 1.2em !important;
      font-weight: bold !important;
      text-align: left !important;
      width:auto;
      padding:0 10px;
      border-bottom:none;
  }
</style>