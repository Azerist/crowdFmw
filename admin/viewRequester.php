<?php
//Check if id is provided.
if(!isset($_GET['id']))
	error('Requester id missing',$db);

//==================================================================================================================================================
//if the form has been submitted, treat the data
if(isset($_POST['username']) && $_POST['username'] != ''){
	$db->query("UPDATE requester SET username='$_POST[username]' WHERE id=$_GET[id]") or dbErr($db);
	echo "<p>Username successfully updated.</p>";

	if(isset($_POST['password']) && $_POST['password'] != '' && $_POST['password']==$_POST['password2']){
		$db->query("UPDATE requester SET password='".password_hash($_POST['password'], PASSWORD_DEFAULT)."' WHERE id=$_GET[id]") or dbErr($db);
		echo "<p>password correctly changed.</p>";
	}
	else
		echo "<p>No change has been made to the requester's password.</p>";

	if(isset($_POST['balance']) && $_POST['balance']!='' && is_numeric($_POST['balance'])){
		$db->query("UPDATE requester SET balance=$_POST[balance] WHERE id=$_GET[id]") or dbErr($db);
		echo "<p>Balance updated successfully.</p>";
	}
	else {
		echo "<p>No changes have been made to the requester's balance.</p>";
	}
}
//=================================================================================================================================================
//Display the requester information
$query = $db->query("SELECT * FROM requester WHERE id=$_GET[id]") or dbErr($db);

$requester = $query->fetch_assoc();
?>

<h2>Details of requester : <?=$requester['username'];?></h2>

<form action="" method="post" accept-charset="utf-8">
	username : <input type="text" name="username" value="<?=$requester['username']?>" maxlenght='16'/><br/>
	Password : <input type="text" name="password" placeholder="Enter new password"/><input type="text" name="password2" placeholder="Confirm new password"/><br/>
	Balance : <input type="text" value="<?=$requester['balance']?>" name='balance'/><br/>
	Warning : as an admin, you won't be warned if you set a balance lower than needed for the requester's current tasks. It is therefore advised to only increase
	a requester's balance, except if you're sure of yourself.
	<input type="submit"/>
</form>

<h2>Requester's tasks :</h2>
<?php
//get the tasks linked to the requester
$query = $db->query("SELECT * FROM task WHERE id_requester=$_GET[id]") or dbErr();

if($query->num_rows == 0)
	echo "<p>No tasks found</p>";
else{
?>
<p>
<table>
	<thead>
		<tr>
			<th>Task name</th>
			<th>Task description</th>
			<th>Status</th>
			<th>Contributions/target</th>
			<th>Reward</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>

<?php

while($task = $query->fetch_assoc()){
	echo "<tr>\n<td><a href='?page=viewTask&id=$task[id]'>$task[name]</a></td>\n<td>$task[description]</td>\n<td>";
	if($task['status'] == 'waiting')
		echo "<a href='?page=assign&id=$result[id]' title='assign task'>waiting</a>";
	else
		echo $task['status'];
	echo "</td>\n<td>$task[current]/$task[target]</td>\n<td>$task[reward]</td>\n<td><a href='?page=deleteTask&id=$task[id]'>Delete</a></td>\n
				</tr>\n";
}
?>
	</tbody>
</table>
</p>
<?php
}
?>
