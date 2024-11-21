<?php
$count=0;
if(isset($frequent->listatransferencias->array)){
	$count=count($frequent->listatransferencias->array);
}
?>
<script type="text/javascript">
	var URL='<?php echo Yii::app()->createUrl('transfer/getAccountDesc'); ?>';
	var total=<?php echo $count ?>;
	var cant=Math.abs(total - 2);
	var height=320+(35*cant);
</script>
<?php
HScript::register('transfer/form.min');
HScript::register('plugins/jquery.mask.min');
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('transfers', 'Transferencias Entre Cuentas');
?>

<h3>
	<?php echo Yii::t('transfers', 'Transferencias Entre Cuentas'); ?>
</h3>

<?php
	echo $this->renderPartial('/commons/_wizardForm', array(
		'form'=>$form,
		'wizardOptions'=>$wizardOptions,
		'frequentAccountsNormal'=>$frequent,
        'isSipap' => false,
	));

?>
