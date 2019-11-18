<?php
//Activation des sessions
session_start();

//Connexion BDD
try
{
  $bdd = new PDO('mysql:host=localhost;dbname=webprojet;charset=utf8', 'webprojet', '7Ydeuzdb52:!');
}
catch (Exception $e)
{
  die('Erreur : ' . $e->getMessage());
}

//Vérification de l'existance des droits
if(!isset($_SESSION['droit']))
{
     //Accès non autorisé
     http_response_code(401);
}
else
{   
    //Vérification des droits
    if($_SESSION['droit'] == 2 OR $_SESSION['droit'] == 4)
    {
      if(isset($_POST['nom_cat']))
      {
        //Sécuration des données
        $cat_name = (string)htmlspecialchars($_POST['nom_cat']);
        
        $req1 = $bdd->prepare('SELECT * FROM CATEGORIE WHERE NOM_CATEGORIE = ?');
        $req1->execute(array($cat_name));

        //Vérification de la requête
        if(!$check = $req1->fetch())
        {
           $req1->closeCursor();
           $req1 = $bdd->prepare('INSERT INTO CATEGORIE(NOM_CATEGORIE) VALUES(?)');
           $req1->execute(array($cat_name));
           
           $req_finale = $bdd->query('SELECT * FROM CATEGORIE');
           $categories = $req_finale->fetchAll();
           foreach($categories as $categorie)
           {
	     echo '<option value="' . $categorie['ID_CATEGORIE'] . '">' . $categorie['NOM_CATEGORIE'] . '</option>';
           }
           $req_finale->closeCursor();
        }
        else
        {
           //Si elle existe, bad request
           $req1->closeCursor();
           http_response_code(400);
           echo 'Catégorie existante';
        }
      }
      else
      {
        //Bad request
        http_response_code(400);
      }
  }
  else
  {
    //Accès interdit
    http_response_code(401);
  }
}?>
