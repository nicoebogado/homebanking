<?php

class ExtractOnRequestForm extends FormModel
{
	public $month;
	public $pdfOpt;
	public $printOpt;
	public $viewOpt;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('month', 'required'),
			array('pdfOpt, printOpt, viewOpt', 'safe'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'month'=>Yii::t('commons', 'Mes'),
		);
	}

	public function formConfig()
	{
		return array(
			//'title'=> 'Seleccione una cuenta',
			'showErrorSummary'=>false,
			//'target' => '_blank',
			'elements'=>array(
				'month'=>array(
                    'type'          => 'dropdownlist',
                    'items'         => $this->_monthOptions(),
                ),
			),
			'buttons'=> array(
				'ExtractOnRequestForm[viewOpt]'=> array(
					'buttonType'	=> 'submit',
					'context'		=> 'primary',
					'label'			=> Yii::t('extractOnRequest', 'Ver Extracto'),
					'htmlOptions'	=> array(
						'value' 	=> 1,
					),
				),
				'ExtractOnRequestForm[pdfOpt]'=> array(
					'buttonType'	=> 'submit',
					'context'		=> 'default',
					'label'			=> Yii::t('extractOnRequest', 'Descargar PDF'),
					'htmlOptions'	=> array(
						'value' 	=> 1,
					),
				),
			),
		);
	}

	public function formHiddenConfig()
	{
		return array(
			//'title'=> 'Seleccione una cuenta',
			'showErrorSummary'=>false,
			//'target' => '_blank',
			'elements'=>array(
				'month'=>array(
					'type'=>'hidden',
				),
			),
			'buttons'=> array(
				'ExtractOnRequestForm[pdfOpt]'=> array(
					'buttonType'	=> 'submit',
					'context'		=> 'primary',
					'label'			=> Yii::t('extractOnRequest', 'Descargar PDF'),
					'htmlOptions'	=> array(
						'value' 	=> 1,
					),
				),
			),
		);
	}

    private function _monthOptions()
    {
        $response = [];

        $start = new DateTime();
        $start->modify('-6 month');
        $start->modify('first day of next month');

        $end = new DateTime();
        $end->modify('first day of next month');

        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            $response[$dt->format('Y/m')] = strftime('%B', $dt->getTimestamp()).' '.$dt->format('Y');
        }

        return $response;
    }
}
