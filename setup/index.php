<html>
	<head>
		<meta charset='utf-8'/>
		<title>Crowdsourcing framework setup</title>
	</head>

	<body>
		<h1>Crowdsourcing framework setup</h1>

		<?php
			//this script has several steps, each one reading and acting on the information given by the user in the previous step, and then asking further information.
			function error($err,$step){	//Error function. Displays $err and a link to retry from the step $step and stops the script.
				?>
					<h2>Error while executing the script :</h2><br/>
					<p color='red'><?=$err?></p>
					<a href='.?step=<?=$step?>'>Try again</a>
				<?php
				exit;
			}

			//=====================================================================================================================================================
			if(!isset($_GET['step']) || $_GET['step'] == 'init'){	#Initial script call : ask for basic information
				if(file_exists("../.fmwName") && file_exists('../.mysqlInfo'))
					error("This framework has already be configured. If you are this server's administrator and want to take the config script again, 
							please remove the '.fmwName' and '.mysqlInfo' files in the main framework directory.",'init');
				?>

					<h2>Initial framework setup</h2>
					<p>Lets now configure your crowdsourcing framework in a few short steps. <br/>Please avoid using the 'previous' button during this setup.</p>
					<form action=".?step=db&fileExists=False" method="post" accept-charset="utf-8">
						Please chose a name for this platform :
						<input type="text" name="name" value="Crowdsourcing platform"/>
						<br/>
						<input type="submit"/>
					</form>

				<?php
			}

			//=====================================================================================================================================================
			elseif ($_GET['step'] == 'db') {	#Database setup
				
				if(!isset($_GET['fileExists']) || $_GET['fileExists'] != 'True'){

					//Create a '.fmwName' file in the main folder to store the platform name.
					if(!isset($_POST['name']))
						error('Error while parsing the entered name. Please check the name you entered and try again.','init',$db);

					$file = fopen("../.fmwName", 'w');
					if(!$file){
						$err = 'Error while creating a file containing the framework name.<br/> 
								Please check the directory access rights.';
						error($err,'init');
					}

					fwrite($file,$_POST['name'],strlen($_POST['name']));
					fclose($file);

				}

				elseif (!file_exists('../.fmwName')) {
					error('Could not find the file ".fmwName" in the main directory.','init',$db);
				}

				
				//Ask the user which way he wants to initialize the database.
				?>

				<h2>Database setup</h2>
				<p>Please choose how you want to initialize the database :</p>
				<form action="" method="get" accept-charset="utf-8">
					<input type='hidden' name='step' value='db2'/>
					<input type='radio' name='mode' value="auto"/>Automatic mode : requires the mysql admin password. Due to mysql server limitations, this will only work on local mysql servers.<br/>
					<input type='radio' name='mode' value="manual"/>Manual mode : create a user yourself and use it here, or use an existing user.<br/>
					<input type="submit"/>
				</form>

				<?php
			}

			//=====================================================================================================================================================
			elseif($_GET['step'] == 'db2'){

				if(!isset($_GET['mode']) || !in_array($_GET['mode'],array('auto','manual')))
					error('the data sent by your browser is incorrect.','db&fileExists=True',$db);

				//Ask the user the required information to configure the database, based on the mode he chose.
				if($_GET['mode'] == 'auto'){
					?>

					<h2>Database setup : Automatic mode</h2>
					<form action=".?step=db3&mode=auto" method="post" accept-charset="utf-8">
						<input type="hidden" name="address" value="localhost"/><input type="hidden" name="port" value="3306"/>
						Mysql server admin account : <input type="text" name="adminId" value="root" placeholder='login'/> <input type="password" name="adminPw" placeholder='password'/><br/>
						Create a mysql account for the platform : <input type="text" name="mysqlId" value="crowdFmw" placeholder='login'/> <input type="password" name="mysqlPw" placeholder='password'/> <input type="password" name="mysqlPw2" placeholder='confirm password'/><br/>
						<font color='red'>WARNING : The username must not be already used on the mysql server. Use manual mode <a href='.?step=db&fileExists=True'>here</a> to use an existing user.</font><br/>
						Choose a database name : <input type="text" name="dbName" value="crowdFmw"/><br/>
						Please use a database name that does not already exist on the mysql server.<br/>
						<input type="submit"/>
						<a href='.?step=db&fileExists=True'>Return</a>
					</form>

					<?php
				}

				elseif($_GET['mode'] == 'manual'){
					?>
					<h2>Database setup : Manual mode</h2>
					<form action=".?step=db3&mode=manual" method="post" accept-charset="utf-8">
						Mysql server address : <input type="text" name="address" value="localhost"/> Port : <input type="text" name="port" value="3306"/><br/>
						Mysql account to use : <input type="text" name="mysqlId" placeholder='login'/> <input type="password" placeholder='password' name="mysqlPw"/><br/>
						Database to use : <input type="text" name="dbName"/><br/>
						The user and the database must exist on the specified server. Moreover, the specified user must have all rights on the specified database.<br/>
						<input type="submit"/><br/>
						<a href='.?step=db&fileExists=True'>Return</a>
					</form>

					<?php
				}
			}

			//=====================================================================================================================================================
			elseif($_GET['step'] == 'db3'){

				if(!isset($_GET["mode"]) || !in_array($_GET['mode'],array('auto','manual')) )
					error('The data sent by your browser is incorrect.','db&fileExists=True',$db);

				//Create a file ".mysqlInfo" to store the mysql connection informations.
				$file = fopen('../.mysqlInfo','w');
				if(!$file){
						$err = 'Error while creating a file containing the mysql informations.<br/> 
								Please check the directory access rights.';
						error($err,'init');
					}

				//Connect to mysql to :
				//		1) Check the information given by the user;
				//		2) Perform the initialization tasks on it.

				//If auto mode, first connect as mysql admin and create the user and the database
				if($_GET['mode'] == 'auto'){

					if($_POST['mysqlId'] == '' || $_POST['mysqlPw'] == '' || $_POST['dbName'] == '' || $_POST['mysqlPw'] != $_POST['mysqlPw2'])
						error('The data you entered is incorrect.','db&fileExists=True',$db);

					$db = new mysqli($_POST['address'],$_POST['adminId'],$_POST['adminPw'],NULL,$_POST['port']);
					if($db->connect_error)
						error('Could not connect to mysql : <br/>'.$db->connect_errno.' '.$db->connect_error,'db&fileExists=True',$db);

					$query = $db->query('CREATE USER "'.$_POST['mysqlId'].'"@"localhost" IDENTIFIED BY "'.$_POST['mysqlPw'].'";');
					if(!$query){
						$db->close();
						error('Could not create the mysql user. Retry, or create a user manually and use manual setup mode.','db&fileExists=True',$db);
					}
					$query = $db->query('CREATE USER "'.$_POST['mysqlId'].'"@"%" IDENTIFIED BY "'.$_POST['mysqlPw'].'";');
					if(!$query){
						$err = $db->error;
						$db->query('DROP USER "'.$_POST['mysqlId'].'"@"localhost";');
						$db->close();
						error('Could not create the mysql user : '.$err.'<br/>Retry, or create a user manually and use manual setup mode.','db&fileExists=True',$db);
					}


					$query = $db->query('CREATE DATABASE '.$_POST['dbName'].';');
					if(!$query){
						$err = $db->error;
						$db->query('DROP USER "'.$_POST['mysqlId'].'"@"localhost";');
						$db->query('DROP USER "'.$_POST['mysqlId'].'"@"%";');
						$db->close();
						error('Could not create the database : '.$err.' <br/>Retry, or create a database manually and use manual setup mode.','db&fileExists=True',$db);
					}

					$query = $db->query('GRANT ALL ON '.$_POST['dbName'].'.* TO "'.$_POST['mysqlId'].'"@"localhost" , "'.$_POST['mysqlId'].'"@"%";');
					if(!$query){
						$err = $db->error;
						$db->query('DROP USER "'.$_POST['mysqlId'].'"@"localhost";');
						$db->query('DROP USER "'.$_POST['mysqlId'].'"@"%";');
						$db->query('DROP DATABASE '.$_POST['dbName'].';');
						$db->close();
						error('Could not grant rights to the new user on the new database :'.$err.'<br/>Retry, or use manual setup mode.','db&fileExists=True',$db);
					}

					$db->close();

				}

				//Create an object containing the mysql informations.
				$mysql = new stdClass();
				$mysql->server = new stdClass();
				$mysql->user = new stdClass();
				$mysql->server->address = $_POST['address'];
				$mysql->server->port = $_POST['port'];
				$mysql->user->id = $_POST['mysqlId'];
				$mysql->user->pass = $_POST['mysqlPw'];
				$mysql->db = $_POST['dbName'];

				//Initialize the database
				$db = new mysqli($mysql->server->address,$mysql->user->id,$mysql->user->pass,$mysql->db,$mysql->server->port);
				if(!$db)
					error('Could not connect to the mysql server using ".$mysql->user->id." ".$mysql->user->pass." as information.","db&fileExists=True',$db);

				$sql = file_get_contents("init_db.sql");
				if(!$sql){
					$db->close();
					error('Could not find the file "init_db.sql"',"db2&mode=manual",$db);
				}

				if ($db->multi_query($sql)) {
				  while ($db->more_results()) {
				  	$next = $db->next_result();
				    if (!$next) {
				      $err = $db->error;
				      $db->close();
				      error("Error while initalizing the database : $err <br/> Please check that 'init_db.sql' is correct.","db2&mode=manual",$db);
				    }
				  }
				}
				else{
					$err = $db->error;
					$db->close();
					error("Error while initalizing the database : $err <br/> Please check that 'init_db.sql' is correct.","db2&mode=manual",$db);
				}

				#Initialize the worker features
				$features = scandir("../features");
				foreach ($features as $filename) {
					if(substr($filename,-4) == ".php"){
						include '../features/'.$filename;
						$class = str_replace('.php', '', $filename);
						$feat = new $class();
						$feat->initDb($mysql);
					}	
				}


				//Save the database information to disk as json
				$json = json_encode($mysql);
				fwrite($file, $json,strlen($json));
				fclose($file);

				$db->close();
				?>
				<h2>Setup complete !</h2>
				<p>The database has been correctly initialized. <br/>
				You have three last thing to do : <br/>
				 - connect with the default admin account (admin/admin) and change the password;<br/>
				 - Make sure that the '.mysqlInfo' file in the framework main directory is readable only by the web server software, but no other users;<br/>
				 - Make sure that your web server allows .htaccess files.</p>
				<a href="..">Go to the framework index</a>
				<?php
			}
		?>
	</body>
</html>