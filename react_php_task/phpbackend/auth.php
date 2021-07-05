<?php
header("Content-Type: application/json; charset=UTF-8");

include_once 'helpers/common/authHelper.php';
include_once 'helpers/common/apiHelpers.php';

$tokenHelper = new Token();
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(!empty($_GET["uid"])){
        echo json_encode($tokenHelper->createToken($_GET["uid"]));
    } else {
        echo json_encode(ResponseBuilder::error("Correct auth action type is not provided", ErrorCode::INVALID_REQUEST, 400));
    }
}

?>