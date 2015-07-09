<?php

if(!isset($_GET['id']))
	exit('task id missing');

$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
if(!$db)
	exit('Error while connecting to the database :<br/>'.$db->connect_error);

if(isset($_POST['taskName'])){
	#TODO
}

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
?>
<h2>Details of task  <?=$task['name']?></h2>
<h3>task description :</h3>
<p><?=$task['description']?></p>
<h3>Use this form to edit any of the above :</h3>
<form method='post'>
	Task name : <input type="text" name="taskName"/><br/>
	<textarea name="description" rows="5" cols="50">Task description</textarea><br/>
	<input type="submit"/>
</form>

<?php
$query = $db->query('SELECT * FROM question WHERE id='.$task['id']);

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
				<th># of contrib.</th>
				<th>target # of contrib.</th>
			</tr>
		</thead>
		<tbody>
		<?php
		while($question = $query->fetch_assoc()){
			$query2 = $db->query('SELECT answer FROM answer WHERE id_question='.$question['id']);
			if(!$query){
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
					<?php echo $db->query('SELECT count(*) FROM contribution WHERE id_answer='.$question['id'])->fetch_assoc()['count(*)'];?>
				</td>
				<td>
					<?=$question['target']?>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
	}
}

$db->close();
?>