<?php



class Login{
    public $user;
    public $jwt;

    public function __construct(Usuario $user, string $jwt = ''){
        if(!empty($user) && !empty($jwt)){
            $this->user = $user;
            $this->jwt = $jwt;
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

    public function Equals($login1, $login2){
        if($login1->user->Equals($login1->user, $login2->user) && ($login1->jwt == $login2->jwt))
        {
            return true;
        } 
        return false;
    }

    public function __toString(){
        return json_encode($this, JSON_PRETTY_PRINT);
    }
    
    public static function userLogin(Usuario $user, array $usuarios){
        $flag = false;
    
        //itera el listado de usuarios
        foreach ($usuarios as $value) {
            //busca el usuario            
            if($user->Equals($user, $value)){
                if(password_verify(base64_decode($user->password), $value->password)){
                    $flag = true;
                    break;
                }     

            }
        }
        return $flag;
    }
    
    public static function verifyToken(string $jwt, $key, array $users){
        $flag = false;
    
        if(!empty($jwt) && preg_match_all("/\./",$jwt) == 2){
            $tks = explode('.', $jwt);       
            list($headb64, $bodyb64, $cryptob64) = $tks;
            $user_verify = \Firebase\JWT\JWT::jsonDecode(Firebase\JWT\JWT::urlsafeB64Decode($bodyb64));
            $user_verify = new Usuario($user_verify->email, $user_verify->user_type, $user_verify->password);

            if(Login::userLogin($user_verify, $users)){
                $flag = true;
            }
        }    
        return $flag;
    }



}




?>