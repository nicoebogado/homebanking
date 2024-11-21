<?php
class WSErrorMsgs
{
	/**
	 * Errores que al ocurrir se debe deslogear al usuario
	 */
	private static $_errorsThrowsLogout = array(
		'L_SESSION_EXP',
		'L_AUTH_ERROR',
		'238', // Sesión inválida
		'241', // La sesion ha caducado
	);
	/**
	 * Errores que al ocurrir deben redirigirse al index
	 */
	private static $_errorsThrowsRedirection = array(
		'CTANOCORRESPOND',// cuenta no corresponde al cliente
	);

	private static $_logoutMessage = "Por inactividad de su sesión la misma fue automáticamente cerrada.<br>Su ultima operación no fue realizada.<br>Por favor ingrese de nuevo.";

	public static function processError($resp)
	{
		if( isset($resp->error) && $resp->error === 'S' ) {

			Yii::log($resp->error, 'info');

			if(in_array($resp->nombrerespuesta, self::$_errorsThrowsLogout)) {
				Yii::app()->user->logout();
				Yii::app()->request->redirect(Yii::app()->createUrl('site/login', array('e'=>base64_encode(self::$_logoutMessage))));
			}

			if(in_array($resp->nombrerespuesta, self::$_errorsThrowsRedirection)) {
				Yii::app()->user->setFlash('error', $resp->descripcionrespuesta);
				Yii::app()->request->redirect(Yii::app()->createUrl('site/index'));
			}

		}
	}
}
