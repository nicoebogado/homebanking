<?php
HScript::register('user/updateDatas.min');

$this->pageTitle=Yii::app()->name . ' - '. Yii::t('updateProfile', 'Actualización de Datos');
?>

<h3><?php echo Yii::t('updateProfile', 'Actualización de Datos'); ?></h3>

<div class="form">
    <?php echo $this->renderPartial('/commons/_form', array('form'=>$form));?>
</div>

<!-- Modal -->
<div class="agi-modal">
  <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Formulario</h4>
        </div>
        <div class="modal-body" id="modal-form-body">
            Solicitando datos...
        </div>
      </div>
    </div>
  </div>
</div>