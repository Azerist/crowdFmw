<h2>View a question</h2>

<?php 
//Check if a question id is provided
if(!isset($_GET['id']))
	exit('No question id provided.');

//Connect to the database
$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
if(!$db)
	exit('Error while connecting to the database :<br/>'.$db->connect_error);

//get the question information from the database
$query = $db->query("SELECT * FROM question WHERE id=$_GET[id]");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}
//if the query returns 0 rows, exit the script
if($query->num_rows == 0)
	exit('No question was found with the provided id.');

$question = $query->fetch_assoc();

//Get the information of the task linked to the question
$query = $db->query("SELECT * FROM task WHERE id=$question[id_task]");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}
$task = $query->fetch_assoc();

//Echo this information
echo "<h3>Task description</h3>\n<h4>$task[name]</h4>\n<p>$task[description]</p>\n";
echo "<h3>Question :</h3>\n";
echo "<h4>$question[question]</h4>\n";

//if the question has an input file, load the relevant inputType class and display the file
if(file_exists("inputTypes/$question[inputType].php")){
	include "inputTypes/$question[inputType].php";
	$input = new $question['inputType']($question['input']);
	$input->display();
}

//Get the answers linked to the question
$query = $db->query("SELECT * FROM answer WHERE id_question=$_GET[id]");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}
if($query->num_rows == 0)
	exit('No answers were found in the database for this question.');

//echo the form to choose an answer
echo '<form method="post" action="?page=contribute&question='.$_GET['id'].'">';
while($answer = $query->fetch_assoc()){
	echo '<input type="radio" name="answer" value="'.$answer['id'].'"/>'.$answer['answer'].'<br/>';
}
echo '<input type="submit"/></form>';

$db->close();
?>
