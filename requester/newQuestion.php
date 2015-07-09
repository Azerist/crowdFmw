<h2>Add a question to a task</h2>

<?php
if(isset($_POST['question'])){
	if($_POST['question'] == '' || $_POST['answers'] == '')
		echo 'please fill the form !';
	else{
		if(!include('inputTypes/'.$_POST['inputType'].'.php'))
			exit('FATAL ERROR : could not find file '.'inputTypes/'.$_POST['inputType'].'.php');
		
		if($_POST['inputType'] == 'none')
			$_FILES['inputFile']['name'] = '';
		
		$type = new $_POST['inputType']($_FILES['inputFile']['name']);

		if(!$type->checkType())
			echo 'The uploaded file extension do not match the chosen input type';
		else{

			if($_POST['inputType'] != "none")
				if(!move_uploaded_file($_FILES['inputFile']['tmp_name'], 'questionFiles/'.$_FILES['inputFile']['name']))
					exit('Fatal error : could not move the uploaded file.');


			$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
			if(!$db)
				exit('Error while connecting to the database :<br/>'.$db->connect_error);

			$sql1 = "INSERT INTO question(question,inputType,id_task,status,target";
			$question = str_replace(array(';','"'),array(',',"'"),$_POST['question']);
			$sql2 = 'VALUES ("'.$question.'","'.$_POST['inputType'].'",'.$_POST['taskid'].',"'.$_POST['assignment'].'",'.$_POST['target'];

			if($_POST['inputType']!="none"){
				$sql1 = $sql1.',input';
				$sql2 = $sql2.',"'.'questionFiles/'.$_FILES['inputFile']['name'].'"';
			}

			$sql1 = $sql1.')';
			$sql2 = $sql2.')';
		
			$query = $db->query($sql1.$sql2);
			if(!$query){
				$err = $db->error;
				$db->close();
				exit('Database error : '.$err);
			}

			$answers = explode(';',str_replace(array('\r','\n'),'',$_POST['answers']));
			$id = $db->insert_id;

			foreach ($answers as $ans) {
				$ans = str_replace('"',"'",$ans);
				$query = $db->query('INSERT INTO answer(answer,id_question) VALUES ("'.$ans.'",'.$id.');');
				if(!$query){
					$err = $db->error;
					$db->close();
					exit('Database error : '.$err);
				}
			}
			?>
			<p>Question successfully added</p>
			<a href=''>Add a new question</a>
			<a href='?page=index'>Return to the index</a>
			<?php
			exit();
		}

	}
}
?>

<form method='post' enctype="multipart/form-data">
	<?php
	//Take the list of the user's tasks from the db and generate a choice list in the form
	$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
	if(!$db)
		exit('Error while connecting to the database :<br/>'.$db->connect_error);

	$query = $db->query('SELECT id,name FROM task WHERE id_requester='.$_SESSION['userid']);

	if(!$query){
		$err = $db->error;
		$db->close();
		exit('Database error : '.$err);
	}

	if($query->num_rows == 0)
		exit('No tasks linked to your account were found. <a href="?page=newTask">Create a task</a>');

	echo "Choose the task linked to this question : <select name='taskid'>";
	while($result = $query->fetch_assoc()){
		echo "<option value='".$result['id']."'";
		if(isset($_GET['task']) && $_GET['task'] == $result['id'])
			echo ' selected';
		echo ">".$result['name']."</option>";
	}
	?>
	</select>
	<br/>
	Question : <textarea name="question" cols="50" rows="1"></textarea><br/>
	<br/>
	Input file type : <select name='inputType'><option selected>none</option>
		<?php
			$types = scandir("inputTypes");
			foreach ($types as $filename) {
				if(substr($filename,-4) == ".php"){
					$class = str_replace('.php', '', $filename);
					if($class != "none")
						echo '<option>'.$class.'</option>';
				}	
			}
		?>
	</select><br/>
	Input file : <input type="file" name="inputFile"/><br/>
	<br/>
	Assignment type : <select name="assignment">
		<option>open</option>
		<option>waiting</option>
	</select>
	 if "waiting", you can specify here parameters for an external assignment algorithm : <input type="text" name="extparams"/>
	 <br/>
	Question possible answers, separated by ";" :<br/>
	<textarea name="answers" rows='4' cols='50'></textarea><br/>
	Target number of contributions : <input type="text" name="target"/> Integer value, -1 for manual or external algorithm.<br/>
	<input type="submit"/>
</form>