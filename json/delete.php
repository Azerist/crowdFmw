<?php
//Check if the input is correct
if(!isset($input->delete->id) || !isset($input->delete->type))
	error('Json query syntax incorrect',9,$db);

$delete = $input->delete;
$db->query("DELETE FROM $delete->type WHERE id=$delete->id") or dbErr($db);

$ans = new stdClass();
$ans->code = 0;
$ans->message = "row successfully deleted";
echo json_encode($ans,JSON_PRETTY_PRINT);