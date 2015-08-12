<?php

//Check if the input is correct
if(!isset($input->request))
	error('Json query syntax incorrect',9,$db);

$request = $input->request;

if(!isset($request->type))
	error('Json query syntax incorrect : No type specified in the request',9,$db);

//If id is set, update the existing record
if(isset($request->id)){
	$sql = "UPDATE $request->type SET ";
	foreach ($request as $key => $value) {
		if($key != 'type' && $key != 'id')
			$sql = $sql."$key='$value',";
	}
	$sql = rtrim($sql,',');	//Remove the last (and excedentary) coma.

	$sql = $sql." WHERE id=$request->id";

	$query = $db->query($sql) or dbErr($db);
	$ans = new stdClass();
	$ans->code = 0;
	$ans->message = 'Row edition successful';
	echo json_encode($ans,JSON_PRETTY_PRINT);

}
//else, insert new ones
else{
	if(!isset($request->values) || !is_array($request->values))
		error('Json query syntax incorrect',9,$db);

	$sql = "INSERT INTO $request->type (";
	
	//Initialize the query with the first object to insert
	foreach ($request->values[0] as $key => $value) {
		$sql = $sql."$key,";
		$keys[] = $key;
	}
	$sql = rtrim($sql,',').") ";	//Remove the last (and excedentary) coma.
	$sql = $sql."VALUES (";

	//Add all values fom the request to the query
	foreach ($request->values as $value) {
		foreach($keys as $key)
			if(isset($value->$key))
				$sql = $sql."'".$value->$key."',";
			else
				$sql = $sql."'',";
		$sql = rtrim($sql,',')."),( ";	//Remove the last (and excedentary) coma.
	}
	
	$sql = rtrim($sql,',( ');	//Remove the last (and excedentary) coma and parenthesis.
	
	$query = $db->query($sql) or dbErr($db);
	
	$ans = new stdClass();
	$ans->code = 0;
	$ans->message = "successfully inserted ".sizeof($request->values)." rows.";
	echo json_encode($ans,JSON_PRETTY_PRINT);
}