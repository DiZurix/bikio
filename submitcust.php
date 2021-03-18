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
	$PasswordVerif = mysqli_real_escape_string($dbc, sha1($_GET['PasswordVerif']));
	$Email = mysqli_real_escape_string($dbc, $_GET['Email']);
	$EmailVerif = mysqli_real_escape_string($dbc, $_GET['EmailVerif']);

	if(!empty($_GET['Pseudo']) && !empty($_GET['Password']) && !empty($_GET['Email'])){
		$pseudolength = strlen($Pseudo);
		if($pseudolength <= 20){
			if($Email == $EmailVerif){
				if(filter_var($Email, FILTER_VALIDATE_EMAIL)){
					$sql = "SELECT email, pseudo FROM membres WHERE pseudo='".$Pseudo."' OR email='".$Email."'";
					$result = $dbc->query($sql);
					if($result->num_rows <= 0){
						if ($Password == $PasswordVerif){
							$query = "INSERT INTO membres (pseudo, password, email) VALUES ('$Pseudo', '$Password', '$Email')";
							$erreur = "Votre compte a été créé";
						}
						else
							$erreur = "Vos mots de passe ne correspondent pas.";
					}
					else
						$erreur = "Pseudo et/ou adresse mail déjà utilisée.";
				}
				else
					$erreur = "Votre adresse mail n'est pas valide.";
			}
			else
				$erreur = "Vos adresses mails ne correspondent pas.";
		}
		else
			$erreur = "Votre pseudo ne doit pas dépasser 20 caractères.";
	}
	else
		$erreur = "Tous les champs doivent être complétés correctement.";

	if (isset($erreur))
		echo '<center><font color="red">'.$erreur.'</center></font>';

	if ($query)
		$result = mysqli_query($dbc, $query) or trigger_error("Query MySQL Error: " . mysqli_error($dbc));

	mysqli_close($dbc);
?>