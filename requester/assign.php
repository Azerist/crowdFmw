<h2>assign a question to users</h2>
<?php
if(!isset($_GET['id']))
	exit('Question id missing');

$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
if(!$db)
	exit('Error while connecting to the database :<br/>'.$db->connect_error);
//===============================================================================================================================
//if the form has been submitted, create the assignments
if(isset($_POST['submit'])){
	$sql = new stdClass();
	$sql->sql = "INSERT INTO assignment(id_worker,id_question) SELECT id,$_GET[id] FROM worker WHERE 1=1 "; //1=1 is needed because the features add the AND keyword
	$sql->ok = TRUE;
	$features = scandir("features");
	foreach ($features as $filename) {
		if(substr($filename,-4) == ".php"){
			include 'features/'.$filename;
			$class = str_replace('.php', '', $filename);
			$feat = new $class();
			$feat->getAssignmentForm($_POST,$sql);
			if(!$sql->ok)
				exit($sql->err);
		}	
	}
	$query = $db->query($sql->sql);
	if(!$query){
		$err = $db->error;
		$db->close();
		exit('Database error : '.$err);
	}
	$count = $db->affected_rows;

	$query = $db->query("UPDATE question SET status='assigned' WHERE id=$_GET[id]");
	$db->close();
	exit("Question successfully assigned to $count workers");
}
//===============================================================================================================================
//if the form has not been submitted, check if the question is waiting for assignment…
$query = $db->query('SELECT task.id_requester FROM task,question WHERE question.id_task=task.id AND question.id='.$_GET['id']);
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}
$result = $query->fetch_assoc();
if($result == NULL || $result['id_requester'] != $_SESSION['userid'])
	exit('Error : You are not owner of this question !');

$query = $db->query("SELECT status FROM question WHERE id=$_GET[id]");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}

if($query->fetch_assoc()['status'] != 'waiting'){
	$db->close();
	exit('this question is not waiting for assignment...');
}

$db->close();

//…then generate the form
?>
<form method="post">
	<h3>Filter criteria availables :</h3>
	<table border='1'>
		<thead>
			<tr>
				<th>Feature name</td>
				<th>Filter form</td>
			</tr>
		</thead>
		<tbody>
		<?php
		$features = scandir("features");
		foreach ($features as $filename) {
			if(substr($filename,-4) == ".php"){
				include 'features/'.$filename;
				$class = str_replace('.php', '', $filename);
				$feat = new $class();
				?>
				<tr>
					<td><?=$class?></td>
					<td><?=$feat->assignmentForm()?></td>
				</tr>
				<?php
			}	
		}
		?>
		</tbody>
	</table>
	<input type="submit" name="submit"/>
</form>