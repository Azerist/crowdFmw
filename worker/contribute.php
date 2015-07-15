<h2>contribute</h2>
<?php 

if(!isset($_GET['question'],$_POST['answer']))
	exit('Error : the data sent by your browser is incorrect.');

$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
if(!$db)
	exit('Error while connecting to the database :<br/>'.$db->connect_error);

$question = $_GET['question'];
$userid = $_SESSION['userid'];
$answer = $_POST['answer'];

//insert contribution into the database
$query = $db->query("INSERT INTO contribution (id_question,id_worker,id_answer) VALUES ($question,$userid,$answer);");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}

//remove the attribution if existing
$db->query("DELETE FROM attribution WHERE id_question=$question AND id_user=$userid");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}

?>
<p>Contribution successfully registered.</p>