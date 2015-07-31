<h2>Add a question to a task</h2>

<?php
//if the form has been submitted, treat the data
if(isset($_POST['question'])){
	if($_POST['question'] == '' || $_POST['answers'] == '')//If essential data is missing
		echo 'please fill the form !';
	else{
		//Load the inputType class 
		if(!include("inputTypes/$_POST[inputType].php"))
			exit("FATAL ERROR : could not find file 'inputTypes/$_POST[inputType].php'");
		
		//if the inputType is null, use an empty file name
		if($_POST['inputType'] == 'none')
			$file = '';
		else
			$file = 'questionFiles/'.$_FILES['inputFile']['name'];

		//Check if there exists a file with the same name, and rename it if necessary
		$numb = 1;
		while(file_exists($file)){
			$file = "questionFiles/$numb-$_FILES[inputFile][name]";
			$numb++;
		}
		
		//create an instance of the inputType class
		$type = new $_POST['inputType']($file);

		if(!$type->checkType())//check the file extention
			echo 'The uploaded file extension do not match the chosen input type';
		else{
			//if correct, move the file to the questionFiles directory
			if($_POST['inputType'] != "none")
				if(!move_uploaded_file($_FILES['inputFile']['tmp_name'], $file))
					error('Fatal error : could not move the uploaded file.');

			//escape the quote character to prevent sql syntax error
			$question = str_replace("'","''",$_POST['question']);			
			$file = str_replace("'","''",$file);

			//prepare the query to insert the new question in the database
			$sql = "INSERT INTO question(question,inputType,id_task,input)
						VALUES ('$question','$_POST[inputType]',$_POST[taskid],'$file')";

			//execute it		
			$query = $db->query($sql) or dbErr($db);
		
			//Treat the question answers
			//explode the form string to get the separate answers
			$answers = explode(';',str_replace(array('\r','\n'),'',$_POST['answers']));

			//get the id of the inserted question
			$id = $db->insert_id;

			//insert each answer in the database
			foreach ($answers as $ans) {
				$ans = str_replace("'","''",$ans); //escape quotes
				//insert it
				$query = $db->query("INSERT INTO answer(answer,id_question) VALUES ('$ans',$id);") or dbErr($db);
			}
			?>
			<p>Question successfully added</p>
			<a href=''>Add a new question</a>
			<?php
			$db->close();
			exit();
		}

	}
}
?>

<form method='post' enctype="multipart/form-data">
	<?php
	//Take the list of the user's tasks from the db and generate a choice list in the form

	$query = $db->query('SELECT id,name FROM task') or dbErr($db);


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
			//generate a choice list with the available inputTypes
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
	Question possible answers, separated by ";" :<br/>
	<textarea name="answers" rows='4' cols='50'></textarea><br/>
	<input type="submit"/>
</form>