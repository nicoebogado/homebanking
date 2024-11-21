<?php

/**
 * MovementForm class.
 */
class MovementForm extends FormModel
{
	public $accountNumber;
	public $dateFrom;
	public $dateTo;
    public $viewOpt;
    public $pdfOpt;
    public $printOpt;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			['accountNumber', 'required', 'message'=>Yii::t('commons', 'Debe seleccionar una cuenta ')],
			['dateFrom, dateTo', 'required'],
			['viewOpt, pdfOpt, printOpt', 'safe'],
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'accountNumber'=>Yii::t('commons', 'Cuenta'),
			'dateFrom'=>Yii::t('movements', 'Fecha desde'),
			'dateTo'=>Yii::t('movements', 'Fecha hasta'),
		);
	}

	public function formConfig($accountOptions)
	{
		return array(
			'showErrorSummary'=>false,
			'elements'=>array(
				'accountNumber' => is_array($accountOptions) ? array(
					'type'=>'application.components.widgets.RadioGridInput',
					'gridItems'=>$accountOptions,
				) : [
					'type' => 'hidden',
				],
				'dateFrom'	=> [
					'type'			=> 'text',
					'groupOptions'	=> array('class'=>'col-md-6'),
				],
				'dateTo'	=> [
					'type'			=> 'text',
					'groupOptions'	=> array('class'=>'col-md-6'),
				],
			),
			'buttons'=>array(
				'MovementForm[viewOpt]'=> array(
                    'buttonType'    => 'submit',
                    'context'       => 'primary',
					'label'			=> Yii::t('movements', 'Ver Movimientos'),
                    'htmlOptions'   => array(
                        'value'     => 1,
                    ),
                ),
				'cancel'=>array(
					'buttonType'=>'link',
					'context'=>'danger',
					'label'=>Yii::t('commons', 'Cancelar'),
					'url'=>array('/site/index'),
				),
			),
		);
	}

	public function formHiddenConfig()
    {
        return array(
            'showErrorSummary'=>false,
            'elements'=>array(
                'accountHash'=>array(
                    'type'=>'hidden',
                ),
                'month'=>array(
                    'type'=>'hidden',
                ),
            ),
            'elements'=>array(
				'accountNumber' => [
					'type' => 'hidden',
				],
				'dateFrom' => [
					'type' => 'hidden',
				],
				'dateTo' => [
					'type' => 'hidden',
				],
			),
            'buttons'=> array(
                'MovementForm[pdfOpt]'=> array(
                    'buttonType'    => 'submit',
                    'context'       => 'primary',
                    'label'         => Yii::t('commons', 'Descargar PDF'),
                    'htmlOptions'   => array(
                        'value'     => 1,
                    ),
                ),
            ),
        );
    }
}
