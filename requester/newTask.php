<h2>Create a new task</h2>

<?php
//If the form has been submitted, insert the new task
if(isset($_POST['taskName']))
	if($_POST['taskName'] == '')
		echo 'Please enter a name for your task';

	//And if the user has specified a non-empty task name
	else{
		//Create the two parts of the sql query
		$sql1 = "INSERT INTO task(name,id_requester,status,target,extParams";
		$sql2 = "VALUES ('".str_replace('"',"'",$_POST['taskName'])."',$_SESSION[userid],$_POST[assignment],$_POST[target],$_POST[extparams]";

		//if a description is provided, add it to the query
		if($_POST['description'] != "" && $_POST['description'] != 'Task description'){
			$sql1 = $sql1.',description';
			$sql2 = $sql2.',"'.htmlentities($_POST['description']).'"';
		}

		//Complete the query and execute it
		$sql1 = $sql1.')';
		$sql2 = $sql2.')';
		$query = $db->query($sql1.$sql2) or dbErr();
		?>
		<p>Task successfully inserted into database.</p>
		<a href='?page=newQuestion'>Add a question</a><br/>
		<a href='?page=listTasks'>See your tasks list</a><br/>
		<a href='?page=index'>return to index</a>

		<?php
	}
//if not, display the form
?>
<form method='post'>
	Task name : <input type="text" name="taskName"/><br/>
	<textarea name="description" rows="5" cols="50">Task description</textarea><hr/>
	Assignment type : <select name="assignment">
		<option>open</option>
		<option>waiting</option>
	</select>
	if "waiting", you can specify here parameters for an external assignment algorithm : <input type="text" name="extparams"/><br/>
	Target number of contributions : <input type="text" name="target"/> Integer value, -1 for manual or external algorithm.<br/>
	<input type="submit"/>
</form>