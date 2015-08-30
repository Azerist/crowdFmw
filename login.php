<h2>Login</h2>
<?php
//If a session is already set, exit this script
if(isset($_SESSION['usermode']))
	exit("It looks like you're already connected.<br/> Do you want to <a href='.?page=logout'>logout</a> ?");

//======================================================================================================================
//if the login form has already been submitted, check the entered login data
if(isset($_POST['logtype'])){

	$query = $db->query("SELECT id,password,username FROM ".$_POST['logtype']." WHERE username='".$_POST['login']."';") or dbErr($db);

	$result = $query->fetch_assoc();
	if($result != NULL)
		if(password_verify($_POST['pass'],$result['password'])){
			$_SESSION['usermode'] = $_POST['logtype'];
			$_SESSION['userid'] = $result['id'];
			$_SESSION['username'] = $result['username'];
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

<form action="" method="post" accept-charset="utf-8">
	Login as : <select name="logtype"><option >worker</option><option>requester</option><option>admin</option></select><br/>
	Username : <input type="text" name="login"/><br/>
	password : <input type="password" name="pass"/><br/>
	Please note that logging in will create a cookie on your system to store your user information. Login won't work if your browser doesn't accept cookies.<br/>
	<input type="submit"/>
</form>
<br/>
<a class="button" href='?page=register'>Register on the platform</a>
<h2>Notice : browser compatibility</h2>
<div class='content'>This software is designed to work with the following browser versions or more recent :
	<ul>
		<li>Internet Explorer 9+</li>
		<li>Firefox 4+</li>
		<li>Chrome 19+</li>
		<li>Opera 15+</li>
		<li>Opera Mobile 21+	 </li>
		<li>Safari 6+</li>
		<li>Safari Mobile 6+</li>
		<li>Android Browser 4.4+</li>
	</ul>
	It should work on older versions, but some elements may not be displayed properly.
</div>
