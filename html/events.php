<?php require 'php/header.php'; 
function month($month_p) {

  switch($month_p) {
     case 1:
        return 'janvier';
        break;
     case 2:
        return 'février';
        break;
     case 3:
        return 'mars';
        break;
     case 4:
        return 'avril';
        break;
     case 5:
        return 'mai';
        break;
     case 6:
        return 'juin';
        break;
     case 7:
        return 'juillet';
        break;
     case 8:
        return 'août';
        break;
     case 9:
        return 'septembre';
        break;
     case 10:
        return 'octobre';
        break;
     case 11:
        return 'novembre';
        break;
     case 12:
        return 'décembre';
        break;
  }
}
                if(!isset($_GET['id_event']) AND !isset($_GET['ajout']) AND !isset($_GET['id_image']))
                {?>
                    <h1>Evènements</h1>
                    <?php
                    if(isset($_SESSION['droit']) AND ($_SESSION['droit'] == 2 OR $_SESSION['droit'] == 4))
                    {?>
                       <h4 class="post_event"><a href="events.php?ajout=1">Poster un event</a></h4>
                    <?php
                    }?>
		    <p>Ces pages regroupent les différents évènements qui vous sont proposés et qui ont été proposés par le bureau des étudiants :</p>
                    <?php
                    if(!isset($_GET['page']))
                    {
                      if(!isset($_SESSION['id_region']) OR (isset($_SESSION['droit']) AND $_SESSION['droit'] >= 3))
                      {
                      	$req = $bdd->query('SELECT ID_EVENTS, EVENTS, E_DESCRIPTION, DAY(E_DATE) AS jour, MONTH(E_DATE) AS mois, YEAR(E_DATE) AS annee, CHEMIN, REGION FROM EVENEMENTS INNER JOIN PHOTO ON EVENEMENTS.ID_PHOTO = PHOTO.ID_PHOTO INNER JOIN REGION ON REGION.ID_REGION = EVENEMENTS.ID_REGION ORDER BY E_DATE DESC LIMIT 0, 5');
                      }
                      else
                      {
                        $req = $bdd->query('SELECT ID_EVENTS, EVENTS, E_DESCRIPTION, DAY(E_DATE) AS jour, MONTH(E_DATE) AS mois, YEAR(E_DATE) AS annee, CHEMIN FROM EVENEMENTS INNER JOIN PHOTO ON EVENEMENTS.ID_PHOTO = PHOTO.ID_PHOTO WHERE ID_REGION = ' . $_SESSION['id_region'] . ' ORDER BY E_DATE DESC LIMIT 0, 5');
                      }
                      $events = $req->fetchAll();
                      foreach($events as $event)
                      {
                      ?>
		        <article>
		           <a href="events.php?id_event=<?php echo $event['ID_EVENTS']; ?>"><h2 class="linkpost"> <?php echo $event['EVENTS']; ?> </h2></a>
			   <img class="center" src="photos/<?php echo $event['CHEMIN']; ?>">
		 	   <br/>
                           <span class="metadata">Le <?php echo $event['jour']; ?> <?php echo month($event['mois']); ?> <?php echo $event['annee']; ?></span><br />
                           <?php
                           if(isset($_SESSION['id']))
                           {
                              echo '<a class="linkpost" href="php/e_register.php?e_id=' . $event['ID_EVENTS'] .'">S\'inscrire</a><br />';
                           }

                           if(isset($_SESSION['droit']) AND $_SESSION['droit'] >= 3)
                           {
                              echo '<a class="linkpost metadata" href="php/notice.php?e_id=' . $event['ID_EVENTS'] .'">Signaler</a><br />';
                           }


                           if(!isset($_SESSION['id_region']) OR (isset($_SESSION['droit']) AND $_SESSION['droit'] >= 3))
                           { ?>
                           <span class="metadata">A <?php echo $event['REGION'] ?></span><br /><br />
                           <?php
                           }
                           ?>
			   <p><?php echo nl2br(htmlspecialchars($event['E_DESCRIPTION'])); ?>
			   </p>
		       </article>
                       <?php
                      }
                      $req->closeCursor();
                      
                      $req = $bdd->query('SELECT * FROM EVENEMENTS');
                      $donnee = $req->fetchAll();
                      $i = 0;
                      foreach($donnee as $event)
                      {
                        $i++;
                      }
                      $req->closeCursor();

                      $mod = $i%5;
                      $nbpage = ($i - $mod)/5;
                      $nbpage++;
                      
                      $j = 1;

                      while($j <= $nbpage)
                      {
                        if($j == 1)
                        {
                           echo '<p>Page : <a href="#">1</a> ';
                        }
                        else
                        {
                           echo '- <a href="events.php?page=' . $j . '">' . $j . '</a> ';
                        }
                        $j++;
                       }
                       echo '</p>';
                     }
                     else
                     {
                       $page = (int)$_GET['page'];
                       if($page <= 1)
                       {
                         header('Location: events.php');
                       }
                       else
                       {
                         $debut = ($page - 1) * 5;
                         $fin = $page * 5;

                         if(!isset($_SESSION['id_region']) OR (isset($_SESSION['droit']) AND $_SESSION['droit'] >= 3))
                         {
                      	   $req = $bdd->query('SELECT ID_EVENTS, EVENTS, E_DESCRIPTION, DAY(E_DATE) AS jour, MONTH(E_DATE) AS mois, YEAR(E_DATE) AS annee, CHEMIN, REGION FROM EVENEMENTS INNER JOIN PHOTO ON EVENEMENTS.ID_PHOTO = PHOTO.ID_PHOTO INNER JOIN REGION ON REGION.ID_REGION = EVENEMENTS.ID_REGION ORDER BY E_DATE DESC LIMIT ' . $debut . ', ' . $fin . '');
                         }
                         else
                         {
                           $req = $bdd->query('SELECT ID_EVENTS, EVENTS, E_DESCRIPTION, DAY(E_DATE) AS jour, MONTH(E_DATE) AS mois, YEAR(E_DATE) AS annee, CHEMIN FROM EVENEMENTS INNER JOIN PHOTO ON EVENEMENTS.ID_PHOTO = PHOTO.ID_PHOTO WHERE ID_REGION = ' . $_SESSION['id_region'] . ' ORDER BY E_DATE DESC LIMIT ' . $debut . ', ' . $fin . '');
                         }
                     
                         $events = $req->fetchAll();
    			 foreach($events as $event)
                         {
                         ?>
		           <article>
		              <a class="linkpost" href="events.php?id_event=<?php echo $event['ID_EVENTS']; ?>"><h2 class="linkpost" > <?php echo $event['EVENTS']; ?> </h2></a>
			      <img class="center" src="photos/<?php echo $event['CHEMIN']; ?>">
		 	      <br/>
                              <span class="metadata">Le <?php echo $event['jour']; ?> <?php echo month($event['mois']); ?> <?php echo $event['annee']; ?></span><br />
                              <?php
                               if(isset($_SESSION['droit']) AND $_SESSION['droit'] >= 3)
                               {
                                  echo '<a class="linkpost metadata" href="php/notice.php?e_id=' . $event['ID_EVENTS'] .'">Signaler</a><br />';
                               }

                               if(isset($_SESSION['id']))
                               {
                                  echo '<a class="linkpost" href="php/e_register.php?e_id=' . $event['ID_EVENTS'] .'">S\'inscrire</a><br />';
                               }

                               if(!isset($_SESSION['id_region']) OR (isset($_SESSION['droit']) AND $_SESSION['droit'] >= 3))
                               { ?>
                                 <span class="metadata">A <?php echo $event['REGION'] ?></span><br /><br />
                               <?php
                               }
                               ?>
			      <p><?php echo nl2br(htmlspecialchars($event['E_DESCRIPTION'])); ?></p>
		           </article>
                         <?php
                         }
                         $req->closeCursor();
                      
                         $req = $bdd->query('SELECT * FROM EVENEMENTS');
                         $donnee = $req->fetchAll();
                         $i = 0;
                         foreach($donnee as $event)
                         {
                           $i++;
                         }
                         $req->closeCursor();

                         $mod = $i%5;
                         $nbpage = ($i - $mod)/5;
                         $nbpage++;
                      
                         $j = 1;

                        while($j <= $nbpage)
                        {
                          if($j == 1)
                          {
                             echo '<p>Page : <a href="events.php">1</a> ';
                          }
                          else
                          {
                             echo '- <a href="events.php?page=' . $j . '">' . $j . '</a> ';
                          }
                          $j++;
                         }
                         echo '</p>';                  
                       } 
                    }                       
                 }
                 else if(isset($_GET['ajout']) AND !isset($_GET['id_event']) AND !isset($_GET['id_image']))
                 {
                    if(isset($_SESSION['droit']) AND ($_SESSION['droit'] == 2 OR $_SESSION['droit'] == 4))
                    {?> 
                      <h1>Poster un event</h1>
                       <form action="php/e_post.php" method="post">
                              <a href="events.php">Retour à la liste des events<a/>
                              <label for="name">Le nom de l'event:</label>
                              <input type="text" name="name" id="name" required/><br />
                              <label for="image">Votre photo :</label>
                              <input type="file" name="image" id="image" required/><br />
                              <label for="date">La date :</label>
                              <input type="date" name="date" id="date" required/><br />
                              <label for="date">La description de l'event :</label><br />
                              <textarea type="date" name="date" id="date" required></textarea><br />
                              <?php
                              if($_SESSION['droit'] == 4)
                              {
                                $req_region = $bdd->query("SELECT * FROM REGION");
                                $regions = $req_region->fetchAll();
                                ?>
                                <label for="region">Region :</label>
                                <select name="region" id="region" required>
                                <?php                  
		  		foreach($regions as $element)
                  		{
		     		  if($_SESSION['id_region'] == $element['ID_REGION'])
                     		  {
		     			echo '<option value="' . $element['ID_REGION'] . '" selected>' . $element['REGION'] . '</option>';
                     		  }
		     		  else
		     		  {
					echo '<option value="' . $element['ID_REGION'] . '">' . $element['REGION'] . '</option>';
		     		  }
		  		}
			       ?>
                               </select><br />
                              <?php  
                              }?>
                              <input type="submit" value="Poster" />
                       </form>
                    <?php 
                    }
                    else
                    {
                       header('Location: events.php');
                    }
                 }
                 else if(isset($_GET['id_event']) AND !isset($_GET['id_image']))
                 {
                   $e_id = (int)$_GET['id_event'];
                   if($e_id < 1)
                   {
                     header('Location: events.php');
                   }
                   else
                   {
                      $req = $bdd->prepare('SELECT ID_EVENTS, EVENTS, E_DESCRIPTION, EVENEMENTS.ID_PHOTO, DAY(E_DATE) AS jour, MONTH(E_DATE) AS mois, YEAR(E_DATE) AS annee, CHEMIN, EVENEMENTS.ID_REGION, REGION FROM EVENEMENTS INNER JOIN PHOTO ON EVENEMENTS.ID_PHOTO = PHOTO.ID_PHOTO INNER JOIN REGION ON REGION.ID_REGION = EVENEMENTS.ID_REGION WHERE ID_EVENTS = ?');
                      $req->execute(array($e_id));
                      if(!$donnee = $req->fetch())
                      {
                        header('Location: events.php');
                        $req->closeCursor();
                      }
                      else
                      {
                        if(isset($_SESSION['id_region']) AND $_SESSION['id_region'] != $donnee['ID_REGION'] AND $_SESSION['droit'] < 3)
                        {
                          header('Location: events.php');
                          $req->closeCursor();
                        }
                        else
                        {
                        ?>
			  <article>
                          <a class="linkpost" href="events.php">Retour à la liste des events<a/>
                          <h1><?php echo $donnee['EVENTS']; ?></h1>
                          <p>Le <?php echo $donnee['jour']; ?> <?php echo month($donnee['mois']); ?> <?php echo $donnee['annee']; ?></p>
                          <?php
                          if(isset($_SESSION['droit']) AND $_SESSION['droit'] >= 3)
                          {
                              echo '<a class="linkpost" href="php/notice.php?e_id=' . $donnee['ID_EVENTS'] .'">Signaler</a><br />';
                          }

                          if(isset($_SESSION['id']))
                          {
                              echo '<a class="linkpost" href="php/e_register.php?e_id=' . $donnee['ID_EVENTS'] .'">S\'inscrire</a><br />';
                          }

                          if(!isset($_SESSION['id_region']))
                          {?>
                          <span>A <?php echo $donnee['REGION'] ?></span><br /><br />
                          <?php
                          }
                          ?>
                          <p><?php echo nl2br(htmlspecialchars($donnee['E_DESCRIPTION'])); ?></p>
                          <a href="events.php?id_event=<?php echo $donnee['ID_EVENTS'];?>&id_image=<?php echo $donnee['ID_PHOTO'];?>"><img class="center" src="photos/<?php echo $donnee['CHEMIN']; ?>"></a>
                          <?php
                          $req2 = $bdd->prepare('SELECT COMMENTAIRE, NOM, PRENOM FROM COMMENTAIRE INNER JOIN MEMBRE ON MEMBRE.ID_MEMBRE = COMMENTAIRE.ID_MEMBRE WHERE ID_PHOTO = ? ORDER BY date DESC LIMIT 0, 1');
                          $req2->execute(array($donnee['ID_PHOTO']));
                          if(!$donnee2 = $req2->fetch())
                          {?>
                             <span>Pas de commentaire</span>
                          <?php
                          }
                          else
                          {?>
                            <p>Commentaires :<br /><div class="com"><div class="pseudo"> <?php echo $donnee2['NOM'] . ' ' . $donnee2['PRENOM'] . ' </div>: ' . $donnee2['COMMENTAIRE'];?></div></p>
                          <?php
                          }
                          $req2->closeCursor(); 
                          $req2 = $bdd->prepare('SELECT COUNT(*) AS nb FROM LIKES WHERE ID_PHOTO = ?');
                          $req2->execute(array($donnee['ID_PHOTO']));
                          $donnee2 = $req2->fetch();
                          ?>
                          <p>Like(s) : <?php echo $donnee2['nb']; ?>. <a href="#" id="Bphoto1" ><input type="image" alt="Like" src="images/love.png" height="45px"></a>
                          <?php
                          $req2->closeCursor(); 
                          $req->closeCursor();
                          $req = $bdd->prepare('SELECT ILLUSTRER.ID_PHOTO, CHEMIN, NOM, PRENOM FROM ILLUSTRER INNER JOIN PHOTO ON ILLUSTRER.ID_PHOTO = PHOTO.ID_PHOTO INNER JOIN MEMBRE ON MEMBRE.ID_MEMBRE = PHOTO.ID_MEMBRE WHERE ID_EVENTS = ? ORDER BY PHOTO.DATE DESC');
                          $req->execute(array($e_id));
                          $donnees = $req->fetchAll();
                          if($req->columnCount() == 0)
                          {?>
                             <p>Pas de photos postées pour cet event.</p>
                          <?php
                          }
                          else
                          {?>
                             <p>Photos postées de l'event :</p>
                             <?php
                              foreach($donnees as $image)
                              {?>
                                 <p>Posté par <?php echo $image['NOM'] . ' ' . $image['PRENOM']?></p>
                                 <a href="events.php?id_event=<?php echo $donnee['ID_EVENTS'];?>&id_image=<?php echo $image['ID_PHOTO'];?>"><img class="center" src="photos/<?php echo $image['CHEMIN']; ?>"></a>
                                 <?php
                                 $req2 = $bdd->prepare('SELECT COMMENTAIRE, NOM, PRENOM FROM COMMENTAIRE INNER JOIN MEMBRE ON MEMBRE.ID_MEMBRE = COMMENTAIRE.ID_MEMBRE WHERE ID_PHOTO = ? ORDER BY date DESC LIMIT 0, 1');
                                 $req2->execute(array($image['ID_PHOTO']));
                              
                                 if(!$donnee2 = $req2->fetch())
                                 {?>
                                   <p>Pas de commentaire</p>
                                 <?php
                                 }
                                 else
                                 {
                                    echo   ' <div class="com"><div class="pseudo"> '. $donnee2['NOM'] . ' ' . $donnee2['PRENOM'] . ' </div> : ' . $donnee2['COMMENTAIRE'] . '</div> ';
                                 }
                                 $req2->closeCursor(); 
                                 $req2 = $bdd->prepare('SELECT COUNT(*) AS nb FROM LIKES WHERE ID_PHOTO = ?');
                                 $req2->execute(array($donnee['ID_PHOTO']));
                                 $donnee2 = $req2->fetch();
                                 ?>
                                 <p>Like(s) : <?php echo $donnee2['nb']; ?>. <a href="#" id="Bphoto1" ><input type="image" alt="Like" src="images/love.png" height="45px"></a>
                                 <?php
                                 $req2->closeCursor();  
                              }
                          }
                          $req->closeCursor();

                          if(isset($_SESSION['id']))
                          {?>
                            <form action="php/post.php" method="post" enctype="multipart/form-data">
                              <h5>Poster votre photo</h5>
                              <input type=text name="e_id" value="<?php echo $e_id;?>" hidden />
                              <label for="image">Votre photo :</label>
                              <input type="file" name="image" id="image" required/><br />
                              <input type="submit" value="Poster" />
                            </form>
			  </article>
                        <?php
                         }
                        }
                     }
                   }
                 }
                 else if(isset($_GET['id_event']) AND isset($_GET['id_image']))
                 {
                    $e_id = (int)$_GET['id_event'];
                    $i_img = (int)$_GET['id_image'];

                    if($e_id < 1 OR $i_img < 1)
                    {
                      header('Location: events.php');
                    }
                    else
                    {
                      $req = $bdd->prepare('SELECT CHEMIN, EVENTS, NOM, PRENOM, DAY(DATE) AS jour, MONTH(DATE) AS mois, YEAR(DATE) AS annee  FROM PHOTO INNER JOIN ILLUSTRER ON PHOTO.ID_PHOTO = ILLUSTRER.ID_PHOTO INNER JOIN EVENEMENTS ON EVENEMENTS.ID_EVENTS = ILLUSTRER.ID_EVENTS INNER JOIN MEMBRE ON MEMBRE.ID_MEMBRE = PHOTO.ID_MEMBRE WHERE PHOTO.ID_PHOTO = ? AND EVENEMENTS.ID_EVENTS = ?');
                      $req->execute(array($i_img, $e_id));
                      if(!$donnee = $req->fetch())
                      {
                        $req->closeCursor();
                        $req = $bdd->prepare('SELECT CHEMIN, EVENTS, NOM, PRENOM, DAY(DATE) AS jour, MONTH(DATE) AS mois, YEAR(DATE) AS annee FROM PHOTO INNER JOIN EVENEMENTS ON EVENEMENTS.ID_PHOTO = PHOTO.ID_PHOTO INNER JOIN MEMBRE ON MEMBRE.ID_MEMBRE = PHOTO.ID_MEMBRE WHERE PHOTO.ID_PHOTO = ? AND EVENEMENTS.ID_EVENTS = ?');
                        $req->execute(array($i_img, $e_id));
                        if(!$donnee = $req->fetch())
                        {
                          header('Location: events.php');
                        }
                      }
                      ?>
                      <h1><?php echo $donnee['EVENTS'];?></h1>
                      <article>
                        <p><?php if(isset($_SESSION['droit']) AND $_SESSION['droit'] >= 3) { echo '<a href="php/notice.php?id_img=' . $i_img . '">[Signaler]</a> '; }?>Postée par <?php echo $donnee['NOM'] . ' ' . $donnee['PRENOM']?>, le <?php echo $donnee['jour']; ?> <?php echo month($donnee['mois']); ?> <?php echo $donnee['annee']; ?></p>
                        <p><?php echo '<a href="events.php?id_event=' . $e_id . '">Retour</a>';?></p>
                        <img class="center" src="photos/<?php echo $donnee['CHEMIN']; ?>" />
                        <?php 
                        if(isset($_SESSION['id']))
                        {?>
                          <input id="com" type="text" placeholder="Tapez votre commentaire" /><input id="com_send" type="button" value="Envoyer" />
                        <?php
                        }
                        $req2 = $bdd->prepare('SELECT ID_COMMENTAIRE, COMMENTAIRE, NOM, PRENOM, MINUTE(DATE) AS min, HOUR(DATE) AS heures, DAY(DATE) AS jour, MONTH(DATE) AS mois, YEAR(DATE) AS annee FROM COMMENTAIRE INNER JOIN MEMBRE ON MEMBRE.ID_MEMBRE = COMMENTAIRE.ID_MEMBRE WHERE ID_PHOTO = ? ORDER BY DATE DESC');
                        $req2->execute(array($i_img));
                        if(!$donnees = $req2->fetchAll())
                        {
                           echo '<p class="comment">Pas de commentaire</p>';
                        }
                        else
                        {
                           foreach($donnees as $donnee)
                           {?>
                             <p class="comment"><?php if(isset($_SESSION['droit']) AND $_SESSION['droit'] >= 3) { echo '<a href="php/notice.php?id_com=' . $donnee['ID_COMMENTAIRE'] . '">[Signaler]</a> '; }?>Le <?php echo $donnee['jour']; ?> <?php echo month($donnee['mois']); ?> <?php echo $donnee['annee']; ?> à <?php echo $donnee['heures']; ?>:<?php echo $donnee['min']; ?> par <?php echo $donnee['NOM'] . ' ' . $donnee['PRENOM']; ?> : <?php echo htmlspecialchars($donnee['COMMENTAIRE']);?></p>
                           <?php   
                           }
  	                }
                        ?>
                      <article>
                      <?php
                      $req->closeCursor();
                    }
                 }
                 else
                 {
                    header('Location: events.php');
                 }
             ?>
	</body>
</html>
