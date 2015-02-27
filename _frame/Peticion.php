<?php
	class Peticion{
		private $_controlador;
		private $_metodo;
		private $_argumentos;
		private $_metodoHTTP;

		public function __construct(){
			$this->_metodoHTTP = strtoupper(trim($_SERVER['REQUEST_METHOD']));
			if(isset($_GET['url'])){
			#Obtener la URL solicitada
				$url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
				$url = explode('/', $url);
				$url = array_filter($url);
			#Convertir la URL a nombres de objetos del sistema
				$this->_controlador = strtolower(array_shift($url));
				$this->_metodo = strtolower(array_shift($url));
				$this->_argumentos = $url;
			}
			#Inicializar los atributos no recibidos en la URL
			if(!$this->_controlador)
				$this->_controlador = DEFAULT_CONTROLLER;
			if(!$this->_metodo)
				$this->_metodo = 'index';
			if(!$this->_argumentos)
				$this->_argumentos = array();
		}
	#Metodos para lectura de atributos
		public function getControlador(){
			return $this->_controlador;
		}
		public function getMetodo(){
			return $this->_metodo;
		}
		public function getArgumentos(){
			return $this->_argumentos;
		}
		public function getMetodoHTTP() {
			return $this->_metodoHTTP;
		}
	}
