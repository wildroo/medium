<?php
class UserHelper{
	
    private $usersTable = "users";
    private $profilesTable = "profiles";

	public function createUser($connection, $input){
		//check inputs
		if(!isset($input['email']) || !isset($input['password']))
		{
			return $json = array("success" => false, "Info" => "Invalid Inputs");
		}
		//sanitise inputs
		$email = htmlspecialchars(strip_tags($input['email']));
		$password = htmlspecialchars(strip_tags(base64_decode($input['password'])));

		//check if valid email
		if($this->validEmail($email)===false)
		{
			return $json = array("success" => false, "Info" => "Invalid Email");
		}
		
		//check email exists  START
		$sql      = "SELECT * FROM $this->profilesTable WHERE email =:email";
		$stmt = $connection->prepare($sql);
		$stmt->bindParam(":email",$email);
		$stmt->execute();
		$profile = $stmt->fetch();
		if(isset($profile[0]))
		{
			return $json = array("success" => false, "Info" => "Email Already Exists");
		}		

		//SET @VAR $UID
		$uid = md5(time().rand());
		$public_name = $this->splitEmail($email);

        $connection->beginTransaction();

        try{                    
            $sql  = "INSERT INTO $this->usersTable SET `uid` =:uid, `password` =:password, `activated` = 0, `enabled`=0";
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(":uid",$uid);
            $stmt->bindParam(":password",$password);
            $stmt->execute();
            if($stmt->rowCount() === 1){
                //execute profile table  START
                $sql  = "INSERT INTO $this->profilesTable SET `uid`= :uid,`public_name`= :public_name,`email`= :email,`active`='0'";
                $stmt = $connection->prepare($sql);
                $stmt->bindParam(":uid",$uid);
                $stmt->bindParam(":public_name",$public_name);
                $stmt->bindParam(":email",$email);
                $stmt->execute();       
                if($stmt->rowCount() !== 1){
                    $connection->rollBack();
                    return array("success" => false, "Info" => "Couldn't create a profile for user");
                }     
            } else {
                $connection->rollBack();
                return array("success" => false, "Info" => "Couldn't create a user");
            }

            //send activationlink after user creation
            $this->sendActivationLink($email);
            $connection->commit();
            return array("success" => true, "uid" => $uid);
        } catch(Exception $e){
            error_log($e);
            $connection->rollBack();
            return array("success" => false, "Info" => "Error while creating a user");
        }
	}
	
	/*
		Action:  resendActivationLink
		Required Inputs: Email
		Output:  Success True or false.
	*/
	public function resendActivationLink($connection, $input){
		//sanitise inputs
		//check inputs
		if(!isset($input['email']))
		{
			return $json = array("success" => false, "Info" => "Invalid Inputs");
		}
		$email = htmlspecialchars(strip_tags($input['email']));
		//check if valid email START
		if($this->validEmail($email)===false){
			return $json = array("success" => false, "Info" => "Invalid Email");
		}
		//check if valid email END
		


		//check email exists  START
		$sql      = "SELECT * FROM $this->profilesTable WHERE email =:email";
		$stmt = $connection->prepare($sql);
		$stmt->bindParam(":email",$email);
		$stmt->execute();
		$profile  =  $stmt->fetch();
		//printr($profile);
		if(isset($profile[0])){
			$this->sendActivationLink($email);
			return $json = array("success" => true, "Info" => "Email sent");
		}else{
			return $json = array("success" => false, "Info" => "Email not found");
		}
		//check email exists  END
	}

	/*
		Action:  activateUser
		Required Inputs: UID
		Output:  Success True or false.
	*/
	public function activateUser($connection, $input){
		//check inputs
		if(!isset($input['uid']))
		{
			return $json = array("success" => false, "Info" => "Invalid Inputs");
		}
		//sanitise inputs
		$uid = htmlspecialchars(strip_tags($input['uid']));

        $connection->beginTransaction();

		//check email exists  START
		$sql      = "SELECT * FROM $this->usersTable WHERE uid = :uid";
		$stmt     = $connection->prepare($sql);
		$stmt->bindParam(":uid",$uid);
		$stmt->execute();
		$users = $stmt->fetch();
		//printr($profile);
		if(isset($users[0])){            

			$sql = "UPDATE $this->usersTable SET activated=1, enabled=1 WHERE uid=:uid";
			$stmt = $connection->prepare($sql);
			$stmt->bindParam(":uid",$uid);
			$stmt->execute();
            if($stmt->rowCount() === 1){
                $sql = "UPDATE $this->profilesTable SET active=1 WHERE uid=:uid";
                $stmt = $connection->prepare($sql);
                $stmt->bindParam(":uid",$uid);
                $stmt->execute();
                if($stmt->rowCount() === 1){
                    $connection->commit();
                }
                else {
                    $connection->rollBack();
                    return $json = array("success" => false, "Info" => "Couldn't activate the user. Try again later.");
                }
            }
            
			return $json = array("success" => true, "Info" => "User activated");
		}else{
			return $json = array("success" => false, "Info" => "Invalid Request");
		}
		//check email exists  END
	}	

	/*
		Action:  changePassword
		Required Inputs: UID and Password
		Output:  Success True or false.
	*/
	public function changePassword($connection, $input){
		//check inputs
		if(!isset($input['uid']) || !isset($input['newPassword']))
		{
			return $json = array("success" => false, "Info" => "Invalid Inputs");
		}
		//sanitise inputs
		$uid      = htmlspecialchars(strip_tags($input['uid']));
		$password = htmlspecialchars(strip_tags(base64_decode($input['newPassword'])));

		//check email exists  START
		$sql      = "SELECT * FROM $this->profilesTable WHERE uid =:uid";
		$stmt     = $connection->prepare($sql);
		$stmt->bindParam(":uid",$uid);
		$stmt->execute();
		$profile = $stmt->fetch();
		//printr($profile);
		if(isset($profile[0])){
			$sql = "UPDATE $this->usersTable SET password=:password, enabled=1 WHERE uid=:uid";
			$stmt = $connection->prepare($sql);
			$stmt->bindParam(":password",$password);
			$stmt->bindParam(":uid",$uid);
			$stmt->execute();
			return $json = array("success" => true, "Info" => "Password Changed");
		}else{
			return $json = array("success" => false, "Info" => "Invalid Request");
		}
		//check email exists  END
	}

	/*
		Action:  changePassword
		Required Inputs: email and Password
		Output:  Success True or false.
	*/
	public function validateUser($connection, $input){
		//check inputs
		if(!isset($input['email']) || !isset($input['password']))
		{
			return $json = array("success" => false, "Info" => "Invalid Inputs");
		}
		//sanitise inputs
		$email      = htmlspecialchars(strip_tags($input['email']));
		$password = htmlspecialchars(strip_tags(base64_decode($input['password'])));

		//check email exists  START
		$sql  = "SELECT u.uid FROM $this->profilesTable p, $this->usersTable u WHERE p.uid=u.uid AND p.email =:email AND u.password =:password and p.active=1;";
		$stmt = $connection->prepare($sql);
		$stmt->bindParam( ":email", $email, PDO::PARAM_STR);
		$stmt->bindParam( ":password", $password, PDO::PARAM_STR);
		$stmt->execute();
		$profile = $stmt->fetch();
		if(isset($profile[0])){
			return $json = array("success" => true, "Info" => "User okay");
		}else{
			return $json = array("success" => false, "Info" => "Invalid Login");
		}
		//check email exists  END
	}

	/*
		Action:  sendActivationLink
		Required Inputs: email
		Output:  Success True or false.
	*/
	public function sendActivationLink($input){
		//we will do this late
	
	}

    public function validEmail($email){
        //Validate the email address
		//return type boolean true or false
        if(!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)){
			return false;
		}else{
			return true;
		}
    }
	public function splitEmail($email){
        $email = explode('@',$email);
		return $email[0] ;
    }
}//end class


///dev function
function printr($data){
	echo '<pre>';
	print_r($data);
	echo '</pre>';	
}