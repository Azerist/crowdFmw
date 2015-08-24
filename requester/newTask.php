<h2>Create a new task</h2>

<?php
//If the form has been submitted, insert the new task
if(isset($_POST['taskName']))
	if($_POST['taskName'] == '' && is_int($_POST['target']))
		echo 'Please enter a name for your task';

	//And if the user has specified a non-empty task name, and the target number is correct
	else{

		//Check if the requester's balance is enough to use the selected reward
		//Get the current balance
		$query = $db->query("SELECT balance FROM requester WHERE id=$_SESSION[userid]") or dbErr($db);
		$balance = $query->fetch_assoc()['balance'];
		//Compute the total cost of the requester's other tasks
		$query = $db->query("SELECT SUM((target-current)*reward) AS total FROM task WHERE id_requester=$_SESSION[userid] AND status!='completed'") or dbErr($db);
		$total = $query->fetch_assoc()['total'] ;
		$cost = $_POST['target']*$_POST['reward'];
		if($total + $cost > $balance)
			error("Insuficient balance : Your balance is $balance, this task costs $cost, and your other unfinished tasks cost $total",$db);
			
		//Create the two parts of the sql query
		$sql1 = "INSERT INTO task(name,id_requester,status,target,reward";
		$sql2 = "VALUES ('".str_replace("'","''",$_POST['taskName'])."',$_SESSION[userid],'$_POST[assignment]',$_POST[target],$_POST[reward]";

		if($_POST['status'] == 'waiting' && $_POST['extparams'] != ''){
			$sql1 = $sql1.',extparams';
			$sql2 = $sql2.",'$_POST[extparams]'";
		}

		//if a description is provided, add it to the query
		if($_POST['description'] != "" && $_POST['description'] != 'Task description'){
			$sql1 = $sql1.',description';
			$sql2 = $sql2.',"'.str_replace(array("\r\n","\r","\n"),'<br/>',$_POST['description']).'"';
		}

		//Complete the query and execute it
		$sql1 = $sql1.')';
		$sql2 = $sql2.')';

		$query = $db->query($sql1.$sql2) or dbErr($db);
		?>
		<p>Task successfully inserted into database.</p>
		<a href='?page=newQuestion'>Add a question</a><br/>
		<a href='?page=listTasks'>See your tasks list</a><br/>
		<a href='?page=index'>return to index</a>
		<hr/>

		<?php
	}
//if not, display the form
$query = $db->query("SELECT balance FROM requester WHERE id=$_SESSION[userid]") or dbErr($db);
$balance = $query->fetch_assoc()['balance'];
?>
<form method='post' onkeyup="addVal(this)">
	Task name : <input type="text" name="taskName" maxlength="64" /><br/>
	<textarea name="description" rows="5" cols="50" maxlenght="512">Task description</textarea><hr/>
	Assignment type : <select name="assignment">
		<option>open</option>
		<option>waiting</option>
	</select>
	if "waiting", you can specify here parameters for an external assignment algorithm : <input type="text" name="extparams" maxlength="128" /><br/>
	Target number of contributions : <input type="text" name="target"/> Integer value, -1 for manual or external algorithm.<br/>
	Individual reward for this task : <input type="text" name="reward"/>
	Total cost : <b id='total'>0</b> — Current balance : <b id='balance'><?=$balance?></b><br/>
	<input type="submit" name='submit'/>
</form>
<script>
	function addVal(form){
		submit = form.children[name='submit'];
		inputTarget = form.children[name='target'];
		inputReward = form.children[name='reward'];
		target = Number(inputTarget.value);
		reward = Number(inputReward.value);
		if(isNaN(reward)){
			document.getElementById('total').innerHTML = 0.00;
			inputReward.style.color = 'red';
			submit.disabled = true;
		}
		else if(isNaN(target)){
			document.getElementById('total').innerHTML = 0.00;
			inputTarget.style.color = 'red';
			submit.disabled = true;
		}
		else{
			document.getElementById('total').innerHTML = (target * reward).toFixed(2);
			inputTarget.style.color = '';
			inputReward.style.color = '';
			submit.disabled = false;
		}
	}
</script>
