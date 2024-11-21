<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('register', 'Activar Clave');
HScript::register([
  'libs/emn178/md5.min',
  'libs/emn178/sha256.min',
  'plugins/jquery.secureKeypad.min',
  'commons/securizeKeypad',
]);
?>
<style>
  .form-actions {
    clear: both;
    padding: 10px 0px 10px 15px;
  }
</style>
<div style="width:100%; position:absolute;">
  <?php $this->widget('booster.widgets.TbAlert', array(
    'htmlOptions' => array(
      'style' => 'width:50%;  margin: 10px auto;',
    ),
  )); ?>
</div>
<div style="width:40%;margin:0 auto;text-align:center">
  <?php echo HImage::html('logo_only.png', 'Logo', array(
    'class' => 'img-responsive',
    'style' => 'margin:20px auto; width:20%; height:auto;'
  )); ?>
  <h3 style="margin:20px auto;">
    <?php echo Yii::t('register', 'Activar Clave'); ?>
  </h3>
  <?php echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>
</div>