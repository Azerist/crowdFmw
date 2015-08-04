<h2>Register</h2>
<?php

if(isset($_POST['regUN'])){		//If the register form has already be submitted, treat the data
	
	//Check if the username and password are correct
	if($_POST['regUN'] == '' || $_POST['pass'] == '')
		echo 'Please enter a username and a password.';
	elseif($_POST['pass'] != $_POST['pass2'])
		echo "The two passwords don't match";
	//Refuse forbidden characters.
	elseif(strpos($_POST['regUN'],';')!==FALSE || strpos($_POST['regUN'],"'")!==FALSE || strpos($_POST['regUN'],'"')!==FALSE)
		echo "You must not use ; ' or \" in the username.";

	else{//if everything is ok
		$db->query("INSERT INTO admin(username,password) VALUES ('$_POST[regUn]','".password_hash($_POST['pass'], PASSWORD_DEFAULT)."')") or dbErr($db);
	}
}

//======================================================================================================================================================
?>
<form action=".?page=register" method="post" accept-charset="utf-8">
	Chose a username : <input type="text" name="regUN"/><br/>
	Chose a password and confirm : <input type="password" name="pass"/> <input type="password" name="pass2"/><br/>
	<input type="submit"/>
</form>
<?php

