<?php
#-------------------------------
#Manejo de sesiones por archivo
#-------------------------------

class SesionFile implements SessionHandlerInterface {
    private $savePath;

    public function open($savePath, $sessionName) {
        $this->savePath = $savePath;
    #Si el directorio de control de sesiones no existe, se crea y se limita el listado desde servidor
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0600);
            file_put_contents("$this->savePath/.htaccess", "Options -Indexes");
        }
        return true;
    }

    public function close() {
        return true;
    }

    public function read($id) {
    #Como todo esta codificado, se decodifca al leer la sesion
        return (string)@Framework::Decrypt(file_get_contents("$this->savePath/$id"));
    }

    public function write($id, $data) {
    #Todo lo almacenado en la sesion se codifica para mayor seguridad
        return file_put_contents("$this->savePath/$id", Framework::Encrypt($data)) === false ? false : true;
    }

    public function destroy($id) {
        $_SESSION = array();
        $file = "$this->savePath/$id";
        if (file_exists($file)) {
            unlink($file);
            session_regenerate_id();
        }
        return true;
    }

    public function gc($maxlifetime) {
        foreach (glob("$this->savePath/*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }
        return true;
    }
}