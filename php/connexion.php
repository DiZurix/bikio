<?php
	session_start();
	$bdd = new PDO('mysql:host=127.0.0.1;dbname=bikio', 'root', '');

	if(isset ($_POST['formconnect']))
	{
		$mailconnect = htmlspecialchars($_POST['mailconnect']);
		$mdpconnect = sha1($_POST['mdpconnect']);
		if(!empty($mailconnect) AND !empty($mdpconnect))
		{
			$requser = $bdd->prepare("SELECT * FROM membres WHERE email = ? AND password = ?");
			$requser->execute(array($mailconnect, $mdpconnect));
			$userexist = $requser->rowCount();
			if($userexist==1)
			{
				$userinfo = $requser->fetch();
				$_SESSION['id'] = $userinfo['id'];
				$_SESSION['pseudo'] = $userinfo['pseudo'];
				$_SESSION['email'] = $userinfo['email'];
				header('Location: ..\html\indexconnection.html');
			}
			else
			{
				$erreur = "Mauvais identifiants";
			}
		}
		else
		{
			$erreur = "Tous les champs doivent être complétés.";
		}
	}
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<title>Connexion</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<link rel="stylesheet" href="../css/formulaire.css">
		<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
		<script src="../javascript/formulaire.js"></script>
	</head>

	<body onload="if($('#error') !== null){
	$('#overlay').addClass('open');}">

	<main role="main" class="html">
		<button class="popup-trigger btn" id="popup-trigger"><span>Connexion<i class="fa fa-plus-square-o"></i></span></button>
	</main>

	<div class="overlay" id="overlay">
		<div class="overlay-background" id="overlay-background"></div>
		<div class="overlay-content" id="overlay-content">
			<div class="fa fa-times fa-lg overlay-close" id="overlay-close"></div>
			<h1 class="main-heading">Connexion</h1>
			<form class="signup-form" method="post" action="#" novalidate="novalidate">
				<label for="signup-email">Adresse mail</label>
				<input type="email" name="mailconnect" placeholder="Mail"/>
				<label for="signup-pw">Mot de passe</label>
				<input type="password" name="mdpconnect" placeholder="Mot de passe"/>
				<input type="submit" name="formconnect" value="Se connecter"/>
			</form>
			<?php 
				if(isset($erreur))
				{
					echo '<font color="red">'.$erreur.'</font>';
				}
			?>
		</div>
	</body>
</html>