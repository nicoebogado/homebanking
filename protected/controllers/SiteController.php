<?php


class SiteController extends Controller
{
    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            'page' => array(
                'class' => 'CViewAction',
            ),
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
                'foreColor' => 0x000000,
                'testLimit' => 1, 
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {

        if (Yii::app()->user->isGuest)
            $this->redirect(array('/site/login'));

        $dataProvider = null;

        $AHaccounts = Yii::app()->user->accounts->getList(array('conditions' => array(
            'accountType' => 'AH, AP',
        )));

        $PTaccounts = Yii::app()->user->accounts->getList(array('conditions' => array(
            'accountType' => 'PT',
        )));
        $TJaccounts = Yii::app()->user->accounts->getList(array('conditions' => array(
            'accountType' => 'TJ',
        )));
        $dataProvider = array(
            'AH' => new CArrayDataProvider($AHaccounts, array(
                'keyField' => 'hash',
                'pagination' => array(
                    'pageSize' => 5,
                ),
            )),
            'PT' => new CArrayDataProvider($PTaccounts, array(
                'keyField' => 'hash',
                'pagination' => array(
                    'pageSize' => 5,
                ),
            )),
            'TJ' => new CArrayDataProvider($TJaccounts, array(
                'keyField' => 'hash',
                'pagination' => array(
                    'pageSize' => 5,
                ),
            )),
        );

        $riesgo = $this->wsClient->getRateRisk();
        if ($riesgo->error === 'N' && isset($riesgo->calificacionriesgo)) {
            $this->render('index', array('dataProvider' => $dataProvider, 'data' => $riesgo));
        } else {
            $this->render('index', array('dataProvider' => $dataProvider));
        }
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else {
                if (Yii::app()->user->isGuest)
                    $this->layout = 'base';
                $this->render('error', $error);
            }
        }
    }

    public function actionShowPermissionError()
    {
        $this->layout = 'base';
        $this->render('showPermissionError');
    }

    public function actionStatus()
    {
        try {
            $response = $this->wsClient->getDataBaseConnection();
            $response = explode(' ', $response);
            $response = $response[0];
            $dbServerDate = $response;
            $phpServerDate = date('d/m/Y');
            if ($dbServerDate == $phpServerDate) {
                echo '1';
            } else {
                echo '0';
            }
            Yii::app()->end();
        } catch (Exception $e) {
            echo '0';
            Yii::app()->end();
        }
    }

    public function actionCheckSessionStatus()
    {
        if (Yii::app()->request->isPostRequest) {
            try {
                $riesgo = $this->wsClient->getRateRisk();
                $this->renderJSON(['valid' => ($riesgo->error == 'N')]);
            } catch (Exception $e) {
                $this->renderJSON(['valid' => false]);
            }
        }
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {

        $this->layout = 'base';

        if (!Yii::app()->user->isGuest)
            $this->redirect(array('/site/index'));

        $model = new LoginForm;

        // collect user input data
        if (isset($_POST['form'], $_POST['LoginForm'][$_POST['form']])) {
            $model->attributes = $_POST['LoginForm'][$_POST['form']];

            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {
          
                Yii::app()->user->accounts->set();
                
                $this->redirect(Yii::app()->user->returnUrl);
            } else {
                if (!Yii::app()->user->hasFlash('error')) {
                    Yii::app()->user->setFlash('error', Yii::t('login', 'No se pudo validar las credenciales'));
                }
            }
        }

        if (Yii::app()->user->hasFlash('error'))
            HScript::registerCode('lunchFormModal', '$(function(){$("#btn_loginForm").click()})');

        $this->render('login');
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        $wsMsg = $this->wsClient->endSession();
        if ($wsMsg->error === 'S')
            Yii::log('Error al cerrar sesion en el WebService', 'error', 'app.SiteController');

        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionHalt()
    {
        Yii::app()->user->logout();
        Yii::app()->request->redirect(Yii::app()->createUrl('site/login', array('e' => base64_encode('El servicio se encuentra temporalmente inaccesible. Por favor intente más tarde.'))));
    }


    //Datos extra para cambio de perfil
    //Cambio de perfil
    public function actionCambioperfil()
    {
        //Desloguear
        $wsMsg = $this->wsClient->endSession();
        if ($wsMsg->error === 'S')
           Yii::log('Error al cerrar sesion en el WebService', 'error', 'app.SiteController');

        Yii::app()->user->logout();

        echo "ok";
    }



   

    public function decript256($textoLetra){
        $clave = "Hola.12345678#";
        // Se decodifican los datos base64
        $datos = base64_decode($textoLetra);
        // Obtener el tamaño del IV
        $iv_size = openssl_cipher_iv_length('aes-256-cbc');
        // Extraer el IV
        $iv = substr($datos, 0, $iv_size);
        // Extraer los datos cifrados (sin el IV)
        $cifrado = substr($datos, $iv_size);
        // Descifrar los datos utilizando AES-256 en modo CBC
        $descifrado = openssl_decrypt($cifrado, 'aes-256-cbc', $clave, 0, $iv);
    
    return $descifrado;
        
    }


  
}
