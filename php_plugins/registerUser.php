<?php

//grab defaults and autoloader
include('IAL.php');

//connect to database.//
new mysqlConnector();


//check passed data for errors
$cleanData = new errorParser($_GET['rawdata']);

//if data has no errors, pass to register class.
if($cleanData == TRUE)
{
	echo new registerUser($_GET['rawdata']);
}
else
{
	//data has errors, return errorParsers() generated json string//
	echo $cleanData;
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
*	This class takes an errorParsed json string and creates a user row in the users table of the database.
*
*/

class registerUser
{
	
	public function __construct($package)
	{
		//decode json into a php array.//
		$obj = json_decode($package);
		
		//encrypt password and generate a salt for password using our encryptPassword() class.//
		$ProtectedPass = new encryptPassword($obj->pass1);
		
		//generate creation dates.//
		$date = date_create();
		$date = date_format($date, 'Y-m-d H:i:s');
		
		// add user to db //
		$sql = mysql_query("INSERT INTO users (email, password, salt, creationdate, lastlogin) VALUES( '$obj->email', '$ProtectedPass->password', '$ProtectedPass->salt', '$date', '$date')") or die (mysql_error());
		
		// grab newly generated db id and set it as the users id //
		$userId = mysql_insert_id();
		
		
		//email user activation code//
		
		//generate a json string for angular to process.//
		$array = array(
			"failure" => false,
			"msg" => "Success! you will receive an activation email shortly."
			);
		return json_encode($array);		
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

?>