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
			header('Location: profil.php?id='.$_SESSION['id']);

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
<html >
<head>
  <meta charset="UTF-8">
  <title>Connexion</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <link rel='stylesheet prefetch' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
<link rel='stylesheet prefetch' href='http://fonts.googleapis.com/css?family=Open+Sans:400,600'>

      <link rel="stylesheet" href="..\css\Formulaire.css">
      <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
      <script src="..\javascript\Formulaire.js"></script>
  
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
