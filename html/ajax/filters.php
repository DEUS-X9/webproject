<?php

try
{
  $bdd = new PDO('mysql:host=localhost;dbname=webprojet;charset=utf8', 'webprojet', '7Ydeuzdb52:!');
}
catch (Exception $e)
{
  die('Erreur : ' . $e->getMessage());
}

if(!isset($_POST['prix1']) OR !isset($_POST['prix2']) OR !isset($_POST['categorie']) OR !isset($_POST['ordre_prix']))
{
  die('Wtf');
  $req1 = $bdd->query('SELECT * FROM CATEGORIE ORDER BY ID_CATEGORIE');
  $categories = $req1->fetchAll();
  foreach($categories as $categorie)
  {?>
    <h5 class="categorie"><?php echo $categorie['NOM_CATEGORIE']; ?></h5><br />
    <?php
    $req2 = $bdd->prepare('SELECT SHOP.ID_ITEM, ITEM, PRIX, DESCRIPTION, CHEMIN FROM SHOP INNER JOIN REPRESENTER ON REPRESENTER.ID_ITEM = SHOP.ID_ITEM INNER JOIN PHOTO ON REPRESENTER.ID_PHOTO = PHOTO.ID_PHOTO WHERE ID_CATEGORIE = ? ORDER BY PRIX DESC');
    $req2->execute(array($categorie['ID_CATEGORIE']));
    $articles = $req2->fetchAll();
    
    foreach($articles as $article)
    {?>
      <div class="item">
	 <a href="shop.php?id_item=<?php echo $article['ID_ITEM']; ?>"><h4><?php echo $article['ITEM']; ?></h4></a>
	 <img src="photos/<?php echo $article['CHEMIN']; ?>" style="margin-left:90%; height:20%"/>
	 <p>Prix : <?php echo $article['PRIX']; ?>€<br /><?php echo nl2br($article['DESCRIPTION']); ?></p>
      </div>
    <?php
    }
  }
  $req1->closeCursor();
}
else
{
  $prix_min = (int)$_POST['prix1'];
  $prix_max = (int)$_POST['prix2'];
  $categorie = (int)$_POST['categorie'];
  $ordre_prix = (int)$_POST['ordre_prix'];

  $req1 = $bdd->prepare('SELECT * FROM CATEGORIE WHERE ID_CATEGORIE = ?');
  $req1->execute(array($categorie));
  if($d_categorie = $req1->fetch())
  {?>
        <h5 class="categorie"><?php echo $d_categorie['NOM_CATEGORIE']; ?></h5><br />
        <?php
        if($ordre_prix == 0)
        {
          $req2 = $bdd->prepare('SELECT SHOP.ID_ITEM, ITEM, PRIX, DESCRIPTION, CHEMIN FROM SHOP INNER JOIN REPRESENTER ON REPRESENTER.ID_ITEM = SHOP.ID_ITEM INNER JOIN PHOTO ON REPRESENTER.ID_PHOTO = PHOTO.ID_PHOTO WHERE ID_CATEGORIE = ? AND PRIX >= ? AND PRIX <= ? ORDER BY PRIX');
        }
        else
        {
           $req2 = $bdd->prepare('SELECT SHOP.ID_ITEM, ITEM, PRIX, DESCRIPTION, CHEMIN FROM SHOP INNER JOIN REPRESENTER ON REPRESENTER.ID_ITEM = SHOP.ID_ITEM INNER JOIN PHOTO ON REPRESENTER.ID_PHOTO = PHOTO.ID_PHOTO WHERE ID_CATEGORIE = ? AND PRIX >= ? AND PRIX <= ? ORDER BY PRIX DESC');     
        }

        $req2->execute(array($d_categorie['ID_CATEGORIE'], $prix_min, $prix_max));
        $articles = $req2->fetchAll();
    
        foreach($articles as $article)
        {?>
           <div class="item">
	     <a href="shop.php?id_item=<?php echo $article['ID_ITEM']; ?>"><h4><?php echo $article['ITEM']; ?></h4></a>
	     <img src="photos/<?php echo $article['CHEMIN']; ?>" style="margin-left:90%; height:20%"/>
	     <p>Prix : <?php echo $article['PRIX']; ?>€<br /><?php echo nl2br($article['DESCRIPTION']); ?></p>
           </div>
        <?php
        }
  }
  else
  {
     $req1->closeCursor();
     $req1 = $bdd->query('SELECT * FROM CATEGORIE');
     $categories = $req1->fetchAll();
     foreach($categories as $categorie)
     {?>
        <h5 class="categorie"><?php echo $categorie['NOM_CATEGORIE']; ?></h5><br />
        <?php
        if($ordre_prix == 0)
        {
          $req2 = $bdd->prepare('SELECT SHOP.ID_ITEM, ITEM, PRIX, DESCRIPTION, CHEMIN FROM SHOP INNER JOIN REPRESENTER ON REPRESENTER.ID_ITEM = SHOP.ID_ITEM INNER JOIN PHOTO ON REPRESENTER.ID_PHOTO = PHOTO.ID_PHOTO WHERE ID_CATEGORIE = ? AND PRIX >= ? AND PRIX <= ? ORDER BY PRIX');
        }
        else
        {
           $req2 = $bdd->prepare('SELECT SHOP.ID_ITEM, ITEM, PRIX, DESCRIPTION, CHEMIN FROM SHOP INNER JOIN REPRESENTER ON REPRESENTER.ID_ITEM = SHOP.ID_ITEM INNER JOIN PHOTO ON REPRESENTER.ID_PHOTO = PHOTO.ID_PHOTO WHERE ID_CATEGORIE = ? AND PRIX >= ? AND PRIX <= ? ORDER BY PRIX DESC');     
        }

        $req2->execute(array($categorie['ID_CATEGORIE'], $prix_min, $prix_max));
        $articles = $req2->fetchAll();
    
        foreach($articles as $article)
        {?>
           <div class="item">
	     <a href="shop.php?id_item=<?php echo $article['ID_ITEM'] ?>"><h4><?php echo $article['ITEM'] ?></h4></a>
	     <img src="photos/<?php echo $article['CHEMIN'] ?>" style="margin-left:90%; height:20%"/>
	     <p>Prix : <?php echo $article['PRIX'] ?>€<br /><?php echo nl2br($article['DESCRIPTION']); ?></p>
           </div>
        <?php
        }
     }
  }
  $req1->closeCursor();
}

