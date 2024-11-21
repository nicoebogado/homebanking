<?php
/* @var $this UserController */
HScript::register([
    'libs/emn178/md5.min',
    'libs/emn178/sha256.min',
    'plugins/jquery.secureKeypad.min',
    'user/changePassword',
]);

HScript::registerCode('securizeKeypad', '$(".secureKeypadInput").secureKeypad({validate:true});');

$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('changePassword', 'Cambiar Clave de Acceso');
?>

<h3><?php echo Yii::t('changePassword', 'Cambiar Clave de Acceso') ?></h3>
<div class="panel panel-warning">
    <div class="panel-heading"> Recomendaciones</div>
    <div class="panel-body">

        <ul>
            <li>No puede repetir el mismo caracter en la clave en forma consecutiva.</li>
            <li>La clave debe ser diferente a las anteriores.</li>
            <li>La clave debe tener una extensión de al menos 6 carácteres.</li>
            <li>Evitar el uso de contraseñas que hagan referencia a datos fácilmente deducibles como son las fechas de cumpleaños, números de documento de identidad, cuentas o cliente.</li>
        </ul>
    </div>
</div>
<?php echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>