<h2>List of questions waiting for your answer </h2>

<?php

$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
if(!$db)
	exit('Error while connecting to the database :<br/>'.$db->connect_error);

$query = $db->query('SELECT question.id, question.question FROM question,attribution 
						WHERE question.id=attribution.id_question AND attribution.id_user='.$_SESSION['userid']);

if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}

if($query->num_rows != 0){
	echo "<h3>Questions attributed to you :</h3><ul>";
	while($result = $query->fetch_assoc())
		echo "<li><a href='?page=viewQuestion&id=".$result['question.id']."'>".$result['question.question']."</a></li>";
	echo('</ul>');
}

$query = $db->query('SELECT id,question FROM question WHERE status="open" AND id='.$_GET['id']);

if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}

if($query->num_rows != 0){
	echo "<h3>Open questions :</h3><ul>";
	while($result = $query->fetch_assoc())
		echo "<li><a href='?page=viewQuestion&id=".$result['id']."'>".$result['question']."</a></li>";
	echo('</ul>');
}

$db->close();
?>