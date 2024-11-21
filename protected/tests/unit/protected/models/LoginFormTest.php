<?php
/**
 *
 */
class LoginFormTest extends CTestCase {

    /**
     * @var LoginForm
     */
    protected $object;
    

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new LoginForm;
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

        
    }

    /**
     * @covers LoginForm::rules
     * @todo   Implement testRules().
     */
    public function testRules() {
        // Remove the following lines when you implement this test.

        $this->assertInternalType('array', $this->object->rules());
                $this->assertEquals(array(
                array('data, password, dataType', 'required'),
                array('companyDocNum, companyDocType', 'safe'),
                array('verifyCode', 'captcha', 'allowEmpty'=>false),
                array('password', 'authenticate'),
            ), $this->object->rules());
        
    }

    /**
     * @covers LoginForm::attributeLabels
     * @todo   Implement testAttributeLabels().
     */
    public function testAttributeLabels() {
        // Remove the following lines when you implement this test.
        $this->assertInternalType('array', $this->object->attributeLabels());
        $this->assertEquals(array(
            'data'=>Yii::t('appModels', 'Nro. de Cliente'),
            'password'=>Yii::t('appModels', 'Clave de acceso'),
            'dataType'=>Yii::t('appModels', 'Tipo de acceso'),
            'companyDocNum'=>Yii::t('appModels', 'Dato de acceso de empresa'),
            'companyDocType'=>Yii::t('appModels', 'Tipo de acceso de empresa'),
            'verifyCode'=>Yii::t('appModels', 'Código de validación'),
            ), $this->object->attributeLabels());
    }

    /**
     * @covers LoginForm::authenticate
     * @todo   Implement testAuthenticate().
     */
    public function testAuthenticate() {
        
        $this->assertEquals(null, $this->object->authenticate('$attribute', '$params'));
  
    }

    /**
     * @covers LoginForm::login
     * @todo   Implement testLogin().
     * 
     * 
     */
    public function testLoginDatosCorrectos() {
                
        $this->object->data='77685';
        $this->object->password='e10adc3949ba59abbe56e057f20f883e';
        $this->object->dataType='K';
        
        $mockSession = $this->getMock('CHttpSession', array('regenerateID'));
        Yii::app()->setComponent('session', $mockSession);
        //$this->object->companyDocNum='';
        //$this->object->companyDocType='';
        $this->assertTrue($this->object->login());
        //$this->ident
       
    }
    
        /**
        * @covers LoginForm::login
        * @todo   Implement testLogin().
        * 
        * 
        */
       public function testLoginTipoDatoNulo() {

           $this->object->data='77685';
           $this->object->password='e10adc3949ba59abbe56e057f20f883e';
           $this->object->dataType='';

           $mockSession = $this->getMock('CHttpSession', array('regenerateID'));
           Yii::app()->setComponent('session', $mockSession);
           //$this->object->companyDocNum='';
           //$this->object->companyDocType='';
           $this->assertFalse($this->object->login());

       }
       
       /**
        * @covers LoginForm::login
        * @todo   Implement testLogin().
        * 
        * 
        */
       public function testLoginTipoDatoInexistente() {

           $this->object->data='77685453';
           $this->object->password='e10adc3949ba59abbe56e057f20f883e';
           $this->object->dataType='W';

           $mockSession = $this->getMock('CHttpSession', array('regenerateID'));
           Yii::app()->setComponent('session', $mockSession);
           //$this->object->companyDocNum='';
           //$this->object->companyDocType='';
           $this->assertFalse($this->object->login());

       }
       
       /**
     * @covers LoginForm::login
     * @todo   Implement testLogin().
     * 
     * 
     */
    public function testLoginClaveNula() {
                
        $this->object->data='77685';
        $this->object->password='';
        $this->object->dataType='K';
        
        $mockSession = $this->getMock('CHttpSession', array('regenerateID'));
        Yii::app()->setComponent('session', $mockSession);
        //$this->object->companyDocNum='';
        //$this->object->companyDocType='';
        $this->assertFalse($this->object->login());
       
    }
    
    /**
     * @covers LoginForm::login
     * @todo   Implement testLogin().
     * 
     * 
     */
    public function testLoginClaveErronea() {
                
        $this->object->data='77685';
        $this->object->password='e10adc3949ba59abbe56';
        $this->object->dataType='K';
        
        $mockSession = $this->getMock('CHttpSession', array('regenerateID'));
        Yii::app()->setComponent('session', $mockSession);
        //$this->object->companyDocNum='';
        //$this->object->companyDocType='';
        $this->assertFalse($this->object->login());
       
    }
    
    /**
     * @covers LoginForm::login
     * @todo   Implement testLogin().
     * 
     * 
     */
    public function testLoginDatoInexistente() {
                
        $this->object->data='77685453';
        $this->object->password='e10adc3949ba59abbe56';
        $this->object->dataType='K';
        
        $mockSession = $this->getMock('CHttpSession', array('regenerateID'));
        Yii::app()->setComponent('session', $mockSession);
        //$this->object->companyDocNum='';
        //$this->object->companyDocType='';
        $this->assertFalse($this->object->login());
       
    }
    
    /**
     * @covers LoginForm::login
     * @todo   Implement testLogin().
     * 
     * 
     */
    public function testLoginDatoNulo() {
                
        $this->object->data='';
        $this->object->password='e10adc3949ba59abbe56';
        $this->object->dataType='K';
        
        $mockSession = $this->getMock('CHttpSession', array('regenerateID'));
        Yii::app()->setComponent('session', $mockSession);
        //$this->object->companyDocNum='';
        //$this->object->companyDocType='';
        $this->assertFalse($this->object->login());
       
    }




    /**
     * @covers LoginForm::getDataTypeOptions
     * @todo   Implement testGetDataTypeOptions().
     */
    public function testGetDataTypeOptions() {
        // Remove the following lines when you implement this test.
        $this->assertInternalType('array', $this->object->getDataTypeOptions());
        $this->assertEquals(array(
            'K' => 'Nro. de Cliente',
            'C' => 'Cuenta',
            'T' => 'Tarjeta',
            
        ), $this->object->getDataTypeOptions());
    }

}