<?php
/************************************
* Control de vistas en el Framework *
*************************************
*************************************
Rev:
*************************************
@ivanmiranda: 1.0
************************************/
class Vista {
	private $_header = "";
	private $_footer = "";
	private $_html = "";
	private $_path = "";

	public function __construct() {
		if(file_exists(APP_PATH.'_vistas/header.tpl')) {
			$this->_path = APP_PATH.'_vistas/';
		} else {
			$this->_path = APP_PATH.'_vistas/';
		}
	}

	#Muestra en pantalla una plantilla
	public function dibuja($vista) {
		if(file_exists($this->_path.'/'.$vista)) {
			$this->_path = dirname($this->_path.'/'.$vista);
		#Leer el contenido del archivo
			$_archivo = @fopen($this->_path.'/'.$vista, 'r');
			$this->_html = fread($_archivo, filesize($this->_path.'/'.$vista));
	        fclose($_archivo);
		#Sobreescribir las variables asignadas a la vista para referenciarlas a la plantilla
			foreach (get_object_vars($this) as $_nombre => $_valor ) {
				$$_nombre = $_valor;
			}
			$this->procesa_tags();
		#Procesar las etiquetas de la plantilla
			$this->procesa_valores();
		#Tokens de seguridad a formularios
			$this->procesa_form();
			eval("?>".$this->_html);
		} else {
			throw new FrameworkException("Vista ".$this->_path.'/'.$vista." no existe", 1);
		}
	}

#***********
#Procesa los tags aceptados en las plantillas
#***********
	private function procesa_tags() {
	#Incluir cualquier archivo solicitado...
		$this->_html = preg_replace('#\{incluir ([\.\sa-z0-9\-_]*?)\}#is', "<?php include \"".$this->_path."\\1\" ?>", $this->_html);
	#-------------------
	#TABLA
	#-------------------
	#Busco cada etiqueta que se definió en la plantilla...
    	preg_match_all ('#\{tabla ([\.\sa-z0-9\-_]*?)\}#is', $this->_html, $_tablas);
    #...para revisar si se pasó como variable al invocar la vista...
    	foreach ($_tablas[1] as $clave => $valor) {
    		$valores = explode(" ", $valor);
    		$valor = $valores[0];
    		if(count($valores) > 1)
    			$clase = $valores[1];
    		else
    			$clase = $valores[0];
    	#...y si no, quitarla de la vista para evitar errores por no definir variables
    		if(isset(get_object_vars($this)[$valor])) {
    			$_tabla = $this->procesa_tabla(get_object_vars($this)[$valor], $valor, $clase);
    			if(count($valores) == 1) 
    				$tag = "{tabla ".$valor."}";
    			else
    				$tag = "{tabla ".$valor." ".$clase."}";
    			$this->_html = str_replace($tag, $_tabla, $this->_html);
    		}
    	}
    #-------------------
	#ESCUCHA DE UNA TABLA
	#-------------------
	#Busco cada etiqueta que se definió en la plantilla...
    	preg_match_all ('#\{escucha_tabla ([\.\sa-z0-9\-_]*?)\}#is', $this->_html, $_tablas);
    #...para revisar si se pasó como variable al invocar la vista...
    	foreach ($_tablas[1] as $clave => $valor) {
    	#...y si no, quitarla de la vista para evitar errores por no definir variables
    		if(isset(get_object_vars($this)[$valor])) {
    			$_tabla = $this->procesa_escucha_tabla($valor);
   				$tag = "{escucha_tabla {$valor}}";
    			$this->_html = str_replace($tag, $_tabla, $this->_html);
    		}
    	}
	}

	private function procesa_valores() {
    #Busco cada etiqueta que se definió en la plantilla...
    	preg_match_all ('#\{([a-z0-9\-_]*?)\}#is', $this->_html, $_etiquetas);
    #...para revisar si se pasó como variable al invocar la vista...
    	foreach ($_etiquetas[0] as $clave => $valor) {
    		$valor = preg_replace('#\{([a-z0-9\-_]*?)\}#is', "\\1", $valor);
    	#...y si no, quitarla de la vista para evitar errores por no definir variables
    		if(!isset(get_object_vars($this)[$valor]))
    			$this->_html = str_replace("{".$valor."}", "", $this->_html);
    	}
    #Ya con la plantilla depurada, convierto las etiquetas a variables de PHP
        $this->_html = str_replace ("'", "\'", $this->_html);
        $this->_html = preg_replace('#\{([a-z0-9\-_]*?)\}#is', "<?= $\\1 ?>", $this->_html);
	}

	#Anexar los tokens de seguridad a cada formulario contenido en las plantillas
	private function procesa_form() {
		$this->_html = preg_replace('</form>', 'input type="hidden" name="__token" value="'.Framework::TokenSesion().'"/></form', $this->_html);
	}

	#Arma una tabla de datos en base al arreglo pasado
	private function procesa_tabla($datos, $nombre = "", $clase = "") {
		if(!is_array($datos))
			return false;
		$_header = false;
		$_tabla = "<table id=\"tabla_{$nombre}\" name=\"tabla_{$nombre}\" class=\"{$clase}\" />\n";
		foreach ($datos as $_fila) {
			if(!$_header) {
				$_header = true;
				$_tabla .= "\t<tr>\n";
				$_campos = array_keys($_fila);
				foreach ($_campos as $_campo) {
					$_tabla .= "\t\t<th>{$_campo}</th>";
				}
			}
			$_tabla .= "\n\t</tr>\n\t<tr>\n";
			foreach ($_fila as $_campo => $_valor) {
				$_tabla .= "\t\t<td>{$_valor}</td>";
			}
		}
		$_tabla .= "\t</tr>\n</table>\n";
		return $_tabla;
	}

	#Arma un script de escucha de una tabla para detectar cuando se ha hecho click en un objeto
	private function procesa_escucha_tabla($nombre) {
		$_script = "
		<script>\n
		\t$(\"#tabla_{$nombre}\").delegate(\"tr\", \"click\", function() {\n
		\tvar elementos = $(this).html().trim().split(\"</td>\");\n
		\tfor (i = 0; i < elementos.length; i++) { \n
			\t\telementos[i] = elementos[i].replace(\"<td>\",\"\");\n
			\t\telementos[i] = elementos[i].trim();\n
		\t}\n
		\tescucha_tabla_{$nombre}(elementos);\n
		});
		</script>";
		return $_script;
	}
}
