<?php

    class ScheduledSavingsController extends Controller
    {

        public function actionCreate()
        { 
            // Renderiza la vista 'create' en la carpeta views/scheduledSavings
            $this->render('create');
        }

        public function actionSuccess()
        {
            // Renderiza la vista 'success' en la carpeta views/scheduledSavings
            $this->render('success');
        }

        public function actionGenerate() {
            //necesito que lleve al inicio y que refresque las cuentas
            Yii::app()->user->accounts->refresh(); 
            $this->redirect(Yii::app()->createUrl('/site/index'));
            Yii::app()->user->accounts->refresh();

        }

    }

?>