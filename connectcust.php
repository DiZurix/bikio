<?php

	DEFINE ('DBUSER', 'root'); 
	DEFINE ('DBPW', '9LWTa2D$Cisx');
	DEFINE ('DBHOST', '127.0.0.1'); 
	DEFINE ('DBNAME', 'bikio');
	DEFINE ('DBPORT', '3307');

	$dbc = mysqli_connect(DBHOST, DBUSER, DBPW, DBNAME, DBPORT);
	if (!$dbc) {
	    die("Connexion impossible à la base de donnée: " . mysqli_connect_error());
	    exit();
	}

	$dbs = mysqli_select_db($dbc, DBNAME);
	if (!$dbs) {
	    die("Nom de la base de donnée inconnu: " . mysqli_error($dbc));
	    exit(); 
	}

	$Pseudo = mysqli_real_escape_string($dbc, $_GET['Pseudo']);
	$Password = mysqli_real_escape_string($dbc, sha1($_GET['Password']));

	$sql = $dbc->query("SELECT * FROM membres WHERE pseudo='$Pseudo' AND password='$Password'");

	if (mysqli_num_rows($sql) > 0){
		echo "conex";
	}
	else{
		echo "error";
	}
	
	mysqli_close($dbc);
?>