<?php

class SemiannualReport extends FormModel
{
    public $year;
    public $semester;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are required
            ['year, semester', 'required'],
        ];
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return [
            'year' => 'Año',
            'semester' => 'Semestre',
        ];
    }

    public function formConfig()
    {
        return [
            'showErrorSummary' => false,
            'elements' => [
                'year' => [
                    'type'  => 'dropdownlist',
                    'items' => $this->yearOptions(),
                ],
                'semester' => [
                    'type'  => 'dropdownlist',
                    'items' => [
                        1 => 'Primer',
                        2 => 'Segundo',
                    ],
                ],
            ],
            'buttons' => [
                'SemiannualReport[viewOpt]' => [
                    'buttonType'    => 'submit',
                    'context'       => 'primary',
                    'label'         => Yii::t('extractOnRequest', 'Ver Extracto'),
                    'htmlOptions'   => [
                        'value' => 1,
                    ],
                ],
            ],
        ];
    }

    protected function yearOptions()
    {
        $current = (int) date('Y');
        $before = $current - 1;
        return [
            $current => $current,
            $before => $before,
        ];
    }
}
