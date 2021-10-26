<?php
	header("Content-Type: application/json; charset=UTF-8");

    include_once '../config/dbconfig.php';
    include_once '../helpers/userHelper.php';

	$db = new DBClass();
	$userHelper = new UserHelper();
	//$data = $connection->query('SELECT NOW()');
	//print_r($data);
	
	//main function 
	//catch action
	//@var $request_method
	$request_method = isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:'';
	$data = json_decode(file_get_contents("php://input"),true);
	
	if($request_method == "POST"){
		//Get action method
		$action = $_REQUEST['action'];
        $connection = $db->getConnection();
		switch ($action) {
		  case "createUser":
			$json = $userHelper->createUser($connection, $data);
			break;
		  case "resendActivationLink":
			$json = $userHelper->resendActivationLink($connection, $data);
			break;
		  case "activateUser":
			$json = $userHelper->activateUser($connection, $data);
			break;
		  case "changePassword":
			$json = $userHelper->changePassword($connection, $data);
			break;
		  case "validateUser":
			$json = $userHelper->validateUser($connection, $data);
			break;
		  default:
			$json = array("success" => false, "Info" => "Request method not available!");
		}
        $connection = null;
		echo json_encode($json);
	}else{
		$json = array("success" => false, "Info" => "Request method not accepted!");
		echo json_encode($json);
	}
?>
