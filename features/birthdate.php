<?php

class birthdate{

	private $date;

	public function initDb($mysql){ #Initialize the database column, takes mysql server information as a stdClass as argument.

		$db = new mysqli($myslq->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
		if(!$db)
			exit('Database connection problem while initializing birthdate feature : '.$db->connect_error());

		$query = $db->query( 'ALTER TABLE worker ADD birthdate DATE DEFAULT NULL');
		if(!$query){
			$err = $db->error;
			$db->close();
			exit("could not create a column 'birthdate' in the table 'worker' : ".$err);
		}

		$db->close();
	}

	public function htmlForm(){	#Writes the html code to be inserted in the registration form.
		?>
		Birthdate : <input type="date" name="birthdate"/>
		<?php
	}

	public function getForm($form,$sql){
		#Gets the html form result in $form, edit first part of sql query (INSERT INTO worker(...)) in $sql->sql1 and the second part (VALUES (...)) in $sql->sql2
		#Also returns $sql->ok as TRUE or FALSE, and can set an error message in $sql->err

		if(!isset($form['birthdate']) || $form['birthdate'] == ''){
			$sql->ok = FALSE;
			$sql->err = "You must enter your birthdate.";
		}
		else{
			$date = $post['birthdate'];
			$sql->sql1 = $sql->sql1.',birthdate';
			$sql->sql2 = $sql->sql2.',"'.$date.'"';
			$sql->ok = TRUE;
		}
		return $sql;

	}
} 
