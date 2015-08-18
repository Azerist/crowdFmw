<h2>Create a new task</h2>

<?php
//If the form has been submitted, insert the new task
if(isset($_POST['taskName']))
	if($_POST['taskName'] == '' && is_int($_POST['target']))
		echo 'Please enter a name for your task';

	//And if the user has specified a non-empty task name, and the target number is correct
	else{
		//Create the two parts of the sql query
		$sql1 = "INSERT INTO task(name,id_requester,status,target";
		$sql2 = "VALUES ('".str_replace("'","''",$_POST['taskName'])."',$_SESSION[userid],'$_POST[assignment]',$_POST[target]";

		if($_POST['status'] == 'waiting' && $_POST['extparams'] != ''){
			$sql1 = $sql1.',extparams';
			$sql2 = $sql2.",'$_POST[extparams]'";
		}

		//if a description is provided, add it to the query
		if($_POST['description'] != "" && $_POST['description'] != 'Task description'){
			$sql1 = $sql1.',description';
			$sql2 = $sql2.',"'.str_replace(array("\r\n","\r","\n"),'<br/>',$_POST['description']).'"';
		}

		//Complete the query and execute it
		$sql1 = $sql1.')';
		$sql2 = $sql2.')';

		$query = $db->query($sql1.$sql2) or dbErr($db);
		?>
		<p>Task successfully inserted into database.</p>
		<a href='?page=newQuestion'>Add a question</a><br/>
		<a href='?page=listTasks'>See your tasks list</a><br/>
		<a href='?page=index'>return to index</a>
		<hr/>

		<?php
	}
//if not, display the form
?>
<form method='post'>
	Task name : <input type="text" name="taskName" maxlength="64" /><br/>
	<textarea name="description" rows="5" cols="50" maxlenght="512">Task description</textarea><hr/>
	Assignment type : <select name="assignment">
		<option>open</option>
		<option>waiting</option>
	</select>
	if "waiting", you can specify here parameters for an external assignment algorithm : <input type="text" name="extparams" maxlength="128" /><br/>
	Target number of contributions : <input type="text" name="target"/> Integer value, -1 for manual or external algorithm.<br/>
	<input type="submit"/>
</form>