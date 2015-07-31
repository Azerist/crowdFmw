<h2>List of tasks waiting for your answer </h2>

<?php

$query = $db->query('SELECT task.id, task.name, task.description FROM task,assignment 
						WHERE task.id=assignment.id_task AND assignment.id_worker='.$_SESSION['userid'])
		or dbErr($db);

if($query->num_rows != 0){
	echo "<h3>tasks attributed to you :</h3><ul>";
	while($result = $query->fetch_assoc())
		echo "<li><a href='?page=viewTask&id=".$result['id']."'>".$result['name']."</a></li>";
	echo('</ul>');
}

//get available tasks where the user has already contributed.
$query = $db->query("SELECT id,name,description FROM task WHERE status='open' 
						AND id NOT IN (
						SELECT id_task FROM contribution,question WHERE contribution.id_question=question.id AND id_worker=$_SESSION[userid]
						)")
		or dbErr($db);

if($query->num_rows != 0){
	?>
	<h3>Open tasks :</h3>
	<table border='1'>
		<thead>
			<tr>
				<th>Task name</th>
				<th>Task description</th>
			</tr>
		</thead>
		<tbody>
		<?php
	while($result = $query->fetch_assoc()){
		?>
		<tr>
			<td><a href='?page=viewTask&id=<?=$result['id']?>'><?=$result['name']?></a></td>
			<td><?=$result['description']?></td>
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