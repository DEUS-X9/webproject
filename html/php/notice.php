<?php
require('header.php');

function del_event($e_id, $bdd, $raison)
{
    if($e_id < 1)
    {
       header('Location: ../events.php');
    }
    else
    {
      $req = $bdd->prepare('SELECT ID_EVENTS, EVENTS, ID_PHOTO FROM EVENEMENTS WHERE ID_EVENTS = ?');
      $req->execute(array($e_id));
      if(!$donnee = $req->fetch())
      {
        header('Location: ../events.php');
        $req->closeCursor();
      }
      else
      {
        $req->closeCursor();
        $req = $bdd->prepare('SELECT ID_PHOTO FROM ILLUSTRER WHERE ID_EVENTS = ? LIMIT 0, 1');
        $req->execute(array($e_id));

        $req_reg = $bdd->prepare('DELETE FROM INSCRIRE WHERE ID_EVENTS = ?');
        $req_reg->execute(array($e_id));

        email('Event', $donnee['ID_EVENTS'], $raison, $bdd);

        if(!$donnee2 = $req->fetch())
        {
          $req2 = $bdd->prepare('DELETE FROM EVENEMENTS WHERE ID_EVENTS = ?');
          $req2->execute(array($e_id));
          
	  $req3 = $bdd->prepare('DELETE FROM COMMENTAIRE WHERE ID_PHOTO = ?');
          $req3->execute(array($donnee['ID_PHOTO']));

          $req4 = $bdd->prepare('DELETE FROM LIKES WHERE ID_PHOTO = ?');
          $req4->execute(array($donnee['ID_PHOTO']));
      
          $req5 = $bdd->prepare('DELETE FROM PHOTO WHERE ID_PHOTO = ?');
          $req5->execute(array($donnee['ID_PHOTO']));
          $req->closeCursor();
          $req2->closeCursor();
          $req3->closeCursor();
          $req4->closeCursor();
          $req5->closeCursor();
        }
        else
        {
           $req->closeCursor();
           $req = $bdd->prepare('SELECT ID_PHOTO FROM ILLUSTRER WHERE ID_EVENTS = ?');
           $req->execute(array($e_id));
           $photos = $req->fetchAll();

           $sup = $bdd->prepare('DELETE FROM ILLUSTRER WHERE ID_EVENTS = ?');
           $sup->execute(array($e_id));

           $req2 = $bdd->prepare('DELETE FROM EVENEMENTS WHERE ID_EVENTS = ?');
           $req2->execute(array($e_id));
           $req2->closeCursor();

           $req3 = $bdd->prepare('DELETE FROM COMMENTAIRE WHERE ID_PHOTO = ?');
           $req3->execute(array($donnee['ID_PHOTO']));
           $req3->closeCursor();
           
           $req4 = $bdd->prepare('DELETE FROM LIKES WHERE ID_PHOTO = ?');
           $req4->execute(array($donnee['ID_PHOTO']));
           $req4->closeCursor();
      
           $req5 = $bdd->prepare('DELETE FROM PHOTO WHERE ID_PHOTO = ?');
           $req5->execute(array($donnee['ID_PHOTO']));
           $req5->closeCursor();
           
           foreach($photos as $photo)
           {
              $req2 = $bdd->prepare('DELETE FROM COMMENTAIRE WHERE ID_PHOTO = ?');
              $req2->execute(array($photo['ID_PHOTO']));

              $req3 = $bdd->prepare('DELETE FROM PHOTO WHERE ID_PHOTO = ?');
              $req3->execute(array($photo['ID_PHOTO']));

              $req2->closeCursor();
              $req3->closeCursor();
           }
           $req->closeCursor();
        }
        header('Location: ../events.php');
      }
    }
}

function email($message_type, $id, $message, $bdd) {

   $adresses_mail = array();
   $objet = '';
   $mail = '';

   if($message_type == 'Event')
   {
     $req = $bdd->prepare("SELECT EVENTS, EVENEMENTS.ID_REGION, REGION FROM EVENEMENTS INNER JOIN REGION ON REGION.ID_REGION = EVENEMENTS.ID_REGION WHERE ID_EVENTS = ?");
     $req->execute(array($id));
     $donnee = $req->fetch();
     $mail = "Membre du BDE de " .  $donnee['REGION'] . ",\n\nSuite à un report d'un membre du personnel CESI ou de l'administrateur du site, l'événement nommé \"" . $donnee['EVENTS'] . "\" a été supprimé à " . date("H:i:s") . " le " . date("d/m/Y") . " pour la raison suivante :\n\"" . $message . "\".\n Pour tout complément d'information, veuillez adresser un mail à " . $_SESSION['mail'] . ".\n\nBonne réception.\n\nCeci est un mail généré automatiquement. Merci de ne pas répondre.";
     $objet = "Supression événement " . $donnee['EVENTS'];

     $req->closeCursor();
     $req = $bdd->prepare("SELECT MAIL FROM MEMBRE WHERE (ID_REGION = ? AND TYPE_UTILISATEUR = 2) OR TYPE_UTILISATEUR = 4");
     $req->execute(array($donnee['ID_REGION']));
     $adresses_mail = $req->fetchAll();
     $req->closeCursor();
   }
   else if($message_type == 'Image')
   {
     $req = $bdd->prepare("SELECT EVENTS, EVENEMENTS.ID_REGION, REGION FROM EVENEMENTS INNER JOIN REGION ON REGION.ID_REGION = EVENEMENTS.ID_REGION WHERE ID_EVENTS = ?");
     $req->execute(array($id));
     $donnee = $req->fetch();
     $mail = "Membre du BDE de " .  $donnee['REGION'] . ",\n\nSuite à un report d'un membre du personnel CESI ou de l'administrateur du site, une image associée à l'événement nommé \"" . $donnee['EVENTS'] . "\" a été supprimé à " . date("H:i:s") . " le " . date("d/m/Y") . " pour la raison suivante :\n\"" . $message . "\".\n Pour tout complément d'information, veuillez adresser un mail à " . $_SESSION['mail'] . ".\n\nBonne réception.\n\nCeci est un mail généré automatiquement. Merci de ne pas répondre.";
     $objet = "Supression image associé à l'événement " . $donnee['EVENTS'];

     $req->closeCursor();
     $req = $bdd->prepare("SELECT MAIL FROM MEMBRE WHERE (ID_REGION = ? AND TYPE_UTILISATEUR = 2) OR TYPE_UTILISATEUR = 4");
     $req->execute(array($donnee['ID_REGION']));
     $adresses_mail = $req->fetchAll();
     $req->closeCursor();
   }
   else
   {
     $req = $bdd->prepare("SELECT COMMENTAIRE, EVENTS, EVENEMENTS.ID_REGION, REGION FROM COMMENTAIRE INNER JOIN ILLUSTRER ON ILLUSTRER.ID_PHOTO = COMMENTAIRE.ID_PHOTO INNER JOIN EVENEMENTS ON EVENEMENTS.ID_EVENTS = ILLUSTRER.ID_EVENTS INNER JOIN REGION ON REGION.ID_REGION = EVENEMENTS.ID_REGION WHERE ID_COMMENTAIRE = ?");
     $req->execute(array($id));

     if(!$donnee = $req->fetch())
     {
       $req->closeCursor();
       $req = $bdd->prepare("SELECT COMMENTAIRE, EVENTS, EVENEMENTS.ID_REGION, REGION FROM COMMENTAIRE INNER JOIN EVENEMENTS ON EVENEMENTS.ID_PHOTO = COMMENTAIRE.ID_PHOTO INNER JOIN REGION ON REGION.ID_REGION = EVENEMENTS.ID_REGION WHERE ID_COMMENTAIRE = ?");
       $req->execute(array($id));
       $donnee = $req->fetch();
     }

     $mail = "Membre du BDE de " .  $donnee['REGION'] . ",\n\nSuite à un report d'un membre du personnel CESI ou de l'administrateur du site, le commentaire \"" . $donnee['COMMENTAIRE'] . "\" associé à l'événement nommé \"" . $donnee['EVENTS'] . "\" a été supprimé à " . date("H:i:s") . " le " . date("d/m/Y") . " pour la raison suivante :\n\"" . $message . "\".\n Pour tout complément d'information, veuillez adresser un mail à " . $_SESSION['mail'] . ".\n\nBonne réception.\n\nCeci est un mail généré automatiquement. Merci de ne pas répondre.";
     $objet = "Supression commentaire associé à l'événement " . $donnee['EVENTS'];

     $req->closeCursor();
     $req = $bdd->prepare("SELECT MAIL FROM MEMBRE WHERE (ID_REGION = ? AND TYPE_UTILISATEUR = 2) OR TYPE_UTILISATEUR = 4");
     $req->execute(array($donnee['ID_REGION']));
     $adresses_mail = $req->fetchAll();
     $req->closeCursor(); 
   }
   
   $mail_html = nl2br($mail);

   $boundary = "-----=".md5(rand());

   //Création du header de l'e-mail.
   $header = "From: \"Site BDE CESI\"<web@webfacile76.fr>\n";
   $header .= "Reply-to: \"Site BDE CESI\" <web@webfacile76.fr>\n";
   $header .= "MIME-Version: 1.0\n";
   $header .= "Content-Type: multipart/alternative;\n boundary=\"$boundary\"\n";
                          
  //Définition du message
  $message = "\n--" . $boundary . "\n";
  $message .= "Content-Type: text/plain; charset=\"utf8\"\n";
  $message .= "Content-Transfer-Encoding: 8bit\n";
  $message .= "\n" . $mail . "\n";
  $message .= "\n--" . $boundary . "\n";
  $message .= "Content-Type: text/html; charset=\"utf8\"\n";
  $message .= "Content-Transfer-Encoding: 8bit\n";
  $message .= "\n" . $mail_html . "\n";
  $message .= "\n--" . $boundary . "--\n";
  $message .= "\n--" . $boundary . "--\n";
 
  foreach($adresses_mail as $adresse_mail)
  {
    mail($adresse_mail['MAIL'], $objet, $message, $header, NULL);
  }   
}

if(!isset($_SESSION['id']))
{
  header('Location: ../events.php');
}
else if($_SESSION['droit'] < 3)
{
  die('Wtf');
  header('Location: ../events.php');
}
else if(empty($_GET) && empty($_POST))
{
  header('Location: ../events.php');
}
else if(empty($_POST))
{
  if(isset($_GET['e_id']))
  {
    $e_id = (int)$_GET['e_id'];
    if($e_id < 1)
    {
       header('Location: ../events.php');
    }
    else
    {
      $req = $bdd->prepare('SELECT ID_EVENTS, EVENTS FROM EVENEMENTS WHERE ID_EVENTS = ?');
      $req->execute(array($e_id));
      if(!$donnee = $req->fetch())
      {
        header('Location: ../events.php');
        $req->closeCursor();
      }
      else
      {
        $req->closeCursor();
      ?>
        <h1>Signalement d'event</h1>
        <form action="notice.php?e_id=<?php echo $donnee['ID_EVENTS']; ?>" method="post">
          <input type="text" name="e_id" value="<?php echo $donnee['ID_EVENTS']; ?>" hidden required/>
          <span>Pourquoi voulez signaler l'event "<?php echo $donnee['EVENTS']; ?>" ?</span><br />
      <?php
      }
    }  
  }
  else if(isset($_GET['id_img']))
  {
    $id_img = (int)$_GET['id_img'];
    if($id_img < 1)
    {
       header('Location: ../events.php');
    }
    else
    {
      $req = $bdd->prepare('SELECT ID_PHOTO, CHEMIN FROM PHOTO WHERE ID_PHOTO = ?');
      $req->execute(array($id_img));
      if(!$donnee = $req->fetch())
      {
        header('Location: ../events.php');
        $req->closeCursor();
      }
      else
      {
        $req->closeCursor();
        $req2 = $bdd->prepare('SELECT EVENTS FROM EVENEMENTS WHERE ID_PHOTO = ?');
        $req2->execute(array($id_img));
        ?>
        <h1>Signalement de photo</h1>
        <form action="notice.php?id_img=<?php echo $donnee['ID_PHOTO']; ?>" method="post">
          <?php
          if($donnee2 = $req2->fetch())
          {?>
            <span class="warning">La signalement de cette photo supprimera l'event associé</span><br />
          <?php
          }?>
          <input type="text" name="id_img" value="<?php echo $donnee['ID_PHOTO']; ?>" hidden required/>
          <span>Pourquoi voulez signaler l'image suivante ?</span><br />
          <img src="../photos/<?php echo $donnee['CHEMIN']; ?>" /><br />
      <?php
      } 
    } 
  }
  else if(isset($_GET['id_com']))
  {
    $id_com = (int)$_GET['id_com'];
    if($id_com < 1)
    {
       header('Location: ../events.php');
    }
    else
    {
      $req = $bdd->prepare('SELECT ID_COMMENTAIRE, COMMENTAIRE FROM COMMENTAIRE WHERE ID_COMMENTAIRE = ?');
      $req->execute(array($id_com));
      if(!$donnee = $req->fetch())
      {
        header('Location: ../events.php');
        $req->closeCursor();
      }
      else
      {
        $req->closeCursor();
        ?>
        <h1>Signalement de commentaire</h1>
        <form action="notice.php" method="post">
           <input type="text" name="id_com" value="<?php echo $donnee['ID_COMMENTAIRE']; ?>" hidden required/>
           <span>Pourquoi voulez signaler le commentaire suivant : "<?php echo $donnee['COMMENTAIRE']?>"?</span><br />
      <?php
      }
    } 
  } 
}
else if(!empty($_POST) AND isset($_POST))
{
  if(!isset($_POST['raison']))
  {
     header('Location: ../events.php');
  }
  else
  {
    $raison = htmlspecialchars($_POST['raison']);
  }

  if(isset($_POST['e_id']))
  {
    $e_id = (int)$_POST['e_id'];
    
    del_event($e_id, $bdd, $raison); 
  }
  else if(isset($_POST['id_img']))
  {
    $id_img = (int)$_POST['id_img'];
    if($id_img < 1)
    {
       header('Location: ../events.php');
    }
    else
    {
      $req = $bdd->prepare('SELECT ID_PHOTO FROM PHOTO WHERE ID_PHOTO = ?');
      $req->execute(array($id_img));
      if(!$donnee = $req->fetch())
      {
        header('Location: ../events.php');
        $req->closeCursor();
      }
      else
      {
        $req->closeCursor();
        $req2 = $bdd->prepare('SELECT ID_EVENTS FROM EVENEMENTS WHERE ID_PHOTO = ?');
        $req2->execute(array($id_img));
        if(!$donnee2 = $req2->fetch())
        {
           $req2->closeCursor();

           $req = $bdd->prepare('SELECT ID_EVENTS, PHOTO.ID_PHOTO FROM PHOTO INNER JOIN ILLUSTRER ON ILLUSTRER.ID_PHOTO = PHOTO.ID_PHOTO WHERE PHOTO.ID_PHOTO = ?');
           $req->execute(array($id_img));
           $donnee = $req->fetch();
           
           email('Image', $donnee['ID_EVENTS'], $raison, $bdd);

           $req2 = $bdd->prepare('DELETE FROM COMMENTAIRE WHERE ID_PHOTO = ?');
           $req2->execute(array($donnee['ID_PHOTO']));
           $req2->closeCursor();

           $req3 = $bdd->prepare('DELETE FROM LIKES WHERE ID_PHOTO = ?');
           $req3->execute(array($donnee['ID_PHOTO']));
           $req3->closeCursor();

           $req3 = $bdd->prepare('DELETE FROM ILLUSTRER WHERE ID_PHOTO = ?');
           $req3->execute(array($donnee['ID_PHOTO']));
           $req3->closeCursor();
      
           $req4 = $bdd->prepare('DELETE FROM PHOTO WHERE ID_PHOTO = ?');
           $req4->execute(array($donnee['ID_PHOTO']));
           $req4->closeCursor();

           header('Location: ../events.php?id_event=' . $donnee['ID_EVENTS']);
        }
        else
        {
          del_event($donnee2['ID_EVENTS'], $bdd, $raison);
        }
        $req2->closeCursor();
        $req->closeCursor();
      } 
    } 
  }
  else if(isset($_POST['id_com']))
  {
    $id_com = (int)$_POST['id_com'];
    if($id_com < 1)
    {
       header('Location: ../events.php');
    }
    else
    {
      $req = $bdd->prepare('SELECT ID_COMMENTAIRE FROM COMMENTAIRE WHERE ID_COMMENTAIRE = ?');
      $req->execute(array($id_com));
      if(!$donnee = $req->fetch())
      {
        header('Location: ../events.php');
        $req->closeCursor();
      }
      else
      {
        $req->closeCursor();
        $req = $bdd->prepare('SELECT ID_COMMENTAIRE, ID_EVENTS, COMMENTAIRE.ID_PHOTO FROM COMMENTAIRE INNER JOIN ILLUSTRER ON ILLUSTRER.ID_PHOTO = COMMENTAIRE.ID_PHOTO WHERE ID_COMMENTAIRE = ?');
        $req->execute(array($id_com));

        if(!$donnee = $req->fetch())
        {
          $req->closeCursor();
          $req = $bdd->prepare('SELECT ID_COMMENTAIRE, ID_EVENTS, COMMENTAIRE.ID_PHOTO FROM COMMENTAIRE INNER JOIN EVENEMENTS ON EVENEMENTS.ID_PHOTO = COMMENTAIRE.ID_PHOTO WHERE ID_COMMENTAIRE = ?');
          $req->execute(array($id_com));
          $donnee = $req->fetch();
        }

        email('Commentaire', $donnee['ID_COMMENTAIRE'], $raison, $bdd);
        $req->closeCursor();
        $req = $bdd->prepare('DELETE FROM COMMENTAIRE WHERE ID_COMMENTAIRE = ?');
        $req->execute(array($id_com));

        header('Location: ../events.php?id_event=' . $donnee['ID_EVENTS'] . '&id_image=' . $donnee['ID_PHOTO']);
      }
    } 
  }
  else
  {
    header('Location: ../events.php');
  }
}
else
{
  header('Location: ../events.php');
}
?>
     <label for="raison">Votre raison :</label><br />
     <textarea name="raison" id="raison" required></textarea><br />
     <input type="submit" value="Signaler" />
    </form>
    <p><a href="../events.php">Retour</a></p>
  </body>
</html>
