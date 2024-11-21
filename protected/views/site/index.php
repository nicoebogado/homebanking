<?php
$this->pageTitle="Bienvenido al HomeBanking - FIC";
?>
<h3>
	<?php echo Yii::t('accountSummary', 'Resumen de Cuentas'); ?>
</h3>

<?php if(isset($data)): ?>

<?php
	//If user have not token assigned, show message warnings
	$codigoEmpresa = Yii::app()->user->getState('codeToken');
	
	if(Yii::app()->user->getState('codeToken') == "811"): ?>
		<div class="alert alert-warning" role="alert">
			El cliente no tiene un token físico asignado [code:811]
		</div>
<?php endif; ?>

<?php
	//If user doesn't exists in token admin
	$codigoEmpresa = Yii::app()->user->getState('codeToken');
	if(Yii::app()->user->getState('codeToken') == "97"): ?>
		<div class="alert alert-warning" role="alert">
			El cliente no posee token físico para realizar operaciones [code:97]
		</div>
<?php endif; ?>

<div class="well  well-sm">
	<p>
		Estimado Cliente, cumplimos en informarle que vuestra calificación de Riesgo correspondiente al <?php echo isset($data->fecha)?$data->fecha:'01-01-2015'; ?> es <b><?php echo $data->calificacionriesgo.'-'.$data->descripcioncalificacion; ?></b>; conforme a los criterios definidos por la Resolución N° 1, acta N°60 de fecha 28 de septiembre de 2007, del Directorio del Banco Central del Paraguay.
	</p>
</div>
<?php endif; ?>

<?php Yii::app()->user->accounts->refresh(); ?>;

<?php

	$tabs = array(
			array(
				'label' => '<em class="icon-drawer"></em> '.Yii::t('accountSummary', 'Cuentas de Ahorro'),
				'content' => $this->widget('zii.widgets.CListView',
				array(
					'dataProvider'=>$dataProvider['AH'],
					'itemView'=>'_accountDetail',
					'template' => '{items}{pager}',
					'pagerCssClass' => 'no-class',
					'pager' => array('class' => 'booster.widgets.TbPager'),
				),
				true),
				'active' => true,
			),
			array(
				'label' => '<em class="icon-credit-card"></em> '.Yii::t('accountSummary', 'Tarjetas de Crédito'),
				'content' => $this->widget('zii.widgets.CListView',
				array(
					'dataProvider'=>$dataProvider['TJ'],
					'itemView'=>'_accountDetail',
					'template' => '{items}{pager}',
				),
				true),
			),
			array(
				'label' => '<em class="icon-briefcase"></em> '.Yii::t('accountSummary', 'Préstamos'),
				'content' => $this->widget('zii.widgets.CListView',
				array(
					'dataProvider'=>$dataProvider['PT'],
					'itemView'=>'_accountDetail',
					'template' => '{items}{pager}',
				),
				true),
			),
	);


	// Elimina los tabs de Ahorros y Tarjetas para tipo de implementación diferente a Casas de Préstamos
	if( isset( Yii::app()->user->deploymentType ) && Yii::app()->user->deploymentType=='9999' ){

		array_shift($tabs);
		array_shift($tabs);
		$prestamo=$tabs[0];
		$prestamo['active']=true;
		$tabs[0]=$prestamo;

	}

	$this->widget(
  	'booster.widgets.TbTabs',
    array(
        'type' => 'tabs',
        'justified' => true,
        'encodeLabel' => false,
        'htmlOptions' => array(
        	'class' => 'panel',
        ),
        'tabs' => $tabs,
    )
	);

?>
