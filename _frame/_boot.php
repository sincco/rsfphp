<?php
###############################
#NO TOCAR
###############################
define('DEV_SHOWPHPERRORS', TRUE); #Ocultar los errores de PHP
define('DEV_LOG', TRUE); #Activo el log de errores en archivo

require_once './config.php'; #<-- NO TOCAR

#Para separar el manejo de errores
class FrameworkException extends Exception {};

#Controlador por default
define('DEFAULT_CONTROLLER', 'index');
#Directorios del sistema
define('FRAME_PATH','./_frame/');
define('LIBS_PATH','./_libs/');
#Directorios de la aplicacion
define('APP_PATH','./_app/');
#Archivo de log
define('DEV_LOGFILE', './_logs/'.date('YW').'.txt');

#Apagar o mostrar los warnings de PHP
  if(DEV_SHOWPHPERRORS)
    error_reporting(-1);
  else
    error_reporting(0);
#Que la cookie de sesión no pueda accederse por javascript.
  $httponly = true;
#Definir el tipo de manejador de las sesiones
  session_set_save_handler(new SesionFile(), true);
#Configuracion para calcular el ID de la sesion
  $session_hash = 'sha512';
  if (in_array($session_hash, hash_algos()))
    ini_set('session.hash_function', $session_hash);
  ini_set('session.hash_bits_per_character', 5);
#Forza la sesión para que sólo use cookies, no variables URL.
  ini_set('session.use_only_cookies', 1);
#Define el tiempo en que una sesion puede seguir activa sin tener algún cambio
  ini_set('session.gc_maxlifetime', SESSION_INACTIVITY);
#Asigna el directorio de sesiones dentro de la ruta de la APP
  session_save_path(str_replace("_frame", "_sessions", realpath(dirname(__FILE__))));
#Configura los parametros de la sesion
  $cookieParams = session_get_cookie_params();
  if($cookieParams["lifetime"] == 0)
      $cookieParams["lifetime"] = 28800; #Se mantiene una sesion activa hasta por 8 horas en el navegador
#Configura los parámetros
  session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], SESSION_SSLONLY, $httponly); 
#Definir el nombre de la sesion segun la configuracion de la APP
  session_name(SESSION_NAME);
#Ahora podemos iniciar la sesión
  session_start();
#Por default la sesion lleva informacion del navegador, sistema e ip y un token con esa información (tratando de hacer unico ese identificador)
  $navegador = Framework::Obten_Navegador();
  $_SESSION['__browser'] = $navegador['name'];
  $_SESSION['__so'] = PHP_OS;
  $_SESSION['__ip'] = Framework::Obten_IP();
  $_SESSION['__token'] = md5($_SESSION['__browser'].$_SESSION['__so'].$_SESSION['__ip']);

#Carga automatica de funciones
function __autoload($class_name) {
  #Para los archivos propios del framework
  $file = FRAME_PATH.$class_name.'.php';
  if (file_exists($file)) {
  	require_once $file;
  } else {
  #Para las extensiones del sistema
  	$file = LIBS_PATH.$class_name.'.php';
	  if (file_exists($file))
	  	require_once $file;
	  else
	  	throw new FrameworkException("No se pudo encontrar la clase {$class_name}");
  }
}