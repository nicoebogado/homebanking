<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('loans', 'Consulta de Solicitudes de Préstamos');
?>
<h3>
    <?php echo Yii::t('loans', 'Consulta de Solicitudes de Préstamos'); ?>
</h3>
<div class="panel panel-default">
    <div class="panel-body">
        <style media="screen">
            #formDate td{padding: 5px;}
            #formDate {margin: 20px;}
        </style>
        <center>
            <?= CHtml::form() ?>
                <table id="formDate">
                    <tr>
                        <td>
                            <?php echo Yii::t('commons','Desde'); ?>:
                        </td>
                        <td>
                            <?php
                            $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                            'name'=>'fromDate',
                            'options'=>array('dateFormat'=>'dd/mm/yy',),
                            'value'=>$fromDate,
                            'htmlOptions'=>array(
                            'id'=>'fromDate',
                            'class'=>"form-control",
                            ),
                            ));
                            ?>
                        </td>
                        <td>
                            <?php echo Yii::t('commons','Hasta'); ?>:
                        </td>
                        <td>
                            <?php
                            $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                            'name'=>'toDate',
                            'options'=>array('dateFormat'=>'dd/mm/yy',),
                            'value'=>$toDate,
                            'htmlOptions'=>array(
                            'id'=>'toDate',
                            'class'=>"form-control",
                            ),
                            ));
                            ?>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Desde Solicitud:
                        </td>
                        <td>
                            <?php
                            echo CHtml::textField('fromApplicationId', $fromApplicationId, array('class'=>'form-control',
                            'width'=>100,
                            'maxlength'=>100));
                            ?>
                        </td>
                        <td>
                            Hasta Solicitud:
                        </td>
                        <td>
                            <?php
                            echo CHtml::textField('toApplicationId', $toApplicationId, array('class'=>'form-control',
                            'width'=>100,
                            'maxlength'=>100));
                            ?>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Estados:
                        </td>
                        <td colspan="3">
                            <?php
                            echo CHtml::dropDownList('status',$status,
                            array('I'=>'Todos','1' => 'Ingresado', '2' => 'Aprobado'),
                            array('class'=>'form-control'));
                            ?>
                        </td>
                        <td>
                            <?php
                            echo CHtml::htmlButton(Yii::t('commons','Ver'),array(
                            'submit' => array('loanApplication/queryForm'),
                            "id"=>'chtmlbutton',
                            'class'=>'btn btn-primary'));
                            ?>
                        </td>
                    </tr>
                </table>
            <?= CHtml::endForm() ?>
        </center>
        <?php $this->widget('booster.widgets.TbGridView', array(
        'type'=>'striped condensed',
        'dataProvider'=>$dataProvider1,
        'enableSorting'=>false,
        'template'=>'{items}{pager}',
        'selectableRows'=>0,
        'columns'=>array(
        array(
        'name'=>Yii::t('commons', 'Nro Solicitud'),
        'value'=>'$data->numerosolicitud',
        ),
        array(
        'name'=>Yii::t('sipapTransfer', 'Fecha'),
        'value'=>'$data->fechasolicitud',
        ),
        array(
        'name'=>Yii::t('sipapTransfer', 'Plazo'),
        'value'=>'$data->plazo',
        ),
        array(
        'name'=>Yii::t('sipapTransfer', 'Monto'),
        'value'=>'$data->importe',
        ),
        array(
        'name'=>Yii::t('transfers', 'Sucursal'),
        'value'=>'$data->sucursal',
        ),
        array(
        'class'=>'booster.widgets.TbButtonColumn',
        'template'=>'{pay}',
        'buttons'=>array(
        'pay'=>array(
        'label'=>Yii::t('commons', 'Ver'),
        'url'=>'Yii::app()->createUrl("/sipapTransfers/detail", array("id"=>$data->numerosolicitud))',
        'icon'=>'fa fa-search',
        ),
        ),
        ),
        ),
        )); ?>
    </div>
</div>