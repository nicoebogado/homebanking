<?php
class RadioGridInput extends CInputWidget
{
	private $_gridItems;

	public function run()
	{
		list($name, $id) = $this->resolveNameID();
		$this->widget('ext.selgridview.BootSelGridView', array(
			'id' => $id,
			'type'=>'striped condensed hover',
			'template' => '{items}{pager}',
			'dataProvider' => $this->_getDataProvider(),
			'selectableRows' => 1,
			'columns' => CMap::mergeArray(
				$this->gridItems['columns'],
				array(
					array(
						'header'=>Yii::t('commons', 'Opciones'),
						'type'=>'raw',
						'value' => 'CHtml::radioButton("'.$name.'", false, array(
							"value"=>$data["hash"],
							"id" => "'.$id.'_".$data["hash"],
						))',
					),
				)
			),
			'selectionChanged' => 'function(id){
				var rowSelected = $("#"+id).selGridView("getAllSelection");
				var selectBoxId = rowSelected[0] ? id+"_"+rowSelected[0] : null;
				$("#"+selectBoxId).attr("checked", true);
			}',
			'afterAjaxUpdate' => 'function(id, options){
				var idSelected = id+"_"+$("#" + id).selGridView("getAllSelection");
				$("#"+idSelected).attr("checked", true);
			}',
		));
	}

	public function getGridItems()
	{
		return $this->_gridItems;
	}

	public function setGridItems($items)
	{
		if(empty($items['columns']))
			throw new Exception('$item must have columns array', 1);
		if(!isset($items['datas']))
			throw new Exception('$item must have datas array', 1);

		$this->_gridItems = $items;
	}

	public function _getDataProvider()
	{
		return new CArrayDataProvider(
			$this->gridItems['datas'],
			array(
				'keyField'=>'hash',
				'pagination'=>array(
					'pageSize'=>8,
				),
			)
		);
	}
}