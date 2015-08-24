<h2>contribute</h2>
<?php

//if the required data is not provided, exit the script
if(!isset($_GET['task']))
	exit('Error : the data sent by your browser is incorrect.');

$userid = $_SESSION['userid'];

//Get the list of the question ids linked to the task
$query = $db->query("SELECT id FROM question WHERE id_task=$_GET[task]") or dbErr($db);

if($query->num_rows == 0)
	error('The task id provided is incorrect.',$db);

//initialize the sql query
$sql = "INSERT INTO contribution(id_worker,id_answer,id_question) VALUES ";

$question = $query->fetch_assoc();

if(!isset($_POST["ans-$question[id]"]))
	error('The data sent by your browser is incorrect or incomplete.',$db);

$sql = $sql."($userid,".$_POST["ans-$question[id]"].",$question[id])";

//Complete the query iteratively
while($question = $query->fetch_assoc()){

	if(!isset($_POST["ans-$question[id]"]))
		error('The data sent by your browser is incorrect or incomplete.',$db);

	$sql = $sql.", ($userid,".$_POST["ans-$question[id]"].",$question[id])";
}

//execute the query
$db->query($sql) or dbErr($db);

//remove the assignment if existing
$db->query("DELETE FROM assignment WHERE id_task=$_GET[task] AND id_worker=$userid") or dbErr($db);

//Increment the current number of contributions of the task
$db->query("UPDATE task SET current=current+1 WHERE id=$_GET[task]");

//Change the task state to "completed" if the target number of contributions is reached
$db->query("UPDATE task SET status='completed' WHERE id=$_GET[task] AND current>=target");

//Transfer the reward from the requester to the Worker
	$db->query("UPDATE worker,task SET balance=balance+reward WHERE task.id=$_GET[task] AND worker.id=$userid") or dbErr($db);
	$db->query("UPDATE requester,task SET balance=balance-reward WHERE task.id=$_GET[task] AND requester.id=task.id_requester") or dbErr($db);


?>
<p>Contribution successfully registered.</p>
