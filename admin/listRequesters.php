<?php
//Connect to database.

$query = $db->query("SELECT name,id FROM requester") or dbErr($db);

echo  "<ul>\n";

while($requester = $query->fetch_assoc())
	echo "<li><a href='page=viewRequester&id=$requester[id]'>$requester[name]</a></li>\n";

echo "</ul>\n";