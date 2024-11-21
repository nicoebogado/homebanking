<?php
HScript::register([
	'libs/emn178/md5.min',
	'libs/emn178/sha256.min',
	'plugins/jquery.secureKeypad.min',
	'commons/securizeKeypad',
]);
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('supplierPayment', 'Pagos a Proveedores');
?>
<h3>
	<?php echo Yii::t('supplierPayment', 'Pagos a Proveedores'); ?>
</h3>
<div class="form">
	<?php echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		var text = $("#PaymentToSuppliersForm_entityCode").find(":selected").text();
		text = text.split("#");
		var cta = text[1];
		setCta(cta);
		$("#PaymentToSuppliersForm_debitAccount td").click(function(event) {
			var cta = $(this).parent().find('td').eq(0).html();
			var val = $('#PaymentToSuppliersForm_entityCode option').filter(function() {
				return $(this).html().indexOf(cta) >= 0;
			}).val();
			$('#PaymentToSuppliersForm_entityCode').val(val);
		});
		$("#PaymentToSuppliersForm_entityCode").change(function(event) {
			var text = $(this).find(":selected").text();
			text = text.split('#');
			var cta = text[1];
			setCta(cta);
		});
	});

	function setCta(cta) {
		var tableRow = $("#PaymentToSuppliersForm_debitAccount td").filter(function() {
			return $(this).text() == cta;
		}).closest("tr");
		tableRow.html();
		tableRow.find("input[type=radio]").prop('checked', true);
	}
</script>