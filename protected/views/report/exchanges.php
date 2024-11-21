<?php

$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('exchanges', 'Cotizaciones');
?>

<h3><?php echo Yii::t('exchanges', 'Cotizaciones'); ?></h3>
<div class="panel panel-default">
    <div class="panel-body">
        <?php

            $elements=array(
                'moneda:text:'.Yii::t('commons', 'Moneda'),
                array(
                    'name'=>Yii::t('exchanges', 'Compra en efectivo'),
                    'value'=>'Yii::app()->numberFormatter->formatDecimal($data->efectivocompra)',
					'htmlOptions'=>array(
						'style'=>'text-align: right;'
                ),
				),
                array(
                    'name'=>Yii::t('exchanges', 'Venta en efectivo'),
                    'value'=>'Yii::app()->numberFormatter->formatDecimal($data->efectivoventa)',
					'htmlOptions'=>array(
						'style'=>'text-align: right;'
                ),
				),
            );

            $bancaElectronica=array(
                array(
                    'name'=>Yii::t('exchanges', 'Compra electrónica'),
                    'value'=>'Yii::app()->numberFormatter->formatDecimal($data->electronicacompra)',
					'htmlOptions'=>array(
						'style'=>'text-align: right;'
                ),
				),
                array(
                    'name'=>Yii::t('exchanges', 'Venta electrónica'),
                    'value'=>'Yii::app()->numberFormatter->formatDecimal($data->electronicaventa)',
					'htmlOptions'=>array(
						'style'=>'text-align: right;'
					),
                ),
            );

            if( isset( Yii::app()->user->deploymentType ) && Yii::app()->user->deploymentType!='9999' ){
                $elements=array_merge($elements,$bancaElectronica);
            }

            $this->widget('booster.widgets.TbGridView', array(
            'type'=>array('striped'),
            'dataProvider'=>$dataProvider,
            'enableSorting'=>false,
            'template'=>'{items}{pager}',
            'selectableRows'=>0,
            'columns'=>$elements,
        ));

        ?>

    </div>
</div>
