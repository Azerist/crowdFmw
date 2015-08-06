<?php
	header('Content-Type:application/json; charset=UTF-8');
	session_start();
	//Read config files :
	$fmwName = file_get_contents('../.fmwName');
	$mysql = file_get_contents('../.mysqlInfo');
	$headers = getallheaders();

	//function to exit after correctly destroying the mysql session
	function error($message,$code,$db){
		$error = new stdClass();
		$error->error = new stdClass();
		$error->error->code = $code;
		$error->error->message = $message;
		if($db != NULL)
			$db->close();
		exit(json_encode($error));
	}
	//function to exit any script in case of database error
	function dbErr($db){
		$err = $db->error;
		error('Database error : '.$err,7,$db);
	}

	//Check if everything is ok, then display the requested page.
	if(!isset($_GET['page']))
		error('Invalid GET variable : page',3,NULL);

	if(!($fmwName && $mysql))
		error('Fatal error : could not read platform configuration files.',0,NULL);

	$mysql = json_decode($mysql);
	if($mysql == NULL)
		error('Fatal error : the mysql configuration could not be decoded as json.',1,NULL);

	//check headers and read json input
	if(!$headers || $headers['Content-Type']!='application/json')
		error('This page requires a json POST input.',5,NULL);
	
	$input = json_decode(file_get_contents('php://input')) or error('Json query syntax incorrect',9,$db);

	//Connect to the database
	$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port)
	or error('Error while connecting to the database : '.$db->connect_error,2,$db);

	include 'login.php';
	if(!include("$_GET[page].php"))
		error('Invalid GET variable : page',3,$db);

	$db->close();
?>