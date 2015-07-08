<h2>Create a new task</h2>

<?php
if(isset($_POST['taskName']))
	if($_POST['taskName'] == '')
		echo 'Please enter a name for your task';

	elseif(strpos($_POST['taskName'],';')!==FALSE || strpos($_POST['taskName'],"'")!==FALSE || strpos($_POST['taskName'],'"')!==FALSE)
		echo "You must not use ; ' or ".'" in the task name.';

	else{
		$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
		if(!$db)
			exit('Error while connecting to the database :<br/>'.$db->connect_error);

		$sql1 = "INSERT INTO task(name,id_requester";
		$sql2 = "VALUES ('".$_POST['taskName']."',".$_SESSION['userid'];

		if($_POST['description'] != "" && $_POST['description'] != 'Task description'){
			$sql1 = $sql1.',description';
			$sql2 = $sql2.',"'.str_replace('"',"'",$_POST['description']).'"';
		}

		$sql1 = $sql1.')';
		$sql2 = $sql2.')';
		$query = $db->query($sql1.$sql2);

		if(!$query){
			$err = $db->error;
			$db->close();
			exit('Database error : '.$err);
		}
		else{
		?>
		<p>Task successfully created.</p>
		<a href='?page=newQuestion'>Add a question</a><br/>
		<a href='?page=index'>return to index</a>

		<?php
		exit();
		}
	}


?>
<form method='post'>
	Task name : <input type="text" name="taskName"/><br/>
	<textarea name="description" rows="5" cols="50">Task description</textarea><br/>
	<input type="submit"/>
</form>