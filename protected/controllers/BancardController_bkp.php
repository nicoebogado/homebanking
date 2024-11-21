<?php

Yii::import('booster.widgets.TbForm');
Yii::import('application.components.bancardclient.PaymentPayload');

/**
 * Controlador de pagos
 */
class BancardController extends Controller
{
    public function filters()
    {
        return array(
            'accessControl',
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
                'users' => array('@'),
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionList()
    {
        $brands = Yii::app()->bancardClient->brands();

        if ($brands->status === 'success') {
            $brands = $this->_categorizeBrands($brands);

            // cambiar jQuery 1.1 por el jQuery (3.3) para que funcione filterizr
            $cs = Yii::app()->clientScript;
            $cs->scriptMap = array(
                'jquery.min.js' => 'https://code.jquery.com/jquery-3.3.1.min.js',
            );

            $this->render('brands', compact('brands'));
        } else {
            $this->setFlashError('No se pudo obtener la lista de servicios');
            $this->redirect(array('/site/index'));
        }
    }

    public function actionServices($id)
    {
        // verificar si no se intenta realizar un reenvío de sms
        if ($id === 'resendSms') {
            return $this->_resendSms();
        }

        $service = Yii::app()->bancardClient->services($id);

        if ($service->status === 'success') {
            return $this->_serviceForm($service->service);
        } else {
            $this->setFlashError('No se pudo obtener los detalles del servicio');
            $this->redirect(array('/site/index'));
        }
    }

    public function actionPayBill()
    {
        if (isset($_POST['BancardServiceForm'])) {

            $model = $this->_createModelFromRequest();
            $model->scenario = 'payBill';
            $brandData = $this->_decodeBrandData($_GET['bd']);

            // si existen $_POST['BancardServiceForm']['detectIdMobileToken']
            // o $_POST['BancardServiceForm']['detectIdOobSms']
            // es porque se está procesando el envío del formulario
            if (
                !(isset($_POST['BancardServiceForm']['detectIdMobileToken']) ||
                    isset($_POST['BancardServiceForm']['detectIdOobSms']))
            ) {
                if ($model->validate()) {
                    $this->_renderConfirmForm($model, $model->invoicesFormConfig(true), $brandData);
                } else {
                    if ($model->getError('amount'))
                        $this->setFlashError($model->getError('amount'));
                    if ($model->getError('account'))
                        $this->setFlashError($model->getError('account'));
                    $this->_renderInvoiceForm($model, $brandData);
                }
            } else {
                // confirmar pago con comisión
                try {
                    $this->_confirmPayment($model, $brandData[0], false);
                    $this->_makePayment($model);
                    Yii::app()->end();
                } catch (Exception $e) {
                    $this->setFlashError($e->getMessage());
                    $model->detectIdMobileToken = $model->detectIdOobSms = null;
                    $this->_renderConfirmForm($model, $model->invoicesFormConfig(true), $brandData);
                }
            }
        }

        $this->redirect(array('/bancard/list'));
    }

    public function actionBillCommission()
    {
        if (isset($_POST['BancardServiceForm'])) {

            $model = $this->_createModelFromRequest();
            $brandData = $this->_decodeBrandData($_GET['bd']);

            // si existe $_POST['BancardServiceForm']['commission'] es porque se está procesando el envío del formulario
            if (!isset($_POST['BancardServiceForm']['commission'])) {
                $service = $model->service;
                // consultar la comisión
                try {
                    $commissionResponse = Yii::app()->bancardClient->commissions($service->id, $model->amount);

                    if ($commissionResponse->status == 'success') {
                        $model->commission = $commissionResponse->td;

                        // verificar en itgf si la cuenta puede realizar el pago
                        $this->_checkAccount($model, $brandData[0]);
                    } else {
                        $this->setFlashError('Error al obtener la comisión');
                    }
                } catch (Exception $e) {
                    $this->setFlashError($e->getMessage());
                    $this->redirect(Yii::app()->request->urlReferrer);
                }
            } else {
                // confirmar pago con comisión
                try {
                    $this->_confirmPayment($model, $brandData[0], false);
                    $this->_makePayment($model);
                    Yii::app()->end();
                } catch (Exception $e) {
                    $this->setFlashError($e->getMessage());
                    $model->detectIdMobileToken = $model->detectIdOobSms = null;
                }
            }

            $form = new TbForm($model->commissionFormConfig(), $model);
            $this->render('commissionForm', compact('model', 'form', 'brandData'));
        } else
            $this->redirect(array('/bancard/list'));
    }

    public function actionConfirm()
    {
        if (isset($_POST['BancardServiceForm'])) {

            $model = $this->_createModelFromRequest();
            $brandData = $this->_decodeBrandData($_GET['bd']);

            try {
                $this->_confirmPayment($model, $brandData[0], false);
                // realizar el pago
                $this->_makePayment($model);
            } catch (Exception $e) {
                $this->setFlashError($e->getMessage());
                $model->detectIdMobileToken = null;
                $model->detectIdOobSms = null;
                return $this->_serviceForm($model->service);
            }
        } else
            $this->redirect(['/bancard/list']);
    }

    public function actionResendSms()
    {
        $this->_resendSms();
    }

    private function _resendSms()
    {
        $response = Yii::app()->detectId->outOfBandSmsService->retrieveNewOTP([
            'sharedKey' => Yii::app()->user->getState('sharedKey'),
        ])->WSRetrieveOtpResult;

        if ($response->resultCode === 1020) {
            $this->wsClient->getSendMessage([
                'message' => 'FIC S.A. Informa: Su codigo de seguridad para confimar la operacion es ' . $response->otp,
            ]);
        }
    }

    private function _serviceForm($service)
    {
        $model = new BancardServiceForm;
        $model->initFromService($service);
        $brandData = $this->_decodeBrandData($_GET['bd']);

        if (isset($_POST['BancardServiceForm'])) {
            $model->setValues($_POST['BancardServiceForm']);

            if ($model->validate()) {

                // si no consulta facturas es un pago directo
                if (!$service->queries_debt) {
                    $this->_renderConfirmForm(
                        $model,
                        $model->confirmFormConfig(),
                        $brandData,
                        Yii::app()->createUrl('/bancard/confirm', [
                            'bd' => $_GET['bd'],
                        ])
                    );
                } else {
                    $this->_renderInvoiceForm($model, $brandData);
                }
            }
        }

        $form = new TbForm($model->formConfig, $model);
        $this->render('serviceForm', compact('form', 'service', 'brandData'));
    }

    private function _renderConfirmForm($model, $formConfig, $brandData, $action = null)
    {
        // verificar en itgf si la cuenta puede realizar el pago
        if ($this->_checkAccount($model, $brandData[0], false)) {
            $form = new TbForm($formConfig, $model);
            $formParams = [
                'form' => $form,
                'withoutPanel' => true,
            ];
            if ($action) $formParams['action'] = $action;
            $this->render('confirmForm', compact('model', 'formParams', 'brandData'));
            Yii::app()->end();
        }
    }

    private function _renderInvoiceForm($model, $brandData)
    {
        try {
            $dataProvider = $this->_getInvoicesDataProvider($model);

            $form = new TbForm($model->invoicesFormConfig(), $model);
            $this->render('invoicesForm', compact('model', 'form', 'brandData', 'dataProvider'));
            Yii::app()->end();
        } catch (CHttpException $e) {
            $this->setFlashError($e->getMessage());
            $this->redirect(Yii::app()->request->urlReferrer);
        }
    }

    private function _getInvoicesDataProvider($model)
    {
        $service = $model->service;

        // consultar las facturas
        $payload = $service->temporary_identification ?
            $model->customerTemporaryFields :
            $model->customerFields;
        $invoices = Yii::app()->bancardClient->invoices($service->id, $payload);

        if ($invoices->status != 'success' || empty($invoices->bills))
            throw new CHttpException(503, 'No se encontraron facturas');

        $datas = [];
        $numberFormatter = Yii::app()->numberFormatter;
        foreach ($invoices->bills as $bill) {
            $row = $bill;
            $row->f_amount          = 'Gs. ' . $numberFormatter->formatDecimal($bill->amount);
            $row->f_minimum_payment = 'Gs. ' . $numberFormatter->formatDecimal($bill->minimum_payment);
            $row->f_due_date        = date('d-m-Y', strtotime($bill->due_date));
            $row->button            = '<button
                class="btn btn-info pay-bill"
                type="button"
                data-bill=\'' . json_encode($row) . '\'
                data-toggle="modal"
                data-target="#invoice-form-modal">
                    Seleccionar Factura
                </button>';

            $datas[] = $row;
        }

        return new CArrayDataProvider($datas, [
            'keyField'      => false,
            'pagination'    => false,
        ]);;
    }

    private function _checkAccount($model, $brandName, $redirect = true)
    {
        $response = $this->wsClient->paymentServices([
            'controlMode' => 'V',
            'debitAccount' => $model->accountData['accountNumber'],
            'operationAmount' => $model->amount,
            'commissionAmount' => $model->commission,
            'operationDescription' => $brandName . ' - ' . $model->service->name,
        ]);

        if ($response->error == 'S') {
            $this->setFlashError($response->descripcionrespuesta);
            if ($redirect)
                $this->redirect([
                    'services',
                    'id' => $model->service->id,
                    'bd' => $_GET['bd'],
                ]);
            else
                return false;
        }

        return true;
    }

    private function _confirmPayment($model, $brandName, $redirect = true)
    {
        $model->scenario = 'confirm';
        if (!$model->validate()) {
            throw new Exception(array_values($model->getErrors())[0][0]);
        }

        $response = $this->wsClient->paymentServices([
            'controlMode' => 'T',
            'debitAccount' => $model->accountData['accountNumber'],
            'operationAmount' => $model->amount,
            'commissionAmount' => $model->commission,
            'operationDescription' => $brandName . ' - ' . $model->service->name,
        ]);

        if ($response->error == 'S') {
            if ($redirect) {
                $this->setFlashError($response->descripcionrespuesta);
                $this->redirect([
                    'services',
                    'id' => $model->service->id,
                    'bd' => $_GET['bd'],
                ]);
            } else
                throw new Exception($response->descripcionrespuesta, 1);
        }

        return $response;
    }

    private function _makePayment($model)
    {
        $service = $model->service;
        $payload = new PaymentPayload($model);
        $payload = $payload->getArray();

        try {
            $paymentResponse = Yii::app()->bancardClient->payment($service->id, $payload);
        } catch (Exception $e) {
            Yii::app()->bancardClient->reverse($service->id, $payload);
            throw new CHttpException(500, $e->getMessage());
        }

        if ($paymentResponse->status != 'success') {
            throw new CHttpException(500, 'El pago fue rechazado');
        } else {
            $brandData = $this->_decodeBrandData($_GET['bd']);
            $model->payment = $paymentResponse->payment;

            // confirmar a itgf que se realizó el pago
            $response = $this->wsClient->paymentServices([
                'controlMode' => 'C',
                'debitAccount' => $model->accountData['accountNumber'],
                'operationAmount' => $model->amount,
                'commissionAmount' => $model->commission,
                'operationDescription' => $brandData[0] . ' - ' . $model->service->name,
                'operationNumber' => $payload['transaction_id'],
                'operationParameter' => $model->itgfOperationParameter(),
                'securityIdentifier' => $model->payment->crc,
            ]);

            // refrescar estado de las cuentas
            Yii::app()->user->accounts->refresh();

            $this->render('paymentVoucher', compact('model', 'brandData'));
        }
    }

    private function _createModelFromRequest()
    {
        $service = json_decode(base64_decode($_POST['BancardServiceForm']['serviceEncode']));

        $model = new BancardServiceForm;
        $model->initFromService($service);
        $model->bill = isset($_POST['BancardServiceForm']['billJSON']) ?
            json_decode($_POST['BancardServiceForm']['billJSON']) :
            null;
        $model->setValues($_POST['BancardServiceForm']);

        return $model;
    }

    /**
     * Agrega al objeto brands el atributo categoryLabels (nombres de las categorías)
     * y a cada brand el atributo categories (ids de categorías a las que pertenece el brand)
     */
    private function _categorizeBrands($brands)
    {
        $categoryLabels = [
            1 => 'Servicios Públicos',
            2 => 'Telefonía',
            3 => 'Pago de Tarjeta',
            4 => 'Televisión Paga',
            5 => 'Pago de Cuota',
            6 => 'Pago de Factura',
            7 => 'Pago de Préstamos',
            8 => 'Pago de Aportes',
            9 => 'Pago Póliza',
            10 => 'Donaciones',
            11 => 'Compra de entradas',
        ];

        $categoryBrands = [
            1 => ['ANDE', 'Copaco', 'Essap', 'Municipalidad de Asuncion', 'IPS Instituto de Prevision Social', 'Secretaria de Accion Social'],

            2 => ['Copaco', 'Personal', 'TIGO', 'Claro', 'VOX'],

            3 => ['Banco Basa S.S', 'Banco Atlas', 'BBVA', 'Cefisa', 'Banco Continental', 'Coop Coomecipar LTDA', 'Coop Universitaria', 'Banco Familiar', 'Banco GNB', 'Banco Itapua', 'Banco Itau', 'Banco Regional', 'Banco Sudameris', 'Vision Banco', 'Financiera el Comercio', 'Bancop', 'Caja Mutual', 'Solar', 'Coop Fernando de la Mora LTDA', 'Coop Medalla Milagrosa LTDA', 'Coop Copacons LTDA', 'Coop Multiactiva Luque LTDA', 'Coop San Cristobal LTDA', 'Interfisa Banco', 'Caja Medica y de Profes Univer', 'Coop San Juan Bta LTDA', 'Coop Israelita LTDA', 'Financiera Rio', 'Credicentro', 'Coopec LTDA', 'Tu Financiera', 'FIC de Finanzas', 'Finexpar', 'LCR SAECA', 'Aseguradora Tajy', 'Credisimple', 'Apolo', 'Banco Nacional de Fomento', 'Financiera Paraguayo Japonesa', 'Nexoos', 'Tarjetami', 'Grupo H3', 'Cooperativa Villa Morra Ltda.', 'Cooperativa San Pedro Ltda.', 'Coop. Cumbre de la Coordillera LTDA.', 'Progresar Corporation S.A', 'Pasfin', 'Coop. Pinoza', 'Coop. Yoayu LTDA.', 'Fast Credit', 'Coop. Ypacarai Ltda.', 'Credigrow', 'Fane', 'Coop. Santisima Trinidad LTDA', 'Sarraff', 'Nueva Americana'],

            4 => ['Personal', 'Claro', 'TIGO STAR', 'Universidad Católica'],

            5 => ['Colegio Santa Clara', 'Asoc del Colegio Internacional', 'Chacomer', 'Tu Financiera', 'Universidad Americana', 'Monital', 'La Loteadora', 'Megaloi', 'Universidad UPAP', 'Universidad Desarrollo Sustentable', 'Techo', 'La Hora De Las Compras', 'Alto Impacto', 'Gestión 21', 'Futuro Sepelios', 'Tupi', 'Bella Vista', 'Schulz', 'El Toke', 'Achon', 'Colegio Santa Caterina da Siena', 'Universidad San Carlos', 'Amnistía Internacional Paraguay', 'Centro Monseñor Bogarin', 'OAMI', 'Equilibrio', 'Activo Créditos', 'Jardín de la Paz', 'Fortaleza de Inmuebles', 'Electroban', 'Olier', 'Instituto de Odontología Avanzada', 'Bigg Crossfit', 'Asunsion Center', 'Fleming', 'Promed', 'Electrocenter', 'Parque Serenidad', 'Tecnolandia', 'La Paraguaya Inmobiliaria', 'Ailen Electrodomésticos', 'Centro odontológico asistencial', 'Artaza Hermanos', 'Colegio Instituto Cristiano Interactivo', 'Alex S.A', 'Liberato', 'Plaza de los Mangos', 'Electro Jet Electrodomésticos', 'Koala', 'Sueñolar', 'Crecer Inmobiliaria', 'Law & Medicine', 'Hostipy', 'Club Deportivo Sajonia', 'Wilmar Center', 'Raices', 'Prosperar Paraguay', 'Medilife', 'Unimed', 'Touring y Automóvil Club Paraguayo', 'Efuneral', 'Bristol', 'Centro Educativo Sagrado Corazón de Jesús', 'Unhuman', 'Club Internacional de Tenis', 'Prosalud', 'Clasipar', 'Wevia', 'Fundacion del Clero Arquidiocesano', 'De la Sobera', 'ClinCaja', 'Credisolución', 'Club Olimpia', 'Facultad de Ciencias Económicas UNA', 'Colegio Dante Alighieri - Fdo de la Mora', 'Colegio Dante Alighieri - Asuncion'],

            6 => ['Asismed', 'Bancard', 'Equifax Paraguay SA', 'La Consolidada Seguros', 'Universidad Católica', 'Credisimple', 'Amazonas Express', 'Santa Clara Seguro', 'Compañía de Luz y Fuerza S.A.', 'Achon', 'Colegio Centro Educativo Los Laureles', 'CCPA Central', 'CCPA Villa Morra', 'CCPA San Lorenzo', 'CCPA Itapúa', 'CCPA Coronel Oviedo', 'CCPA Ciudad del Este', 'CCPA Pilar', 'Procard', 'BuscoInfo', 'Puerto Caacupe-mí', 'Lions Media Games', 'Comprame Barato', 'Dinapi', 'Sendit', 'FIUNA', 'Taxit', 'Fast Box', 'Colegio de Escribanos del Paraguay'],

            7 => ['Banco Familiar', 'Banco Sudameris', 'Vision Banco', 'Caja Mutual', 'Coop Medalla Milagrosa LTDA', 'LCR SAECA', 'Credisimple', 'Financiera Paraguayo Japonesa', 'Crediplus', 'Crediflex'],

            8 => ['Caja Mutual', 'IPS Instituto de Prevision Social', 'Po Paraguay', 'Fundación Dequení', 'Fundación Operación Sonrisa Paraguay'],

            9 => ['Regional Seguros', 'Aseguradora del Sur S.A.', 'La Agricola Seguros', 'Nobleza Seguros', 'Aseguradora Tajy', 'Rumbos Seguros', 'Sermed S.A.', 'Aseguradora Yacyreta S.A', 'MAPFRE', 'Fenix S.A de Seguros y Reaseguros', 'Cenit S.A de Seguros', 'El Productor S.A. Seguros', 'Seguros Generales', 'El sol del Paraguay S.A de Seguros', 'El comercio Paraguayo', 'Royal Seguros', 'La Independencia de Seguros', 'General Seguros', 'Panal Seguros', 'Seguridad Seguros', 'Alianza Garantia Seguros y Reaseguros'],

            10 => ['Techo', 'FundaProva', 'Asoleu', 'San Peregrino', 'Fundación Rocio Cabriza', 'Fundación Apostar por la Vida', 'Legion de la Buena Voluntad', 'Asociacion Adoptame', 'Amnistía Internacional Paraguay', 'Po Paraguay', 'Fundación Dequení', 'Fundación Operación Sonrisa Paraguay', 'Fundación Virgen de Fátima'],

            11 => ['Prestigio Producciones', 'Sonidos de la tierra', 'Fundación Virgen de Fátima'],
        ];

        $brands->categoryLabels = $categoryLabels;

        // por cada brand
        foreach ($brands->brands as $brand) {
            // crear el atributo categories
            $brand->categories = [];
            // recorrer categoryBrands
            foreach ($categoryBrands as $k => $v) {
                // si el brand pertenece a la categoría
                if (in_array(trim($brand->name), $v)) {
                    // asignar la categoría al brand
                    $brand->categories[] = $k;
                }
            }
            // convertir el array categories en un string separados por comas
            $brand->categories = implode(',', $brand->categories);
        }

        return $brands;
    }

    protected function _decodeBrandData($bd)
    {
        return explode('|', base64_decode($bd));
    }

    protected function _encodeBrand($brandObj)
    {
        foreach ($brandObj->services as $service) {
            $imgUrl = 'https://www.bancard.com.py/s4/public/billing_brands_logos/' . $brandObj->logo_resource_id . '.normal.png';
            $service->url = Yii::app()->createUrl('bancard/services/', [
                'id' => $service->id,
                'bd' => base64_encode($brandObj->name . '|' . $imgUrl), //brand datas
            ]);
        }

        return base64_encode(json_encode($brandObj));
    }
}
