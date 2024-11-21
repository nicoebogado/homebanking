
<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('register', 'Crear Clave');
?>
<style>
  .form-actions{clear:both;padding:10px 0px 10px 15px;}
</style>
<div style="width:100%; position:absolute;">
  <?php $this->widget('booster.widgets.TbAlert', array(
    'htmlOptions'=> array(
      'style'=>'width:50%;  margin: 10px auto;',
    ),
  ));?>
</div>
<div style="width:55%;margin:0 auto;text-align:center">
  <?php echo HImage::html('logo_only.png', 'Logo', array(
    'class' => 'img-responsive',
    'style' => 'margin:20px auto; width:20%; height:auto;'
  )); ?>
  <h3 style="margin:20px auto;">
  	<?php echo Yii::t('register', 'Crear Clave'); ?>
  </h3>
<?php echo $this->renderPartial('/commons/_form', array('form'=>$form));?>
</div>
