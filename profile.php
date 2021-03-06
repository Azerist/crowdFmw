<h2>View and edit your profile</h2>

<?php 

//================================================================================================================================
//if the form has been submitted, treat the data
if(isset($_POST['regUN'])){

	if($_POST['regUN'] == '')
		echo "please enter a non-empty username";
	else{
		//Initialize an object to store the two parts of the sql request, that will be filled iteratively
		$sql = new stdClass();
		$sql->sql = 'UPDATE '.$_SESSION['usermode'].' SET username="'.$_POST['regUN'].'"';
		$sql->ok = TRUE;

		if($_POST['pass'] != '')
			if($_POST['pass']!=$_POST['pass2'])
				echo "The two passwords don't match";
			else
				$sql->sql = $sql->sql.',password="'.password_hash($_POST['pass'], PASSWORD_DEFAULT).'"';
		
		//If the user is registering as a worker, treat the features
		if($_SESSION['usermode'] == 'worker'){
			$features = scandir("features");
			foreach ($features as $filename) {
				if(substr($filename,-4) == ".php"){
					$class = str_replace('.php', '', $filename);
					if(!class_exists($class))
						include 'features/'.$filename;
					$feat = new $class();
					$sql = $feat->getProfileForm($_POST,$sql);
					if(!$sql->ok){
						echo $sql->err;
						break;
					}
				}	
			}
		}

		$sql->sql = $sql->sql.' WHERE id='.$_SESSION['userid'];
		if($sql->ok){
			$query = $db->query($sql->sql) or dbErr($db);

			echo 'Profile successfully updated !';
			echo "<br/>";
		}
	}
}

//======================================================================================================================
//Get the user info from the database
$query = $db->query("SELECT * FROM ".$_SESSION['usermode']." WHERE id=".$_SESSION['userid']) or dbErr($db);

$user = $query->fetch_assoc();
//Echo the form prefilled with the user data
?>

<form method="post" accept-charset="utf-8">
	Username : <input type="text" name="regUN" value="<?=$user['username']?>"/><br/>
	Password and confirm : <input type="password" name="pass"/> <input type="password" name="pass2"/><br/>
	<?php
	if($_SESSION['usermode'] == 'worker'){
		//For workers, add the form lines for the features
		$features = scandir("features");
		foreach ($features as $filename) {
			if(substr($filename,-4) == ".php"){
				$class = str_replace('.php', '', $filename);
				if(!class_exists($class))
					include 'features/'.$filename;
				$feat = new $class();
				$feat->htmlForm($user);
				echo "<br/>";
			}	
		}
	}
	?>
	<input type="submit"/><input type='reset'/>
</form>