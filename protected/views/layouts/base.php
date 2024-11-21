<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language ?>">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge"><!-- To be sure it's using the latest rendering mode for IE -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo HCss::url('fontawesome/css/font-awesome.min.css'); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo HCss::url('simple-line-icons/css/simple-line-icons.css'); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo HCss::url('animate.css/animate.min.css'); ?>" />
	<link rel="stylesheet" type="text/css" id="maincss" href="<?php echo HCss::url('app.css?v=1'); ?>" />
	<link rel="stylesheet" type="text/css" id="maincss" href="<?php echo HCss::url('backend.css?v=1'); ?>" />

	<!--[if lt IE 9]>
	<script type="text/javascript" src="<?php echo HScript::url('respond/1.4.2/respond.min') ?>"></script>
	<script type="text/javascript" src="<?php echo HScript::url('html5shiv/3.7.2/html5shiv.min') ?>"></script>
	<![endif]-->
	<script>
	    try {
	        if (window.self !== window.top) {
	        	top.location = window.location;
	        }
	    } catch (e) {
	        window.location = '<?= Yii::app()->createUrl('site/showPermissionError') ?>';
	    }
	</script>
</head>
	<body>
		<div class="wrapper">
			<?php echo $content; ?>
		</div>
	</body>
</html>
