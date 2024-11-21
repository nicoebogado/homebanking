<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- To be sure it's using the latest rendering mode for IE -->
		<title><?php echo CHtml::encode($this->pageTitle); ?></title>
		<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="<?php echo HCss::url('bootstrap.css'); ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo HCss::url('styles.css', false); ?>" />
		<!--[if lt IE 9]>
		<script type="text/javascript" src="<?php echo HScript::url('respond/1.4.2/respond.min') ?>"></script>
		<script type="text/javascript" src="<?php echo HScript::url('html5shiv/3.7.2/html5shiv.min') ?>"></script>
		<![endif]-->
	</head>
	<body>
		
		<div class="fondotop">
			<?php if (!Yii::app()->user->isGuest): ?>
				<div class="pull-right" style="margin-right:13px; margin-top:25px; text-align:right;">
					<p><?php echo Yii::app()->user->getState('clientArea')['email']; ?></p>
					<p><?php echo Yii::app()->user->getState('clientArea')['phoneNumber']; ?></p>
					<p><?php echo Yii::app()->user->getState('clientArea')['address']; ?></p>
				</div>
			<?php endif ?>
		</div>
		
		<div class="logomain"><?php echo HImage::link('logocefisa.png', '#', array(
					'data-toggle'=>'modal',
					'data-target'=>'#index'
				)); ?></div>
		
		<?php if(!Yii::app()->user->isGuest) require '_menu.php'; ?>
		
		<div class="container">

			<?php $this->widget('booster.widgets.TbAlert');?>

			<div class="row">
				<?php echo $content; ?>
			</div>

			<hr />
			<footer>
				Copyright &copy; <?php echo date('Y'); ?><br/>
				<?php echo Yii::t('layouts', 'Todos los derechos reservados') ?><br/>
			</footer>
		</div>

		<span id="phplive_btn_1414176914" onclick="phplive_launch_chat_0(0)" style="color: #0000FF; text-decoration: underline; cursor: pointer; position:fixed; bottom:0; left:10px;"></span>
		<script type="text/javascript">
		(function() {
		var phplive_e_1414176914 = document.createElement("script") ;
		phplive_e_1414176914.type = "text/javascript" ;
		phplive_e_1414176914.async = true ;
		phplive_e_1414176914.src = "//t2.phplivesupport.com/norotaro/js/phplive_v2.js.php?v=0|1414176914|0|" ;
		document.getElementById("phplive_btn_1414176914").appendChild( phplive_e_1414176914 ) ;
		})() ;
		</script>
	</body>
</html>