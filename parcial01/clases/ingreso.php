<?php
require_once __DIR__.'\Files.php';
require_once __DIR__.'\login.php';

class Ingreso extends Files{
    public $patente;
    public $fecha_ingreso;
    public $tipo;

    public function __construct(string $patente = '', int $cuatri = 0){
        if(!empty($patente) && !empty($cuatri)){
            $this->patente = $patente;
            $this->fecha_ingreso = $cuatri;
            $this->tipo = rand(1,1000);
        }   
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function Equals($ingreso1, $ingreso2){
        if($ingreso1->patente == $ingreso2->patente)
        {
            return true;
        } 
        return false;
    }

    public function __toString(){
        return json_encode($this, JSON_PRETTY_PRINT); 
    }

    public function ingresoExists($ingresos){
        $flag = false;
        foreach($ingresos as $value) {
            if($this->Equals($this, $value)){
                $flag = true;
                break;
            }
        }
        
        return $flag;
    }
    
    public static function mostrarIngresos(array $ingresos){
        echo "Ingresos: <br>";
        foreach ($ingresos as $value) {
            echo "$value->patente,<br>";
        }
    
    }

}


?>