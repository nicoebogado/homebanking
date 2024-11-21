<?php $this->beginWidget('booster.widgets.TbModal', array('id'=>'perfilesmostrar')); ?>
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4><?php echo Yii::t('login', 'SELECCIONE PERFIL') ?></h4>
</div>

<div class="modal-body" id="listadeempresas">
<!--loading-->
<div class="custom-loader" id="espere"></div>
<!--Alertst-->
<!--<button type="button" class="btn btn-primary btn-lg btn-block">Empresa A</button>
<button type="button" class="btn btn-primary btn-lg btn-block">Empresa B</button>
<button type="button" class="btn btn-primary btn-lg btn-block">Empresa c</button>-->

</div>

<div class="modal-footer">

    <?php $this->widget('booster.widgets.TbButton', array(
        'context'=>'danger',
        'label'=>Yii::t('login', 'Cancelar'),
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>

<?php $this->endWidget(); ?>

<style>

.custom-loader{
  width: fit-content;
  font-weight: bold;
  font-family: monospace;
  font-size: 30px;
  clip-path: inset(0 3ch 0 0);
  animation: l4 1s steps(4) infinite;
}
.custom-loader:before {
  content:"Cargando..."
}
@keyframes l4 {to{clip-path: inset(0 -1ch 0 0)}}

</style>
