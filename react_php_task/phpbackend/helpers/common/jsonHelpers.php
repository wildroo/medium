<?php

include_once 'apiHelpers.php';

class JsonHelpers{    

    public static function jsonToArray(){
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);

        return $input;
    }
    
}

?>