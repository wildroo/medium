<?php

include_once 'apiHelpers.php';

class Token{

    private $token;
    private $secret = "<YOUR_SUPER_SERCRET_STRING>";
    
    public function createToken($uid){        
        if($this->checkUidIsValid($uid) === null){
            return ResponseBuilder::error( "Bad request", ErrorCode::INVALID_REQUEST, 400);
        }
        $token = base64_encode($this->xor_string($uid));
        return ResponseBuilder::success($token);        
    }
    
    public function getUidFromToken($token){
        $uid = $this->xor_string(base64_decode($token));
        return $uid;
    }
    
    public function checkUidIsValid($uid){
        if(strlen($uid) === 28 && preg_match('/[a-z]|[A-Z]|[0-9]/', $uid) && strpos($uid, ' ') === false){
            return $uid;
        }
        else{
            return null;
        }
    }
    
    //This is a temporary solution, should add proper JWT dec/enc
    function xor_string($string) {
        $str_len = strlen($string);
        $key_len = strlen($this->secret);
    
        for($i = 0; $i < $str_len; $i++) {
            $string[$i] = $string[$i] ^ $this->secret[$i % $key_len];
        }
    
        return $string;
    }

    function extractUid(){        
        $uid = $this->getUidFromToken(str_replace("Token ", "", $_SERVER['HTTP_AUTHORIZATION']));
        if($this->checkUidIsValid($uid) === null){            
            return null;
        } else {
            return $uid;
        }        
    }
}

?>