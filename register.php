<h2>Register</h2>
<?php

if(isset($_POST['regUN'])){		//If the complete register form has already be submitted, treat the data

	//Check if the username and password are correct
	if($_POST['regUN'] == '' || $_POST['pass'] == '')
		echo 'Please enter a username and a password.';
	elseif($_POST['pass'] != $_POST['pass2'])
		echo "The two passwords don't match";
	//Refuse forbidden characters.
	elseif(strpos($_POST['regUN'],';')!==FALSE || strpos($_POST['regUN'],"'")!==FALSE || strpos($_POST['regUN'],'"')!==FALSE)
		echo "You must not use ; ' or \" in your username.";

	else{//if everything is ok
		//Initialize an object to store the two parts of the sql request, that will be filled iteratively by the feature classes
		$sql = new stdClass();
		$sql->sql1 = "INSERT INTO $_POST[regType](username,password";
		$sql->sql2 = "VALUES ('$_POST[regUN]','".password_hash($_POST['pass'], PASSWORD_DEFAULT)."'";
		$sql->ok = TRUE;

		//If the user is registering as a worker, treat the features
		if($_POST['regType'] == 'worker'){
			$features = scandir("features");
			foreach ($features as $filename) {
				if(substr($filename,-4) == ".php"){
					$class = str_replace('.php', '', $filename);
					if(!class_exists($class))
						include 'features/'.$filename;
					$feat = new $class();
					$sql = $feat->getForm($_POST,$sql);
					if(!$sql->ok){
						echo $sql->err;
						break;
					}
				}
			}
		}
		if($sql->ok){
			//Insert the data

			$sql->sql1 = $sql->sql1.')';
			$sql->sql2 = $sql->sql2.')';

			$query = $db->query($sql->sql1.$sql->sql2) or dbErr($db);
			?>
			<p>Account succesfully created !</p>
			<a class="button" href='.?page=login'>Go back to the login page</a>
			<?php
			$db->close();
			exit();
		}
	}
}

//======================================================================================================================================================
if(isset($_POST['regType'])){ 	//treat the data from the first form, and generate the adapted form for requesters or workers
	?>
	<form action=".?page=register" method="post" accept-charset="utf-8">
		Chose a username : <input type="text" name="regUN" maxlength="16" /><br/>
		Chose a password and confirm : <input type="password" name="pass"/> <input type="password" name="pass2"/><br/>
		<?php
		if($_POST['regType'] == 'worker'){
			//For workers, add the form lines for the features
			$features = scandir("features");
			foreach ($features as $filename) {
				if(substr($filename,-4) == ".php"){
					$class = str_replace('.php', '', $filename);
					if(!class_exists($class))
						include 'features/'.$filename;
					$feat = new $class();
					$feat->htmlForm();
					echo "<br/>";
				}
			}
		}
		?>
		<input type="hidden" name="regType" value="<?=$_POST['regType']?>">
		<input type="submit"/>
	</form>
	<?php
	exit();
}

//======================================================================================================================================================
//If first execution of this script, return the minimal form to ask if the user wants to register as a worker or a requester.
?>

<form action=".?page=register" method="post" accept-charset="utf-8">
	Please chose if you want to register as a requester or a worker : <br/>
	<select name='regType'><option>worker</option><option>requester</option></select><br/>
	<input type="submit"/>
</form>
