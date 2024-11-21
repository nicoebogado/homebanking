<?php

/**
 * Clase para manejar las cuentas del usuario
 *
 * Atributos:
 * accountNumber string Cuenta de la cuenta cifrada con cadena aleatoria
 * denomination string Denominacion de la cuenta
 * currency string Moneda de la cuenta
 * accountType string Tipo de cuenta
 * accountTypeDesc string Descripcion del tipo de cuenta
 * credit string Credito disponible
 */
class Accounts
{
    private $_wsClient = null;

    public function __call($name, $args)
    {
        $name = '_' . $name;
        if (method_exists($this, $name)) {

            //if($this->_wsClient === null)
            $this->_wsClient = new WebServiceClient;

            return $this->$name($args);
        } else
            throw new CException('El método ' . $name . ' no existe');
    }

    /**
     * Inicializa las variables de sesion
     */
    private function _set()
    {
        // recuperar resumen de cuentas
        $accounts = $this->_wsClient->getAccountSummary();
        if ($accounts->error === 'S') {
            Yii::app()->user->setFlash('error', $accounts->descripcionrespuesta);
            Yii::app()->session['accountList'] = array('time' => null);
            return;
        }

        $accounts = $accounts->resumencuentas->array;
        $aList = array('time' => time());
        $algos = hash_algos();

        foreach ($accounts as $k => $val) {
            $hash = substr(hash($algos[array_rand(hash_algos())], $val->numerocuenta), 0, 30);
            $aList[] = $this->_accountToArray($val, $hash);
        }

        Yii::app()->session['accountList'] = $aList;
    }

    public static function maskAccountNumber($accountNumber)
    {
        //return '**********' . substr($accountNumber, -4);
        return substr($accountNumber, 0,6).'******' . substr($accountNumber, -4);
    }

    /**
     * Refresca las variables de sesion manteniendo los ids encriptados
     */
    private function _refresh()
    {
        // recuperar resumen de cuentas
        $accounts = $this->_wsClient->getAccountSummary();
        if ($accounts->error === 'S') {
            Yii::app()->user->setFlash('error', $accounts->descripcionrespuesta);
            Yii::app()->session['accountList'] = array('time' => null);
            return;
        }

        $accounts = $accounts->resumencuentas->array;
        $raList = Yii::app()->session['accountList']; //raList es temporal, para recorrer en el foreach
        unset($raList['time']);
        $aList = array('time' => time());
        $algos = hash_algos();

        foreach ($accounts as $k => $val) {

            $f = false;
            if (isset($raList)) {
                foreach ($raList as $kl => $row) {
		  if (isset($row['accountNumber'])) {	
                    if ($row['accountNumber'] === $val->numerocuenta) { //verificar que los nros de cuenta correspondan
                        $f = true;
                        $aList[] = $this->_accountToArray($val, $row['hash']);
                        unset($raList[$kl]); //borrar de la lista temporal para optimizar los siguientes recorridos
                        break;
                    }
                }
            }
            }

            if (!$f) { //no se encontro el nro de cuenta en la variable de sesion, nueva cuenta?
                $hash = substr(hash($algos[array_rand(hash_algos())], $val->numerocuenta), 0, 30);
                $aList[] = $this->_accountToArray($val, $hash);
            }
        }

        Yii::app()->session['accountList'] = $aList;
    }

    /**
     * Retorna un array con los atributos de las cuentas
     *
     * $conditions es un array del tipo:
     *  array(
     *      'attribute'=>value
     *  )
     * $hashKey Si se establecera como llave del array al hash de la cuenta
     */
    public function getList($options = array())
    {
        $defaults = array(
            'conditions'    => null,
            'hashKey'       => false,
        );
        $opts = array_merge($defaults, $options);

        $aList = Yii::app()->session['accountList'];
        unset($aList['time']);

        if ($opts === $defaults) return $aList;

        $list = array();

        foreach ($aList as $k => $val) {
            if (empty($opts['conditions']) || $this->_searchConditionMatch($opts['conditions'], $val)) {
                if ($opts['hashKey'])
                    $list[$val['hash']] = $val;
                else
                    $list[] = $val;
            }
        }
        return $list;
    }

    public function getByHash($hash, $attribute = null)
    {
        $accountList = $this->getList(array('hashKey' => true));

        if (empty($accountList[$hash])) return null;

        $account = $accountList[$hash];

        if (isset($attribute)) {
            if (empty($account[$attribute])) return null;
            return $account[$attribute];
        }

        return $account;
    }

    /**
     * Retorna un array del tipo:
     *  array(
     *      'hashCuentaSesion'=>$label
     *  )
     * Donde $label es el formato de la etiqueta en la cual se pueden acceder
     * a los atributos de la cuenta por medio de {attribute}
     */
    public function getLabelList($options = array())
    {
        $defaults = array(
            'conditions'    => null,
            'label'         => (Yii::app()->params['maskedAccountNumber'] == 'N') ? '{accountNumber} {denomination} - {currency}' : '{maskedAccountNumber} {denomination} - {currency}',
            // 'label'          => 'Cta {maskedAccountNumber} {denomination} - {currency} {credit}',
        );
        $opts = array_merge($defaults, $options);

        $aList = Yii::app()->session['accountList'];
        unset($aList['time']);
        $list = array();

        foreach ($aList as $k => $val) {
            if ($opts['conditions'] === null || $this->_searchConditionMatch($opts['conditions'], $val)) {
                $pattern = '/\{(\w+)\}/';
                // $replacement = '"$1" == "credit" ? Yii::app()->numberFormatter->formatDecimal($val[\'$1\']) : $val[\'$1\']';
                $list[$val['hash']] = preg_replace_callback($pattern, function ($m) use ($val) {
                    return $m[1] == 'credit' ? Yii::app()->numberFormatter->formatDecimal($val[$m[1]]) : $val[$m[1]];
                }, $opts['label']);
            }
        }

        return $list;
    }

    public function getLabelBalanceList($options = array())
    {
        $defaults = array(
            'conditions'    => null,
            'label'         => (Yii::app()->params['maskedAccountNumber'] == 'N') ? '{accountNumber} {denomination} - {currency} {credit}' : '{maskedAccountNumber} {denomination} - {currency} {credit}',
            // 'label'          => 'Cta {maskedAccountNumber} {denomination} - {currency} {credit}',
        );
        $opts = array_merge($defaults, $options);

        $aList = Yii::app()->session['accountList'];
        unset($aList['time']);
        $list = array();

        foreach ($aList as $k => $val) {
            if ($opts['conditions'] === null || $this->_searchConditionMatch($opts['conditions'], $val)) {
                $pattern = '/\{(\w+)\}/';
                // $replacement = '"$1" == "credit" ? Yii::app()->numberFormatter->formatDecimal($val[\'$1\']) : $val[\'$1\']';
                $list[$val['hash']] = preg_replace_callback($pattern, function ($m) use ($val) {
                    return $m[1] == 'credit' ? Yii::app()->numberFormatter->formatDecimal($val[$m[1]]) : $val[$m[1]];
                }, $opts['label']);
            }
        }

        return $list;
    }

    /**
     * Retorna un array del tipo:
     *  array(
     *      'columns' => array(*campos de session['accountList']*)
     *      'datas' => array(*datos filtrados, el valor hash se agrega por defecto*)
     *  )
     */
    public function getGridArray($options = array(), $opcion = null)
    {
        /**
         * formato de cada campo de session['accountList']
         * para el parametro columns de CGridView
         */
        $columnsConfig = array(
            'hash' => 'hash:text:Hash',
            'accountNumber' => 'accountNumber:text:' . Yii::t('commons', 'Nro. Cuenta'),
            'maskedAccountNumber' => 'maskedAccountNumber:text:' . Yii::t('commons', 'Cuenta'),
            'accountType' => 'accountType:text:' . Yii::t('commons', 'Tipo Cuenta'),
            'accountTypeDesc' => 'accountTypeDesc:text:' . Yii::t('commons', 'Tipo Cuenta'),
            'denomination' => 'denomination:text:' . Yii::t('commons', 'Descripción'),
            'currency' => 'currency:text:' . Yii::t('commons', 'Moneda'),
            'credit' => array(
                'name' => Yii::t('commons', 'Crédito'),
                'value' => 'Yii::app()->numberFormatter->formatDecimal($data["credit"])',
            ),
        );


        if ($opcion == null) {
            $defaults = array(
                'conditions' => null,
                'columns' => array(
                    (Yii::app()->params['maskedAccountNumber'] == 'N') ? 'accountNumber' : 'maskedAccountNumber',
                    'accountTypeDesc',
                    'denomination',
                    'currency',
                    'credit',
                ),
            );
        } elseif ($opcion == 'TA') {

            $columnsConfig = array(
                'hash' => 'hash:text:Hash',
                'accountNumber' => 'accountNumber:text:' . Yii::t('commons', 'Nro. Cuenta'),
                'maskedAccountNumber' => 'maskedAccountNumber:text:' . Yii::t('commons', 'Cuenta'),
                'accountType' => 'accountType:text:' . Yii::t('commons', 'Tipo Cuenta'),
                'accountTypeDesc' => 'accountTypeDesc:text:' . Yii::t('commons', 'Tipo Cuenta'),
                'denomination' => 'denomination:text:' . Yii::t('commons', 'Descripción'),
                'currency' => 'currency:text:' . Yii::t('commons', 'Moneda'),
                'credit' => array(
                    'name' => Yii::t('commons', 'Saldo Disponible'),
                    'value' => 'Yii::app()->numberFormatter->formatDecimal($data["credit"])',
                ),
            );

            $defaults = array(
                'conditions' => null,
                'columns' => array(
                    (Yii::app()->params['maskedAccountNumber'] == 'N') ? 'accountNumber' : 'maskedAccountNumber',
                    'accountTypeDesc',
                    'denomination',
                    'currency',
                    'credit',
                ),
            );
        } else {
            $defaults = array(
                'conditions' => null,
                'columns' => array(
                    'accountNumber',
                    'accountTypeDesc',
                    'denomination',
                    'currency',
                    'credit',
                ),
            );
        }

        $opts = array_merge($defaults, $options);

        $aList = Yii::app()->session['accountList'];
        unset($aList['time']);
        // filtrar configuracion de columnas
        $list = array(
            'columns' => array_intersect_key($columnsConfig, array_flip($opts['columns'])),
            'datas' => array(),
        );

        foreach ($aList as $k => $val) {
            if ($opts['conditions'] === null || $this->_searchConditionMatch($opts['conditions'], $val)) {
                // filtrar
                $row = array_intersect_key($val, array_flip($opts['columns']));
                // agregar el valor hash
                $row['hash'] = $val['hash'];
                $list['datas'][] = $row;
            }
        }

        return $list;
    }

    private function _accountToArray($account, $hash)
    {
       	$doMask = isset($account->tipocuenta) ? $account->tipocuenta : '' === 'TJ';
        $a = [
            'hash'                  => $hash,
            'accountNumber'         => $account->numerocuenta,
            'maskedAccountNumber'   => self::maskAccountNumber($account->numerocuenta),
            'denomination'          => $account->denominacion,
            'currency'              => $account->codigomoneda,
            'accountType'           => isset($account->tipocuenta) ? $account->tipocuenta : '',
            'accountTypeDesc'       => $account->descripciontipocuenta,
            'credit'                => isset($account->saldoactual) ? str_replace(',', '.', $account->saldoactual) : '',
        ];

        if (isset($account->numerotarjetaprincipal)) {
            $a['creditCardNumber'] = $account->numerotarjetaprincipal;
            $a['maskedCreditCardNumber'] = self::maskAccountNumber($account->numerotarjetaprincipal);
        }

        return $a;
    }

    /**
     * Verifica si las condiciones de $conditionsArray son cumplidas en $subject
     * @param $conditionsArray array con las condiciones buscadas
     *  (array('attribute'=>'val1, val2'))
     *  Tambien se puede especificar la operacion en el indice '__operType__'
     *  El tipo de operacion puede ser '&&' o '||'
     * @param $subject array en donde buscar las condiciones
     */
    private function _searchConditionMatch($conditionsArray, $subject)
    {

        // set operation (&& or ||)
        if (isset($conditionsArray['__operType__'])) {
            $operation = $conditionsArray['__operType__'];
            unset($conditionsArray['__operType__']);
        } else
            $operation = '&&';

        foreach ($conditionsArray as $k => $conditions) {
            $c = explode(',', $conditions);
            $match = false; // if match some of the items from $c

            foreach ($c as $v) {
		if (isset($subject[$k])){
                if ($subject[$k] === trim($v)) {
                    if ($operation === '||') return true;
                    else $match = true;
		   }
                }
            }

            if ($operation === '&&' && !$match) {
                return false;
            }
        }

        return $match;
    }
}
