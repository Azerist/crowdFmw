<?php
//Connect to database.
$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
if(!$db)
	exit('Error while connecting to the database :<br/>'.$db->connect_error);

$query = $db->query("SELECT name,id FROM requester");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}

echo  "<ul>\n";

while($requester = $query->fetch_assoc())
	echo "<li><a href='page=viewRequester&id=$requester[id]'>$requester[name]</a></li>\n";

echo "</ul>\n";

$db->close();