<?php
session_start();
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
	  $_SESSION['droit'] = $donnee['TYPE_UTILISATEUR'];
	  $_SESSION['id_region'] = $donnee['ID_REGION'];

          $req2 = $bdd->prepare('INSERT INTO loging(ip, email, login) VALUES(:ip, :email, :login)');
          $req2->execute(array(
               'ip' => $_SERVER['REMOTE_ADDR'],
               'email' => $_COOKIE['mail'],
               'login' => true ));
          $req2->closeCursor();
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
	
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>	
	
  <link rel="icon" type="image/png" href="images/favicon.png" />
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
        else if($_SESSION['droit'] == 2)
        {?>
		<a class="nav-link nav-item <?php if($pageEnCours == "/webprojet/bde.php") { echo "active";} ?>" href="bde.php">Administration</a>
		<a class="nav-link nav-item" href="logout.php">Se déconnecter</a>
	<?php
	}
	else
	{
	?>
		<a class="nav-link nav-item" href="logout.php">Se déconnecter</a>
        <?php
        } ?>
    </nav>
 </header>

