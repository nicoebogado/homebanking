<?php
/* @var $this SiteController */

$this->pageTitle = Yii::app()->name;

HScript::register([
	'layouts/_loginForm.min',
	'libs/emn178/md5.min',
	'libs/emn178/sha256.min',
	'plugins/jquery.secureKeypad.min',
	'commons/securizeKeypad.min',
]);

// Obtener el valor cifrado de la solicitud de la cookie
$valor = "";
$valor = base64_decode(@$_GET['x']);

if(@$valor != ""){

	$array = explode("&",$valor);
	//inicializamos por cualquier cosita
	$valor1 = $tipoPer = $dataType = $data = $password = $document = $companyDocType = $companyDocNum = "";
	$valor1 = explode("=",$array[0]);
	$tipoPer = explode("=",$array[1]);
	$dataType = explode("=",$array[2]);
	$data = explode("=",$array[3]);
	$password = explode("=",$array[4]);
	$document = explode("=",@$array[5]);
	$companyDocType = explode("=",@$array[6]);
	$companyDocNum = explode("=",@$array[7]);
	
} 	
?>

<?php if(@$valor1[1] == ""){ ?>
	
<div class="abs-center wd-xl" >

	<br /><br />
	<div class="text-center mb-xl well">
		<div class="text-center mb-xl">
			<?php echo HImage::html('logo.png', 'Logo', ['style' => 'width:280px;margin-bottom: -13%;']); ?>
		</div>
		<br />
		<?php if (isset($_GET['e'])) Yii::app()->user->setFlash('warning', base64_decode($_GET['e'])); ?>
		<?php $this->widget('booster.widgets.TbAlert'); ?>
		<?php
        $model = new LoginForm;
        $form = $this->beginWidget('booster.widgets.TbActiveForm', array(
            'id'=>'people-login-form',
            'action'=>array('/site/login'),
            'type'=>'horizontal',
        ));
    ?>

<!--
  Fix
  Objetivo : Evitar el autocomplete de chrome o el almacenamiento de contraseñas.
  Autor : Javier Mendoza
-->
<input style="display:none" type="text" name="fakeusernameremembered"/>
<input style="display:none" type="password" name="fakepasswordremembered"/>
<!-- Fin Fix -->

        <?php echo $form->textFieldGroup($model, '[1]data', array(
            'widgetOptions'=>array('htmlOptions'=>array(
                'autocomplete'=>'off',
                'placeholder'=>'',
            ))
        )); ?>


		<input style="display:none" type="text" class="form-control" value="D" name="LoginForm[1][dataType]" id="LoginForm_1_dataType">

     

        <?php echo $form->passwordFieldGroup($model, '[1]password', array(
            'widgetOptions'=>array('htmlOptions'=>array(
                'autocomplete'=>'off',
                'class' => 'secureKeypadInput',
                'placeholder'=>'',
            ))
        )); ?>

    <?php $this->widget('booster.widgets.TbButton', array(
        'buttonType'=>'submit',
        'context'=>'primary',
        'label'=>Yii::t('login', 'Acceder'),
        'htmlOptions'=>array('style'=>'display:none;'),
    )); ?>

    <?php echo CHtml::hiddenField('form', 1); ?>

    <?php $this->endWidget(); ?>

	<div class="row" style="padding: 2%; display:flex; justify-content:center">
	<div class="col" style="padding: 1%;">

	<?php $this->widget('booster.widgets.TbButton', array(
        'context'=>'primary',
        'label'=>Yii::t('login', 'Acceder'),
        'htmlOptions'=>array(
            'onclick' => '$("#people-login-form").submit()',
        ),
    )); ?>

</div><div class="col" style="padding: 1%;">
		<!--<div class="text-center p-lg">-->
		
		<p style="text-align: center;text-center: inter-word;font-size:x-medium;">
			<a href='#' data-toggle="modal" data-target="#recuperarAcceso" >Recuperar Contrase&ntildea</a>
		</p>
		</div></div>
			<p style="text-align: justify;text-justify: inter-word;font-size:x-small;">
				<?php echo Yii::t('layouts', 'No revele su clave de acceso, es de uso personal, privado, secreto e intransferible. Para todo efecto, será considerado como el equivalente a la firma electrónica. Recuerde que Usted es el único responsable de su manejo y adecuada aplicación por lo que libera a la Entidad de toda responsabilidad en caso de que sea utilizado por otras personas.') ?>
			</p>
			<p style="text-align: justify;text-justify: inter-word;font-size:x-small;">
				<b>
					<?php echo Yii::t('layouts', 'Tenga presente que la Entidad NUNCA solicitará vía telefónica o e-mail datos personales, número de cuenta, contraseña o ninguna otra información confidencial.') ?>
				</b>
			</p>
		<!--</div>-->
	</div>
</div>

<?php require '_login/recuperarAcceso.php'; ?>

<!--Backgroung-->
<style>
	.wrapper{
		background-image: url("/homebanking/themes/itgf_hb/img/homebanking-bg.jpg");
		background-repeat: no-repeat;
		background-size: 100% auto;
	}

	.abs-center{
		width: 80% ;
	}

	@media screen and (min-width: 750px) {
		.abs-center{
			width: 30% ;
		}
	}
</style>
<?php }else if(@$valor1[1] == 2){
	
	if($tipoPer[1] == 'E'){
		
 ?>
	<div class="container2">
	<div class="custom-loader"></div></div>
		<!--Cambios en el login-->
		<form class="form-horizontal" id="formempresa" action="<?php echo Yii::app()->createUrl('site/login'); ?>" method="post">
			<input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

			<input style="display:none" type="text" name="fakeusernameremembered">
			<input style="display:none" type="password" name="fakepasswordremembered">
			<!-- Fin Fix -->

			<input autocomplete="off" value="<?php echo $document[1]; ?>" placeholder="" class="form-control" name="LoginForm[2][document]" id="LoginForm_2_document" type="text">
			<input autocomplete="off" value="<?php echo $dataType[1]; ?>" placeholder="" class="form-control" name="LoginForm[2][dataType]" id="LoginForm[2][dataType]" type="text">
			<input autocomplete="off" value="<?php echo $data[1]; ?>" placeholder=""  class="form-control" name="LoginForm[2][data]" id="LoginForm_2_data" type="text">
			<input autocomplete="off" value="<?php echo $companyDocType[1]; ?>" placeholder=""  class="form-control" name="LoginForm[2][companyDocType]" id="LoginForm[2][companyDocType]" type="text">
			<input autocomplete="off" value="<?php echo $companyDocNum[1]; ?>" placeholder="" class="form-control" name="LoginForm[2][companyDocNum]" id="LoginForm_2_companyDocNum" type="text">
			<input autocomplete="off" value="<?php echo $this->decript256($password[1]); ?>"  placeholder="" class="form-control" name="LoginForm[2][password]" id="LoginForm_2_password" type="password" readonly="readonly">
			<button style="display:none;" class="btn btn-primary" id="yw6" type="submit" name="yt5">Acceder</button>
			<input type="hidden" value="2" name="form" id="form">
		</form>

<?php }else if($tipoPer[1] == 'P'){ ?>
	<div class="container2">
 <div class="custom-loader"></div></div>
		<!--Cambios en el login-->
		<form class="form-horizontal" id="formempresa" action="<?php echo Yii::app()->createUrl('site/login'); ?>" method="post">
			<input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

			<input style="display:none" type="text" name="fakeusernameremembered">
			<input style="display:none" type="password" name="fakepasswordremembered">
			<!-- Fin Fix -->

			<input autocomplete="off" value="<?php echo $dataType[1]; ?>" placeholder="" class="form-control" name="LoginForm[1][dataType]" id="LoginForm[2][dataType]" type="text">
			<input autocomplete="off" value="<?php echo $data[1]; ?>" placeholder=""  class="form-control" name="LoginForm[1][data]" id="LoginForm_1_data" type="text">
			<input autocomplete="off" value="<?php echo $this->decript256($password[1]); ?>"  placeholder="" class="form-control" name="LoginForm[1][password]" id="LoginForm_1_password" type="password" readonly="readonly">
			<button style="display:none;" class="btn btn-primary" id="yw6" type="submit" name="yt5">Acceder</button>
			<input type="hidden" value="1" name="form" id="form">
		</form>

<?php }} ?>
<style>
	#formempresa{
		display:none;
	}
	.container2 {
		display: flex;
		justify-content: center; /* Centrar horizontalmente */
		align-items: center; /* Centrar verticalmente */
		height: 100%;
	}
	.custom-loader{
		width: fit-content;
		font-weight: bold;
		font-family: monospace;
		font-size: 30px;
		clip-path: inset(0 3ch 0 0);
		animation: l4 1s steps(4) infinite;
		color:white;
		background-color: #c70c0c;
		padding: 20px;
		border-radius: 10px;
		text-align: center; /* Para centrar el contenido dentro del div */
		
		}
	.custom-loader:before {
		content:"Cargando..."
		}
		@keyframes l4 {to{clip-path: inset(0 -1ch 0 0)}
		}
</style>
<script>
$(document).ready(function(){
  //Llamar funcion que carga empresas
  var form = document.getElementById("formempresa");

// Hacer submit del formulario
form.submit();

});
</script>