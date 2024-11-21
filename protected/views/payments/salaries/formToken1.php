<?php
HScript::register([
    'libs/emn178/md5.min',
    'libs/emn178/sha256.min',
    'plugins/jquery.secureKeypad.min',
    'commons/securizeKeypad',
    'payments/form',
]);
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('salaryPayment', 'Pagos de Salarios');
?>

<h3>
    <?php echo Yii::t('salaryPayment', 'Pagos de Salarios'); ?>
</h3>

<!--<div class="form">-->

<div class="panel panel-default">
<div class="panel-body">
	<form id="salaryForm"  enctype="multipart/form-data" class="form-horizontal" action="<?php echo Yii::app()->createUrl('payments/salariesVerification'); ?>" method="post">
		<input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
		<div style="display:none">
			<input type="hidden" value="1" name="yform_salary-payment-form" id="yform_salary-payment-form">
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label required" for="SalaryPaymentForm_entityCode">Empresa <span class="required">*</span>
			</label>
		<div class="col-sm-9">
				
			<select class="form-control" placeholder="Empresa" name="SalaryPaymentForm[entityCode]" id="SalaryPaymentForm_entityCode">
				
				<?php foreach($entities as $data => $key){ ?>
					<option value="<?php echo $data; ?>"><?php echo $key; ?></option>	
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label required" for="SalaryPaymentForm_remunerationType">Tipo de Remuneración <span class="required">*</span>
		</label>
		<div class="col-sm-9" style="padding-left:33px !important;">
			<input id="ytSalaryPaymentForm_remunerationType" type="hidden" value="" name="SalaryPaymentForm[remunerationType]"><span id="SalaryPaymentForm_remunerationType"></span>
				<label class="radio">
					<input placeholder="Tipo de Remuneración" id="SalaryPaymentForm_remunerationType_0" value="S" type="radio" name="SalaryPaymentForm[remunerationType]">Sueldo
				</label>
				<label class="radio">
					<input placeholder="Tipo de Remuneración" id="SalaryPaymentForm_remunerationType_1" value="A" type="radio" name="SalaryPaymentForm[remunerationType]">Aguinaldo
				</label>
				<!--<label class="radio">
					<input placeholder="Tipo de Remuneración" id="SalaryPaymentForm_remunerationType_2" value="C" type="radio" name="SalaryPaymentForm[remunerationType]">Comisión
				</label>
				<label class="radio">
					<input placeholder="Tipo de Remuneración" id="SalaryPaymentForm_remunerationType_3" value="T" type="radio" name="SalaryPaymentForm[remunerationType]">Tarjeta
				</label>-->
				
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label required" for="SalaryPaymentForm_recordsReadNumber">Cantidad de Registros a Leer <span class="required">*</span>
		</label>
		<div class="col-sm-9">
			<input class="form-control" placeholder="Cantidad de Registros a Leer" name="SalaryPaymentForm[recordsReadNumber]" id="SalaryPaymentForm_recordsReadNumber" type="text">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label required" for="SalaryPaymentForm_totalAmount">Monto Total en Guaraníes <span class="required">*</span>
		</label>
		<div class="col-sm-9">
			<input class="form-control" required  placeholder="Monto Total en Guaraníes" name="SalaryPaymentForm[totalAmount]" id="SalaryPaymentForm_totalAmount" type="text">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="SalaryPaymentForm_currencyCode"></label>
		<div class="col-sm-9">
			<input value="GS" required style="display:none" class="form-control" placeholder="" name="SalaryPaymentForm[currencyCode]" id="SalaryPaymentForm_currencyCode" type="text">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label required" for="SalaryPaymentForm_controlMode">Método de Carga <span class="required">*</span>
		</label>
		<div class="col-sm-9">
			<select class="form-control" placeholder="Método de Carga" name="SalaryPaymentForm[controlMode]" id="SalaryPaymentForm_controlMode">
				<option value="V">Archivo</option>
				<option value="L">Carga Manual</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<span class="col-sm-3">
		</span>
		<div class="col-sm-9">
			<input id="ytSalaryPaymentForm_paymentFile" type="hidden" value="" name="SalaryPaymentForm[paymentFile]">
			<input class="form-control" placeholder="Payment File" name="SalaryPaymentForm[paymentFile]" id="SalaryPaymentForm_paymentFile" type="file">
		</div>
	</div>
	<div class="form-actions">
		<button name="submit" class="btn btn-primary" id="yw0" type="submit">Enviar</button>
	</div>
	</form>
</div>

</div>

<script>
$(document).ready(function(){
  
	$("#SalaryPaymentForm_controlMode").change(function(){

		if($(this).val() == 'L'){
			$("#salaryForm").attr('action', '<?php echo Yii::app()->createUrl('payments/salariesManualLoading'); ?>');
		}else if($(this).val() == 'V'){
			$("#salaryForm").attr('action', '<?php echo Yii::app()->createUrl('payments/salariesVerification'); ?>');
		}
  	});

});
</script>