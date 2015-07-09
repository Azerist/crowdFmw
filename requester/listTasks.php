<h2>List of your tasks</h2>
<?php
$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
if(!$db)
	exit('Error while connecting to the database :<br/>'.$db->connect_error);

$query = $db->query('SELECT * FROM task WHERE id_requester='.$_SESSION['userid']);

if(!$query){
	$err = $db->error;
	$sb->close();
	exit('Database error : '.$err);
}

$db->close();
?>
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