<?php
session_start();

$bdd = new PDO('mysql:host=127.0.0.1;dbname=bikio', 'root', '');
if(isset($_SESSION['id']))
{
    $requser = $bdd->prepare("SELECT * FROM membres WHERE id = ?");
    $requser->execute(array($_SESSION['id']));
    $user = $requser ->fetch();

    if(isset($_POST['newpseudo']) AND !empty($_POST['newpseudo']) AND $_POST['newpseudo'] != $user['pseudo'])
    {
        $newpseudo = htmlspecialchars($_POST['newpseudo']);
        $insertpseudo = $bdd ->prepare("UPDATE membres SET pseudo = ? WHERE id = ?");
        $insertpseudo->execute(array($newpseudo, $_SESSION['id']));
        header('Location: profil.php?id='.$_SESSION['id']);
    }

    if(isset($_POST['newmail']) AND !empty($_POST['newmail']) AND $_POST['newmail'] != $user['email'])
    {
        $newmail = htmlspecialchars($_POST['newmail']);
        $insertmail = $bdd ->prepare("UPDATE membres SET email = ? WHERE id = ?");
        $insertmail->execute(array($newmail, $_SESSION['id']));
        header('Location: profil.php?id='.$_SESSION['id']);
    }

    if(isset($_POST['newmdp1']) AND !empty($_POST['newmdp1']) AND isset($_POST['newmdp2']) AND !empty($_POST['newmdp2']))
    {
        $mdp1 = sha1($_POST['newmdp1']);
        $mdp2 = sha1($_POST['newmdp2']);

        if($mdp1 == $mdp2)
        {
            $insertmdp = $bdd->prepare("UPDATE membres SET password = ? WHERE id = ?");
            $insertmdp->execute(array($mdp1, $_SESSION['id']));
            header('Location: profil.php?id='.$_SESSION['id']);
        }
        else
        {
            $msg =" vos deux mots de passes ne correspondent pas";
        }
    }

    if(isset($_FILES['avatar']) AND !empty($_FILES['avatar']['name'])) {
        $tailleMax = 2097152;
        $extensionsValides = array('jpg', 'jpeg', 'gif', 'png');
        if($_FILES['avatar']['size'] <= $tailleMax) {
           $extensionUpload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));
           if(in_array($extensionUpload, $extensionsValides)) {
              $chemin = "membres/avatars/".$_SESSION['id'].".".$extensionUpload;
              $resultat = move_uploaded_file($_FILES['avatar']['tmp_name'], $chemin);
              if (file_exists("membres/avatars/".$_SESSION['id'].'.png') && $extensionUpload !='png')
              {
                  unlink("membres/avatars/".$_SESSION['id'].'.png');
              }
              if (file_exists("membres/avatars/".$_SESSION['id'].'.gif') && $extensionUpload !='gif')
              {
                  unlink("membres/avatars/".$_SESSION['id'].'.gif');
              }
              if (file_exists("membres/avatars/".$_SESSION['id'].'.jpeg') && $extensionUpload !='jpeg')
              {
                  unlink("membres/avatars/".$_SESSION['id'].'.jpeg');
              }
              if (file_exists("membres/avatars/".$_SESSION['id'].'.jpg') && $extensionUpload !='jpg')
              {
                  unlink("membres/avatars/".$_SESSION['id'].'.jpg');
              }
              if($resultat) {
                 $updateavatar = $bdd->prepare('UPDATE membres SET avatar = :avatar WHERE id = :id');
                 $updateavatar->execute(array(
                    'avatar' => $_SESSION['id'].".".$extensionUpload,
                    'id' => $_SESSION['id']
                    ));
                 header('Location: profil.php?id='.$_SESSION['id']);
              } else {
                 $msg = "Erreur durant l'importation de votre photo de profil";
              }
           } else {
              $msg = "Votre photo de profil doit être au format jpg, jpeg, gif ou png";
           }
        } else {
           $msg = "Votre photo de profil ne doit pas dépasser 2Mo";
        }
     }
    ?>
<html>
	<head>
		<title>Profil de <?php echo $userinfo['pseudo']?></title>
		<meta charset="utf-8">
	</head>
	<body>
		<div align="center">
			<h2>Edition de mon profil</h2>
			<form method="POST" action="" enctype="multipart/form-data">
                <label>Avatar :</label>
                <input type="file" name="avatar"/><br /><br />
                <label>Pseudo :</label>
                <input  type ="text" name="newpseudo" placeholder="Pseudo" value="<?php  echo $user['pseudo']; ?>" /> <br /><br />
                <label>Mail :</label>
                <input  type ="text" name="newmail" placeholder="Mail" value="<?php  echo $user['email']; ?>"/> <br /><br />
                <label>Mot de passe :</label>
                <input  type ="password" name="newmdp1" placeholder="Mot de passe"/> <br /><br />
                <label>Confirmation - mot de passe :</label>
                <input  type ="password" name="newmdp2" placeholder="Confirmation du mot de passe"/> <br /><br />
                <input type="submit" value="Mettre à jour mon profil">
                
                
            </form>
            <?php if(isset($msg)) { echo $msg;} ?>
		</div>
	</body>
</html>
<?php
}
else
{
    header("Location: connexion.php");
}
?>