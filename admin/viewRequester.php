<?php
//Check if id is provided.
if(!isset($_GET['id']))
	error('Requester id missing',$db);

//==================================================================================================================================================
//if the form has been submitted, treat the data
if(isset($_POST['username']) && $_POST['username'] != '')
	$db->query("UPDATE requester SET username='$_POST[username]' WHERE id=$_GET[id]") or dbErr($db);

if(isset($_POST['password']) && $_POST['username'] != '' && $_POST['password']==$_POST['password2']){
	$db->query("UPDATE requester SET password='".password_hash($_POST['password'], PASSWORD_DEFAULT)."' WHERE id=$_GET[id]") or dbErr($db);
	echo "<p>password correctly changed.</p>";
}
else
	echo "<p>The two passwords don't match.</p>";

//=================================================================================================================================================
//Display the requester information
$query = $db->query("SELECT * FROM requester WHERE id=$_GET[id]") or dbErr($db);

$requester = $query->fetch_assoc();
?>

<h2>Details of requester : <?=$requester['username'];?></h2>

<form action="" method="post" accept-charset="utf-8">
	username : <input type="text" name="username" value="<?=$requester['username']?>" maxlenght='16'/><br/>
	Password : <input type="text" name="password" placeholder="Enter new password"/><input type="text" name="password2" placeholder="Confirm new password"/><br/>
	<input type="submit"/>
</form>

<h2>Requester's tasks :</h2>
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