<?php $form=$this->beginWidget('booster.widgets.TbActiveForm',array(
	'id'=>'change-access-pass-form',
	'enableAjaxValidation'=>false,
	'focus'=>array($model, 'currentPassword'),
)); ?>

	<p class="help-block">Campos con <span class="required">*</span> son requeridos.</p>

	<?php echo $form->passwordFieldGroup($model,'currentPassword',array('maxlength'=>100)); ?>

	<?php echo $form->passwordFieldGroup($model,'newPassword',array('maxlength'=>100)); ?>

	<?php echo $form->passwordFieldGroup($model,'repeatPassword',array('maxlength'=>100)); ?>

	<div class="form-actions">
		<?php $this->widget('booster.widgets.TbButton', array(
			'buttonType'=>'submit',
			'context'=>'primary',
			'label'=>'Enviar',
		)); ?>
		<?php $this->widget('booster.widgets.TbButton', array(
			'context'=>'danger',
			'label'=>'Cancelar',
			'url'=>array('/site/index')
		)); ?>
	</div>

<?php $this->endWidget(); ?>
