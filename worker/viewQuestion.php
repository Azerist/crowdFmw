<h2>View a question</h2>

<?php 
if(!isset($_GET['id']))
	exit('No question id provided.');

$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
if(!$db)
	exit('Error while connecting to the database :<br/>'.$db->connect_error);

$query = $db->query('SELECT * FROM question WHERE id='.$_GET['id']);

if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}

if($query->num_rows == 0)
	exit('No question was found with the provided id.');

$question = $query->fetch_assoc();
echo '<h3>'.$question['question'].'</h3>';

if(file_exists('../inputType/'.$question['inputType'].'.php')){
	include '../inputType/'.$question['inputType'].'.php';
	$input = new $question['inputType']('../questionFiles/'.$question['input'].'.php');
	$input->display();
}

$query = $db->query('SELECT * FROM answer WHERE id_question='.$_GET['id']);

if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}

if($query->num_rows == 0)
	exit('No answers were found for this question.');

echo '<form method="post" action="?page=contribute">';

while($answer = $query->fetch_assoc()){
	echo '<input type="radio" value="'.$answer['id'].'"/>'.$answer['answer'].'<br/>';
}

echo '<input type="submit"/></form>';
?>