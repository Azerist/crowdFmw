<?php
//Check if id is provided.
if(!isset($_GET['id']))
	exit('Question id missing');

//Connect to database.
$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
if(!$db)
	exit('Error while connecting to the database :<br/>'.$db->connect_error);

$query = $db->query("SELECT name FROM requester WHERE id=$GET[id]");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}
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
$query = $db->query("SELECT * FROM task WHERE id_requester=$_GET[id]");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}

while($task = $query->fetch_assoc())
	echo "<tr>\n<td><a href='?page=viewTask&id=$task[name]</td>\n<td>$task[description]</td>\n";

$db->close();
?>
	</tbody>
</table>