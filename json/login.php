<?php
//Check the input data
if(!isset($input->id,$input->password))
	error('id or password fields not found in the input data',3,$db);

//query the database
$query = $db->query("SELECT id,password FROM admin WHERE username='$input->id'") or dbErr($db);
$result = $query->fetch_assoc();

if($result == NULL || !password_verify($input->password,$result['password']))
	error('Incorrect login information.',3,$db);
?>