<?php
Yii::import('booster.widgets.TbForm');
Yii::import('application.models.profiledatas.AddressData');
Yii::import('application.models.profiledatas.PhoneData');

class UserController extends Controller
{
    const TAG = 'application.controllers.UserController';

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array(
                    'changeAccessPassword',
                    'changeTransactionalKey',
                    'detectIdRegister',
                    'updateDatas',
                    'renderAddressFormAjax',
                    'renderPhoneFormAjax',
                    'districtOptionsAjax',
                ),
                'users' => array('@'),
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionChangeAccessPassword()
    {
        $model = new ChangePasswordForm();


        if (isset($_POST['ChangePasswordForm'])) {
            $model->attributes = $_POST['ChangePasswordForm'];
            if ($model->validate()) {
                $wsrs = $this->wsClient->changeAccessPassword(array(
                    'currentPassword' => $model->currentPassword,
                    'newPassword' => $model->newPassword,
                ));
                if ($wsrs->error === 'N') {
                    if (Yii::app()->user->getState('changePassword'))
                        Yii::app()->user->setState('changePassword', null);
                    $this->setFlashSuccess(Yii::t('login', 'La clave de acceso se ha cambiado correctamente'));
                    $this->redirect(Yii::app()->homeUrl);
                } else {
                    $this->setFlashError($wsrs->descripcionrespuesta);
                    $this->redirect(array('user/changeAccessPassword'));
                }
            }
        }

        // eliminar los valores para las claves
        $model->attributes = ['currentPassword' => '', 'newPassword' => '', 'repeatPassword' => ''];
        $form = new TbForm($model->formConfig(), $model);
        $this->render('changeAccessPassword', array('form' => $form));
    }

    public function actionChangeTransactionalKey()
    {
        $model = new ChangePasswordForm();

        if (isset($_POST['ChangePasswordForm'])) {
            $model->attributes = $_POST['ChangePasswordForm'];
            if ($model->validate()) {
                $wsrs = $this->wsClient->changeTransactionalKey(array(
                    'currentPassword' => $model->currentPassword,
                    'newPassword' => $model->newPassword,
                ));
                if ($wsrs->error === 'N') {
                    $this->setFlashSuccess('La clave ha cambiado correctamente');
                    $this->redirect(Yii::app()->homeUrl);
                } else {
                    $this->setFlashError($wsrs->descripcionrespuesta);
                    $this->redirect(array('changeTransactionalKey'));
                }
            }
        }

        $form = new TbForm($model->formConfig(), $model);
        $this->render('changeTransactionalKey', array('form' => $form));
    }

    public function actionDetectIdRegister()
    {
        $userDetails = Yii::app()->user->getState('clientArea');
        $cellphone = str_replace('-', '', $userDetails['phoneNumber']);

        if (isset($cellphone)) {
            $clientService = Yii::app()->detectId->clientService;

            $response = $clientService
                ->createClient([
                    'sharedKey' => $userDetails['sharedKeyFromDatas'],
                    'businessIdentifier' => $userDetails['nombrecompleto'],
                    'mail' => $userDetails['email'],
                    'cellPhoneNumber' => $cellphone,
                ])
                ->createClientResponse;

            if ($response->resultCode !== 1020) {
                $this->setFlashError($response->resultDescription);
            } else {
                // registrar sharedKey en el core
                $this->wsClient->registerShareKey([
                    'shareKey' => $userDetails['sharedKeyFromDatas'],
                ]);
                $this->setFlashSuccess(Yii::t('login', 'Su número de celular ha sido confirmado'));
                $this->redirect(Yii::app()->homeUrl);
            }
        }

        $this->render('detectIdRegister', compact('cellphone'));
    }

    public function actionUpdateDatas()
    {
        $model = new UpdateDatasForm;

        if (isset($_POST['UpdateDatasForm'])) {

            $cabecera = [
                'email' => $_POST['UpdateDatasForm']['email'],
                'tipoExtracto' => 'Z',
            ];
            $direcciones = [];
            $telefonos = [];

            if (isset($_POST['AddressData']))
                foreach ($_POST['AddressData'] as $cd => $addressRow) {
                    if (empty($addressRow['tipo'])) continue;
                    $direccion = [
                        'tipo'             => $addressRow['tipo'],
                        'principal'        => $addressRow['principal'],
                        'ciudad'           => $addressRow['ciudad'],
                        'codigoCiudad'     => $addressRow['codigoCiudad'],
                        'direccion'        => $addressRow['direccion'],
                        'observacion'      => $addressRow['observacion'],
                        'numero'           => $addressRow['numero'],
                        'departamento'     => $addressRow['departamento'],
                        'edificio'         => $addressRow['edificio'],
                        'piso'             => $addressRow['piso'],
                        'accion'           => isset($addressRow['accion']) ? $addressRow['accion'] : 'E',
                        'codigoDireccion'  => $cd,
                    ];

                    if ((bool) $addressRow['codigoBarrio']) {
                        $direccion['barrio']        = $addressRow['barrio'];
                        $direccion['codigoBarrio']  = $addressRow['codigoBarrio'];
                    }

                    $direcciones[] = $direccion;
                }

            if (isset($_POST['PhoneData'])) foreach ($_POST['PhoneData'] as $ct => $phoneRow) {
                if (empty($phoneRow['tipo'])) continue;
                $telefonos[] = [
                    'area'             => $phoneRow['area'],
                    'interno'          => $phoneRow['interno'],
                    'telefono'         => $phoneRow['telefono'],
                    'tipoLinea'        => $phoneRow['tipolinea'],
                    'telTipo'          => $phoneRow['tipo'],
                    'telPrincipal'     => $phoneRow['principal'],
                    'telAccion'        => isset($phoneRow['accion']) ? $phoneRow['accion'] : 'E',
                    'codigoTelefono'   => $ct,
                ];
            }

            $json = json_encode([
                'cabecera' => $cabecera,
                'direcciones' => $direcciones,
                'telefonos' => $telefonos,
            ]);

            $response = $this->wsClient->managementData(array("mode" => 'C', "json" => $json));

            if ($response->error === 'S') {
                $this->setFlashError($response->descripcionrespuesta);
            } else {
                $this->setFlashSuccess($response->descripcionrespuesta);
                $this->redirect(array('/user/updateDatas'));
            }
        } else {
            // recuperar datos del WS
            $response = $this->wsClient->managementData(array("mode" => 'P', "json" => ""));

            if ($response->error == 'S') {
                throw new CHttpException(505, 'No se pudo procesar la solicitud.');
            }
        }

        $model->email = $response->email;
        $model->addresses = $this->_responseToAddressesList($response->listadirecciones);
        $model->phones = $this->_responseToPhonesList($response->listatelefonos);

        $form = new TbForm($model->formConfig(), $model);

        $this->render('updateDatas', array('form' => $form));
    }

    public function actionRenderAddressFormAjax($containerId, $model = null, $modelId = null)
    {
        if (!Yii::app()->request->isAjaxRequest) {
            echo "Invalid request";
            return;
        }

        $edit = !empty($model);
        $model = $this->_initModelFromBase64(new AddressData, $model);
        $config = $model->formConfig($this->_getAddressOptions($model));

        $this->_renderAjaxForm($containerId, $modelId, $model, $edit, $config, 'user/renderAddressFormAjax');
    }

    public function actionRenderPhoneFormAjax($containerId, $model = null, $modelId = null)
    {
        if (!Yii::app()->request->isAjaxRequest) {
            echo "Invalid request";
            return;
        }
        $edit = !empty($model);
        $model = $this->_initModelFromBase64(new PhoneData, $model);
        $config = $model->formConfig();

        $this->_renderAjaxForm($containerId, $modelId, $model, $edit, $config, 'user/renderPhoneFormAjax');
    }

    public function actionDistrictOptionsAjax($cityCode)
    {
        if (!Yii::app()->request->isAjaxRequest) {
            echo "Invalid request";
            return;
        }

        $districts = $this->_getDistrictOptions($cityCode);

        foreach ($districts as $k => $v) {
            echo '<option value="' . $k . '">' . $v . '</option>';
        }
    }

    private function _renderAjaxForm($containerId, $modelId, $model, $edit, $config, $formUrl)
    {
        foreach ($config['elements'] as $e => $c) {
            $config['elements'][$e]['widgetOptions']['htmlOptions']['data-attrname'] = $e;
        }

        $config['buttons'] = [
            'submit' => [
                'buttonType'    => 'submit',
                'context'       => 'primary',
                'label'         => Yii::t('commons', $edit ? 'Actualizar' : 'Insertar'),
                'htmlOptions'   => [
                    'class'     => 'agi-submit-button',
                ],
            ],
            'cancel' => [
                'buttonType'    => 'button',
                'context'       => 'danger',
                'label'         => Yii::t('commons', 'Cancelar'),
                'htmlOptions'   => [
                    'data-dismiss'  => 'modal',
                ],
            ],
        ];
        $config['attributes'] = [
            'data-containerid'  => $containerId,
            'data-modelid'      => $modelId,
            'data-formurl'      => Yii::app()->createUrl($formUrl),
        ];

        $form = new TbForm($config, $model);
        $this->renderPartial('/commons/_form', ['form' => $form, 'withoutPanel' => true]);
    }

    private function _responseToAddressesList($list)
    {
        $response = [];
        if (!empty($list->array)) foreach ($list->array as $addressData) {
            $address = new AddressData;
            $address->codigoCiudad = $addressData->codigociudad;
            $address->ciudad = $addressData->ciudad;
            $address->codigoBarrio = isset($addressData->codigobarrio) ? $addressData->codigobarrio : 0;
            $address->barrio = isset($addressData->barrio) ? $addressData->barrio : '';
            $address->principal = $addressData->principal;
            $address->tipo = $addressData->tipo;
            $address->direccion = $addressData->direccion;
            $address->codigoDireccion = $addressData->codigodireccion;
            $address->accion = $addressData->accion;
            $address->numero = isset($addressData->numero) ? $addressData->numero : null;
            $address->observacion = isset($addressData->observacion) ? $addressData->observacion : null;
            $address->departamento = isset($addressData->departamento) ? $addressData->departamento : null;
            $address->edificio = isset($addressData->edificio) ? $addressData->edificio : null;
            $address->piso = isset($addressData->piso) ? $addressData->piso : null;

            $response[] = $address;
        }

        return $response;
    }

    private function _responseToPhonesList($list)
    {
        $response = [];
        if (!empty($list->array)) foreach ($list->array as $data) {
            $phone = new PhoneData;
            $phone->attributes = (array) $data;

            $response[] = $phone;
        }

        return $response;
    }

    private function _initModelFromBase64($model, $base64)
    {
        if (!empty($base64)) {
            $modelArray = json_decode(base64_decode($base64), true);
            $model->attributes = $modelArray;
        }

        return $model;
    }

    private function _getAddressOptions($model)
    {
        return [
            'cities'     => $this->_getCitiesOptions(),
            'districts'   => $this->_getDistrictOptions($model->codigoCiudad),
        ];
    }

    private function _getCitiesOptions()
    {
        $return = [];
        $response = $this->wsClient->getCities(array('codecountry' => '586'));

        if (!empty($response->listaciudades->array)) {
            $response = $response->listaciudades->array;

            foreach ($response as $city) {
                $return[$city->codigociudad] = $city->descripcion;
            }
        }

        return $return;
    }

    private function _getDistrictOptions($cityCode)
    {
        $return = [];
        $response = $this->wsClient->getDistrict([
            'codecity'      => $cityCode,
            'codecountry'   => '586',
        ]);

        if ($response->error === 'N') {
            $districts = $response->listabarrios->array;
            foreach ($districts as $d) {
                $return[$d->codigobarrio] = $d->descripcion;
            }
        } else {
            $return[0] = $response->descripcionrespuesta;
        }

        return $return;
    }

    /**
     * Added: 02-04-2024:función requerida para ofuscar password
     */
    public function encript256($textoLetra){
        $clave = "Hola.12345678#";
        // Se genera un vector de inicialización (IV) aleatorio
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));       
        // Se cifran los datos utilizando AES-256 en modo CBC (Cipher Block Chaining)
        $cifrado = openssl_encrypt($textoLetra, 'aes-256-cbc', $clave, 0, $iv);       
        // Se devuelve el IV concatenado con los datos cifrados
        return base64_encode($iv . $cifrado);
        
    }

}
