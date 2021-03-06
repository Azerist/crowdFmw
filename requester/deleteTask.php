<h2>Delete task</h2>

<?php

//Check if task id is provided
if(!isset($_GET['id']))
	error('task id missing',$db);

//Check if the user owns this task
$query = $db->query("SELECT id_requester FROM task WHERE id=$_GET[id]") or dbErr($db);

if(($result = $query->fetch_assoc()) == NULL || $result['id_requester'] != $_SESSION['userid'])
	error("Error : you don't own this task.",$db);


//If the user is 'sure', delete the task
if(isset($_POST['sure']) && $_POST['sure'] == 'yes'){

	//Delete the input files linked to this task's questions; the questions will be automatically deleted from database thanks to 'ON DELETE CASCADE' option.
	$query = $db->query("SELECT input FROM question WHERE id_task=$_GET[id]") or dbErr($db);

	while($result = $query->fetch_assoc()){
		$input = $result['input'];
		if($input != NULL)
			unlink($input);
	}

	//Delete the task
	$query = $db->query("DELETE FROM task WHERE id=$_GET[id]") or dbErr($db);

	$db->close();
	exit('<p>Task correctly deleted.</p><a class="button" href="?page=index">Go back to the index.</a>');
}

//Warn the user if this task already has contributions
$query = $db->query("SELECT current FROM task WHERE id=$_GET[id]") or dbErr($db);

$count = $query->fetch_assoc()['current'];

if($count != 0)
	echo "Be careful, this task already has $count contributions !<br/>";

?>

<form method='post'>
	Confirm that you want to delete this task :<br/>
	<input type="radio" name="sure" value='yes'/>Yes<br/>
	<input type="radio" name="sure" value='no' checked/>No<br/>
	<input type="submit"/>
</form>
