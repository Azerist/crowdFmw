<?php
	session_start();
	//Read config files :
	$fmwName = file_get_contents('.fmwName');
	$mysql = file_get_contents('.mysqlInfo');

	if(!($fmwName && $mysql))
		exit('Fatal error : could not read platform configuration files.');

	$mysql = json_decode($mysql);
	if($mysql == NULL)
		exit('Fatal error : the mysql configuration could not be decoded as json.');

	//Connect to the database
	$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port)
	or exit('Error while connecting to the database :<br/>'.$db->connect_error);

	//function to exit after correctly destroying the mysql session
	function error($message,$db){
		$db->close();
		exit($message);
	}
	//function to exit any script in case of database error
	function dbErr($db){
		$err = $db->error;
		error('Database error : '.$err,$db);
	}

?>

<html>
	<head>
		<meta charset='utf-8'/>
		<title><?=$fmwName?></title>
	</head> 
	<body>
		<h1><?=$fmwName?></h1>
		<?php
		if(isset($_SESSION['usermode']))
			if(isset($_GET['page']))
				if(file_exists($_SESSION['usermode'].'/'.$_GET['page'].'.php'))
					include $_SESSION['usermode'].'/'.$_GET['page'].'.php';
				elseif(file_exists($_GET['page'].'.php'))
					include $_GET['page'].'.php';
				else
					exit('The requested page does not exist, or is not accessible for the '.$_SESSION['usermode'].' user mode.');
			else
				include $_SESSION['usermode'].'/index.php';
		else
			if(isset($_GET['page']) && $_GET['page'] == 'register')
				include('register.php');
			else
				include 'login.php';

		$db->close();
		?>
	</body>
</html>

