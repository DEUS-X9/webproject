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
      if(isset($_POST['nom_item']) AND isset($_POST['cat_item']) AND isset($_POST['prix_item']) AND isset($_POST['desc_item']) AND isset($_FILES['image']) AND $_FILES['image']['error'] == 0)
      {
        //Sécuration des données
        $nom_item = (string)htmlspecialchars($_POST['nom_item']);
        $cat_item = (int)$_POST['cat_item'];
        $prix_item = (int)$_POST['prix_item'];
        $desc_item = (string)htmlspecialchars($_POST['desc_item']);
       
        $req1 = $bdd->prepare('SELECT * FROM CATEGORIE WHERE ID_CATEGORIE = ?');
        $req1->execute(array($cat_item));

        $req2 = $bdd->prepare('SELECT ITEM FROM SHOP WHERE ITEM = ?');
        $req2->execute(array($nom_item));

        //Vérification de la requête
        if(!$check1 = $req1->fetch() OR $nom_item == '' OR $prix_item <= 0 OR $desc_item == '' OR $check2 = $req2->fetch())
        {
           $req1->closeCursor();
           $req2->closeCursor();
           http_response_code(400);
        }
        else
        {

          //On crée l'ID de l'item
          $req1->closeCursor();
          $req2->closeCursor();
          $req1 = $bdd->prepare('INSERT INTO SHOP(ITEM, PRIX, DESCRIPTION, ID_CATEGORIE) VALUES(?, ?, ?, ?)');
          $req1->execute(array($nom_item, $prix_item, $desc_item, $cat_item));
          $id_item = $bdd->lastInsertId();
          $req1->closeCursor();

          //On upload le fichier
          if($_FILES['image']['size'] <= 5242880)
          {
                $infosfichier = pathinfo($_FILES['image']['name']);
                $extension_upload = $infosfichier['extension'];
                $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png');
                if(in_array($extension_upload, $extensions_autorisees))
                {
                   //On l'enregistre définitivement
                   $nom_fichier = '' . time() . '.' . $infosfichier['extension'] . '';
                   move_uploaded_file($_FILES['image']['tmp_name'], '/home/webprojet/www/photos/' . $nom_fichier);
                   
                   //On l'enregistre dans la BDD
                   $req_img = $bdd->prepare('INSERT INTO PHOTO(CHEMIN, ID_MEMBRE, USED_FOR_EVENT) VALUES(?, ?, 0)');
                   $req_img->execute(array($nom_fichier, $_SESSION['id']));
                   //Création de l'ID de l'image
                   $id_img = $bdd->lastInsertId();
                   $req_img->closeCursor();

                   $req_img = $bdd->prepare('INSERT INTO REPRESENTER(ID_PHOTO, ID_ITEM) VALUES(?, ?)');
                   $req_img->execute(array($id_img, $id_item));
                   $req_img->closeCursor();

                }
                else
                {
                   //Bad request si l'extension est invalide
                   http_response_code(400);
                   echo 'Erreur extension';  
                }
          }
          else
          {
             //Bad request si le fichier est trop volumineux
             http_response_code(400);
             echo 'Fichier trop volumineux';  
          }

          //Si l'ID de l'image existe c'est que tout est OK
          if(isset($id_img))
          {
            //On génère la page
            $req = $bdd->query('SELECT ID_ITEM, ITEM, PRIX, DESCRIPTION, ACTIF, NOM_CATEGORIE FROM SHOP INNER JOIN CATEGORIE ON CATEGORIE.ID_CATEGORIE = SHOP.ID_CATEGORIE');
		  
	    if(!$check = $req->fetch())
	    {
                 //Internal server error si il n'existe pas d'entrée
		 http_response_code(500);
	    }
	    else
	    {
                  $req = $bdd->query('SELECT ID_ITEM, ITEM, PRIX, DESCRIPTION, ACTIF, NOM_CATEGORIE FROM SHOP INNER JOIN CATEGORIE ON CATEGORIE.ID_CATEGORIE = SHOP.ID_CATEGORIE');
                  ?>
                  <table class="table table-striped table-dark">
  		  <thead>
    			<tr>
      			  <th scope="col">ID</th>
      			  <th scope="col">Nom</th>
			  <th scope="col">Catégorie</th>
      		          <th scope="col">Prix</th>
      			  <th scope="col">Gérer</th>
    			</tr>
  		  </thead>
                  <tbody>
                <?php
                  //Tant qu'il y a des articles, on génère les lignes
		  while($donnees = $req->fetch())
		  {
		      echo '    <tr><th scope="row"> ' . $donnees['ID_ITEM'] . '</th><td>' . $donnees['ITEM']. '</td><td>' . $donnees['NOM_CATEGORIE']. '</td><td>' . $donnees['PRIX']. '</td><td>';
                      //SI l'article est disponible à la vente, on ajoute la fonction supprimer
                      if($donnees['ACTIF'] == 1)
                      {
                        echo '<a href="#" onclick="supp_item(' . $donnees['ID_ITEM'] . '); return false;">Supprimer</a></td></tr>' ;
                      }
                      else
                      {
                        echo 'Article supprimé</td></tr>' ;
                      }
		   }
                   echo '</tbody>';
            }
	    $req->closeCursor();
         }
         else
         {
            //S'il y eu problème, on supprime l'ID
            $req1 = $bdd->prepare('DELETE FROM SHOP WHERE ID_ITEM');
            $req1->execute(array($id_item));
            $req1->closeCursor();
         }
      }
    }
    else
    {
      //Bad request
      http_response_code(400);
    }
  }
}?>
