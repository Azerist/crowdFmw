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

//check if the target number of contributions is reached
$query = $db->query("SELECT target FROM question WHERE id=$question");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}
$target = $query->fetch_assoc()['target'];
//get the actual number of contributions
$query = $db->query("SELECT count(*) FROM contribution WHERE id_question=$question");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}
$count = $query->fetch_assoc()['count(*)'];

if($count>=$target){
	$query = $db->query("UPDATE question SET status='completed' WHERE id=$question");
	if(!$query){
		$err = $db->error;
		$db->close();
		exit('Database error : '.$err);
	}
}


//remove the attribution if existing
$db->query("DELETE FROM attribution WHERE id_question=$question AND id_user=$userid");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}

$db->close();
?>
<p>Contribution successfully registered.</p>

