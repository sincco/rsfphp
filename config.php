<?php
#/////////////////////////////////
# Configuración de la aplicación
#/////////////////////////////////

#-----------------------
#Constantes del sistema
#-----------------------
define('APP_NAME', 'Simple Framework PHP');
define('APP_COMPANY', 'Sincco.com');
define('APP_KEY', '1628387158543db8f067f578.01851196'); #llave de encripcion de datos
define('BASE_URL', (($_SERVER['SERVER_PORT']==80) ? 'http://' : 'https://').$_SERVER['SERVER_NAME'].'/sfphp/'); #URL base para todos los elementos estaticos

#/////////////////////////////////
# Configuración del framework
#/////////////////////////////////

#------------------------------
#Constantes del desarrollador
#------------------------------
define('DEV_SHOWERRORS', TRUE); #muestra pantalla de errores en el sistema

#-------------------
#Manejo de sesiones
#-------------------
define('SESSION_NAME', 'sfphp'); #nombre de la sesion
define('SESSION_SSLONLY', FALSE); #si debe conectarse sólo por HTTPS
define('SESSION_INACTIVITY', 60); #tiempo de espera para que la sesion caduque por inactividad
define('SESSION_TYPE', 'db'); #define si las sesiones se manejan en archivo o base de datos file|db