<script type="text/javascript">
	var initURL='<?php echo Yii::app()->createUrl('updateProfile/getInitData');?>';
	var distURL='<?php echo Yii::app()->createUrl('updateProfile/getDistrict');?>';
</script>
<?php
HScript::register('updateProfile/data');
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('updateProfile', 'Actualización de Datos');
?>
<h3>
	<?php echo Yii::t('updateProfile', 'Actualización de Datos'); ?>
</h3>
<?php echo $this->renderPartial('_form', array('form'=>$form));?>
