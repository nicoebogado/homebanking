<?php
Yii::import('booster.widgets.TbForm');

class ArrayGridInput extends CInputWidget
{
	private $_assetsUrl;
	private $_modelCollection;
	private $_containerId;
    private $_columns;
    private $_formUrl;
    private $_extraContent;

	public function init()
	{
		parent::init();

		if ($this->_assetsUrl === null)
			$this->_assetsUrl = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.components.widgets.assets'));
    }

	public function run()
	{
		$this->registerClientScript();

		list($name, $id) = $this->resolveNameID();
		$this->widget('booster.widgets.TbGridView', array(
			'id' => $id,
			'type'=>'striped condensed hover',
			'template' => '{items}{pager}',
			'dataProvider' => $this->_getDataProvider(),
			'columns' => $this->columns,
			'htmlOptions' => [
				'class' => 'col-sm-9 agi-table',
                'data-formurl' => $this->_formUrl,
			],
            'emptyText' => null,
		));

		$this->render('array-grid-input-footer', [
            'containerId'    => $this->_containerId,
			'url'            => $this->_formUrl,
            'extraContent'   => $this->_extraContent,
		]);
	}

    public function setColumns($config)
    {
        $this->_columns = $config;
    }

    public function getColumns()
    {
        $columns = $this->_columns;
        $columns['options'] = [
            'name' => 'options',
            'header' => '',
            'type' => 'raw',
        ];

        return $columns;
    }

    public function setModelCollection($collection)
    {
        $this->_modelCollection = $collection;
    }

    public function setContainerId($id)
    {
        $this->_containerId = $id;
    }

    public function setFormUrl($url)
    {
        $this->_formUrl = $url;
    }

    public function setExtraContent($content)
    {
        $this->_extraContent = $content;
    }

	public function _getDataProvider()
	{
        $rows = [];
        foreach ($this->_modelCollection as $model) {
            $rows[] = $this->_getRowArray($model);
        }

		return new CArrayDataProvider(
			$rows,
			array(
				'keyField'		=> false,
				'pagination'	=> false,
			)
		);
	}

	private function _getRowArray($model)
	{
		$row = [];

		foreach($this->_columns as $column => $value) {
			$row[$column] = $model->renderColumn($column);
		}

		$buttonId = 'btn-editar-'.$this->_containerId.'-'.$model->id;

		// agregar columna con botón Editar
        $buttons = CHtml::tag('button', [
            'id'            => $buttonId,
            'type'          => 'button',
            'title'         => 'Editar',
            'class'         => 'btn btn-primary editar-fila',
            'data-toggle'   => 'modal',
            'data-target'   => '#modal-form',
            'data-formurl'  => Yii::app()->createUrl($this->_formUrl),
            'data-formdatas'=> json_encode([
                'containerId'   => $this->_containerId,
                'model'         => base64_encode(json_encode($model)),
                'modelId'       => $model->id,
            ]),
        ], '<i class="icon-pencil"></i>');

        if ($model->canDelete) {
            $buttons .= CHtml::tag('button', [
                'type'          => 'button',
                'title'         => 'Eliminar',
                'class'         => 'btn btn-danger eliminar-fila',
            ], '<i class="icon-close"></i>');
        }

        $row['options'] = $buttons;

		return $row;
	}

	public function registerClientScript()
	{
		Yii::app()->clientScript->registerScriptFile($this->_assetsUrl . '/arrayGridInput.min.js');
	}
}
