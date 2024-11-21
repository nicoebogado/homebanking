<?php
Yii::import('application.components.detectid.EasysolToken');
Yii::import('application.components.detectid.ClientService');
Yii::import('application.components.detectid.OutOfBandSmsService');

//Added: 13-06-2022 Higinio Samaniego, hard token integration actions
Yii::import('application.components.detectid.IsClientPresent');
Yii::import('application.components.detectid.RetrieveToken');
Yii::import('application.components.detectid.ValidateDID200');

class DetectId extends CApplicationComponent
{
    protected $host;
    protected $clientService;
    protected $outOfBandSmsService;
    protected $easysolToken;

    protected $isClientPresent;
    protected $retrieveToken;
    protected $validateDID200;

    public function setSettings($settings)
    {
        $this->host = $settings['host'];
    }

    public function getClientService()
    {
        if (!$this->clientService) {
            $this->clientService = new ClientService($this->host);
        }

        return $this->clientService;
    }

    public function getOutOfBandSmsService()
    {
        if (!$this->outOfBandSmsService) {
            $this->outOfBandSmsService = new OutOfBandSmsService($this->host);
        }

        return $this->outOfBandSmsService;
    }

    public function getEasysolToken()
    {
        if (!$this->easysolToken) {
            $this->easysolToken = new EasysolToken($this->host);
        }

        return $this->easysolToken;
    }

    //Added by: Higinio Samaniego HardToken function to see is or not client
    public function getIsClientPresent()
    {
        if (!$this->isClientPresent) {
            $this->isClientPresent = new IsClientPresent($this->host);
        }

        return $this->isClientPresent;
    }

    //Added by: Higinio Samaniego HardToken function to see is or not client
    public function getRetrieveToken()
    {
        if (!$this->retrieveToken) {
            $this->retrieveToken = new RetrieveToken($this->host);
        }

        return $this->retrieveToken;
    }

    //Added by: Higinio Samaniego HardToken function to see is or not client
    public function getValidateDID200()
    {
        if (!$this->validateDID200) {
            $this->validateDID200 = new ValidateDID200($this->host);
        }

        return $this->validateDID200;
    }

    public function getAvailableFactors()
    {
        $sharedKey = Yii::app()->user->getState('sharedKey');
        $response = $this->getClientService()
            ->retrieveClientInformation(compact('sharedKey'))
            ->retrieveClientInformationResponse;


        if ($response->resultCode !== 1020 || empty($response->client->authenticationFactors)) {
            return [];
        }

        $availableFactors = [];
        foreach ($response->client->authenticationFactors->authenticationFactor as $factorObj) {
            if ($factorObj->status === 'enabled') {
                $availableFactors[] = $factorObj->name;
            }
        }

        return $availableFactors;
    }
}
