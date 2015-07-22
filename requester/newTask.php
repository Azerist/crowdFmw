<h2>Create a new task</h2>

<?php
//If the form has been submitted, insert the new task
if(isset($_POST['taskName']))
	if($_POST['taskName'] == '')
		echo 'Please enter a name for your task';

	//And if the user has specified a non-empty task name
	else{
		//Connect to the database
		$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
		if(!$db)
			exit('Error while connecting to the database :<br/>'.$db->connect_error);

		//Create the two parts of the sql query
		$sql1 = "INSERT INTO task(name,id_requester";
		$sql2 = 'VALUES ("'.str_replace('"',"'",$_POST['taskName']).'",'.$_SESSION['userid'];

		//if a description is provided, add it to the query
		if($_POST['description'] != "" && $_POST['description'] != 'Task description'){
			$sql1 = $sql1.',description';
			$sql2 = $sql2.',"'.str_replace('"',"'",$_POST['description']).'"';
		}

		//Complete the query and execute it
		$sql1 = $sql1.')';
		$sql2 = $sql2.')';
		$query = $db->query($sql1.$sql2);
		if(!$query){
			$err = $db->error;
			$db->close();
			exit('Database error : '.$err);
		}
		?>
		<p>Task successfully inserted into database.</p>
		<a href='?page=newQuestion'>Add a question</a><br/>
		<a href='?page=listTasks'>See your tasks list</a><br/>
		<a href='?page=index'>return to index</a>

		<?php
		exit();
	}
//if not, display the form
?>
<form method='post'>
	Task name : <input type="text" name="taskName"/><br/>
	<textarea name="description" rows="5" cols="50">Task description</textarea><br/>
	<input type="submit"/>
</form>