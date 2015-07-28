<h2>Login</h2>
<?php
//If a session is already set, exit this script
if(isset($_SESSION['usermode']))
	exit("It looks like you're already connected.<br/> Do you want to <a href='.?page=logout'>logout</a> ?");

//======================================================================================================================
//if the login form has already been submitted, check the entered login data
if(isset($_POST['logtype'])){
	
	$query = $db->query("SELECT id,password FROM ".$_POST['logtype']." WHERE username='".$_POST['login']."';") or dbErr();

	$result = $query->fetch_assoc();
	if($result != NULL)
		if(password_verify($_POST['pass'],$result['password'])){
			$_SESSION['usermode'] = $_POST['logtype'];
			$_SESSION['userid'] = $result['id'];
			header('location: .?page=index');
			$db->close();
			exit();
		}

	?>
	<p color='red'>Incorrect login information.</p>
	<?php
}

//======================================================================================================================
//else, or if the data is incorrect, echo the login form
?>

<form action=".?page=login" method="post" accept-charset="utf-8">
	Login as : <select name="logtype"><option >worker</option><option>requester</option><option>admin</option></select><br/>
	Username : <input type="text" name="login"/><br/>
	password : <input type="password" name="pass"/><br/>
	<input type="submit"/>
</form>
<br/>
<a href='?page=register'>Register on the platform</a>