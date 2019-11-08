<?php
session_start();
header('Content-Type: text/javascript; charset=utf-8');
header('Content-Type: text/html; charset=utf-8');

try
{
  $bdd = new PDO('mysql:host=localhost;dbname=webprojet;charset=utf8', 'webprojet', '7Ydeuzdb52:!');
}
catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage());
}

if(isset($_COOKIE['mail']) AND isset($_COOKIE['mdp']) AND !isset($_SESSION['id']))
{
	$email = (String)htmlspecialchars($_COOKIE['mail']);
	$mdp = (String)htmlspecialchars($_COOKIE['mdp']);
	$req = $bdd->prepare("SELECT * FROM MEMBRE WHERE MAIL = ? AND PASSWORD = ?");
	
	$req->execute(array($email, $mdp));
        
	if($donnee = $req->fetch())
	{
	  $_SESSION['id'] = $donnee['ID_MEMBRE'];
	  $_SESSION['nom'] = $donnee['NOM'];
	  $_SESSION['prenom'] = $donnee['PRENOM'];
	  $_SESSION['mail'] = $donnee['MAIL'];
	  $_SESSION['droit'] = $donnee['DROIT'];
	  $_SESSION['id_region'] = $donnee['ID_REGION'];
	}
	$req->closeCursor();
} ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>BDE CESI</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
<link rel="icon" type="image/png" href="images/favicon.png" />
  <script src="script.js"></script>
</head>
<body> 
 <header>
  <a href="index.php">
   <img title="Retour à la page d'accueil"  src="images/logo.png">
  </a>

    <?php
    if(isset($_SESSION['nom']))
    {
	echo 'Bienvenue ' . $_SESSION['prenom'] . ' ' . $_SESSION['nom'];
    }
    $pageEnCours = $_SERVER['PHP_SELF']; ?>

    <nav class="nav nav-pills nav-justified flex-column flex-sm-row">
	<a class="nav-link nav-item <?php if($pageEnCours == "/webprojet/index.php") { echo "active";} ?>" href="index.php">Accueil</a>
	<a class="nav-link nav-item <?php if($pageEnCours == "/webprojet/events.php") { echo "active";} ?>" href="events.php">Evenements</a>
	<a class="nav-link nav-item <?php if($pageEnCours == "/webprojet/shop.php") { echo "active";} ?>" href="shop.php">Boutique</a>
	<?php
        if(!isset($_SESSION['id']))
	{ ?>
		<a class="nav-link nav-item <?php if($pageEnCours == "/webprojet/sign.php") { echo "active";} ?>" href="sign.php">Inscription</a>
		<a class="nav-link nav-item <?php if($pageEnCours == "/webprojet/login.php") { echo "active";} ?>" href="login.php">Connexion</a>
	<?php } 
        else
        {?>
		<a class="nav-link nav-item" href="logout.php">Se déconnecter</a>
	<?php
	}
	?>
    </nav>
 </header>

