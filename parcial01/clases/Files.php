<?php
//include_once __DIR__.'/Auto.php';

class Files{
    
    public static function save(string $archivo = '', string $contenido = ''){
        $flag = false;
        $modo = 'a+';

        try{
            if(file_exists($archivo) && $contenido !== ''){
                $file = fopen($archivo, $modo);
    
                fwrite($file, $contenido.PHP_EOL);
            }
            else{
                echo "<br>Error. Debe pasar todos los parametros de Files->save(). <br>";
            }
        }
        catch(Exception $e){
            echo "Hubo un error al escribir el archivo.";
        }
        finally{
            $flag = fclose($file);    
        }
        return $flag;
    }
    
    public static function read(string $archivo = '', string $separador = '', int $cant_datos = 0){
        //$contenido = '';
        $modo = 'r';
        $listaObj = array();

        try{
            if(file_exists($archivo) && $modo !== '' && $separador !== '' && $cant_datos !== ''){
                $file = fopen($archivo, $modo);

                //Autos: {marca, modelo, color, precio, fecha}
                /*
                $contenido = "Autos: ";
                while(!feof($file)){
                    $contenido.= fgets($file).PHP_EOL;
                }
                */

                $linea = fgets($archivo);

                $datos = explode($separador, $linea);
                //Auto->cant_datos = 4 
                if (count($datos) > $cant_datos) {
                    $nuevoObjeto = new Auto($datos[0], $datos[1], $datos[2], $datos[3]);
                    array_push($listaObj, $nuevoObjeto);
                }
            }
            else{
                echo "<br>Error. Debe pasar todos los parametros de Files->read(). <br>";
            }
        }
        catch(Exception $e){
            echo "Hubo un error al escribir el archivo.";
        }
        finally{
            fclose($file);    
        }
        //return $contenido;
        return $listaObj;
    }
    
    public static function readJson($archivo){
        $array = array();
        if(file_exists($archivo)){
            $file = fopen($archivo, 'r');
            $fread = fread($file, filesize($archivo));

            $array = json_decode($fread);
            
            fclose($file);
        }

        return $array;
    }

    public static function saveJson($archivo, $array){
        $flag = false;
        $file = fopen($archivo, 'w+');
        if(fwrite($file, json_encode($array, JSON_PRETTY_PRINT)) > 0){
            $flag = true;
        }
        fclose($file);
        return $flag;
    }

    public static function readSerialize($archivo){
        $array = array();
        if(file_exists($archivo)){
            $file = fopen($archivo, 'r');
            $size = filesize($file);
            if($size > 0){
                $fread = fread($file, $size);
            }else{
                $fread = "{}";
            }
            fclose($file);
            $array = unserialize($fread);
        }
        return $array;
    }

    public static function saveSerialize($archivo, $array){
        $flag = false;
        $file = fopen($archivo, 'w');
        if(fwrite($file, serialize($array))){
            $flag = true;
        }
        fclose($file);
        return $flag;
    }


}


?>