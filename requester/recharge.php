<h2>Recharge your account</h2>
<?php
	//if the form has been submitted, treat the data
	if(isset($_POST['recharge'])){
		if(!is_numeric($_POST['recharge']))
			error('Please enter a numeric value!',$db);

		$db->query("UPDATE requester SET balance=balance+$_POST[recharge] WHERE id=$_SESSION[userid]") or dbErr($db);
	}

	$query = $db->query("SELECT balance FROM requester WHERE id=$_SESSION[userid]") or dbErr($db);
	$balance = $query->fetch_assoc()['balance'];
?>
<h2>current balance : <b id='initBalance'><?=$balance?></b><br/>
Balance after reload : <b id='balance'><?=$balance?></b></h2>
<form action="" method="post" accept-charset="utf-8" onkeyup="addVal(this)">
	Amount to add to your account : <input type="text" name="recharge" placeholder="numeric value"/><br/>
	<input type="submit"/>
</form>
<script>
	function addVal(form){
		input = form.children[0];
		submit = form.children[2];
		val = Number(input.value);
		if(!isNaN(val)){
			document.getElementById('balance').innerHTML = (Number(document.getElementById('initBalance').innerHTML) + val).toFixed(2);
			input.style.color = '';
			submit.disabled = false;
		}
		else{
			document.getElementById('balance').innerHTML = document.getElementById('initBalance').innerHTML;
			input.style.color = 'red';
			submit.disabled = true;
		}
	}
</script>
