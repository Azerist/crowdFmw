<?php
//Check if id is provided.
if(!isset($_GET['id']))
	error('Question id missing',$db);

$query = $db->query("SELECT name FROM requester WHERE id=$GET[id]") or dbErr();

$requester = $query->fetch_assoc();
?>

<h2>Tasks of requester : <?=$requester['name'];?></h2>

<table>
	<thead>
		<tr>
			<th>Task name</th>
			<th>Task description</th>
		</tr>
	</thead>
	<tbody>

<?php
//get the tasks linked to the requester
$query = $db->query("SELECT * FROM task WHERE id_requester=$_GET[id]") or dbErr();

while($task = $query->fetch_assoc())
	echo "<tr>\n<td><a href='?page=viewTask&id=$task[name]</td>\n<td>$task[description]</td>\n";

?>
	</tbody>
</table>