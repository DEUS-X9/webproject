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
	
   <link rel="stylesheet" href="/webprojet/css/bootstrap.min.css">
   <link rel="stylesheet" href="/webprojet/css/style.css">
	
  <link rel="icon" type="image/png" href="/webprojet/images/favicon.png" />
</head>
<body> 
	<!-- script pour les cookies -->
	<script src="https://cc.cdn.civiccomputing.com/8/cookieControl-8.x.min.js%22%3E</script>
<script>
    var config = {
        apiKey: 'ee37b9a968559c92dbad9ce7fe876361563573ab',
        product: 'COMMUNITY',
        optionalCookies: [
            {
                    name: 'cookies obligatoires/fonctionnels',
                    label: 'Cookies obligatoires/fonctionnels',
                    description: '',
                    cookies: [],
                    onAccept : function(){},
                    onRevoke: function(){}
                }
        ],

        position: 'LEFT',
        theme: 'DARK'
    };

    CookieControl.load( config );
</script>
 <header>
	
			<a href="/webprojet/index.php">
        			<img title="Retour à la page d'accueil"  src="/webprojet/images/logo.png">
     			</a>

    <?php
    if(isset($_SESSION['nom']))
    {
	echo 'Bienvenue ' . $_SESSION['prenom'] . ' ' . $_SESSION['nom'];
    }
    $pageEnCours = $_SERVER['PHP_SELF']; ?>
    
    <nav class="nav nav-pills nav-justified flex-column flex-sm-row">
	<a class="nav-link nav-item <?php if($pageEnCours == "/webprojet/index.php") { echo "active";} ?>" href="/webprojet/index.php">Accueil</a>
	<a class="nav-link nav-item <?php if($pageEnCours == "/webprojet/events.php") { echo "active";} ?>" href="/webprojet/events.php">Evenements</a>
	<a class="nav-link nav-item <?php if($pageEnCours == "/webprojet/shop.php") { echo "active";} ?>" href="/webprojet/shop.php">Boutique</a>
	<?php
        if(!isset($_SESSION['id']))
	{ ?>
		<a class="nav-link nav-item <?php if($pageEnCours == "/webprojet/sign.php") { echo "active";} ?>" href="/webprojet/sign.php">Inscription</a>
		<a class="nav-link nav-item <?php if($pageEnCours == "/webprojet/login.php") { echo "active";} ?>" href="/webprojet/login.php">Connexion</a>
	<?php } 
        else if($_SESSION['droit'] == 2 OR $_SESSION['droit'] == 4)
        {?>
		<a class="nav-link nav-item <?php if($pageEnCours == "/webprojet/bde.php") { echo "active";} ?>" href="/webprojet/bde.php">Administration</a>
		<a class="nav-link nav-item" href="/webprojet/logout.php">Se déconnecter</a>
	<?php
	}
	else
	{
	?>
		<a class="nav-link nav-item" href="/webprojet/logout.php">Se déconnecter</a>
        <?php
        } ?>
    </nav>
 </header>

