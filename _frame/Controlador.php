<?php
/************************************
* Control generico en el Framework	*
*************************************
*************************************
Rev:
*************************************
@ivanmiranda: 1.0
************************************/
abstract class Controlador{
	protected $_vista;
	protected $_modelo;
#Cualquier instancia tiene el atributo de vista inicilizado
	public function __construct(){
		$this->_vista = new Vista();
	}
#Cualquier controlador en la aplicacion debe tener un metodo index
	abstract public function index();
}