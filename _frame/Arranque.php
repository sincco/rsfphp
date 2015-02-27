<?php
/************************************
* Control de arranque del Framework *
*************************************
*************************************
Rev:
*************************************
@ivanmiranda: 1.0
************************************/

class Arranque{
	public static function ejecuta(Peticion $peticion, $seguridad = null) {
	#Se invoca la sesion
		Framework::Sesion();
		$controlador = $peticion->getControlador()."Controlador";
		$rutaControlador = APP_PATH.'_controladores/'.$peticion->getControlador().'.php';
		$metodo = $peticion->getMetodo();
		$argumentos = $peticion->getArgumentos();
	print_r(array($controlador, $metodo, $argumentos));
		#Si el archivo que contiene la clase solicitada es accesible...
		if(is_readable($rutaControlador)) {
		#Entonces crear una instancia
			require_once $rutaControlador;
			$objControlador = new $controlador;
		#Si el metodo solicitado no es accesible o no existe lanzamos el metodo index
		#(que es obligatorio para cualquier controlador)
			if(!is_callable(array($objControlador, $metodo)))
				$metodo = DEFAULT_CONTROLLER;
		#Sabiendo que metodo hay que invocar, se lanza con los argumentos recibidos
		#(siempre y cuando se reciba alguno)
			if(!empty($argumentos)) {
				call_user_func_array(array($objControlador, $metodo), $argumentos);
			} else {
				call_user_func(array($objControlador, $metodo));
			}
		} else {
			header("HTTP/1.1 404 Metodo {$metodo}{$peticion->getMetodoHTTP()} no encontrado");
		}
	}
}