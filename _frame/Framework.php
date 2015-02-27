<?php
/*******************************
Funciones comúnes del framework
*******************************/
class Framework {
#------------------------------------
#Sesion basica para el Framework
#------------------------------------
	public static function Sesion() {
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
		if(!$_SESSION)
			session_start();
	#Por default la sesion lleva informacion del navegador, sistema e ip y un token con esa información (tratando de hacer unico ese identificador)
		$navegador = Framework::Obten_Navegador();
		$_SESSION['__browser'] = $navegador['name'];
		$_SESSION['__so'] = PHP_OS;
		$_SESSION['__ip'] = Framework::Obten_IP();
		$_SESSION['__token'] = md5($_SESSION['__browser'].$_SESSION['__so'].$_SESSION['__ip']);
	}

	public static function IDSesion(){ return session_id(); }

	public static function TokenSesion(){ return md5(session_id()); }

	public static function Obten_Navegador() {
		$u_agent = $_SERVER['HTTP_USER_AGENT']; 
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version= "";
	    $ub = "";

	    if(preg_match('/linux/i', $u_agent)) {
	        $platform = 'linux';
	    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        $platform = 'mac';
	    } elseif (preg_match('/windows|win32/i', $u_agent)) {
	        $platform = 'windows';
	    }
	    
	    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) { 
	        $bname = 'Internet Explorer'; 
	        $ub = "MSIE"; 
	    } elseif(preg_match('/Firefox/i',$u_agent)) { 
	        $bname = 'Mozilla Firefox'; 
	        $ub = "Firefox"; 
	    } elseif(preg_match('/Chrome/i',$u_agent)) { 
	        $bname = 'Google Chrome'; 
	        $ub = "Chrome"; 
	    } elseif(preg_match('/Safari/i',$u_agent)) { 
	        $bname = 'Apple Safari'; 
	        $ub = "Safari"; 
	    } elseif(preg_match('/Opera/i',$u_agent)) { 
	        $bname = 'Opera'; 
	        $ub = "Opera"; 
	    } elseif(preg_match('/Netscape/i',$u_agent)) { 
	        $bname = 'Netscape'; 
	        $ub = "Netscape"; 
	    }    

	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>' . join('|', $known) .
	    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!preg_match_all($pattern, $u_agent, $matches)) {
	        $pattern = "";
	    }
	    
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub))
	            $version= $matches['version'][0];
	        else
	            $version= $matches['version'][1];
	    } else {
	        $version= $matches['version'][0];
	    }
	    
	    if ($version==null || $version=="") {$version="?";}
	    
	    return array(
	        'userAgent' => $u_agent,
	        'name'      => $bname,
	        'version'   => $version,
	        'platform'  => $platform,
	        'pattern'    => $pattern
	    );
	}

	public static function Obten_IP(){
		return $_SERVER['REMOTE_ADDR'];
	}

	public static function Limpiar_Entrada($contenido){
		if(is_array($contenido))
			foreach ($contenido as $key => $value)
				$contenido[$key] = filter_var($value,FILTER_SANITIZE_STRING);
		else
			$contenido = filter_var($contenido,FILTER_SANITIZE_STRING);
		return $contenido;
	}

	public static function ParseaUTF8($contenido){
		if(is_array($contenido))
			foreach ($contenido as $key => $value)
				$contenido[$key] = utf8_decode($value);
		else
			$contenido = utf8_decode($contenido);
		return $contenido;
	}
#------------------
#Encripcion AES256
#------------------
	public static function Encrypt($data, $key = APP_KEY) {
		$salt = 'cH!swe!retReGu7W6bEDRup7usuDUh9THeD2CHeGE*ewr4n39=E@rAsp7c-Ph@pH';
		$key = substr(hash('sha256', $salt.$key.$salt), 0, 32);
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, $iv));
		return $encrypted;
	}
	public static function Decrypt($data, $key = APP_KEY) {
		$salt = 'cH!swe!retReGu7W6bEDRup7usuDUh9THeD2CHeGE*ewr4n39=E@rAsp7c-Ph@pH';
		$key = substr(hash('sha256', $salt.$key.$salt), 0, 32);
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($data), MCRYPT_MODE_ECB, $iv);
		return $decrypted;
	}

#------------------------------------
#Traducir fecha al español
#------------------------------------
	public static function FechaEsp($fecha) {
		$anio = date("y", $fecha);
		$dia = date("j", $fecha);
		$mes=date("F", $fecha);
		if ($mes=="January") $mes="Enero";
		if ($mes=="February") $mes="Febrero";
		if ($mes=="March") $mes="Marzo";
		if ($mes=="April") $mes="Abril";
		if ($mes=="May") $mes="Mayo";
		if ($mes=="June") $mes="Junio";
		if ($mes=="July") $mes="Julio";
		if ($mes=="August") $mes="Agosto";
		if ($mes=="September") $mes="Setiembre";
		if ($mes=="October") $mes="Octubre";
		if ($mes=="November") $mes="Noviembre";
		if ($mes=="December") $mes="Diciembre";
		return "{$dia} {$mes} {$anio}";
	}

#------------------------------------
#Consumir un API REST
#------------------------------------
	public function consumirAPI ($url, $metodo = "GET", $parametros = "", $auth = FALSE) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$url);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
	    if($auth)
	    	curl_setopt($ch, CURLOPT_USERPWD, $auth);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',
			'Content-Length: ' . strlen($parametros))
		);    
	    $result = curl_exec($ch);
	    $info = curl_getinfo($ch);
	    if($info["http_code"] != 200) {
	    	$_respuesta = array("status" => 400, "error" => $info["http_code"]);
		} else {
	    	$result = json_decode($result, TRUE);
	    	if(json_last_error() == JSON_ERROR_NONE) {
	    		$_respuesta = array("status" => 200, "respuesta" => $result);
	    	} else {
	    		$_respuesta = array("status" => 400, "error" => "Error en respuesta JSON");
	    	}
	    }
	    curl_close($ch);
	    return $_respuesta;
	}
}