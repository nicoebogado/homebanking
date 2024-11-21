<?php

/**
 * New OTP of Fic, wrote by Higinio Samaniego, is only two functions to use.
 */

Yii::import('application.components.detectfic.Wsotp');

class DetectFic extends CApplicationComponent
{
    protected $host;
    protected $wsotp;

    public function setSettings($settings)
    {
        $this->host = $settings['host'];
    }

    public function getWsotp()
    {
        if (!$this->wsotp) {
            $this->wsotp = new Wsotp($this->host);
        }

        return $this->wsotp;
    }

}
