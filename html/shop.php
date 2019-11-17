<?php require 'php/header.php'; ?>
<h1>Boutique</h1>
<?php 
    if(isset($_SESSION['id']) && !isset($_SESSION['']))
    {
      $req_nombre = $bdd->prepare('SELECT SUM(NOMBRE) AS total FROM ENREGISTRER INNER JOIN PANIER ON PANIER.ID_PANIER = ENREGISTRER.ID_PANIER WHERE PANIER.ID_MEMBRE = ? GROUP BY ENREGISTRER.ID_ITEM');
      $req_nombre->execute(array($_SESSION['id']));
      $nombre = $req_nombre->fetch();
      ?>
      <p><a href="basket.php">Voir mon panier (<?php echo $nombre['total']; ?>)</a></p>
    <?php
    } 
    if(!isset($_GET['admin']) AND !isset($_GET['id_item']))
    {?>
     <h2>Nos meilleures ventes : </h2>
     <style>
      * {box-sizing: border-box}
      .mySlides {display: none}
      img {vertical-align: middle;}
      /* Slideshow container */
      .slideshow-container {
      max-width: 25%;
      position: relative;
      margin: auto;
      }
      /* Next & previous buttons */
      .prev, .next {img
      cursor: pointer;
      position: absolute;
      top: 50%;
      width: auto;
      padding: 16px;
      margin-top: -22px;
      color: white;
      font-weight: bold;
      font-size: 18px;
      transition: 0.6s ease;
      border-radius: 0 3px 3px 0;
      user-select: none;
      }
      /* Position the "next button" to the right */
      .next {
      right: 0;
      border-radius: 3px 0 0 3px;
      }
      /* On hover, add a black background color with a little bit see-through */
      .prev:hover, .next:hover {
      background-color: rgba(0,0,0,0.8);
      }
      /* Caption text */
      .text {
      color: #ffffff;
      background-color:#4d75bb;
      padding: 8px 12px;
      position: absolute;
      bottom: 8px;
      width: 100%;
      text-align: center;
      }
      /* The dots/bullets/indicators */
      .dot {
      cursor: pointer;
      height: 15px;
      width: 15px;
      margin: 0 2px;
      background-color: #bbb;
      border-radius: 50%;
      display: inline-block;
      transition: background-color 0.6s ease;
      }
      .active, .dot:hover {
      background-color: #717171;
      }
      @-webkit-keyframes fade {
      from {opacity: .4} 
      to {opacity: 1}
      }
      @keyframes fade {
      from {opacity: .4} 
      to {opacity: 1}
      }
      /* On smaller screens, decrease text size */
      @media only screen and (max-width: 300px) {
      .prev, .next,.text {font-size: 11px}
      }
      .flex-container {
      display: flex;
      }
     </style>
     <!-- Slideshow container -->
     <div class="slideshow-container">
       <!-- Full-width images with number and caption text -->
       <div class="mySlides">
        <img src="" style="width:100%">
       <div class="text">Caption Text</div>
      </div>
      <div class="mySlides">
          <img src="" style="width:100%">
          <div class="text">Caption Two</div>
      </div>
      <div class="mySlides">
          <img src="" style="width:100%">
          <div class="text">Caption Three</div>
      </div>
      <!-- Next and previous buttons -->
      <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
      <a class="next" onclick="plusSlides(1)">&#10095;</a>
   </div>
   <br>
   <!-- The dots/circles -->
   <div style="text-align:center">
      <span class="dot" onclick="currentSlide(1)"></span> 
      <span class="dot" onclick="currentSlide(2)"></span> 
      <span class="dot" onclick="currentSlide(3)"></span> 
   </div>
   <?php
    $req1 = $bdd->query('SELECT * FROM CATEGORIE');
    $categories = $req1->fetchAll();
    ?>
    <form id="filtres">
       <div class="flex-container">
           <div>
               Filtrer par :
               <label for="categorie">Catégorie : </label>
               <select name="categorie" id="categorie">
                   <option value="0" selected>Toutes</option>
                   <?php  
                       foreach($categories as $categorie)
                       {
                         echo '<option value="' . $categorie['ID_CATEGORIE'] . '">' . $categorie['NOM_CATEGORIE'] . '</option>';
                       }
                       ?>
               </select>
           </div>
           <div>
            <?php
            $req_prix_min = $bdd->query('SELECT PRIX FROM SHOP ORDER BY PRIX LIMIT 0, 1');
            $req_prix_max = $bdd->query('SELECT PRIX FROM SHOP ORDER BY PRIX DESC LIMIT 0, 1');
            $prix_min = $req_prix_min->fetch();
            $prix_max = $req_prix_max->fetch();
            ?>
            <label for="prix1">Prix (en €) entre : </label>
            <input type="text" name="prix1" id="prix1" value="<?php echo $prix_min['PRIX']; ?>"/>
            <label for="prix2"> et </label>
            <input type="text" name="prix2" id="prix2" value="<?php echo $prix_max['PRIX']; ?>"/>
           </div>
             <div><input type="radio" name="ordre_prix" value="0" id="prix_croissant" checked/> <label for="prix_croissant">Prix croissant</label></div>
           <div><input type="radio" name="ordre_prix" value="1" id="prix_decroissant" /> <label for="prix_decroissant">Prix décroissant</label></div>
           <div><input id="send" type="submit" value="Filtrer">  <input id="reset" type="reset" value="Remettre à 0" /></div>
       </div>
   </form>
   <br/>
   <div id="items_box">
   <?php
    foreach($categories as $categorie)
    {?>
       <h4 class="categorie"><?php echo $categorie['NOM_CATEGORIE']; ?></h4>
       <br />
    <?php
    $req2 = $bdd->prepare('SELECT SHOP.ID_ITEM, ITEM, PRIX, DESCRIPTION, CHEMIN FROM SHOP INNER JOIN REPRESENTER ON REPRESENTER.ID_ITEM = SHOP.ID_ITEM INNER JOIN PHOTO ON REPRESENTER.ID_PHOTO = PHOTO.ID_PHOTO WHERE ID_CATEGORIE = ? ORDER BY PRIX');
    $req2->execute(array($categorie['ID_CATEGORIE']));
    $articles = $req2->fetchAll();
    
    foreach($articles as $article)
    {?>
     <div class="item">
        <a href="shop.php?id_item=<?php echo $article['ID_ITEM']; ?>"><h4><?php echo $article['ITEM']; ?></h4></a>
        <img class="center" src="photos/<?php echo $article['CHEMIN']; ?>" />
        <p>Prix : <?php echo $article['PRIX']; ?>€<br /><?php echo nl2br($article['DESCRIPTION']); ?></p>
     </div>
     <?php
     }
    }
    $req1->closeCursor();
    ?>
   <div>
   <script type="text/javascript" src="js/caroussel_shop.js"></script>
   <script type="text/javascript" src="js/ajax_shop.js"></script>
<?php
    }
    else if(isset($_GET['id_item']) AND !isset($_GET['admin']))
    {
      $id_item = (int)$_GET['id_item'];
      $req = $bdd->prepare('SELECT ITEM, PRIX, DESCRIPTION FROM SHOP WHERE ID_ITEM = ? AND ACTIF = 1');
      $req->execute(array($id_item));
      
      if(!$article = $req->fetch())
      {
        header('Location: shop.php');
      }
      else
      {
        $req_img = $bdd->prepare('SELECT CHEMIN FROM SHOP INNER JOIN REPRESENTER ON REPRESENTER.ID_ITEM = SHOP.ID_ITEM INNER JOIN PHOTO ON REPRESENTER.ID_PHOTO = PHOTO.ID_PHOTO WHERE SHOP.ID_ITEM = ? AND ACTIF = 1 ');
        $req_img->execute(array($id_item));
        $images = $req_img->fetchAll(); ?>
        <h2><?php echo $article['ITEM']; ?></h2>
        <p>Description : <?php echo nl2br($article['DESCRIPTION']); ?></p>
        <p>Prix : <?php echo $article['PRIX']; ?></p>
<?php
    foreach($images as $image)
    {?>
       <img class="center" src="photos/<?php echo $image['CHEMIN']; ?>" />
       <br/>
<?php
    }
    if(isset($_SESSION['id']))
    {?>
      <form method="post" action="basket.php">
        Mettre dans le panier<br />
        <input type="text" name="id_item" value="<?php echo $id_item; ?>" hidden/>
        <label for="nombre">Nombre : </label> <input type="number" name="nombre" id="nombre" /><br /><br />
        <input type="submit" value="Mettre dans mon panier" />
      </form>
    <?php
    }
    }
    }
    else if(isset($_GET['admin']))
    {
       if(!isset($_SESSION['droit']))
       {
         header('Location: shop.php'); 
       }
       else
       {
         if($_SESSION['droit'] == 2 OR $_SESSION['droit'] == 4)
         {?>
           <h4>Gestion articles</h4>
           <div id="item_admin_box">
             <?php
               $req = $bdd->query('SELECT ID_ITEM, ITEM, PRIX, DESCRIPTION, ACTIF, NOM_CATEGORIE FROM SHOP INNER JOIN CATEGORIE ON CATEGORIE.ID_CATEGORIE = SHOP.ID_CATEGORIE');
		  
	       if($req->columnCount() == 0)
	       {
		    echo '<p id="result_compte">Aucune item. Erreur BDD</p>';
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
		  while($donnees = $req->fetch())
		  {
		      echo '    <tr><th scope="row"> ' . $donnees['ID_ITEM'] . '</th><td>' . $donnees['ITEM']. '</td><td>' . $donnees['NOM_CATEGORIE']. '</td><td>' . $donnees['PRIX']. '</td><td>';
                      if($donnees['ACTIF'] == 1)
                      {
                        echo '<a href="#" onclick="supp_item(' . $donnees['ID_ITEM'] . '); return false;">Supprimer</a></td></tr>' ;
                      }
                      else
                      {
                        echo 'Article supprimé</td></tr>' ;
                      }
		   }
                   echo '</tbody></table>';
               }
	       $req->closeCursor();
             ?>
           </div>
           <br /><h4>Ajouter article</h4>
           <form id="form_add_item">
               <label for="nom_item">Nom : </label><input type="text" name="nom_item" id="nom_item"/><br />
	       <lablel for="cat_item">Categorie: </lablel>
       	       <select name="cat_item" id="cat_item" required>
	       <?php  
                 $req = $bdd->query('SELECT * FROM CATEGORIE');
                 $categories = $req->fetchAll();
 
		 foreach($categories as $categorie)
                 {
		    echo '<option value="' . $categorie['ID_CATEGORIE'] . '">' . $categorie['NOM_CATEGORIE'] . '</option>';
		 }
                 $req->closeCursor();
	       ?>
               </select><br />
               <label for="prix_item">Prix : </label><input type="number" name="prix_item" id="prix_item"/><br /><br />
               <label for="desc_item">Description : </label><br /><textarea name="desc_item" id="desc_item"/></textarea><br />
               <label for="image">Photo : </label><input type="file" name="image" id="image"/><br /><br />
	       <input id="form_add_submit_item" type="submit" value="Ajouter" /><br />
           </form>
           <br /><h4>Ajouter catégorie</h4>
           <form id="form_add_cat">
               <label for="nom_item">Nom : </label><input type="text" name="nom_item" id="nom_item"/><br />
	       <input id="form_add_submit_cat" type="submit" value="Ajouter" /><br />
           </form>
           <script>
              function supp_item(id_item) {
                var xhr = new XMLHttpRequest();
                var data = new FormData();

                data.append('item_id', id_item);

                xhr.onloadend = function() {
	        if(xhr.status == 200)
	        {
	          item_box.innerHTML = xhr.responseText;
	        }
	        else
	        {
	          alert('Erreur lors du filtrage : Erreur ' + xhr.status + '; ' + xhr.statusText);
	        } 
	        };
	        xhr.open('POST', 'ajax/admin_category.php');
	        xhr.send(data);
              }
              
              function addEvent(element, event, func) {
	        if(element.addEventListener)
		{
		  element.addEventListener(event, func, false);
		}
		else
		{
		  element.attachEvent('on' + event, func);
		}
	      }
	
	      var formulaire_add_item = document.getElementById('form_add_item');
              var formulaire_add_cat = document.getElementById('form_add_cat');
              var boutton_item = document.getElementById('form_add_submit_item');
              var boutton_cat = document.getElementById('form_add_submit_cat');
              
              var item_box = document.getElementById('item_admin_box');
              var cat_item_box = document.getElementById('cat_item');
	
	      addEvent(boutton_item, 'click', function(e){
	          e.preventDefault();
	  
	          var xhr = new XMLHttpRequest();
	          var data = new FormData(formulaire_add_item);
	  
	          xhr.onloadend = function() {
	           if(xhr.status == 200)
	           {
	             item_box.innerHTML = xhr.responseText;
                     formulaire_add_item.reset();
	           }
	           else
	           {
	             alert('Erreur ajout item : Erreur ' + xhr.status + '; ' + xhr.statusText);
	           } 
	      };
	      xhr.open('POST', 'ajax/admin_add_item.php');
	      xhr.send(data);
	      });

              addEvent(boutton_cat, 'click', function(e){
	          e.preventDefault();
	  
	          var xhr = new XMLHttpRequest();
	          var data = new FormData(formulaire_add_cat);
	  
	          xhr.onloadend = function() {
	           if(xhr.status == 200)
	           {
	             cat_item_box.innerHTML = xhr.responseText;
	           }
	           else
	           {
	             alert('Erreur ajout catégorie : Erreur ' + xhr.status + '; ' + xhr.statusText);
	           } 
	      };
	      xhr.open('POST', 'ajax/admin_add_cat.php');
	      xhr.send(data);
	      });
           </script>
         <?php 
         }
         else
         {
           header('Location: shop.php');
         }
       }
    }
    require('php/footer.php');
    ?>
</body>
</html>
