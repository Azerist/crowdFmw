<h2>Delete question</h2>

<?php

//Check if id is provided.
if(!isset($_GET['id']))
	error('Question id missing',$db);

//If the user is 'sure', delete the question
if(isset($_POST['sure']) && $_POST['sure'] == 'yes'){

	//Delete the input file linked to the question
	$query = $db->query('SELECT input FROM question WHERE id='.$_GET['id']) or dbErr($db);
	
	$input = $query->fetch_assoc()['input'];
	if($input != NULL)
		unlink($input);

	//Delete the question from the database
	$query = $db->query('DELETE FROM question WHERE id='.$_GET['id']) or dbErr($db);

	$db->close();
	exit('Question correctly deleted.<br/><a href="?page=index">Go back to the index.</a>');
}

//Warn the user if the question already has contributions from workers
$query = $db->query('SELECT count(*) FROM contribution WHERE id_question='.$_GET['id']) or dbErr($db);

$count = $query->fetch_assoc()['count(*)'];

if($count != 0)
	echo 'Be careful, this question already has '.$count.' contributions !<br/>';


?>

<form method='post'>
	Confirm that you want to delete this question :<br/>
	<input type="radio" name="sure" value='yes'/>Yes<br/>
	<input type="radio" name="sure" value='no' checked/>No<br/>
	<input type="submit"/>
</form>

