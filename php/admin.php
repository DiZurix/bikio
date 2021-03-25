<?php
$bdd = new PDO('mysql:host=127.0.0.1;dbname=bikio;charset=utf8', 'root', '');
if(isset($_GET['type']) AND $_GET['type'] == 'membre') 
{
   if(isset($_GET['confirme']) AND !empty($_GET['confirme'])) 
   {
      $confirme = (int) $_GET['confirme'];
      $req = $bdd->prepare('UPDATE membres SET confirme = 1 WHERE id = ?');
      $req->execute(array($confirme));
   }
   if(isset($_GET['supprime']) AND !empty($_GET['supprime'])) 
   {
      $supprime = (int) $_GET['supprime'];
      $req = $bdd->prepare('DELETE FROM membres WHERE id = ?');
      $req->execute(array($supprime));
   }
} 
$membres = $bdd->query('SELECT * FROM membres ORDER BY id');
?>
<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8" />
   <title>Administration</title>
</head>
<body>
   <ul>
      <?php while($m = $membres->fetch()) { ?>
      <li><?= $m['id'] ?> : <?= $m['pseudo'] ?><?php if($m['confirme'] == 0) { ?> - <a href="admin2.php?type=membre&confirme=<?= $m['id'] ?>">Confirmer</a><?php } ?> - <a href="admin2.php?type=membre&supprime=<?= $m['id'] ?>">Supprimer</a></li>
      <?php } ?>
   </ul>
   <br /><br />
</body>
</html>