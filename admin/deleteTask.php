<h2>Delete task</h2>

<?php

//Check if task id is provided
if(!isset($_GET['id']))
	exit('task id missing');

//Connect to database
$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
if(!$db)
	exit('Error while connecting to the database :<br/>'.$db->connect_error);

//If the user is 'sure', delete the task
if(isset($_POST['sure']) && $_POST['sure'] == 'yes'){

	//Delete the input files linkd to this task's questions
	$query = $db->query('SELECT input FROM question WHERE id_task='.$_GET['id']);
	if(!$query){
		$err = $db->error;
		$db->close();
		exit('Database error : '.$err);
	}
	while($result = $query->fetch_assoc()){
		$input = $result['input'];
		if($input != NULL)
			unlink($input);
	}

	//Delete the task
	$query = $db->query('DELETE FROM task WHERE id='.$_GET['id']);

	if(!$query){
		$err = $db->error;
		$db->close();
		exit('Database error : '.$err);
	}
	
	$db->close();
	exit('Task correctly deleted.<br/><a href="?page=index">Go back to the index.</a>');
}

//Warn the user if this task already has contributions
$query = $db->query('SELECT count(*) FROM contribution,question WHERE contribution.id_question=question.id AND 
						question.id_task='.$_GET['id']);

if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}

$count = $query->fetch_assoc()['count(*)'];

if($count != 0)
	echo 'Be careful, this task already has '.$count.' contributions !<br/>';

$db->close();
?>

<form method='post'>
	Confirm that you want to delete this task :<br/>
	<input type="radio" name="sure" value='yes'/>Yes<br/>
	<input type="radio" name="sure" value='no' checked/>No<br/>
	<input type="submit"/>
</form>