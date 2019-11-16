<?php
session_start();

try
{
  $bdd = new PDO('mysql:host=localhost;dbname=webprojet;charset=utf8', 'webprojet', '7Ydeuzdb52:!');
}
catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage());
}

if(!isset($_SESSION['id']))
{
  header('Location: ../events.php');
}
else if(!isset($_GET['e_id']))
{
  header('Location: ../events.php');
}
else
{
  $e_id = (int)$_GET['e_id'];
  if($e_id < 1)
  {
     header('Location: ../events.php');
  }
  else
  {
    $req = $bdd->prepare('SELECT ID_EVENTS, ID_REGION, EVENTS FROM EVENEMENTS WHERE ID_EVENTS = ?');
    $req->execute(array($e_id));
    if(!$event = $req->fetch())
    {
      header('Location: ../events.php');
      $req->closeCursor();
    }
    else if(isset($_SESSION['id_region']) AND (($_SESSION['id_region'] != $event['ID_REGION'] AND $_SESSION['droit'] != 4) OR ($_SESSION['id_region'] == $event['ID_REGION'] AND ($_SESSION['droit'] != 2 AND $_SESSION['droit'] != 4))))
    {
        header('Location: events.php');
        $req->closeCursor();
    }
    else
    {
       $req->closeCursor();
       $req = $bdd->prepare('SELECT NOM, PRENOM, MAIL FROM INSCRIRE INNER JOIN MEMBRE ON MEMBRE.ID_MEMBRE = INSCRIRE.ID_MEMBRE WHERE ID_EVENTS = ?');
       $req->execute(array($e_id));
       $donnees = $req->fetchAll();

       $nom_fichier = sys_get_temp_dir() . '/' . time();
       $f = fopen($nom_fichier, 'w+');
       fwrite($f, "Nom;Prenom;E-mail;\n");

       foreach($donnees as $donnee)
       {
          fwrite($f, $donnee['NOM'] . ";" . $donnee['PRENOM'] . ";" . $donnee['MAIL'] . ";\n");
       }
       fclose($f);

       header("Cache-Control: no-cache, must-revalidate");
       header("Cache-Control: post-check=0,pre-check=0");
       header("Cache-Control: max-age=0");
       header("Pragma: no-cache");
       header("Expires: 0");
       header("Content-Type: application/force-download");
       header('Content-Disposition: attachment; filename="Inscription '. $event['EVENTS'] .'.csv"');
       header("Content-Length: " . filesize($nom_fichier));
       readfile($nom_fichier);
       unlink($nom_fichier);
    }
  }
}
?>
