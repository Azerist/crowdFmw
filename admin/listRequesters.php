<h2>List of the platform requesters</h2>
<?php
//Connect to database.

$query = $db->query("SELECT username,id FROM requester") or dbErr($db);

echo  "<ul>\n";

while($requester = $query->fetch_assoc())
	echo "<li><a href='?page=viewRequester&id=$requester[id]'>$requester[username]</a></li>\n";

echo "</ul>\n";