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
	$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
	if($db->connect_error)
		exit('Error while connecting to the database :<br/>'.$db->connect_error);

	//function to exit after correctly destroying the mysql session
	function error($message,$db){
		$db->close();
		exit("<p>$message</p>");
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
		<link rel='stylesheet' href="style/main.css" type="text/css"/>
		<link rel="stylesheet" href="style/font-awesome-4.4.0/css/font-awesome.min.css">
		<title><?=$fmwName?></title>
	</head>
	<script>
	function Menu(id){
		menu = document.getElementById(id);
		state = menu.style.maxWidth;
		if(state == '' || state == '0px'){
			menu.style.maxWidth = '300px';
		}
		else{
			menu.style.maxWidth = '0px';
		}
	}
	</script>
	<body>

		<h1><?=$fmwName?></h1>
<?php
		if(isset($_SESSION['usermode'])){
?>
			<div class="header">
<?php
					//Generate all the menus
					if($_SESSION['usermode'] != 'worker'){
?>
					<a href='javascript:void(0)' onclick="Menu('menu')"><i class="fa fa-bars"></i> Menu </a>|
<?php
					}
?>
					<a href='?page=index'><i class="fa fa-home"></i> Index</a>
					<a href='javascript:void(0)' onclick="Menu('usermenu')" class="userlink"><i class="fa fa-user"></i>&nbsp<?=$_SESSION['username']?></a>
			</div>
			
			<div id="usermenu">
				<a href='?page=profile'><i class="fa fa-cog"></i>&nbspView and edit your profile</a>
				<a href='?page=logout'><i class="fa fa-sign-out"></i>&nbspLogout</a>
			</div>
<?php
			if($_SESSION['usermode'] != 'worker'){
?>
				<div id="menu">
<?php
					if($_SESSION['usermode'] == 'requester'){
?>
						<a href='?page=newTask'><i class="fa fa-tasks"></i>&nbspCreate a new task</a>
						<a href='?page=newQuestion'><i class="fa fa-question"></i>&nbspAdd a question to a task</a>
						<a href='?page=recharge'><i class='fa fa-money'></i>&nbspRecharge your account</a>
						<a href='?page=listTasks'><i class="fa fa-tasks"></i>&nbspView your tasks</a>
<?php
					}
					if($_SESSION['usermode'] == 'admin'){
?>
						<a href='?page=newQuestion' ><i class="fa fa-question"></i>&nbspAdd a question to a task</a>
						<a href='?page=listTasks'><i class="fa fa-tasks"></i>&nbspView the platform tasks</a>
						<a href='?page=listRequesters'><i class="fa fa-user"></i>&nbspList the requesters</a>
						<a href='?page=newAdmin'><i class="fa fa-user"></i>&nbspCreate a new administrator</a>
						<a href='?page=newFeature'><i class="fa fa-cog"></i>&nbspInitialize a new worker feature</a>
<?php
					}
?>
				</div>
<?php
			}
			if(isset($_GET['page']))
				if(file_exists($_SESSION['usermode'].'/'.$_GET['page'].'.php'))
					include $_SESSION['usermode'].'/'.$_GET['page'].'.php';
				elseif(file_exists($_GET['page'].'.php'))
					include $_GET['page'].'.php';
				else
					error('The requested page does not exist, or is not accessible for the '.$_SESSION['usermode'].' user mode.',$db);
			else
				include $_SESSION['usermode'].'/index.php';
		}
		else
			if(isset($_GET['page']) && $_GET['page'] == 'register')
				include('register.php');
			else
				include 'login.php';

		$db->close();
?>
	</body>
</html>
