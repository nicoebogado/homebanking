<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-08-25 at 22:21:17.
 */
class ReturnedChecksFormTest extends CTestCase {

    /**
     * @var ReturnedChecksForm
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new ReturnedChecksForm;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers ReturnedChecksForm::rules
     * @todo   Implement testRules().
     */
    public function testRules() {

            $this->assertInternalType('array', $this->object->rules());
            $this->assertEquals(array(
                array('account', 'required'),
            ), $this->object->rules());    
    }

    /**
     * @covers ReturnedChecksForm::attributeLabels
     * @todo   Implement testAttributeLabels().
     */
    public function testAttributeLabels() {
        // Remove the following lines when you implement this test.
        $this->assertInternalType('array', $this->object->attributeLabels());
        $this->assertEquals(array(
                'account'=>Yii::t('app', 'Cuenta'),
            ), $this->object->attributeLabels());
    }

    /**
     * @covers ReturnedChecksForm::formConfig
     * @todo   Implement testFormConfig().
     */
    public function testFormConfig() {
        // Remove the following lines when you implement this test.
        $this->assertEquals(array(
                'title'=> 'Seleccione una cuenta',
			'showErrorSummary'=>false,
			'attributes'=>array('id'=>'select-account-form'),
			'elements'=>array(
				'account'=>array(
					'type'=>'dropdownlist',
					'items'=>'A',
				),
			),
			'buttons'=>array(
				'submit'=>array(
					'id'=>'submit',
					'buttonType'=>'submit',
					'context'=>'primary',
					'label'=>'Ver detalle',
					'url'=>array('checkbooks'),
				),
				'cancel'=>array(
					'context'=>'danger',
					'label'=>'Cancelar',
					'url'=>array('/site/index'),
				),
			),
            
            
            ), $this->object->formConfig($accounts='A'));
    }

}
