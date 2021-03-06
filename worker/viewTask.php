<h2>View a task</h2>
<?php

//Check if a task id is provided
if(!isset($_GET['id']))
	error('No task id provided !',$db);

//Get task information
$query = $db->query("SELECT name,description FROM task WHERE id=$_GET[id]") or dbErr($db);

//Check if a task was found with the id
if($query->num_rows == 0)
	error('Error : No task was found with the id provided by your browser. Please try again.',$db);

$task = $query->fetch_assoc();
?>
<h3><?=$task['name']?></h3>
<p><b>Task description :</b><?=$task['description']?></p>
<form action="?page=contribute&task=<?=$_GET['id']?>" method="post" accept-charset="utf-8">
	<?php
	//Get the task questions
	$query = $db->query("SELECT * FROM question WHERE id_task=$_GET[id]") or dbErr($db);

	//Display each question
	while($question = $query->fetch_assoc()){
		echo "<h3>$question[question]</h3>";
		//if existing, display the input file
		if(file_exists($question['input']) && include("inputTypes/$question[inputType].php")){
			$type = new $question['inputType']($question['input']);
			$type->display();
			echo "<br/>";
		}

		//Get all the possible answers for the question
		$query2 = $db->query("SELECT id,answer FROM answer WHERE id_question=$question[id]") or dbErr($db);
		//Display all the answers
		while($answer = $query2->fetch_assoc())
			echo "<input type='radio' name='ans-$question[id]' value='$answer[id]'/> $answer[answer]<br/>";

	}
	?>
	<input type="submit"/>
</form>
