<h2>Question resuts</h2>
<?php
//check if a question id is provided
if(!isset($_GET['question']))
	exit('No question id is provided !');

//Connect to the database
$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
if(!$db)
	exit('Error while connecting to the database :<br/>'.$db->connect_error);

//Get the question information
$query = $db->query("SELECT * FROM question WHERE id=$_GET[question]");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}
$question = $query->fetch_assoc();

if(!$question)
	exit('Error : No question was found with the provided id');
?>
<h3>Results of question : <?=$question['name']?></h3>
<p>Target number of contributions : <?=$question['target']?><br/>
<?php
//get the current number of contributions
$query = $db->query("SELECT count(*) AS total FROM contribution WHERE id_question=$question[id]");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}
$total = $query->fetch_assoc()['total'];
?>
Current number of contributions : <?=$total?></p>
<?php
//get the answers linked to the question
$query = $db->query("SELECT * FROM answer WHERE id_question=$question[id]");
if(!$query){
	$err = $db->error;
	$db->close();
	exit('Database error : '.$err);
}
?>
<table>
	<thead>
		<tr>
			<th>Answer</th>
			<th>Number</th>
			<th>Percentage</th>
		</tr>
	</thead>
	<tbody>
		<?php
		while($ans = $query->fetch_assoc()){
			echo "<tr>\n<td>$ans[answer]</td>\n";
			$query2 = $db->query("SELECT count(*) FROM contribution WHERE id_answer=$ans[id]");
			if(!$query2){
				$err = $db->error;
				$db->close();
				exit('Database error : '.$err);
			}
			$number = $query2->fetch_assoc()['count(*)'];
			$percent = $number / $total * 100;
			echo "<td>$number</td>\n<td>$percent %</td>\n</tr>";
		}
		$db->close();
		?>
	</tbody>
</table>