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
<html>
	<head>
		<title>Connexion</title>
		<meta charset="utf-8">
	</head>
	<body>
		<div align="center">
			<h2>Connexion</h2>
			</br></br>
			<form method="POST" action="">
				<input type="email" name="mailconnect" placeholder="Mail"/>
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
