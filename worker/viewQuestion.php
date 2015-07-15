<h2>View a question</h2>

<?php 
if(!isset($_GET['id']))
	exit('No question id provided.');

$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
if(!$db)
	exit('Error while connecting to the database :<br/>'.$db->connect_error);

$query = $db->query("SELECT * FROM question WHERE id=$_GET[id]");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}
if($query->num_rows == 0)
	exit('No question was found with the provided id.');
$question = $query->fetch_assoc();

$query = $db->query("SELECT * FROM task WHERE id=$question[id_task]");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}
if($query->num_rows == 0)
	exit('No question was found with the provided id.');
$task = $query->fetch_assoc();

echo "<h3>Task description</h3>\n<h4>$task[name]</h4>\n<p>$task[description]</p>\n";

echo "<h3>Question :</h3>\n";
echo "<h4>$question[question]</h4>\n";

if(file_exists("inputTypes/$question[inputType].php")){
	include "inputTypes/$question[inputType].php";
	$input = new $question['inputType']($question['input']);
	$input->display();
}

$query = $db->query("SELECT * FROM answer WHERE id_question=$_GET[id]");

if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}

if($query->num_rows == 0)
	exit('No answers were found for this question.');

echo '<form method="post" action="?page=contribute&question='.$_GET['id'].'">';

while($answer = $query->fetch_assoc()){
	echo '<input type="radio" name="answer" value="'.$answer['id'].'"/>'.$answer['answer'].'<br/>';
}

echo '<input type="submit"/></form>';
?>