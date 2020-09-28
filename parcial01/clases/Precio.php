<?php
require_once __DIR__.'\Files.php';
require_once __DIR__.'\login.php';

class Precio extends Files{
    public $precio_hora;
    public $precio_estadia;
    public $precio_mensual;

    public function __construct($precio_hora = 0, $precio_estadia = 0, $precio_mensual = 0){
        $this->precio_hora = $precio_hora;
        $this->precio_estadia = $precio_estadia;
        $this->precio_mensual = $precio_mensual;
           
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __toString(){
        return json_encode($this, JSON_PRETTY_PRINT); 
    }

    public static function mostrarPrecios(array $precios){
        echo "Precios: <br>";
        foreach ($precios as $value) {
            echo "Por hora: $$value->precio_hora,<br>";
            echo "Estadia: $$value->precio_estadia,<br>";
            echo "Mensual: $$value->precio_mensual <br><br>";
        }
    
    }

    public function guardarPrecio($archivo, $array){
        return parent::saveJson($archivo, $array);
    }
}


?>