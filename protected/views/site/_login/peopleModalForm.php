<?php $this->beginWidget('booster.widgets.TbModal', array('id'=>'peopleLoginModal')); ?>
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4><?php echo Yii::t('login', 'DATOS DE ACCESO') ?></h4>
</div>

<div class="modal-body">

    <?php
        $model = new LoginForm;
        $form = $this->beginWidget('booster.widgets.TbActiveForm', array(
            'id'=>'people-login-form',
            'action'=>array('/site/login'),
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

        <?php echo $form->textFieldGroup($model, '[1]document', array(
            'widgetOptions'=>array('htmlOptions'=>array(
                'autocomplete'=>'off',
                'placeholder'=>'',
            ))
        )); ?>

        <?php echo $form->dropDownListGroup($model, '[1]dataType', array(
            'widgetOptions' => array(
                'data' => $model->getDataTypeOptions('P'),
            ),
        )); ?>

        <?php echo $form->textFieldGroup($model, '[1]data', array(
            'widgetOptions'=>array('htmlOptions'=>array(
                'autocomplete'=>'off',
                'placeholder'=>'',
            ))
        )); ?>

        <?php echo $form->passwordFieldGroup($model, '[1]password', array(
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

    <?php echo CHtml::hiddenField('form', 1); ?>

    <?php $this->endWidget(); ?>
</div>

<div class="modal-footer">
    <?php $this->widget('booster.widgets.TbButton', array(
        'context'=>'primary',
        'label'=>Yii::t('login', 'Acceder'),
        'htmlOptions'=>array(
            'onclick' => '$("#people-login-form").submit()',
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
