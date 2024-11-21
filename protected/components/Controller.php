<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/backend';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();

    /**
     * Cliente del webservice
     * @var WebServiceClient Clase alojada en components
     * @access public
     */
    protected $wsClient = null;

    /**
     * Lenguaje del usuario
     */
    protected $language = null;
    public $permissionsID;

    protected function setFlash($cat, $msg)
    {
        Yii::app()->user->setFlash($cat, $msg);
    }

    protected function setFlashError($msg)
    {
        $this->setFlash('error', $msg);
    }

    protected function setFlashSuccess($msg)
    {
        $this->setFlash('success', $msg);
    }

    protected function beforeAction($action)
    {
        $this->checkSessionCookie();

        $this->checkAccessToCurrentAction();

        $this->checkPasswordChangeRequired();

        $this->checkDetectIdUserStatus();

        return parent::beforeAction($action);
    }


    public function init()
    {
        parent::init();

        if (isset(Yii::app()->request->cookies['appLanguage']))
            Yii::app()->language = Yii::app()->request->cookies['appLanguage']->value;
        if (isset(Yii::app()->request->cookies['appTheme']))
            Yii::app()->theme = Yii::app()->request->cookies['appTheme']->value;

        if (empty(Yii::app()->theme)) Yii::app()->theme = 'itgf_hb';

        try {
            $this->wsClient = new WebServiceClient();
        } catch (Exception $e) {
            $this->render('error', array(
                'code' => 503,
                'type' => 'CHttpException',
                'message' => 'Error: ' . $e->getMessage(),
            ));
            Yii::app()->end();
        }

        // actualizar la lista de cuentas en la sesion despues de 3 minutos
        if (!Yii::app()->user->isGuest && time() - Yii::app()->session['accountList']['time'] > 3 * 60) { //3 minutos
            Yii::app()->user->accounts->refresh();
        }
    }

    /**
     * Return data to browser as JSON and end application.
     * @param array $data
     */
    protected function renderJSON($data)
    {
        header('Content-type: application/json');
        echo CJSON::encode($data);

        foreach (Yii::app()->log->routes as $route) {
            if ($route instanceof CWebLogRoute) {
                $route->enabled = false; // disable any weblogroutes
            }
        }
        Yii::app()->end();
    }

    /**
     * Busca un cuenta por su hash temporal
     * @throws CHttpException 404
     */
    protected function _accountData($hash)
    {
        $account = Yii::app()->user->accounts->getByHash($hash);
        if (isset($account)) {
            return $account;
        } else {
            throw new CHttpException(404, 'Cuenta no encontrada');
        }
    }

    protected function checkSessionCookie()
    {
        header("X-Frame-Options: DENY");
        $addr = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

        if (isset(Yii::app()->session['REMOTE_ADDR'])) {
            $storedAddr = Yii::app()->session['REMOTE_ADDR'];
            if ($storedAddr != $addr) {
                Yii::log('warning', "REMOTE_ADDR changed from $storedAddr to $addr");
                Yii::app()->user->logout();
                Yii::app()->request->redirect(Yii::app()->createUrl('site/login'));
            }
        } else {
            Yii::app()->session['REMOTE_ADDR'] = $addr;
        }
    }

    protected function checkAccessToCurrentAction()
    {
        // permitir acceso solamente a acciones permitidas desde el core
        // los controladores user, site y appConfig son seguros
        /*if (
            !$this->actionAllowed() &&
            !in_array($this->id, ['user', 'site', 'appConfig', 'report'])
        ) {
            $this->setFlash('error', Yii::t('layouts', 'No posee credenciales para esta acción!'));
            $this->redirect(array('/site/index'));
        }*/
    }

    protected function checkPasswordChangeRequired()
    {
        // verificar si no se solicita cambio de clave de acceso
        if (
            Yii::app()->user->getState('changePassword') &&
            !(
                ($this->id === 'user' && $this->action->id === 'changeAccessPassword') ||
                ($this->id === 'site' && $this->action->id === 'logout') ||
                ($this->id === 'appConfig' && $this->action->id === 'language') ||
                ($this->id === 'site' && $this->action->id === 'status'))
        ) {
            $this->setFlash('warning', Yii::t('layouts', Yii::app()->user->getState('changePassword')));
            $this->redirect(array('/user/changeAccessPassword'));
        }
    }

    protected function checkDetectIdUserStatus()
    {
        $userDetails = Yii::app()->user->getState('clientArea');
        $sharedKey = Yii::app()->user->getState('sharedKey');

        if (
            $userDetails &&
            !$sharedKey &&
            !(
                ($this->id === 'user' && $this->action->id === 'changeAccessPassword') ||
                ($this->id === 'user' && $this->action->id === 'detectidregister') ||
                ($this->id === 'site' && $this->action->id === 'logout') ||
                ($this->id === 'appConfig' && $this->action->id === 'language') ||
                ($this->id === 'site' && $this->action->id === 'status'))
        ) {
            $detectId = Yii::app()->detectId;
            $sharedKey = $userDetails['sharedKeyFromDatas'];
            $response = $detectId
                ->clientService
                ->isClientPresent(compact('sharedKey'));

            if ($response->return->resultCode !== 503) {
                Yii::log('warning', 'El usuario no está registrado en DetectId');
                $this->redirect(array('/user/detectidregister'));
            } else {
                $this->wsClient->registerShareKey([
                    'shareKey' => $sharedKey,
                ]);
                Yii::app()->user->setState('sharedKey', $sharedKey);
            }
        }
    }

    protected function actionAllowed(): bool
    {
        if (isset(Yii::app()->user->availableUrls)) {
            $availableUrls = Yii::app()->user->availableUrls;
            $id = Yii::app()->getRequest()->getQuery('id');
            $url = $this->uniqueid . '/' . $this->action->Id;
            $availableUrls = json_decode(json_encode($availableUrls), true);
            foreach ($availableUrls as $rows) {
                if (in_array($url, $rows) || in_array($url . '/' . $id, $rows)) {
                    return true;
                }

                // en algunas instalaciones $id es nulo cuando se consulta
                // a sipapTransfers/transfer/C o sipapTransfers/transfer/B
                // entonces solamente se puede comparar con sipapTransfers/transfer
                if ($url == 'sipapTransfers/transfer') {
                    if (in_array($url . '/C', $rows) || in_array($url . '/B', $rows)) {
                        return true;
                    }
                }

                // en caso de que existan permisos para acceder a transfer/form
                // tambien debe permitir acceder al action transfer/resendSms
                if ($url === 'transfer/resendSms' && in_array('transfer/form', $rows)) {
                    return true;
                }

                // en caso de que existan permisos para acceder a sipapTransfers/Verify
                // tambien debe permitir acceder al action sipapTransfers/resendSms
                if ($url === 'sipapTransfers/resendSms' && in_array('sipapTransfers/Verify', $rows)) {
                    return true;
                }

                // en caso de que existan permisos para acceder a bancard/list
                // tambien debe permitir acceder al action bancard/resendSms
                if ($url === 'bancard/resendSms' && in_array('bancard/list', $rows)) {
                    return true;
                }

                // en caso de que existan permisos para acceder a creditCard/extracts
                // tambien debe permitir acceder al action creditCard/previousPeriod
                if ($url === 'creditCard/previousPeriod' && in_array('creditCard/extracts', $rows)) {
                    return true;
                }
            }
        }

        return false;
    }
}
