<?php 
if(!isset($_SESSION['id']))
{
  header('Location: index.php');
}
else if($_SESSION['droit'] == 2)
{
  header('Location: index.php');
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
 			<img title="Retour à la page d'accueil" alt="Retour à la page d'accueil" src="images/logo.png">
		</header>
		<h1>Page d'administration</h1>
		<?php 
		if(!isset($_GET['compte']))
		{
		  $req = $bdd->query('SELECT * FROM MEMBRE');
		  
		  if($req->columnCount() == 0)
		  {
		    echo '<p id="result_compte">Aucune compte actif. Erreur BDD';
		  }
		  else
		  {
 		    echo '<p id="result_compte">';
		    while($donnees = $req->fetch())
		    {
			echo '<span class="entite_compte">';
                        echo '';
		    }
		  
		} ?>
	</body>
</html>


