<?php $this->beginWidget('booster.widgets.TbModal', array('id'=>'legalLoginModal')); ?>
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4><?php echo Yii::t('login', 'DATOS DE ACCESO') ?></h4>
</div>

<div class="modal-body">

	<?php
		$model = new LoginForm;
		$form = $this->beginWidget('booster.widgets.TbActiveForm', array(
			'focus'=>array($model,'username'),
			'action'=>array('/site/login'),
			'id'=>'legal-login-form',
			'type'=>'horizontal',
		));
	?>

<!--
  Fix
  Objetivo : Evitar el autocomplete de chrome o el almacenamiento de contraseñas.
  Autor : Javier Mendoza
-->
<input style="display:none" type="text" name="fakeusernameremembered"/>
<input style="display:none" type="password" name="fakepasswordremembered"/>
<!-- Fin Fix -->

        <?php echo $form->textFieldGroup($model, '[2]document', array(
            'widgetOptions'=>array('htmlOptions'=>array(
                'autocomplete'=>'off',
                'placeholder'=>'',
            ))
        )); ?>

		<?php echo $form->dropDownListGroup($model, '[2]dataType', array(
			'widgetOptions' => array(
				'data' => $model->getDataTypeOptions(),
			),
		)); ?>

		<?php echo $form->textFieldGroup($model, '[2]data', array(
			'widgetOptions'=>array('htmlOptions'=>array(
				'autocomplete'=>'off',
				'placeholder'=>'',
			))
		)); ?>

		<?php echo $form->dropDownListGroup($model, '[2]companyDocType', array(
			'widgetOptions' => array(
				'data' => $model->getDataTypeOptions('L'),
			),
		)); ?>

		<?php echo $form->textFieldGroup($model, '[2]companyDocNum', array(
			'widgetOptions'=>array('htmlOptions'=>array(
				'autocomplete'=>'off',
				'placeholder'=>'',
			))
		)); ?>

		<?php echo $form->passwordFieldGroup($model, '[2]password', array(
			'widgetOptions'=>array('htmlOptions'=>array(
				'autocomplete'=>'off',
        'class' => 'secureKeypadInput',
				'placeholder'=>'',
			))
		)); ?>

	<?php $this->widget('booster.widgets.TbButton', array(
		'buttonType'=>'submit',
		'context'=>'primary',
		'label'=>Yii::t('login', 'Acceder'),
		'htmlOptions'=>array('style'=>'display:none;'),
	)); ?>

	<?php echo CHtml::hiddenField('form', 2); ?>

	<?php $this->endWidget(); ?>
</div>

<div class="modal-footer">
	<?php $this->widget('booster.widgets.TbButton', array(
		'context'=>'primary',
		'label'=>Yii::t('login', 'Acceder'),
		'htmlOptions'=>array(
			'onclick'=>'$("#legal-login-form").submit()',
		),
	)); ?>
    <?php $this->widget('booster.widgets.TbButton', array(
    	'context'=>'danger',
        'label'=>Yii::t('login', 'Cancelar'),
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>

<?php $this->endWidget(); ?>
