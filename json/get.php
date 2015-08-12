<?php
//Check if the input is correct
if(!isset($input->request->type))
	error('Json query syntax incorrect',9,$db);

//Build a select query
$sql = "SELECT ";

//If fields are specified in the request, add them to the query
if(isset($input->request->fields) && $input->request->type != "contribution"){
	foreach ($input->request->fields as $field) {
		if(is_string($field) && $field!="false")
			$sql = $sql.$field.",";
	}
	if($sql == "SELECT ")
		$sql = $sql."*";
	elseif(!strpos('id',$sql))
		$sql = $sql.'id';	//Always take the id field for script purposes
	else
		$sql = rtrim($sql,',');	//Remove the last (and excedentary) coma.
}
else
	$sql = $sql."*";

$sql = $sql." FROM ".$input->request->type;

//if filters are specified, add them to the query
if(isset($input->request->filters)){
	$sql = $sql." WHERE 1=1";
	foreach ($input->request->filters as $filter) {
		if(is_string($filter))
			$sql = $sql.' AND '.$filter;
	}
}

print_r($input);
//execute the query
$query = $db->query($sql) or dbErr($db);

$memory = memory_get_usage();

//Build the answer
while($row = $query->fetch_assoc()){
	//======================================================================================================================
	//For tasks, add the questions linked to it if not denied in the request
	if($input->request->type == 'task' && (!isset($input->request->fields->questions) || $input->request->fields->questions!="false")){ 
		$sql = "SELECT "; //As previously, build a select request
		if(isset($input->request->fields->questions)){
			foreach ($input->request->fields->questions as $field) {
				if(is_string($field) && $field!="false")
					$sql = $sql.$field.",";
			}
			if($sql == "SELECT ")
				$sql = $sql."*";
			elseif(!strpos('id',$sql))
				$sql = $sql.'id';	//Always take the id field for script purposes
			else
				$sql = rtrim($sql,',');	//Remove the last (and excedentary) coma.
		}
		else
			$sql = $sql."id,question,input,inputType";
		$sql = $sql." FROM question";
		$sql = $sql." WHERE id_task=$row[id]";
		if(isset($input->request->filters)){
			
			foreach ($input->request->filters->questions as $filter) {
				if(is_string($filter))
					$sql = $sql.' AND '.$filter;
			}
		}
		$query2 = $db->query($sql) or dbErr($db);
		while($subrow = $query2->fetch_assoc()){ //If not denied in the request, add the answers
			if(!isset($input->request->fields->questions->answers) || $input->request->fields->questions->answers!="false"){
				$query3 = $db->query("SELECT id,answer FROM answer WHERE id_question=$subrow[id]") or dbErr($db);
				while($subsubrow = $query3->fetch_assoc())
					$subrow['answers'][] = $subsubrow;
				unset($query3,$subsubrow);
			}
			$row['questions'][]=$subrow;
		}
		unset($query2,$subrow);
	}
	//=========================================================================================================================
	//For questions, add the answers linked to it
	elseif($input->request->type == 'question' && (!isset($input->request->fields->answers) || $input->request->fields->answers!="false")){
		$query2 = $db->query("SELECT id,answer FROM answer WHERE id_question=$row[id]") or dbErr($db);
		while($subrow = $query2->fetch_assoc())
			$row['answers'][] = $subrow;
		unset($query2,$subrow);
	}
	//==========================================================================================================================================
	//For contributions, add the question, the answer chosen and the worker
	elseif($input->request->type == 'contribution'){
		if(!isset($input->request->fields->answer) || $input->request->fields->answer != "false"){
			$sql = "SELECT ";
			if(isset($input->request->fields->answer)){
				foreach ($input->request->fields->answer as $field) {
					if(is_string($field))
						$sql = $sql.$field.",";
				}
				if($sql == "SELECT ")
					$sql = $sql."*";
				else
					$sql = rtrim($sql,',');	//Remove the last (and excedentary) coma.
			}
			else
				$sql = $sql."*";
			$sql = $sql." FROM answer WHERE id=$row[id_answer]";
			$query2 = $db->query($sql) or dbErr($db);
			$row['answer'] = $query2->fetch_assoc();
			unset($row['id_answer']);
		}
		if(!isset($input->request->fields->question) || $input->request->fields->question != "false"){
			$sql = "SELECT ";
			if(isset($input->request->fields->question)){
				foreach ($input->request->fields->question as $field) {
					if(is_string($field))
						$sql = $sql.$field.",";
				}
				if($sql == "SELECT ")
					$sql = $sql."*";
				else
					$sql = rtrim($sql,',');	//Remove the last (and excedentary) coma.
			}
			else
				$sql = $sql."*";
			$sql = $sql." FROM question WHERE id=$row[id_question]";
			$query2 = $db->query($sql) or dbErr($db);
			$row['question'] = $query2->fetch_assoc();
			unset($row['id_question']);
		}
		if(!isset($input->request->fields->worker) || $input->request->fields->worker != "false"){
			$sql = "SELECT ";
			if(isset($input->request->fields->worker)){
				foreach ($input->request->fields->worker as $field) {
					if(is_string($field))
						$sql = $sql.$field.",";
				}
				if($sql == "SELECT ")
					$sql = $sql."*";
				else
					$sql = rtrim($sql,',');	//Remove the last (and excedentary) coma.
			}
			else
				$sql = $sql."*";
			$sql = $sql." FROM worker WHERE id=$row[id_worker]";
			$query2 = $db->query($sql) or dbErr($db);
			$row['worker'] = $query2->fetch_assoc();
			unset($row['id_worker']);
		}
		unset($query2);
	}
	//===================================================================================================================
	$result[] = $row;
}

//Check the return size
$max = 10000000;
if(isset($input->request->max_size)  && is_numeric($input->request->max_size) && $input->request->max_size > 0)
	$max = $input->request->max_size;

if(memory_get_usage() - $memory > $max)
	error('Return size greater than specified maximum.',10,$db);

$ans = new stdClass();
$ans->code = 0;
$ans->result = $result;
echo json_encode($ans,JSON_PRETTY_PRINT);