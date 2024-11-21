<?php
Yii::import('application.models.profiledatas.AddressData');
Yii::import('application.models.profiledatas.PhoneData');

/**
* Modelo para el Formulario de actualización de datos
*/
class UpdateDatasForm extends FormModel
{
    public $email;
    public $addresses = [];
    public $phones = [];
    
    public function rules()
    {
        return [
            ['email, addresses, phones', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email'=>Yii::t('updateProfile', 'Email'),
            'addresses'=>Yii::t('updateProfile', 'Direcciones'),
            'phones'=>Yii::t('updateProfile', 'Teléfonos'),
        ];
    }

    public function formConfig()
    {
        return [
            'showErrorSummary'=>false,
            'elements' => [
                'email' => array(
                    'type' => 'text',
                    'groupOptions' => array('class'=>'col-md-12'),
                ),
                'addresses' => array(
                    'type'      => 'application.components.widgets.ArrayGridInput',
                    'columns' => AddressData::columnsConfig(),
                    'modelCollection' => $this->addresses,
                    'containerId' => 'UpdateDatasForm_addresses',
                    'formUrl' => 'user/renderAddressFormAjax',
                    'extraContent' => $this->_renderAddressessActionsElements(),
                ),
                'phones' => array(
                    'type'      => 'application.components.widgets.ArrayGridInput',
                    'columns' => PhoneData::columnsConfig(),
                    'modelCollection' => $this->phones,
                    'containerId' => 'UpdateDatasForm_phones',
                    'formUrl' => 'user/renderPhoneFormAjax',
                    'extraContent' => $this->_renderPhonesActionsElements(),
                ),
            ],
            'buttons' => [
                'submit'=>[
                    'buttonType'=>'submit',
                    'context'=>'primary',
                    'label'=>Yii::t('commons', 'Guardar'),
                ],
                'cancel'=>[
                    'buttonType'=>'link',
                    'context'=>'danger',
                    'label'=>Yii::t('commons', 'Cancelar'),
                    'url'=>array('/site/index'),
                ],
            ],
        ];
    }

    private function _renderAddressessActionsElements()
    {
        $response = '';
        foreach ($this->addresses as $address) {
            $response .= CHtml::activeHiddenField($address, '['.$address->codigoDireccion.']accion');
        }

        return $response;
    }

    private function _renderPhonesActionsElements()
    {
        $response = '';
        foreach ($this->phones as $phone) {
            $response .= CHtml::activeHiddenField($phone, '['.$phone->codigotelefono.']accion');
        }

        return $response;
    }
}
