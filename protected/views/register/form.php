<script type="text/javascript">
	var distURL='<?php echo Yii::app()->createUrl('register/getDistrict');?>';
</script>
<?php
HScript::register('register/register');
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('register', 'Registro de Clientes');
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
<div style="width:60%;margin:0 auto;text-align:center">
  <?php echo HImage::html('logo_only.png', 'Logo', array(
    'class' => 'img-responsive',
    'style' => 'margin:20px auto; width:10%; height:auto;'
  )); ?>
  <h3 style="margin:20px auto;">
  	<?php echo Yii::t('register', 'Registro de Clientes'); ?>
  </h3>
<?php echo $this->renderPartial('/commons/_form', array('form'=>$form));?>
</div>
<script>

jQuery(document).ready(function($) {
	$("#RegisterForm_birthdate").attr("class","form-control");

  $('#map').click(function(event) {
		leftVal=(window.screen.availWidth - 800) / 2;
		topVal=(window.screen.availHeight - 600) / 2;
	  var WinPrint = window.open("<?php echo Yii::app()->createUrl('register/map') ?>", "Mapa", "left="+leftVal+",top="+topVal+",width=800,height=600,toolbar=0,scrollbars=0,status=0");
  })

  $("#RegisterForm").submit(function(e){
      return true;
  });
});

function HandlePopupResult(lat,long) {
    $("#RegisterForm_latitude").val(lat);
    $("#RegisterForm_longitude").val(long);
}

</script>
