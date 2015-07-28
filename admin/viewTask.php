<?php

//If no id is provided
if(!isset($_GET['id']))
	exit('task id missing');
//open database
$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
if(!$db)
	exit('Error while connecting to the database :<br/>'.$db->connect_error);

//If the form has been submitted, update the database
if(isset($_POST['taskName'])){
	
	$sql = 'UPDATE task SET ';

	$name = FALSE;
	if($_POST['taskName'] != ''){
		$sql = $sql.'name="'.$_POST['taskName'].'"';
		$name = TRUE;
	}

	if($_POST['description'] != '' && $_POST['description'] != "Task description"){
		if($name)
			$sql = $sql.',';
		$sql = $sql.'description="'.$_POST['description'].'" ';
	}
	
	$sql = $sql.'WHERE id='.$_GET['id'];

	$query = $db->query($sql);

	if(!$query)
		echo 'Could not update data : '.$db->error.' <br/>';
}

//Check if the user owns this task
$query = $db->query('SELECT id_requester FROM task WHERE id='.$_GET['id']);
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}
$result = $query->fetch_assoc();
if( $result == NULL || $result['id_requester'] != $_SESSION['userid'])
	exit("Error : you don't own this task.");

//Get task data from the database
$query = $db->query('SELECT * FROM task WHERE id ='.$_GET['id']);

if(!$query){
	$err = $db->error;
	$sb->close();
	exit('Database error : '.$err);
}
if($query->num_rows == 0){
	$sb->close();
	exit('The specified task id could not be found.');
}

$task = $query->fetch_assoc();
//Display task information
?>
<h2>Details of task  <?=$task['name']?></h2>
<h3>task description :</h3>
<p><?=$task['description']?></p>
<p>Target number of contributions : <?=$task['target']?><br/>
Current number of contributions : <?=$task['current']?></p>
<hr/>
<h3>Use this form to edit any of the above :</h3>
<form method='post'>
	Task name : <input type="text" name="taskName"/><br/>
	<textarea name="description" rows="5" cols="50">Task description</textarea><br/>
	<input type="submit"/>
</form>

<?php
//Get all the questions linked to this task from database
$query = $db->query('SELECT * FROM question WHERE id_task='.$task['id']);

if(!$query){
	$err = $db->error;
	$sb->close();
	exit('Database error : '.$err);
}
if($query->num_rows !=0){
	?>
	<h3>Questions linked to this task:</h3>
	<table border='1'>
		<thead>
			<tr>
				<th>Question</th>
				<th>Answers</th>
				<th>Get results</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tbody>
		<?php
		//For each question, get the answers linked to it
		while($question = $query->fetch_assoc()){
			$query2 = $db->query('SELECT answer FROM answer WHERE id_question='.$question['id']);
			if(!$query2){
				$err = $db->error;
				$sb->close();
				exit('Database error : '.$err);
			}
			?>
			<tr>
				<td>
					<?=$question['question']?>
				</td>
				<td>
					<?php
					while($ans = $query2->fetch_assoc())
						echo $ans['answer'].'<br/>';
					?>
				</td>
				<td>
					<a href='?page=getResult&question=<?=$question['id']?>'>Results</a>
				</td>
				<td>
					<a href='?page=deleteQuestion&id=<?=$question["id"]?>'>delete</a>
				</td>
			</tr>
		
		<?php
		}
		?>
		</tbody>
	</table>
	<?php
}

$db->close();
?>
<a href='?page=index'>Go back to the index</a>
<a href='?page=newQuestion&task=<?=$_GET['id']?>'>Add a question to this task</a>