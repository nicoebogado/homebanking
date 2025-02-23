<?php $this->beginContent('//layouts/base'); 

function encript256($textoLetra){
  $clave = "Hola.12345678#";
  // Se genera un vector de inicialización (IV) aleatorio
  $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));       
  // Se cifran los datos utilizando AES-256 en modo CBC (Cipher Block Chaining)
  $cifrado = openssl_encrypt($textoLetra, 'aes-256-cbc', $clave, 0, $iv);       
  // Se devuelve el IV concatenado con los datos cifrados
  return base64_encode($iv . $cifrado);
  
}

?>

<?php HCss::register('secureKeypad.css') ?>
<?php HScript::register(array(
  'libs/modernizr/modernizr',
  'libs/jQuery-Storage-API/jquery.storageapi',
  'libs/jquery.easing/jquery.easing',
  'libs/animo.js/animo',
  'libs/slimScroll/jquery.slimscroll.min',
  'libs/screenfull/screenfull.min',
  'app.min',
  'ping',
  'commons/forms'
)); ?>
<?php HScript::registerCode('globals', '
  var suid = "' . Yii::app()->user->getState('suid') . "\";
  var csrfToken = '" . Yii::app()->request->csrfToken . "';
") ?>

<?php HScript::registerCode('checkSession', '
  (function ($) {
    var d = ' . Yii::app()->user->getState('sessionTimeout') . ';
    window.setTimeout(function(){
      $.ajax({
        type: "POST",
        url: "' . Yii::app()->createUrl('site/checkSessionStatus') . '",
        data: {
		  "YII_CSRF_TOKEN": \'' . Yii::app()->request->csrfToken . '\',
        }
      })
      .done(function(data) {
        if( !data.valid ) {
          window.location = "' . Yii::app()->createUrl('site/login') . '";
        }
      })
      .fail(function() {
        window.location = "' . Yii::app()->createUrl('site/login') . '";
      });
    }, d+3000);
  }(jQuery));
') ?>

<header class="topnavbar-wrapper">
  <nav role="navigation" class="navbar topnavbar">
    <div class="navbar-header">
      <ul class="nav navbar-nav navbar-right">
        <li class="visible-lg">
          <a href="#" data-toggle-fullscreen="">
            <em class="fa fa-expand"></em>
          </a>
        </li>
        <li>
          <a href="#" data-toggle-state="aside-collapsed" class="hidden-xs">
            <em class="fa fa-navicon"></em>
          </a>
          <a href="#" data-toggle-state="aside-toggled" data-no-persist="true" class="visible-xs">
            <em class="fa fa-navicon"></em>
          </a>
        </li>
      </ul>
      <!-- <a href="<?php echo CHtml::normalizeUrl(array('/site/index')) ?>" class="navbar-brand">
        <div class="brand-logo">
          <?php echo HImage::html('logo-navbar.png', 'Logo', array(
            'class' => 'img-responsive',
          )); ?>
        </div>
        <div class="brand-logo-collapsed">
          <?php echo HImage::html('logo-single.png', 'Logo', array(
            'class' => 'img-responsive',
          )); ?>
        </div>
      </a> -->
    </div>
    <?php
    if (!function_exists('getCurrentDate')) {
      function getCurrentDate()
      {
        $arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $arrayDias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado');
        return $arrayDias[date('w')] . ", " . date('d') . " de " . $arrayMeses[date('m') - 1] . " de " . date('Y');
      }
    }
    ?>
    <div class="nav-wrapper">
      <p class="visible-lg" style="float:left;color:#ffffff;margin-left:20px;margin-top:15px;">
        <?php echo getCurrentDate() ?>
      </p>
      <!--nombre de la empresa-->
      <p class="visible-lg" style="float:right;color:#ffffff;margin-left:20px;margin-top:15px;">
        <?php echo (Yii::app()->user->getState('empresa') == "") ? "" : Yii::app()->user->getState('empresa'); ?>
      </p>
      <!--nombre de la empresa-->
      <ul class="nav navbar-nav navbar-right">
        <li>
          <a class="visible-lg" data-toggle="modal" id="cargarperfil" data-target="#perfilesmostrar" href="#"><em class="icon-users"></em> 
            <?php echo Yii::app()->user->getState('clientArea')['nombrecompleto']; ?>
          </a>
          <a class="visible-md" href="#">
            <?php $name = explode(' ', Yii::app()->user->getState('clientArea')['nombrecompleto']); ?>
            <?php echo count($name) > 1 ? $name[0] . ' ' . $name[1] : $name[0]. '- '. Yii::app()->user->getState('entityName') ?>
          </a>
        </li>


        <li class="dropdown dropdown-list">

          <a href="#" data-toggle="dropdown">
            <em class="icon-settings"></em>
          </a>

          <ul class="dropdown-menu animated flipInX">
            <li>
              <div class="list-group">

                <a href="<?php echo CHtml::normalizeUrl(array('/user/changeAccessPassword')) ?>" class="list-group-item">
                  <div class="media-box">
                    <div class="pull-left">
                      <em class="fa fa-lock fa-2x text-info"></em>
                    </div>
                    <div class="media-box-body clearfix">
                      <p><?php echo Yii::t('menu', 'Cambiar Clave de Acceso') ?></p>
                    </div>
                  </div>
                </a>

              </div>
            </li>
          </ul>
        </li>
        <li>
          <a href="<?php echo CHtml::normalizeUrl(array('/site/logout')) ?>">
            <em class="icon-login"></em>
          </a>
        </li>
      </ul>
    </div>
  </nav>
</header>
<aside class="aside">
  <div class="aside-inner">
    <nav class="sidebar">
      <ul id="yw26" class="nav">
        <li>
          <span>
            <div class="item user-block">
              <div class="brand-logo">
                <?php echo HImage::html('logo.png', 'Logo', array(
                  'class' => 'img-responsive',
                )) ?>
              </div>
              <div class="brand-logo-collapsed">
                <?php echo HImage::html('logo-single.png', 'Logo', array(
                  'class' => 'img-responsive',
                )) ?>
              </div>

            </div>
          </span>
        </li>
        <?php
        $availableUrls = isset(Yii::app()->user->availableUrls) ? Yii::app()->user->availableUrls : '';
        $opened = false;
        $lang = isset(Yii::app()->request->cookies['appLanguage']) ? Yii::app()->request->cookies['appLanguage'] : 'es';
        ?>
        <li class="active"><a href="<?php echo CHtml::normalizeUrl(array('/site/index')) ?>"><em class="icon-home"></em>
            <span><?php echo ($lang == 'es') ? 'Inicio' : 'Home'; ?></span></a>
        </li>
        <?php if (is_array($availableUrls)) : ?>
          <?php foreach ($availableUrls as $opcion) : ?>
            <?php if ($opcion->estadomenu === 'A') : ?>
              <?php if (!isset($opcion->codigomenuprincipal)) : ?>
                <!-- nivel = 1 -->
                <?php if ($opened) : ?>
      </ul>
      <?php $opened = false; ?>
    <?php endif; ?>
    <li class='nav-heading'>
      <span><?php echo ($lang == 'es') ? $opcion->intespanol : $opcion->intingles; ?></span>
    </li>
  <?php else : ?>
    <!-- nivel 2 -->
    <?php if (!isset($opcion->descripcionurl) || strlen($opcion->descripcionurl) == 0) : ?>
      <?php if ($opened) : ?>
        </ul>
        <?php $opened = false; ?>
      <?php endif; ?>
      <li>
        <a title="Cuentas" data-toggle="collapse" href="#sb-<?php echo md5($opcion->descripcion); ?>">
          <?php switch ($opcion->intespanol):

                    case 'Cuentas': ?>
              <em class="icon-notebook"></em>
            <?php break;

                    case 'Informaciones' ?>
            <em class="icon-docs"></em>
          <?php break;

                    case 'Pagos' ?>
          <em class="icon-wallet"></em>
        <?php break;

                    case 'Autorizaciones' ?>
        <em class="icon-check"></em>
      <?php break;

                    case 'Transferencias' ?>
      <em class="icon-share-alt"></em>
    <?php break;

                    case 'Solicitudes' ?>
    <em class="icon-drawer"></em>
  <?php break;

                    default: ?>
    <em class="icon-share-alt"></em>
<?php break;

                  endswitch; ?>

<span><?php echo ($lang == 'es') ? $opcion->intespanol : $opcion->intingles; ?></span>
        </a>
        <ul class="sidebar-subnav nav collapse" id="sb-<?php echo md5($opcion->descripcion); ?>" style="height: 0px;">
          <?php $opened = true; ?>
          <!-- nivel 3 -->
        <?php else : ?>
          <li>
            <a href="<?php echo $this->createUrl($opcion->descripcionurl) ?>">
              <span><?php echo ($lang == 'es') ? $opcion->intespanol : $opcion->intingles; ?></span>
            </a>
          </li>
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; ?>
  <?php endforeach; ?>
<?php endif; ?>
        </ul>
    </nav>
  </div>
</aside>
<section>
  <div style="width:100%; position:absolute;">
    <?php $this->widget('booster.widgets.TbAlert', array(
      'htmlOptions' => array(
        'style' => 'width:50%;  margin: 10px auto;',
      ),
    )); ?>
  </div>
  <div class="content-wrapper">
    <?php echo $content; ?>
  </div>
</section>
<footer>
  <span>Copyright &copy; <?php echo date('Y'); ?></span>
</footer>
<?php $this->endContent(); ?>
<?php require '_perfiles.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script>
$(document).ready(function(){
  //Llamar funcion que carga empresas
 
    $("#cargarperfil").click(function(){
      if($('#listadeempresas').find('button').length == 0) {
        var valorrr = $('#listadeempresas').find('button').length;

        document.getElementById("espere").style.display="block";

      $.post("<?php echo Yii::app()->createUrl('report/listempresas'); ?>",
              {
                  YII_CSRF_TOKEN: "<?php echo Yii::app()->request->csrfToken; ?>",
                  cedula: "<?php echo Yii::app()->user->getState('documento'); ?>"

              },
              function(data, status){
                document.getElementById("espere").style.display="none";
                
                const obj = JSON.parse(data);
                  obj.forEach(function(datos,index){
                    
                    $('#listadeempresas').
                      append($('<button>').
                          attr("class", "btn btn-primary btn-lg btn-block").
                          attr("type", "button").
                          attr("value", datos.RUC).
                          text(datos.EMPRESA).
                          click(function() {
                            // Tu función onclick aquí
                            
                            if(confirm("Cambiar a perfil: "+datos.EMPRESA)){
                              
                              document.getElementById("espere").style.display="block";
                              $('#yw25').click();
                              cambioperfil(datos.RUC,datos.COD_PERSONA, datos.NRO_DOCUMENTO);
                            }else{
                              console.log("Has pulsado cancelar");
                            }
                          })
                      );
                  });
                  
              });
      }
    }); 
  
});

function cambioperfil(ruc = '',codigopersona = '', nrodocumento = ''){

  var ruc = ruc;
  var docuempresa = nrodocumento;
  var codigopersona = codigopersona;
  var tipocambio = (ruc.indexOf("-") > -1) ? "E":"P";
  var documento = "<?php echo Yii::app()->user->getState('documento'); ?>";
  var pass = "<?php echo encript256(Yii::app()->user->getState('password')); ?>";
  var datospost = '';
  

  if(tipocambio == 'P'){
    
    datos = "valor=2&tipoPer=P&dataType=D&data="+ruc+"&password="+pass;

    datos = "?x="+btoa(datos);
    

  }else{

    datos = "valor=2&tipoPer=E&dataType=K&data="+codigopersona+"&password="+pass+"&document="+docuempresa+"&companyDocType=D&companyDocNum="+ruc;
    
    datos = "?x="+btoa(datos);
  }
  datospost = {
                YII_CSRF_TOKEN: "<?php echo Yii::app()->request->csrfToken; ?>"
            };
 
  $.post("<?php echo Yii::app()->createUrl('site/cambioperfil'); ?>",
            datospost,
            function(data, status){
                        
                if(status == 'success'){
                    if(data.trim() == 'ok')              
                    
                    var login = "<?php echo Yii::app()->createUrl('site/login'); ?>";
                    window.location.href = login + datos; // Esta línea recarga la página actual.      
                
                   
                }
            });
}

</script>