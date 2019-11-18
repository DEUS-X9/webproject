<?php require('php/header.php'); 
if(!isset($_SESSION['id']))
{
  header('Location: shop.php');
}
else if(isset($_POST['id_item']) AND isset($_POST['nombre']))
{
   $id_item = (int)$_POST['id_item'];
   $nombre = (int)$_POST['nombre'];
   
   $req = $bdd->prepare('SELECT ID_ITEM FROM SHOP WHERE ID_ITEM = ?');
   $req->execute(array($id_item));

   if($check = $req->fetch() AND $nombre >= 1)
   {
     $req->closeCursor();
     if(!isset($_SESSION['id_panier']))
     {
       $req = $bdd->prepare('SELECT ID_PANIER FROM PANIER WHERE ID_MEMBRE = ? ORDER BY DATE DESC LIMIT 0, 1');
       $req->execute(array($_SESSION['id']));
       
       if(!$donnee = $req->fetch())
       {
         $req->closeCursor();
         $req = $bdd->prepare('INSERT INTO PANIER(ID_MEMBRE) VALUES(?)');
         $req->execute(array($_SESSION['id']));
         $_SESSION['id_panier'] = $bdd->lastInsertId();
       }
       else
       {
         $req->closeCursor();
         $req = $bdd->prepare('SELECT ID FROM VENTE WHERE ID_PANIER_DONNE_LIEU = ?');
         $req->execute(array($donnee['ID_PANIER']));
         
         if(!$donnee2 = $req->fetch())
         {
           $req->closeCursor();
           $_SESSION['id_panier'] = $donnee['ID_PANIER'];
         }
         else
         {
           $req->closeCursor();
           $req = $bdd->prepare('INSERT INTO PANIER(ID_MEMBRE) VALUES(?)');
           $req->execute(array($_SESSION['id']));
           $_SESSION['id_panier'] = $bdd->lastInsertId();
         } 
       }
     }

     $req = $bdd->prepare('SELECT NOMBRE FROM ENREGISTRER WHERE ID_PANIER = ? AND ID_ITEM = ?');
     $req->execute(array($_SESSION['id_panier'], $id_item));
     
     if(!$donnee = $req->fetch())
     {
       $req->closeCursor();
       $req = $bdd->prepare('INSERT INTO ENREGISTRER(ID_PANIER, ID_ITEM, NOMBRE) VALUES(?, ?, ?)');
       $req->execute(array($_SESSION['id_panier'], $id_item, $nombre));
     }
     else
     {
       $req->closeCursor();
       $nombre = $nombre + $donnee['NOMBRE'];
       $req = $bdd->prepare('UPDATE ENREGISTRER SET NOMBRE = ? WHERE ID_PANIER = ? AND ID_ITEM = ?');
       $req->execute(array($nombre, $_SESSION['id_panier'], $id_item));
     }
     
     header('Location: shop.php?id_item=' . $id_item);
   }
   else
   {
     $req->closeCursor();
     header('Location: shop.php');
   }
}
else if(isset($_POST['reset']))
{
  $req = $bdd->prepare('DELETE FROM ENREGISTRER WHERE ID_PANIER = ?');
  $req->execute(array($_SESSION['id_panier']));
}
else if(isset($_POST['pay']))
{
  header('Location: pay.php');
}
?>
		<h1>Mon panier</h1>

                <p><a href="shop.php">Retour</a></p>
		
                <?php
                if(!isset($_SESSION['id_panier']))
                {
                  header('Location: shop.php');
                }
                else
                {
                  $total = 0;
                  $req = $bdd->prepare('SELECT ENREGISTRER.ID_ITEM, ITEM, PRIX, NOMBRE FROM ENREGISTRER INNER JOIN SHOP ON SHOP.ID_ITEM = ENREGISTRER.ID_ITEM WHERE ENREGISTRER.ID_PANIER = ?');
                  $req->execute(array($_SESSION['id_panier']));
                  $panier = $req->fetchAll();

                  foreach($panier as $article)
                  {?>
		     <div class="item">
			<a href="PAGE DE L'ARTICLE"><h4><a href="shop.php?id_item=<?php echo $article['ID_ITEM']; ?>"><?php echo $article['ITEM']; ?></a></h4>
			<div class="flex-container">
				<div><p>Prix : <?php echo $article['PRIX']; ?>€</p></div>
				<div style="margin-left:auto; margin-right:auto;"><?php echo $article['NOMBRE']; ?> (Total : <?php echo $article['PRIX'] * $article['NOMBRE']; $total = $total + ($article['PRIX'] * $article['NOMBRE']);?>€)</div>
			</div>
		     </div>
                   <?php
                   } 
                   $req->closeCursor();
                   ?>
                   <h5 id="somme_totale">
                   <?php
                     $req_nombre = $bdd->prepare('SELECT SUM(NOMBRE) AS total FROM ENREGISTRER WHERE ID_PANIER = ?');
                     $req_nombre->execute(array($_SESSION['id_panier']));
                     $nombre = $req_nombre->fetch();
                     if($nombre['total'] == NULL)
                     {
                       $nombre['total'] = 0;
                     } 
                     echo '<p>Total : ' . $total . '€ (total article : ' . $nombre['total'] .')</p>'; 
                   ?>
                   </h5>
                   <div id="boutton_form">
                     <form method="post">
                       <input type="text" name="reset" value=1 hidden />
                       <input type="submit" value="Vider mon pannier" />
                     </form>
                     <form method="post">
                       <input type="text" name="pay" value=1 hidden />
                       <input type="submit" value="Payer <?php echo $total; ?>€" />
                     </form>
                   </div>
                   <p>Si vous achetez nos articles, vous acceptez nos <a href="cgv/cgv.docx">Conditions Générales de Vente</a></p>
                  <?php
                   $req->closeCursor();
                   $req_nombre->closeCursor();
                  }?>
                <?php
		require 'php/footer.php'; ?>
	</body>
</html>
