<h2>List of tasks waiting for your answer </h2>

<?php
//Get the tasks assigned to the user
$query = $db->query("SELECT task.id, task.name, task.description, reward FROM task,assignment
						WHERE task.id=assignment.id_task AND assignment.id_worker=$_SESSION[userid]
						AND task.id IN (SELECT DISTINCT id_task FROM question)") //Select only tasks that have questions…
		or dbErr($db);

if($query->num_rows != 0){
	?>
	<h3>Tasks assigned to you :</h3>
	<p>
	<table>
		<thead>
			<tr>
				<th>Task name</th>
				<th>Task description</th>
			 	<th>Reward</th>
			</tr>
		</thead>
		<tbody>
		<?php
	while($result = $query->fetch_assoc()){
		?>
		<tr>
			<td><a href='?page=viewTask&id=<?=$result['id']?>'><?=$result['name']?></a></td>
			<td><?=$result['description']?></td>
			<td><?=$result['reward']?></td>
		</tr>
		<?php
	}
	?>
		</tbody>
	</table>
</p>
	<?php
}

//get available tasks where the user has not already contributed.
$query = $db->query("SELECT id,name,description,reward FROM task WHERE status='open'
						AND id NOT IN (
						SELECT id_task FROM contribution,question WHERE contribution.id_question=question.id AND id_worker=$_SESSION[userid]
						)
						AND task.id IN (SELECT DISTINCT id_task FROM question)") //Select only tasks that have questions and to which the user has not already contributed
		or dbErr($db);

if($query->num_rows != 0){
	?>
	<h3>Open tasks :</h3>
	<p>
	<table>
		<thead>
			<tr>
				<th>Task name</th>
				<th>Task description</th>
				<th>Reward</th>
			</tr>
		</thead>
		<tbody>
		<?php
	while($result = $query->fetch_assoc()){
		?>
		<tr>
			<td><a href='?page=viewTask&id=<?=$result['id']?>'><?=$result['name']?></a></td>
			<td><?=$result['description']?></td>
			<td><?=$result['reward']?></td>
		</tr>
		<?php
	}
	?>
		</tbody>
	</table>
</p>
	<?php
}

$db->close();
?>
