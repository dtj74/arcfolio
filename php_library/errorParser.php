<?php

/*
*	This class takes a dirty json string and checks it for errors associated with certain forms.
*
*/
class errorParser 
{
	public function __construct($package, $type = '')
	{
		
		//unpackage json string into a php array
		$obj = json_decode($package, true);
					
		//check if type is default/user//
		if($type == '' || $type == 'user')
		{
			$this->email = $obj["email"];
			$this->pass1 = $obj["pass1"];
			$this->pass2 = $obj["pass2"];
			$captcha = $obj["response"];
			//check that no inputs are null.// (note: pass2 does not need a null check, it is match checked against pass1 later.)//
			
			//if($this->username == NULL) 	{ $errors[] = "Username"; }
			
			if($this->email == NULL) 		{ $errors[] = "Email"; }
			if($this->pass1 == NULL) 		{ $errors[] = "Password"; }
			
			$errors[] = "spacer";
			
			if ( preg_match('/\s/',$this->email) )  {$errors[] = 'Email cannot contain spaces';}
			
			if(strpos($this->email, '@') === FALSE && $this->email != NULL)	{ $errors[] = "Email is not a legitimate email"; }
			
			//check that email/password is not overly long i.e. check for SQL INJECTION.//
			if(strlen($this->email) > 40 && $this->email != NULL)	{ $errors[] = "Email is not valid"; }
			if(strlen($this->pass1) > 30 && $this->pass1 != NULL)	{ $errors[] = "Password must be less than 30 chars"; }
			
			//keep a standard of password security.//
			if(strlen($this->pass1) < 5 && $this->pass1 != NULL)	{ $errors[] = "Password must be greater than 5 chars"; }
			
			//check that passwords match..//
			if($this->pass1 !== $this->pass2 && $this->pass1 != NULL)	{ $errors[] = "Passwords do not match"; }
			
			//check that email is not already in db.//
			$emailDUPLICATE = mysql_query("SELECT email FROM  `members` WHERE email =  '$this->email'");
			if(mysql_num_rows($emailDUPLICATE) > 0)		{ $errors[] = "Email is already in our system"; }
			
			//form must have no other errors before checking recaptcha or google will deny it.
			if(count($errors) == 1)
		{
			/////////////////////////google recaptcha code//////////////////////////////////////////////
			if(!$captcha) {$errors[] = 'Captcha must be checked';}
			else{
			$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LcFmQYTAAAAAMR4m-eenxq5Vlj_9r9U-IlZ4WKS&response=".$captcha);
			$response = json_decode($response, true);
			if($response["success"] == false) {$errors[] = 'We have detected that you are a robot, please try again.'.$response["success"].' asdf';}
			}
			////////////////////////////////////////////////////////////////////////////////////////////
		}
		
		}
		
			
		// determine if errors exist //
		if(count($errors) != 1)
		{
			// errors exist, run them through the error message generator //
			$this->json_errors = $this->errorMessageGenerator($errors);
			$this->errorsfound = TRUE;
		}
		else
		{
			// no errors exist, continue processing data //
			$this->errorsfound = FALSE;
		}
				
	}
	
	//takes an array of error messages and converts them into a formated user error message.//
	private function errorMessageGenerator($e)
	{
		//errorMessageGenerator is complex and hard to follow//
		//generates a gramatically correct error message regardless of # of errors.//
		
		// find what number in the queue the spacer is, this is the number of nulls errors found //
		$null_count = array_search("spacer", $e);
		// intialize count //
		$tot_count = count($e);
		// create basic error message //
		$errormsg = '';
		// null errors found //	
		if($null_count > 0)
		{
			$errormsg .= "Please fill <b>all required fields.</b> ";
		}

		// add to message if null exist and more errors exist//
		if($null_count > 0 && ($tot_count - 1 > $null_count))
		{
			$errormsg .= " Also, your ";
		}

		// no null errors but others present. //
		else if ($null_count == 0) 
		{
			$errormsg .= "Your "; $prefix = ". Also,";
		}

		// initialize some vars //			
		$first_msg = 	$e[$null_count + 1];		
		if($tot_count > 2)
		{	
			$second_msg = 	$e[2];
		}
		$last_msg = 	$e[$tot_count - 1];

		// create basic error msg //
		for($i = $null_count+1; $i < $tot_count; ++$i)
		{
			if($e[$i] == $first_msg)				 {	$errormsg .= $e[$i];	}
			else if($e[$i]  == $second_msg) 		 {	$errormsg .= $prefix." your ".$e[$i];	}
			else if($e[$i]  == $last_msg) 			 {	$errormsg .= ", and your ".$e[$i];	}
			else									 {	$errormsg .= ", your ".$e[$i];	}
			if($i == $tot_count - 1){	$errormsg .= ".";	}
		}
		// set public var to msg //
		$array = array(
		"errors" => $e,
		"failure" => true,
		"msg" => $errormsg,
		);
		return json_encode($array);
	}
}

?>