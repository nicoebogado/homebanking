<?php

Yii::import('application.models.traits.HasDetectIdInput');



class BancardServiceForm extends FormModel

{

    use HasDetectIdInput;



    /**

     * Objeto service que representa al servicio seleccionado por el usuario

     */

    public $service;



    /**

     * Objeto payment que representa la respuesta al pago

     */

    public $payment;



    /**

     * Objeto bill (factura) que el usuario selecciona para pagar

     */

    public $bill;



    /**

     * String en formato JSON del objeto bill que el usuario selecciona para pagar.

     * Es utilizado para enviar el objeto por medio de formularios HTML

     */

    public $billJSON;



    /**

     * Objeto commission que representa a la comisión que el usuario debe pagar

     */

    public $commission;



    public $amount;

    public $account;



    public $formConfig = [];



    public $_attributeLabels = [

        'amount' => 'Monto',

        'account' => 'Cuenta para el pago',

    ];



    private $_serviceEncode;

    private $_rules = [

        ['amount', 'required', 'on' => 'payBill'],

        ['account', 'required', 'on' => 'payBill'],

    ];

    private $_formElements = [];

    private $_dynamicFields = [];

    private $_commissionFormElements = [];



    private $_accountData;

    private $_bill_fields = [];

    private $_customer_fields = [];

    private $_additional_data_fields = [];

    private $_commission_bill_fields = [];

    private $_customer_temporary_fields = [];



    /**

     * Declares the validation rules.

     */

    public function rules()

    {

        return $this->addDetectIdInputRules($this->_rules);

    }



    /**

     * Declares attribute labels.

     */

    public function attributeLabels()

    {

        return $this->addDetectIdAttributeLabels($this->_attributeLabels);

    }



    public function initFromService($serviceObj)

    {

        $this->service = $serviceObj;



        foreach ($serviceObj->fields as $k => $field) {

            $this->_addField('field' . $k, $field, $serviceObj->temporary_identification);

        }



        foreach ($serviceObj->commission_bill_fields as $k => $field) {

            $this->_addCommissionField('commission' . $k, $field);

        }



        if (!$serviceObj->queries_debt) {

            // agregar monto al formulario

            $this->_formElements = $this->_addAmountElementToFormConfig($this->_formElements, [

                'type'      => 'text',

                'prepend'   => 'Gs.',

                'hint'      => $serviceObj->amount_hint,

                'visible'   => true,

            ]);

        }



        $this->formConfig = [

            'elements' => $this->_formElements,

            'buttons' => [

                'submit' => array(

                    'id' => 'submit',

                    'buttonType' => 'submit',

                    'context' => 'primary',

                    'label' => Yii::t('bancard', 'Siguiente'),

                ),

                'cancel' => array(

                    'context' => 'danger',

                    'buttonType' => 'link',

                    'label' => Yii::t('commons', 'Cancelar'),

                    'url' => array('/bancard/list'),

                ),

            ],

        ];

    }



    public function detailsConfig()

    {

        $config = [];



        foreach ($this->_dynamicFields as $field => $value) {

            if ($field == 'serviceEncode' || empty($value)) continue;

            $config[] = [

                'name' => $field,

                'value' => $this->{$field},

            ];

        }



        // agregar cuenta débito

        $config[] = [

            'label' => 'Cuenta Débito',

            'value' => ((Yii::app()->params['maskedAccountNumber'] == 'N') ?

                $this->accountData['accountNumber'] :

                $this->accountData['maskedAccountNumber'])

                . ' ' . $this->accountData['denomination'],

        ];



        // agregar monto a pagar

        $config[] = [

            'label' => Yii::t('bancard', 'Importe'),

            'value' => 'Gs. ' . Yii::app()->numberFormatter->formatDecimal($this->amount),

        ];



        return $config;

    }



    public function commissionDetailsConfig()

    {

        $config = $this->detailsConfig();

        $config[] = [

            'label' => 'Comisión',

            'value' => 'Gs. ' . Yii::app()->numberFormatter->formatDecimal($this->commission),

        ];



        // agregar monto total a pagar

        $config[] = [

            'label' => Yii::t('bancard', 'Total a Pagar'),

            'type' => 'raw',

            'value' => '<b>Gs. ' . Yii::app()->numberFormatter->formatDecimal($this->amount + $this->commission) . '</b>',

        ];



        return $config;

    }



    public function voucherDetailsConfig($brandName)

    {

        return [

            [

                'label' => 'Nro. de Boleta',

                'value' => $this->payment->ticket_number,

            ],

            [

                'label' => 'Facturador',

                'value' => $brandName,

            ],

            [

                'label' => 'Servicio',

                'value' => $this->service->name,

            ],

            [

                'label' => 'Identificador del cliente',

                'value' => is_array($this->payment->additional_data) ? implode(', ', $this->payment->additional_data) : '',

            ],

            [

                'label' => 'Institución Financiera',

                'value' => 'FIC SA DE FINANZAS',

            ],

            [

                'label' => 'Nro. de Cuenta',

                'value' => ((Yii::app()->params['maskedAccountNumber'] == 'N') ?

                    $this->accountData['accountNumber'] :

                    $this->accountData['maskedAccountNumber']),

            ],

            [

                'label' => 'Tipo de Cuenta',

                'value' => 'Cuenta de Ahorro',

            ],

            [

                'label' => 'Total Pagado',

                'value' => 'Gs. ' . Yii::app()->numberFormatter->formatDecimal($this->amount + $this->commission),

            ],

            [

                'label' => 'Identificador de Seguridad',

                'value' => $this->payment->crc,

            ],

        ];

    }



    public function confirmFormConfig()

    {

        $config = $this->formConfig;



        foreach ($config['elements'] as $k => $el) {

            $el['type'] = 'hidden';

            $config['elements'][$k] = $el;

        }



        $config['elements']['serviceEncode'] = [

            'type' => 'hidden',

            'visible' => true,

        ];



        $config['buttons']['submit']['label'] = 'Confirmar Pago';



        return $this->addDetectIdInputsConfig($config);

    }



    public function invoicesFormConfig($confirm = false)

    {

        $config = $this->confirmFormConfig();

        $service = $this->service;



        $config['elements'] = $this->_addAmountElementToFormConfig($config['elements'], [

            'type'      => $service->accepts_partial_payment ? 'text' : 'hidden',

            'prepend'   => 'Gs.',

            'hint'      => $service->amount_hint,

            'visible'   => true,

        ]);



        $config['elements']['billJSON'] = [

            'type' => 'hidden',

            'visible' => true,

        ];



        if (!$confirm) {

            unset($config['elements']['detectIdMobileToken']);

            unset($config['elements']['detectIdOobSms']);

            $config['buttons']['cancel']['url'] = '#';

            $config['buttons']['cancel']['htmlOptions'] = ['data-dismiss' => 'modal'];

        } else {

            $config['elements']['amount']['type'] = 'hidden';

            $config['elements']['account']['type'] = 'hidden';

        }



        $config['buttons']['submit']['label'] = $this->service->commission_bill_fields ?

            'Verificar Comisión' : 'Pagar Factura';



        return $config;

    }



    public function commissionFormConfig()

    {

        $config = $this->confirmFormConfig();



        $config['elements'] = array_merge($config['elements'], $this->_commissionFormElements);



        $config['elements']['billJSON'] = [

            'type' => 'hidden',

            'visible' => true,

        ];

        $config['elements']['account'] = [

            'type' => 'hidden',

            'visible' => true,

        ];

        $config['elements']['amount'] = [

            'type' => 'hidden',

            'visible' => true,

        ];

        $config['elements']['commission'] = [

            'type' => 'hidden',

            'visible' => true,

        ];



        // colocar control de otp al final del formulario

        unset($config['elements']['detectIdMobileToken']);

        unset($config['elements']['detectIdOobSms']);



        return $this->addDetectIdInputsConfig($config, false);

    }



    public function setValues($values)

    {

        foreach ($values as $field => $value) {

            if ($field !== 'serviceEncode') {

                $this->{$field} = $value;

            }

        }

    }



    public function getServiceEncode()

    {

        if (empty($this->_serviceEncode)) {

            $this->_serviceEncode = base64_encode(json_encode($this->service));

        }



        return $this->_serviceEncode;

    }



    public function getAccountData()

    {

        if (empty($this->_accountData)) {

            $account = Yii::app()->user->accounts->getByHash($this->account);



            if (isset($account))

                $this->_accountData = $account;

        }



        return $this->_accountData;

    }



    public function getCustomerFields()

    {

        // en casos de pagos sin facturas, $this->bill es nulo y hay que retornar todos los campos que

        // tengan field_type=0

        if (!empty($this->bill)) return $this->bill->customer_fields;



        $customer_fields = [];



        foreach ($this->service->fields as $k => $field) {

            if ($field->field_type == 0) {

                $customer_fields[] = $this->{'field' . $k};

            }

        }



        return $customer_fields;

    }



    public function getCustomerTemporaryFields()

    {

        $customer_temporary_fields = [];



        foreach ($this->service->fields as $k => $field) {

            if ($field->field_type == 3) {

                $customer_temporary_fields[] = $this->{'field' . $k};

            }

        }



        return $customer_temporary_fields;

    }



    public function getBillFields()

    {

        // en casos de pagos sin facturas, $this->bill es nulo y hay que retornar todos los campos que

        // tengan field_type=1

        if (!empty($this->bill)) return $this->bill->bill_identifier;



        $bill_fields = [];



        foreach ($this->service->fields as $k => $field) {

            if ($field->field_type == 1) {

                $bill_fields[] = $this->{'field' . $k};

            }

        }



        return $bill_fields;

    }



    public function getAdditionalDataFields()

    {

        // en casos de pagos sin facturas, $this->bill es nulo y hay que retornar todos los campos que

        // tengan field_type=2

        if (!empty($this->bill)) return $this->bill->additional_data;



        $additional_data_fields = [];



        foreach ($this->service->fields as $k => $field) {

            if ($field->field_type == 2) {

                $additional_data_fields[] = $this->{'field' . $k};

            }

        }



        return $additional_data_fields;

    }



    public function getCommissionBillFields()

    {

        $commission_bill_fields = [];



        foreach ($this->service->commission_bill_fields as $k => $field) {

            $commission_bill_fields[] = $this->{'commission' . $k};

        }



        return $commission_bill_fields;

    }



    public function itgfOperationParameter()

    {

        $params = [];



        foreach ($this->_dynamicFields as $field => $value) {

            if ($field == 'serviceEncode' || empty($value)) continue;

            $params[] = [

                'name' => $this->getAttributeLabel($field),

                'value' => $this->{$field},

            ];

        }



        return json_encode([

            'user_datas' => $params,

            'bill' => $this->bill,

            'commission' => $this->commission,

            'payment' => $this->payment,

        ]);

    }



    private function _addField($fName, $field, $temporary_identification)

    {

        $this->_dynamicFields[$fName] = null;

        $this->_attributeLabels[$fName] = $field->name;

        $this->_formElements[$fName] = $this->_getFieldConfig($field, $temporary_identification);

        $this->_rules = array_merge($this->_rules, $this->_getFieldRules($field, $fName));

    }



    private function _addCommissionField($fName, $field)

    {

        $this->_dynamicFields[$fName] = null;

        $this->_attributeLabels[$fName] = $field->name;

        $this->_commissionFormElements[$fName] = [

            'type' => 'text',

            'groupOptions' => [

                'class' => 'collapse-inputs',

                'style' => 'display: none',

            ],

            'visible' => true,

        ];

        $this->_rules = array_merge($this->_rules, $this->_getCommissionFieldRules($field, $fName));

    }



    private function _getFieldRules($field, $fName)

    {

        $rules = [];

        $pattern = $field->validation_regexp;



        if (!empty($pattern)) {

            $rules[] = [

                $fName, 'match',

                'pattern' => $this->_cleanPattern($pattern),

                'allowEmpty' => !$field->required,

                'message' => $field->error_message,

            ];

        }



        $rules[] = [

            $fName, 'length',

            'min' => $field->min_length,

            'max' => $field->max_length,

        ];



        return $rules;

    }



    private function _getCommissionFieldRules($field, $fName)

    {

        $rules = [];

        $pattern = $field->validation_regexp;



        if (!empty($pattern)) {

            $rules[] = [

                $fName, 'match',

                'pattern' => $this->_cleanPattern($pattern),

                'allowEmpty' => !$field->required,

                'message' => 'Error de validación',

                'on' => 'commissionForm',

            ];

        }



        $rules[] = [

            $fName, 'length',

            'max' => $field->max_length,

            'on' => 'commissionForm',

        ];



        return $rules;

    }



    private function _getFieldConfig($field, $temporary_identification)

    {

        $visible = false;



        /*-- DOC

            Si "temporary_identification" es true, se deberán mostrar los campos con field_type = 3 (Identificador temporal de abonado), si es false, se deberán presentar inputs para los campos con field_type = 0 (Identificador de abonado).

            Si el servicio no consulta deuda se le debe presentar al usuario todos los campos de la lista fields que tengan el campo “visible” en true, para que este los ingrese.

        --*/

        if (!$this->service->queries_debt && $field->user_visible) {



            $visible = true;

        } elseif ($temporary_identification == false) {

            if ($field->field_type == 0 && $field->user_visible) {

                $visible = true;

            }

        } else {

            if ($field->field_type == 3 && $field->user_visible) {

                $visible = true;

            }

        }



        return [

            'type' => $this->_getDataType($field->data_type),

            'hint' => $field->hint,

            'visible' => $visible,

        ];

    }



    private function _getDataType($data_type)

    {

        switch ($data_type) {

            case 0:

                return 'text';

                break;



            case 1:

                return 'text';

                break;



            case 2:

                return 'number';



            default:

                return 'text';

                break;

        }

    }



    private function _cleanPattern($pattern)

    {

        // normalmente se recibe el regexp sin '/'

        // pero hay casos en los que sí se reciben



        return substr($pattern, 0, 1) === '/' ? $pattern : '/' . $pattern . '/';;

    }



    private function _addAmountElementToFormConfig($elements, $elementConfig)

    {

        // agregar monto al formulario

        $elements['amount'] = $elementConfig;



        // agregar selector de cuenta para el pago

        $elements['account'] = [

            'type'      => 'application.components.widgets.RadioGridInput',

            'gridItems' => $this->_getAvailableAccounts(),

            'visible'   => true,

        ];



        // establecer reglas para amount y account

        $this->_rules[] = ['amount, account', 'required'];



        return $elements;

    }



    /**

     * Cuentas disponibles para realizar pagos

     */

    private function _getAvailableAccounts()

    {

        return Yii::app()->user->accounts->getGridArray([

            'conditions' => [

                '__operType__' => '&&',

                //'accountType' => 'AH', // solamente cuentas de ahorro

                'accountType' => 'AH,TJ', // cuentas de ahorro y tarjetas de crédito

                'currency' => 'GS',

            ],

        ]);

    }



    /**

     * Returns the value for a dynamic attribute, if not, falls back to parent

     * method

     * 

     * @param type $name

     * @return type 

     */

    public function __get($name)

    {

        if (array_key_exists($name, $this->_dynamicFields)) {

            return $this->_dynamicFields[$name];

        } else {

            return parent::__get($name);

        }

    }



    /**

     * Overrides the setter to store dynamic data.

     * 

     * @param type $name

     * @param type $val 

     */

    public function __set($name, $val)

    {

        if (array_key_exists($name, $this->_dynamicFields)) {

            $this->_dynamicFields[$name] = $val;

        } else {

            parent::__set($name, $val);

        }

    }

}

