<?php

session_start();
if(isset($_SESSION['id']) && !isset($_GET['id']))
{
	header('Location: profil.php?id='.$_SESSION['id']);
}
$bdd = new PDO('mysql:host=127.0.0.1;dbname=bikio', 'root', '');



if(isset($_GET['id']) AND $_GET['id'] > 0)
{
	$getid = intval($_GET['id']);
	$requser = $bdd->prepare('SELECT * FROM membres WHERE id = ?');
	$requser->execute(array($getid));
	$userinfo = $requser->fetch()


?>
<html>
	<head>
		<title>Profil de <?php echo $userinfo['pseudo']?></title>
		<meta charset="utf-8">
	</head>
	<body>
		<div align="center">
			<h2>Profil de <?php echo $userinfo['pseudo']?></h2>
			</br></br>
			<?php

			if (!empty($userinfo['avatar']))
			{	
			?>
			<img src="membres/avatars/<?php echo $userinfo['id'];if (file_exists("membres/avatars/".$userinfo['id'].'.png'))echo'.png';if (file_exists("membres/avatars/".$userinfo['id'].'.jpg'))echo'.jpg';if (file_exists("membres/avatars/".$userinfo['id'].'.jpeg'))echo'.jpeg';if (file_exists("membres/avatars/".$userinfo['id'].'.gif'))echo'.gif';?>" width="150"/>	
			<?php
			}
			?>
			<br />
			Pseudo = <?php echo $userinfo['pseudo']?>
			<br/>
			Mail = <?php echo $userinfo['email']?>
			<br/>
			<?php
			if(isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id'])
			{
			?>
			<a href="editionprofil.php">Editer mon profil</a>
			<br/>
			<a href="deconnexion.php">Se déconnecter</a>
			<br/>
			<a href="..\html\indexconnection.html">page d'acceuil</a>
			<?php
			}
			?>
		</div>
	</body>
</html>
<?php
}
?>