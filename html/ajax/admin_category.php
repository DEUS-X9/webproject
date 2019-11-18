<?php 
//Activation session
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
     http_response_code(401);
}
else
{
    //Vérification du contenu de la requête
    if(!isset($_POST['item_id']))
    {
         //Vérification des droits
         if($_SESSION['droit'] == 2 OR $_SESSION['droit'] == 4)
         {
            //Récupération des items
            $req = $bdd->query('SELECT ID_ITEM, ITEM, PRIX, DESCRIPTION, ACTIF, NOM_CATEGORIE FROM SHOP INNER JOIN CATEGORIE ON CATEGORIE.ID_CATEGORIE = SHOP.ID_CATEGORIE');
	
            //SI il y en a pas, on retourner une erreur	SINON on génère la page
	    if(!$check = $req->fetch())
	    {
		 http_response_code(500);
	    }
	    else
	    {?>
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
                      $req = $bdd->query('SELECT ID_ITEM, ITEM, PRIX, DESCRIPTION, ACTIF, NOM_CATEGORIE FROM SHOP INNER JOIN CATEGORIE ON CATEGORIE.ID_CATEGORIE = SHOP.ID_CATEGORIE');
		      echo '    <tr><th scope="row"> ' . $donnees['ID_ITEM'] . '</th><td>' . $donnees['ITEM']. '</td><td>' . $donnees['NOM_CATEGORIE']. '</td><td>' . $donnees['PRIX']. '</td><td>';
                      //SI l'article est disponible à la vente, on ajoute la fonction supprimer
                      if($donnees['ACTIF'] == 1)
                      {
                        echo '<a href="#" onclick="supp_item(' . $donnees['ID_ITEM'] . '); return false;">Supprimer</a></td></tr>' ;
                      }
                      else
                      {
                        echo 'Article supprimé</td></tr>';
                      }
		   }
                   echo '</tbody>';
            }
	    $req->closeCursor();
         }
         else
         {
            //Accès non autorisé
            http_response_code(401);
         }
    }
    else
    {
      //Vérification des droits
      if($_SESSION['droit'] == 2 OR $_SESSION['droit'] == 4)
      {
        $item_id = (int)$_POST['item_id'];
        
        //Validation de l'ID
        $req1 = $bdd->prepare('SELECT * FROM SHOP WHERE ID_ITEM = ?');
        $req1->execute(array($item_id));

        if(!$check = $req1->fetch())
        {
           //Bad request si l'ID n'existe pas
           http_response_code(400);
        }
        else
        {
           //Si l'ID existe on le rend inactif
           $req1->closeCursor();
           $req1 = $bdd->prepare('UPDATE SHOP SET ACTIF = 0 WHERE ID_ITEM = ?');
           $req1->execute(array($item_id));
           $req1->closeCursor();

           $req = $bdd->query('SELECT ID_ITEM, ITEM, PRIX, DESCRIPTION, ACTIF, NOM_CATEGORIE FROM SHOP INNER JOIN CATEGORIE ON CATEGORIE.ID_CATEGORIE = SHOP.ID_CATEGORIE');
		
            //SI il y en a pas, on retourner une erreur	SINON on génère la page  
	    if(!$check = $req->fetch())
	    {
                 $req->closeCursor();
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
      }
      else
      {
        //Accès non autorisé
        http_response_code(401);
      }
    }
}
