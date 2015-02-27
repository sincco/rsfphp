<?php
#------------------------------
#-- NO TOCAR
#------------------------------
#Desde aqui se invoca la carga de elementos del framework
#de las librerias y todo lo relacionado con la APP
#------------------------------

require_once './_frame/_boot.php'; #Carga de rutinas del framework

try {
	Arranque::ejecuta(new Peticion); #Se lanza la petición
} catch(FrameworkException $e) { #Se detectó un error en el framework
	Logs::procesa($e);
} catch(Exception $e) { #Cualquier otro error en el sistema
	Logs::procesa($e);
}