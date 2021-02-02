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
        <link href="../css/style.css" rel="stylesheet">
        <link href="http://fonts.googleapis.com/css?family=Crete+Round" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <title>Bikio</title>
	</head>

	<body>
	<header>
            <div class="wrapper">
               <h1><a href="#"><img src="../images/bikio.png" alt="[BIKIO-LOGO]"/></a><span class="bleu"></span></h1>
               <h5><img src="../images/bikiotexte2.png" alt="[BIKIO-TXT]" class="gris"/></h5>
               <nav>
                    <ul>
                        <li><a href="deconnexion.php">Se déconnecter</a></li>
                        <li><a href="..\html\indexconnection.html"> page d'acceuil</a></li>
                    </ul>
                </nav>
            </div>
        </header>
		<div align="center">
			<h2>Profil de <?php echo $userinfo['pseudo']?></h2>
			<br/>
			<?php
				if (!empty($userinfo['avatar']))
				{
			?>
			<img src="membres/avatars/<?php echo $userinfo['id'];if (file_exists("membres/avatars/".$userinfo['id'].'.png'))echo'.png';if (file_exists("membres/avatars/".$userinfo['id'].'.jpg'))echo'.jpg';if (file_exists("membres/avatars/".$userinfo['id'].'.jpeg'))echo'.jpeg';if (file_exists("membres/avatars/".$userinfo['id'].'.gif'))echo'.gif';?>" width="150"/>
			<?php
				}
			?>

			<br/>
			Pseudo = <?php echo $userinfo['pseudo']?>
			<br/>
			Mail = <?php echo $userinfo['email']?>
			<br/>
			<button class="btn btn-primary"><a href="editionprofil.php">Editer mon profil</a></button>
			<?php
				if(isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id'])
				{
			?>
			<?php
				}
			?>
		</div>
	</body>
</html>

<?php
	}
?>