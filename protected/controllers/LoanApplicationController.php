<?php

Yii::import('booster.widgets.TbForm');

/**
 * Controlador de Solicitud de Préstamos
 */
class LoanApplicationController extends Controller
{

  private $_offices;
  private $_docTypes;
  private $_bancos;

  protected function beforeAction($action){
      return true;
  }

  public function actionPersonalLoan()
  {

    $model = new PersonalLoanForm;

    if(isset($_POST['PersonalLoanForm'])){

      $type=$this->wsClient->getModeApplication(array('type'=>'PP'));
      if($type->error==='N'){
        $type=$type->codigomodalidad;
      }
      $header=array(
        'sucursal'=>$_POST['PersonalLoanForm']['branchOffice'],
        'tipproducto'=>$type,
        'plazo'=>$_POST['PersonalLoanForm']['timeLimit'],
        'importe'=>$_POST['PersonalLoanForm']['amount'],
        'fecsolicitud'=>'04-11-2015',
      );
      $request=array('cabecera'=>$header,'detalles'=>array());
      $request=array('json'=>json_encode($request));
      $result=$this->wsClient->registerApplication($request);
      var_dump($result);

    }else{

      $this->getInitData();

      $form = new TbForm($model->formConfig($this->_offices,$this->_docTypes), $model);
      $wizardOptions = array(
        array(
          'title'=>Yii::t('loans', 'Datos'),
          'subtitle'=>Yii::t('loans', 'Ingrese los datos'),
          'elements'=>array('beneficiaryName','beneficiaryDocument', 'beneficiaryDocType' ,'timeLimit','amount','branchOffice'),
        ),
        array(
          'title'=>Yii::t('loans', 'Resumen'),
          'subtitle'=>Yii::t('loans', 'Verifique los datos del préstamo'),
          'view'=>'_wizardReview',
        ),
      );
      $this->render('personalLoan', array(
        'form' => $form,
        'wizardOptions' => $wizardOptions,
      ));

    }

  }

  public function actionCheckDiscounting(){

    $model = new CheckDiscountingForm;

    if(isset($_POST['CheckDiscountingForm'])){

      $type=$this->wsClient->getModeApplication(array('type'=>'DC'));
      if($type->error==='N'){
        $type=$type->codigomodalidad;
      }

      $header=array(
        'nroSolicitud'=>null,
        'sucursal'=>$_POST['CheckDiscountingForm']['branchOffice'],
        'tipoDocumento'=>null,
        'nroDocumento'=>$_POST['CheckDiscountingForm']['beneficiaryDocument'],
        'tipoProducto'=>$type,
        'plazo'=>$_POST['CheckDiscountingForm']['timeLimit'],
        'importe'=>$_POST['CheckDiscountingForm']['amount']
      );

      $header1=array(
        'sucursal'=>$_POST['CheckDiscountingForm']['branchOffice'],
        'tipproducto'=>'610',
        'plazo'=>$_POST['CheckDiscountingForm']['timeLimit'],
        'importe'=>$_POST['CheckDiscountingForm']['amount'],
        'fecsolicitud'=>'29-10-2015',
      );

      $details=array();
      $details1=array();
      foreach ($_POST['checkNumber'] as $i => $value) {
        if(strlen($value)==0)
          continue;

        $result=json_decode($this->uploadImage($_FILES,$i),true);
        if($result['error']==='S'){
          echo 'Error: '.$result['error'].' '.$result['errorMessage'];
        }else{
          $imageFile=$result['imageURL'];
        }

        $detail=array(
          'ctaCorriente'=>$_POST['account'][$i],
          'banco'=>$_POST['bank'][$i],
          'nroCheque'=>$_POST['checkNumber'][$i],
          'plazo'=>$_POST['checkLimit'][$i],
          'monto'=>$_POST['checkAmount'][$i],
          'fechaEmision'=>$_POST['date'][$i],
          'fechaVencimiento'=>$_POST['expirationDate'][$i],
          'imagen'=>$imageFile,
          'tipoDocLibrador'=>$_POST['drawerDocType'][$i],
          'nroDocLibrador'=>$_POST['drawerDocNumber'][$i],
          'librador'=>$_POST['drawerName'][$i],
        );

        $detail1=array(
          'tipdocumento'=>'C',
          'ctacte'=>$_POST['account'][$i],
          'monto'=>$_POST['checkAmount'][$i],
          'banco'=>$_POST['bank'][$i],
          'ctacte'=>$_POST['account'][$i],
          'nrocheque'=>$_POST['checkNumber'][$i],
          'tipdoclibrador'=>$_POST['drawerDocType'][$i],
          'nrodoclibrador'=>$_POST['drawerDocNumber'][$i],
          'imagen'=>$imageFile,
          'fechaemision'=>$_POST['date'][$i],
          'fechavencimiento'=>$_POST['expirationDate'][$i],
        );

        $details[$i]=$detail;
        $details1[$i]=$detail;

      }

      $request=array('cabecera'=>$header,'detalles'=>$details);
      $request1=array('cabecera'=>$header1,'detalles'=>$detail1);
      $request1=array('json'=>json_encode($request1));

      //var_dump($request1);
      echo '<br>';

      $result=$this->wsClient->registerApplication($request1);

      var_dump($result);

    }else{

      $this->getInitData();
      $form = new TbForm($model->formConfig($this->_offices,$this->_bancos,$this->_docTypes), $model);

      $wizardOptions = array(
        array(
          'title'=>Yii::t('loans', 'Datos'),
          'subtitle'=>Yii::t('loans', 'Ingrese los datos'),
          'elements'=>array('beneficiaryName','beneficiaryDocument', 'beneficiaryDocType', 'timeLimit','amount','branchOffice'),
        ),
        array(
          'title'=>Yii::t('loans', 'Detalles'),
          'subtitle'=>Yii::t('loans', 'Ingrese los detalles de cheques'),
          'elements'=>array(
            'bank','account','checkNumber','checkLimit',
            'checkAmount','date','expirationDate',
            'drawerDocType','drawerDocNumber','drawerName','noteImage'
          ),
        ),
        array(
          'title'=>Yii::t('loans', 'Resumen'),
          'subtitle'=>Yii::t('loans', 'Verifique los datos del préstamo'),
          'view'=>'_wizardReview',
        ),
      );

      $this->render('checkDiscounting', array(
        'form' => $form,
        'wizardOptions' => $wizardOptions,
      ));

    }

  }

  public function actionPromissoryNoteDiscounting(){

    $model = new PromissoryNoteDiscountingForm;

    if(isset($_POST['PromissoryNoteDiscountingForm'])){

      $type=$this->wsClient->getModeApplication(array('type'=>'DD'));
      if($type->error==='N'){
        $type=$type->codigomodalidad;
      }

      $header1=array(
        'sucursal'=>$_POST['PromissoryNoteDiscountingForm']['branchOffice'],
        'tipproducto'=>null,
        'plazo'=>$_POST['PromissoryNoteDiscountingForm']['timeLimit'],
        'importe'=>$_POST['PromissoryNoteDiscountingForm']['amount'],
        'fecsolicitud'=>null,
      );

      $header=array(
        'nroSolicitud'=>null,
        'sucursal'=>$_POST['PromissoryNoteDiscountingForm']['branchOffice'],
        'tipoDocumento'=>null,
        'nroDocumento'=>$_POST['PromissoryNoteDiscountingForm']['beneficiaryDocument'],
        'tipoProducto'=>$type,
        'plazo'=>$_POST['PromissoryNoteDiscountingForm']['timeLimit'],
        'importe'=>$_POST['PromissoryNoteDiscountingForm']['amount']
      );

      $details=array();
      $details1=array();
      foreach ($_POST['checkNumber'] as $i => $value) {
        if(strlen($value)==0)
          continue;

        $result=json_decode($this->uploadImage($_FILES,$i));
        if($result['error']==='S'){
          echo 'Error: '.$result['error'].' '.$result['errorMessage'];
        }else{
          $imageFile=$result['imageURL'];
        }

        $detail=array(
          'ctaCorriente'=>$_POST['account'][$i],
          'banco'=>$_POST['bank'][$i],
          'nroCheque'=>$_POST['checkNumber'][$i],
          'plazo'=>$_POST['checkLimit'][$i],
          'monto'=>$_POST['checkAmount'][$i],
          'fechaEmision'=>$_POST['date'][$i],
          'fechaVencimiento'=>$_POST['expirationDate'][$i],
          'imagen'=>$imageFile,
          'tipoDocLibrador'=>$_POST['drawerDocType'][$i],
          'nroDocLibrador'=>$_POST['drawerDocNumber'][$i],
          'librador'=>$_POST['drawerName'][$i],
        );

        $detail1=array(
          'tipdocumento'=>'C',
          'ctacte'=>$_POST['account'][$i],
          'monto'=>$_POST['checkAmount'][$i],
          'banco'=>$_POST['bank'][$i],
          'ctacte'=>$_POST['account'][$i],
          'nrocheque'=>$_POST['checkNumber'][$i],
          'tipdoclibrador'=>$_POST['drawerDocType'][$i],
          'nrodoclibrador'=>$_POST['drawerDocNumber'][$i],
          'imagen'=>$imageFile,
        );

        $details[$i]=$detail;
        $details1[$i]=$detail;

      }

      $request=array('cabecera'=>$header,'detalles'=>$details);
      $request1=array('cabecera'=>$header1,'detalles'=>$details1);

      $result=$this->wsClient->registerApplication($request1);

      //var_dump($result);


    }else{

      $this->getInitData();
      $form = new TbForm($model->formConfig($this->_offices,$this->_docTypes), $model);


      $wizardOptions = array(
        array(
          'title'=>Yii::t('loans', 'Datos'),
          'subtitle'=>Yii::t('loans', 'Ingrese los datos'),
          'elements'=>array('beneficiaryName','beneficiaryDocument', 'beneficiaryDocType', 'timeLimit','amount','branchOffice'),
        ),
        array(
          'title'=>Yii::t('loans', 'Detalles'),
          'subtitle'=>Yii::t('loans', 'Ingrese los detalles de cheques'),
          'elements'=>array(
            'noteLimit','noteAmount','date','expirationDate',
            'drawerDocType','drawerDocNumber','drawerName','noteImage'
          ),
        ),
        array(
          'title'=>Yii::t('loans', 'Resumen'),
          'subtitle'=>Yii::t('loans', 'Verifique los datos del préstamo'),
          'view'=>'_wizardReview',
        ),
      );

      $this->render('promissoryNoteDiscounting', array(
        'form' => $form,
        'wizardOptions' => $wizardOptions,
      ));

    }

  }

  public function actionQueryForm(){

    if(isset($_POST) && !empty($_POST)){
			$fromDate=$_POST['fromDate'];
			$toDate=$_POST['toDate'];
      $fromApplicationId=isset($_POST['fromApplicationId'])?$_POST['fromApplicationId']:null;
      $toApplicationId=isset($_POST['toApplicationId'])?$_POST['toApplicationId']:null;
      $status=$_POST['status'];
		}else{
			$fromDate = DateTime::createFromFormat('d/m/Y',date('d/m/Y'))->format('d/m/Y');
			$toDate = DateTime::createFromFormat('d/m/Y',date('d/m/Y'))->format('d/m/Y');
      $fromApplicationId=null;
      $toApplicationId=null;
      $status='I';
		}

    $transfers=array();

    $transfers=$this->wsClient->getApplications(array(
      'applicationNumberFrom'=>$fromApplicationId,
      'applicationNumberTo'=>$toApplicationId,
      'dateFrom'=>$fromDate,
      'untilDate'=>$toDate,
      'state'=>$status,
    ));

    if(isset($transfers->listasolicitudes->array)){
      $transfers=$transfers->listasolicitudes->array;
    }else{
      $transfers=array();
    }

    $dataProvider1 = new CArrayDataProvider(
      $transfers,
      array(
        'keyField' => 'numerosolicitud',
        'pagination'	=> array('pageSize'=>20),
      )
    );

    $this->render('queryForm', array('fromDate' => $fromDate,
      'toDate' => $toDate,
      'dataProvider1'=>$dataProvider1,
      'fromApplicationId'=>$fromApplicationId,
      'toApplicationId'=>$toApplicationId,
      'status'=>$status,
     ));
  }

  public function getInitData(){

    $offices = $this->wsClient->getOffices();
    $offices = $offices->informacionoficinas->array;
    $this->_offices = json_decode(json_encode($offices),true);
    $this->_offices=array_combine($this->array_column($this->_offices, 'codigooficina'),$this->array_column($this->_offices, 'descripcion'));

    $docTypes = $this->wsClient->getDocumenttyp();
    $docTypes = $docTypes->informaciondocumentos->array;
    $this->_docTypes = json_decode(json_encode($docTypes),true);
    $this->_docTypes = array_combine($this->array_column($this->_docTypes, 'codigotipodocumento'),$this->array_column($this->_docTypes, 'descripcion'));

    $bancos = $this->wsClient->getBanks();
    $bancos = $bancos->informacionbancos->array;
    $this->_bancos = json_decode(json_encode($bancos),true);
    $this->_bancos = array_combine($this->array_column($this->_bancos, 'codigobanco'),$this->array_column($this->_bancos, 'descripcion'));

  }

  public function uploadImage($image,$index){

    $target_dir = "uploads/";
    $finalWidth = 1024;
    $finalHeight = 1024;
    $error = 'N';

    if(!isset($image['noteImage']['name'][$index])){
      $error = 'S';
      $errorMessage='La imágen no existe';
      return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
    }

    $check = getimagesize($image['noteImage']["tmp_name"][$index]);
    if($check === false) {
      $error = 'S';
      $errorMessage='El archivo cargado no es una imágen';
      return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
    }

    $imageFileType = pathinfo($image['noteImage']['name'][$index],PATHINFO_EXTENSION);
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
      $error = 'S';
      $errorMessage='La imágen no es un jpg, jpeg, gif o png válido';
      return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
    }

    $target_file=$target_dir.date('dmY').'_'.$index.'.'.$imageFileType;
    if(!move_uploaded_file($image['noteImage']["tmp_name"][$index], $target_file)){
      $error = 'S';
      $errorMessage='La imágen no fue subida';
      return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
    }

    list($originalWidth, $originalHeight) = getimagesize($target_file);
    $originalRatio = $originalWidth/$originalHeight;

    if($originalWidth>$finalWidth || $originalHeight>$finalHeight){
      return $this->imageResample($originalRatio, $originalWidth, $originalHeight, $finalWidth, $finalHeight, $imageFileType, $target_file);
    }else{
      return json_encode(array('error'=>$error,'imageURL'=>$target_file));
    }

  }

  public function imageResample($originalRatio, $originalWidth, $originalHeight, $finalWidth, $finalHeight, $imageFileType, $target_file){

    $error='N';

    $originalRatio = $originalWidth/$originalHeight;

    if ($finalWidth/$finalHeight > $originalRatio) {
      $finalWidth = $finalHeight*$originalRatio;
    } else {
      $finalHeight = $finalWidth/$originalRatio;
    }

    $imageNew = imagecreatetruecolor($finalWidth, $finalHeight);
    if($imageNew===false){
      $error = 'S';
      $errorMessage='Error al crear nueva imágen';
      return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
    }

    switch ($imageFileType) {
      case 'jpg':
        $image = imagecreatefromjpeg($target_file);
        if(!$image){
          $error = 'S';
          $errorMessage='Error al copiar imágen';
          return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
        }
        if(!imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $finalWidth, $finalHeight, $originalWidth, $originalHeight)){
          $error = 'S';
          $errorMessage='Error al redimensionar imágen';
          return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
        }
        if(!imagejpeg($imageNew,$target_file)){
          $error = 'S';
          $errorMessage='Error al grabar imágen';
          return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
        }
        break;
      case 'png':
        $image = imagecreatefrompng($target_file);
        if($image){
          $error = 'S';
          $errorMessage='Error al copiar imágen';
          return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
        }
        if(!imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $finalWidth, $finalHeight, $originalWidth, $originalHeight)){
          $error = 'S';
          $errorMessage='Error al redimensionar imágen';
          return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
        }
        break;
        if(!imagepng($imageNew,$target_file)){
          $error = 'S';
          $errorMessage='Error al grabar imágen';
          return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
        }
      case 'jpeg':
        $image = imagecreatefromjpeg($target_file);
        if($image){
          $error = 'S';
          $errorMessage='Error al copiar imágen';
          return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
        }
        if(!imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $finalWidth, $finalHeight, $originalWidth, $originalHeight)){
          $error = 'S';
          $errorMessage='Error al redimensionar imágen';
          return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
        }
        if(!imagejpeg($imageNew,$target_file)){
          $error = 'S';
          $errorMessage='Error al grabar imágen';
          return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
        }
        break;
      case 'gif':
        $image = imagecreatefromgif($target_file);
        if($image){
          $error = 'S';
          $errorMessage='Error al copiar imágen';
          return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
        }
        if(!imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $finalWidth, $finalHeight, $originalWidth, $originalHeight)){
          $error = 'S';
          $errorMessage='Error al redimensionar imágen';
          return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
        }
        if(!imagegif($imageNew,$target_file)){
          $error = 'S';
          $errorMessage='Error al grabar imágen';
          return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
        }
        break;
    }

    return json_encode(array('error'=>$error,'imageURL'=>$target_file));

  }

  public function ftpUpload($target_file){

    $error='N';
    $ftp_server='127.0.0.1';
    $connID = ftp_connect($ftp_server);
    if($connID===false){
      $error = 'S';
      $errorMessage='Error al conectar al ftp';
      return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
    }
    if(!ftp_login($connID, $ftp_user_name, $ftp_user_pass)){
      $error = 'S';
      $errorMessage='Error al autenticar con ftp';
      return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
    }
    if (!ftp_put($connID, $target_file, $target_file, FTP_ASCII)) {
      $error = 'S';
      $errorMessage='Error al grabar imágen al ftp';
      return json_encode(array('error'=>$error,'errorMessage'=>$errorMessage));
    }
    ftp_close($connID);
    return json_encode(array('error'=>$error));

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
