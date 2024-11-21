<?php

// protected/controllers/SavingsSimulationController.php
class SavingsSimulationController extends Controller
{
    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionSimulate()
    {
        $amount = Yii::app()->request->getPost('amount');
        $frequency = Yii::app()->request->getPost('frequency');
        $duration = Yii::app()->request->getPost('duration');
        
        // Lógica de simulación
        $savings = $this->calculateSavings($amount, $frequency, $duration);

        $this->render('result', array(
            'savings' => $savings,
            'amount' => $amount,
            'frequency' => $frequency,
            'duration' => $duration,
        ));
    }

    protected function calculateSavings($amount, $frequency, $duration)
    {
        $periods = 0;
        switch ($frequency) {
            case 'daily':
                $periods = 365;
                break;
            case 'weekly':
                $periods = 52;
                break;
            case 'monthly':
                $periods = 12;
                break;
        }
        return $amount * $periods * $duration;
    }
}
    