<?php

class birthdate{

	private $date;

	public function initDb($mysql){ #Initialize the database column, takes mysql server information as a stdClass as argument.

		$db = new mysqli($myslq->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
		if(!$db)
			exit('Database connection problem while initializing birthdate feature : '.$db->connect_error);

		$query = $db->query( 'ALTER TABLE worker ADD birthdate DATE DEFAULT NULL');
		if(!$query){
			$err = $db->error;
			$db->close();
			exit("could not create a column 'birthdate' in the table 'worker' : $err");
		}

		$db->close();
	}

	public function htmlForm($data = NULL){	#Writes the html code to be inserted in the registration or edit profile form.
		if($data != NULL)
			$value = $data['birthdate'];
		else
			$value = '';
		?>
		Birthdate : <input type="date" name="birthdate" value="<?=$value?>"/>
		<?php
	}

	public function getForm($form,$sql){
		#Gets the html registration form result in $form, edit first part of sql query (INSERT INTO worker(...)) in $sql->sql1 and the second part (VALUES (...)) in $sql->sql2
		#Also returns $sql->ok as TRUE or FALSE, and can set an error message in $sql->err

		if(!isset($form['birthdate']) || $form['birthdate'] == ''){
			$sql->ok = FALSE;
			$sql->err = "You must enter your birthdate.";
		}
		else{
			$date = $form['birthdate'];
			$sql->sql1 = $sql->sql1.',birthdate';
			$sql->sql2 = $sql->sql2.',"'.$date.'"';
			$sql->ok = TRUE;
		}
		return $sql;

	}

	public function getProfileForm($form,$sql){
		//Same as getForm, but generate an update query for the update profile form.
		if(!isset($form['birthdate']) || $form['birthdate'] == ''){
			$sql->ok = FALSE;
			$sql->err = "You must enter your birthdate.";
		}
		else{
			$date = $post['birthdate'];
			$sql->sql = $sql->sql.',birthdate='.$date;
			$sql->ok = TRUE;
		}
		return $sql;
	}

	public function assignmentForm(){
		//Generates the html form to chose attribution criteria
		?>
		Minimum age : <input type="text" name="minAge"/><br/>
		Maximum age : <input type="text" name="maxAge"/><br/>
		Integer values; let empty to ignore a criteria.
		<?php
	}

	public function getAssignmentForm($form,$sql){
		//Treats the data from the attribution form and generates a sql condition
		$min = intval($form['minAge']) * 365;
		$max = intval($form['maxAge']) * 365;

		if($min != 0)
			$sql->sql = $sql->sql."AND DATEDIFF(NOW(),birthdate)>=$min";

		if($max != 0)
			$sql->sql = $sql->sql."AND DATEDIFF(NOW(),birthdate)<=$max";		

		$sql->ok = TRUE;

		return $sql;
	}
} 
