<h2>Initialize a new feature</h2>
<?php

if(!isset($_GET['feature']))
	exit('No feature name provided');

if(!file_exists("features/$_GET[feature].php"))
	exit("the file 'features/$_GET[feature].php' was not found.")

include "features/$_GET[feature].php";

$feat = new $_GET['feature']();

$feat->initDb($mysql);

?>

<p>Feature <?=$_GET['feature']?> correctly initialized.</p>