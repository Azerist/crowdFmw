<h2>Initialize a new feature</h2>
<?php

if(isset($_POST['feature'])){

	if(!include("features/$_POST[feature].php")
		error("the file 'features/$_POST[feature].php' was not found.",$db);

	$feat = new $_POST['feature']();

	$feat->initDb($mysql);

	?>

	<p>Feature <?=$_POST['feature']?> correctly initialized.</p>

	<?php
}
?>

<p>To add a worker feature to the platform :<br/>
- Create a new class in the 'features' directory, matching perfectly the template<br/>
- Enter the filename below, without '.php'<br/>
- The class name and the filename must be the same : for instance, the class 'birthdate' is in the 'birthdate.php' file.</p>

<form action="" method="post" accept-charset="utf-8">
	<input type="text" name="feature" placeholder="Class name"/>
	<input type="submit"/>
</form>