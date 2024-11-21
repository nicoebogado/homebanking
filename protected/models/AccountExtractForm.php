<?php

class AccountExtractForm extends FormModel
{
    public $accountHash;
    public $month;
    public $pdfOpt;
    public $excel;
    public $printOpt;
    public $viewOpt;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('month, accountHash', 'required'),
            array('pdfOpt, printOpt, viewOpt, excel', 'safe'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'month'=>Yii::t('commons', 'Mes'),
            'accountHash'=>Yii::t('commons', 'Número de Cuenta'),
        );
    }

    public function formConfig($accountOptions, $account)
    {
        // la grilla de opciones de cuentas se debe mostrar
        // solamente si $accountOptions != false
        $accountHashConfig = $accountOptions == false ? array(
            'type'  => 'hidden',
        ) : array(
            'type'      => 'application.components.widgets.RadioGridInput',
            'gridItems' => $accountOptions,
        );

        return array(
            'title'=> empty($account) ? '' : $this->_accountNameTitle($account),
            'showErrorSummary'=>false,
            'elements'=>array(
                'accountHash'=> $accountHashConfig,
                'month'=>array(
                    'type'          => 'dropdownlist',
                    'items'         => $this->_monthOptions(),
                ),
            ),
            'buttons'=> array(
                'AccountExtractForm[viewOpt]'=> array(
                    'buttonType'    => 'submit',
                    'context'       => 'primary',
                    'label'         => Yii::t('extractOnRequest', 'Ver Extracto'),
                    'htmlOptions'   => array(
                        'value'     => 1,
                    ),
                ),
                'AccountExtractForm[pdfOpt]'=> array(
                    'buttonType'    => 'submit',
                    'context'       => 'default',
                    'label'         => Yii::t('extractOnRequest', 'Descargar PDF'),
                    'htmlOptions'   => array(
                        'value'     => 1,
                    ),
                ),
                'AccountExtractForm[excel]'=> array(
                    'buttonType'    => 'submit',
                    'context'       => 'default',
                    'label'         => Yii::t('extractOnRequest', 'Descargar Excel'),
                    'htmlOptions'   => array(
                        'value'     => 1,
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
                'accountHash'=>array(
                    'type'=>'hidden',
                ),
                'month'=>array(
                    'type'=>'hidden',
                ),
            ),
            'buttons'=> array(
                'AccountExtractForm[pdfOpt]'=> array(
                    'buttonType'    => 'submit',
                    'context'       => 'primary',
                    'label'         => Yii::t('extractOnRequest', 'Descargar PDF'),
                    'htmlOptions'   => array(
                        'value'     => 1,
                    ),
                ),
                'AccountExtractForm[excel]'=> array(
                    'buttonType'    => 'submit',
                    'context'       => 'primary',
                    'label'         => Yii::t('extractOnRequest', 'Descargar Excel'),
                    'htmlOptions'   => array(
                        'value'     => 1,
                    ),
                ),
            ),
        );
    }

    private function _accountNameTitle($cuenta)
    {
        return '<h3>Extracto para la cuenta '.
            $cuenta['currency'].' '.
            ((Yii::app()->params['maskedAccountNumber']=='N')?
                $cuenta['accountNumber']:
                $cuenta['maskedAccountNumber']
            ).
            $cuenta['denomination'].'</h3>';
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
