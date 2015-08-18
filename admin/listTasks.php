<h2>List of your tasks</h2>
<?php

$query = $db->query("SELECT * FROM task,requester WHERE id_requester=requester.id") or dbErr($db);
?>
<table border='1'>
	<thead>
		<tr>
			<th>Task name</th>
			<th>Task description</th>
			<th>requester</th>
			<th>Task status</th>
			<th>Contributions/target</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>
		<?php
		while($result = $query->fetch_assoc()){
		?>
		<tr>
			<td><a href='?page=viewTask&id=<?=$result['id']?>'><?=$result['name']?></a></td>
			<td><?=$result['description']?></td>
			<td><a href='?page=viewRequester&id=<?=$result['id_requester']?>'><?=$result['username']?></a></td>
			<td>
				<?php
				if($result['status'] == 'waiting')
					echo "<a href='?page=assign&id=$result[id]' title='assign task'>waiting</a>";
				else
					echo $result['status'];
				?>
			</td>
			<td><?="$result[current]/$result[target]"?></td>
			<td><a href='?page=deleteTask&id=<?=$result['id']?>'>Delete</a></td> 
		</tr>
		<?php
		}
		?>
	</tbody>
</table>