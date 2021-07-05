<?php
header("Content-Type: application/json; charset=UTF-8");

include_once '../helpers/common/authHelper.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    echo json_encode(ResponseBuilder::success("OK", 1));
}
else if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $tokenHelper = new Token();
    $uid = $tokenHelper->getUidFromToken(str_replace("Token ", "", $_SERVER['HTTP_AUTHORIZATION']));
    $response = array();
    if($tokenHelper->checkUidIsValid($uid) === null){
        echo json_encode(ResponseBuilder::error( "Unauthorized access", ErrorCode::UNAUTHORIZED, 401));
    } else {
        //Valid token continue with new profile creation
        $fileUploaded = false;

        $allow = array("jpg", "jpeg", "gif", "png");

		//Folder where to upload the pictures
        $todir = '../uploads/';
		//Customised uploaded picture name [just as an example]
        $fileName = $uid .".png";

        try{
			//Create folder if doesn't exist
            if(!is_dir($todir)){
                mkdir($todir, 0777, true);
            }
        
            if ( !!$_FILES['file']['tmp_name'] ) // is the file uploaded yet?
            {
                $info = explode('.', strtolower( $_FILES['file']['name']) ); // whats the extension of the file

                if ( in_array( end($info), $allow) ) // is this file allowed
                {
                    if ( move_uploaded_file( $_FILES['file']['tmp_name'], $todir . "/" . $fileName ) )
                    {
                        $fileUploaded = true;
                    }
                }
                else
                {
                    echo json_encode(ResponseBuilder::error( "Available file extensions: jpg, jpeg, png", ErrorCode::CORRUPTED_DATA, 501));
                }
            }
            else {
                echo json_encode(ResponseBuilder::error( "File exists. Delete prior uploading another avatar", ErrorCode::CORRUPTED_DATA, 502));
            }

            if($fileUploaded){
                echo json_encode(ResponseBuilder::success($todir."/".$fileName, $rCnt));
            }

        } catch( Exception $e ) {
            error_log($e);
            echo json_encode(ResponseBuilder::error( "Error while uploading", ErrorCode::CORRUPTED_DATA, 503));
        }
    }
}

?>