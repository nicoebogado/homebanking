<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class ChangePasswordForm extends FormModel
{
    public $currentPassword;
    public $newPassword;
    public $repeatPassword;

    public $blackList = array("123456","654321","123123","112233","000000");

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // username and password are required
            array('currentPassword, newPassword, repeatPassword', 'required'),

            array('repeatPassword', 'compare', 'compareAttribute'=>'newPassword'),
            array('newPassword', 'length', 'min'=>4),
            array('newPassword', 'notAllowed'),
            //array('newPassword', 'occurrences'),
        );
    }

    public function notAllowed($attribute,$params){
        if(in_array($this->$attribute, $this->blackList)){
            $this->addError($attribute, 'Clave no permitida');
        }
    }

    public function occurrences($attribute,$params){
        foreach (count_chars($this->$attribute, 1) as $i => $val) {
            if($val>2){
                $this->addError($attribute, 'La clave no puede tener un carácter con más de 2 ocurrencias');
            }
        }
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'currentPassword'=>Yii::t('changePassword', 'Clave Antigua'),
            'newPassword'=>Yii::t('changePassword', 'Nueva Clave'),
            'repeatPassword'=>Yii::t('changePassword', 'Repetir Nueva Clave'),
        );
    }

    public function formConfig()
    {
        return array(
            'showErrorSummary'=>false,
            'attributes'=>array('id'=>'select-account-form'),
            'elements'=>array(
                'currentPassword'=>array(
                    'type'=>'password',
                ),
                'newPassword'=>array(
                    'type'=>'password',
                    'widgetOptions' => array(
                        'htmlOptions' => array(
                            'class' => 'secureKeypadInput',
                        ),
                    ),
                ),
                'repeatPassword'=>array(
                    'type'=>'password',
                    'widgetOptions' => array(
                        'htmlOptions' => array(
                            'class' => 'secureKeypadInput',
                        ),
                    ),
                ),
            ),
            'buttons'=>array(
                'submit'=>array(
                    'id'=>'submit',
                    'buttonType'=>'submit',
                    'context'=>'primary',
                    'label'=>Yii::t('commons', 'Enviar'),
                    'url'=>array('checkbooks'),
                ),
            ),
        );
    }
}
