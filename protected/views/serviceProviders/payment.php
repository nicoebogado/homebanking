<h3><?php echo Yii::t('serviceProviders', 'Pago de Servicios'); ?></h3>
<?php
	/*  el nro de cuenta viene en la variable php $debitAccount  */
	$ct="0000";
	$dv="0";
	$ns="000";
	$id="000";
	$us="fic";
	$ps="c3f_54.784";
	$ip=$_SERVER['REMOTE_ADDR'];;
	$email="";
?>
<script type="text/javascript">
	$( document ).ready(function() {
		$("#initForm").submit();
	});
</script>
<form style="display:none;" id="initForm" action="https://secure.ticpro.net/pagosWeb/servpagos.php" method="post" target="formPost">
	<input name="ct" type="hidden" value="<?php echo $ct ?>">
	<input name="dv" type="hidden" value="<?php echo $dv ?>">
	<input name="ns" type="hidden" value="<?php echo $ns ?>">
	<input name="id" type="hidden" value="<?php echo $id ?>">
	<input name="us" type="hidden" value="<?php echo $us ?>">
	<input name="ps" type="hidden" value="<?php echo $ps ?>">
	<input name="ip" type="hidden" value="<?php echo $ip ?>">
	<input name="email" type="hidden" value="<?php echo $email ?>">
</form>
<iframe id="pronetPagosWeb" height="980px" width="100%" frameborder="0" name="formPost" style="border:none;"></iframe>
