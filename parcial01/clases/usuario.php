<?php

require_once __DIR__.'\Files.php';
require_once __DIR__.'\login.php';

class Usuario extends Files{
    public $email;
    public $user_type;
    public $password;

    public function __construct($email, $tipo, $pass)
    {
        if(!(empty($email) && empty($tipo) && empty($pass))  && filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->email = $email;
            $this->user_type = $tipo; 
            $this->password = $pass;
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

    public function Equals($user1, $user2){
        if($user1->email == $user2->email)
        {
            return true;
        } 
        return false;
    }

    public function __toString(){
        return json_encode($this, JSON_PRETTY_PRINT);
    }
    
    public function usuarioExists(array $usuarios){
        $flag = false;
        foreach($usuarios as $value) {
            if($this->Equals($this, $value)){
                $flag = true;
                break;
            }
        }
        
        return $flag;
    }

    public function guardarUsuario($archivo, $array){
        return parent::saveJson($archivo, $array);
    }

    public function login($usuarios){
       return Login::userLogin($this, $usuarios);
    }

    public function verifyToken($jwt, $key, $users){
        return Login::verifyToken($jwt, $key, $users);
    }

    public static function checkAdminEmail(string $jwt, $key, array $users){
        $flag = false;

        if(!empty($jwt) && preg_match_all("/\./",$jwt) == 2){
            $tks = explode('.', $jwt);       
            list($headb64, $bodyb64, $cryptob64) = $tks;
            $user_verify = \Firebase\JWT\JWT::jsonDecode(Firebase\JWT\JWT::urlsafeB64Decode($bodyb64));
            $user_verify = new Usuario($user_verify->email, base64_decode($user_verify->clave));
            
            if($user_verify->user_type == 'admin'){
                $flag = true;
            }
        }    

        return $flag;
    }

}




?>