<h2>assign a question to users</h2>
<?php
if(!isset($_GET['id']))
	error('task id missing',$db);

//===============================================================================================================================
//if the form has been submitted, create the assignments
if(isset($_POST['submit'])){
	$sql = new stdClass();
	$sql->sql = "INSERT INTO assignment(id_worker,id_task) SELECT id,$_GET[id] FROM worker WHERE 1=1 "; //1=1 is needed because the features add the AND keyword
	$sql->ok = TRUE;
	$features = scandir("features");
	foreach ($features as $filename) {
		if(substr($filename,-4) == ".php"){
			include 'features/'.$filename;
			$class = str_replace('.php', '', $filename);
			$feat = new $class();
			$feat->getAssignmentForm($_POST,$sql);
			if(!$sql->ok)
				error($sql->err,$db);
		}
	}
	$query = $db->query($sql->sql) or dbErr($db);

	$count = $db->affected_rows;
	if($count>0)
		$query = $db->query("UPDATE task SET status='assigned' WHERE id=$_GET[id]") or dbErr($db);
	$db->close();
	exit("<p>Question successfully assigned to $count workers</p>");
}
//===============================================================================================================================
//if the form has not been submitted, check if the question is waiting for assignment…
$query = $db->query("SELECT * FROM task WHERE id=$_GET[id]") or dbErr($db);

$result = $query->fetch_assoc();
if($result == NULL || $result['id_requester'] != $_SESSION['userid'])
	error('Error : You are not owner of this task !',$db);

if($result['status'] != 'waiting'){
	error('this task is not waiting for assignment...',$db);
}

//…then generate the form
?>
<h3>Filter criteria availables :</h3>
<form method="post">
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
