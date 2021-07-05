<?php

/**
Provides repsonse statuses and response builders
*/

class ResponseBuilder{    

    public static function success($data, $count = 1){
        $response = array();
        $response[ResponseFields::SUCCESS] = true;
        $response[ResponseFields::RESULT] = $data;
        $response[ResponseFields::COUNT] = $count;
        return $response;
    }

    public static function error($errorMsg, $errorCode, $responseStatus){
        $response = array();
        $response[ResponseFields::SUCCESS] = false;
        $response[ResponseFields::ERR_MSG] = $errorMsg;
        $response[ResponseFields::ERR_CODE] = $errorCode;
        http_response_code($responseStatus); // Change response code from default 200
        return $response;
    }

    public static function mixed($data, $count, $errorMsg, $errorCode){
        $response = array();
        $response[ResponseFields::SUCCESS] = false;
        $response[ResponseFields::RESULT] = $data;
        $response[ResponseFields::COUNT] = $count;
        $response[ResponseFields::ERR_MSG] = $errorMsg;
        $response[ResponseFields::ERR_CODE] = $errorCode;
        http_response_code(409); //Conflict request
        return $response;
    }
}

class ResponseFields{
    const SUCCESS = "success";
    const RESULT = "result";
    const COUNT = "count";
    const ERR_MSG = "errorMessage";
    const ERR_CODE = "errorCode";
    const R_STATUS = "responseStatus";
}

class ErrorCode{
    const NO_RECORD = "NO_RECORD";
    const DUPLICATED = "DUPLICATED";
    const INVALID_REQUEST = "INVALID_REQUEST";
    const CORRUPTED_DATA = "CORRUPTED_DATA";
    const DB_ERR = "DB_ERR";
    const SRV_ERR = "SRV_ERR";
    const UNAUTHORIZED = "UNAUTHORIZED";
}

?>