<?php
Yii::import('booster.widgets.TbForm');

class UpdateProfileController extends Controller{

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
      array('allow',
        'users'=>array('@'),
      ),
      array('deny',  // deny all users
        'users'=>array('*'),
      ),
    );
  }

  public function actionData(){

    $model = new UpdateProfileForm;

    if(isset($_POST['UpdateProfileForm'])){

      $cabecera = array(
        'email' => $_POST['UpdateProfileForm']['email'],
        'tipoExtracto' => $_POST['UpdateProfileForm']['summaryType'],
        'esRetener' => $_POST['UpdateProfileForm']['hold'],
        'oficinaCliente' => $_POST['UpdateProfileForm']['clientOffice'],
        'indOficinaCurrier' => $_POST['UpdateProfileForm']['courierOffice'],
      );

      $telefonos=array();
      $i=0;
      $procesar=true;
      foreach ($_POST['UpdateProfileForm']['telephoneNumber'] as $value) {

        if(isset($_POST['UpdateProfileForm']['telephoneCode'][$i]) &&
            strlen($_POST['UpdateProfileForm']['telephoneCode'][$i])>0){
          if(strlen($_POST['UpdateProfileForm']['telephoneNumber'][$i])>0){
            $accion='A';
          }else{
            $accion='E';
          }
        }else{
          if(strlen($_POST['UpdateProfileForm']['telephoneNumber'][$i])>0){
              $accion='N';
          }else{
              $procesar=false;
          }
        }

        if($procesar){
            $telefonos[] = array(
              'area' => $_POST['UpdateProfileForm']['areaCode'][$i],
              'interno' => $_POST['UpdateProfileForm']['extensionNumber'][$i],
              'telefono' => $_POST['UpdateProfileForm']['telephoneNumber'][$i],
              'tipoLinea' => $_POST['UpdateProfileForm']['lineType'][$i],
              'tipo' => $_POST['UpdateProfileForm']['telephoneType'][$i],
              'principal' => $_POST['UpdateProfileForm']['isDefaultTelephone'][$i],
              'codigoTelefono' => $_POST['UpdateProfileForm']['telephoneCode'][$i],
              'accion' => $accion,
            );
        }

        $i++;
      }

      $direcciones=array();
      $i=0;
      foreach ($_POST['UpdateProfileForm']['address'] as $value) {
        if(strlen($_POST['UpdateProfileForm']['addressCode'][$i])>0
          ){
          $direcciones[] = array(
            'tipo' => $_POST['UpdateProfileForm']['addressType'][$i],
            'principal' => $_POST['UpdateProfileForm']['isDefaultAddres'][$i],
            'codigoCiudad' => $_POST['UpdateProfileForm']['city'][$i],
            'direccion' => $_POST['UpdateProfileForm']['address'][$i],
            'codigoBarrio' => $_POST['UpdateProfileForm']['district'][$i],
            'tipoEnvio' => $_POST['UpdateProfileForm']['shippingType'][$i],
            'codigoDireccion' => $_POST['UpdateProfileForm']['addressCode'][$i],
            'accion' => 'A',
          );
        }
        $i++;
      }

      $json = array('cabecera'=>$cabecera,'direcciones'=>$direcciones,'telefonos'=>$telefonos);
      $json = json_encode($json,true);
      //echo $json;

      $result = $this->wsClient->managementData(array("mode"=>'C',"json"=>$json));
	    if($result->error === 'S') {
        $this->setFlashError($result->descripcionrespuesta);
      }else{
        $this->setFlashSuccess($result->descripcionrespuesta);
      }
      $this->redirect(array('/updateProfile/data'));
    }else{
      $form = new TbForm($model->formConfig($this->getCities()), $model);
      $this->render('data',array('form'=>$form));
    }

    Yii::app()->end();
  }

  public function actionGetInitData(){
    $result = $this->wsClient->managementData(array("mode"=>'P',"json"=>""));
    if($result->error==='N'){
      @$direcciones=$result->listadirecciones->array;
      @$telefonos=$result->listatelefonos->array;
    }

    $cabecera = array(
      'email' => $result->email,
      'esretener' => $result->esretener,
      'indoficinacurrier' => $result->indoficinacurrier,
      'tipoextracto'=>isset($result->tipoextracto)?$result->tipoextracto:'',
      'oficinacliente'=>isset($result->oficinacliente)?$result->oficinacliente:'',
    );

    $barrios=array();
    $i=1;
    if(count($direcciones)){
      foreach ($direcciones as $key => $value) {
        if($i==3){
          break;
        }
        $barrios[]=$this->actionGetDistrict($value->codigociudad);
        $i++;
      }
    }
    echo json_encode(array('cabecera'=>$cabecera,'direcciones'=>$direcciones,'telefonos'=>$telefonos,'barrios'=>$barrios));
  }

  public function getCities(){
    $result = $this->wsClient->getCities(array("codecountry"=>'586'));

    if (empty($result->listaciudades->array)) {
      return [];
    }

    $ciudades = json_decode(json_encode($result->listaciudades->array),true);
    $ciudades = array_combine($this->array_column($ciudades, 'codigociudad'),$this->array_column($ciudades, 'descripcion'));
    $ciudades[0]='Seleccione una ciudad';
    return $ciudades;
  }

  public function actionGetDistrict($id){
    $params=explode('-',$id);
    if(count($params)>1){
      $result = $this->wsClient->getDistrict(array("codecity"=>$params[0]));
      if($result->error==='N'){
        $barrios = json_decode(json_encode($result->listabarrios->array),true);
        $barrios=array_combine($this->array_column($barrios, 'codigobarrio'),$this->array_column($barrios, 'descripcion'));
        echo json_encode(array('barrios'=>$barrios));
      }else{
        echo json_encode(array('barrios'=>array()));
      }
    }else{
      $result = $this->wsClient->getDistrict(array("codecity"=>$id));
      if($result->error==='N'){
        $barrios = json_decode(json_encode($result->listabarrios->array),true);
        $barrios=array_combine($this->array_column($barrios, 'codigobarrio'),$this->array_column($barrios, 'descripcion'));
        return $barrios;
      }else{
        return array();
      }
    }
  }

  public function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( ! isset($value[$columnKey])) {
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( ! isset($value[$indexKey])) {
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
  }

}
