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

        if(!$donnee2 = $req->fetch())
        {
          $req2 = $bdd->prepare('DELETE FROM EVENEMENTS WHERE ID_EVENTS = ?');
          $req2->execute(array($e_id));
          
	  $req3 = $bdd->prepare('DELETE FROM COMMENTAIRE WHERE ID_PHOTO = ?');
          $req3->execute(array($donnee['ID_PHOTO']));
      
          $req4 = $bdd->prepare('DELETE FROM PHOTO WHERE ID_PHOTO = ?');
          $req4->execute(array($donnee['ID_PHOTO']));
          $req->closeCursor();
          $req2->closeCursor();
          $req3->closeCursor();
          $req4->closeCursor();
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
      
           $req4 = $bdd->prepare('DELETE FROM PHOTO WHERE ID_PHOTO = ?');
           $req4->execute(array($donnee['ID_PHOTO']));
           $req4->closeCursor();
           
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
        email('Event', $donnee['EVENTS'], $raison);
        header('Location: ../events.php');
      }
    }
}

function email($message_type, $name, $message){

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
    
    del_event($e_id, $bdd); 
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

           $req2 = $bdd->prepare('DELETE FROM COMMENTAIRE WHERE ID_PHOTO = ?');
           $req2->execute(array($donnee['ID_PHOTO']));
           $req2->closeCursor();

           $req3 = $bdd->prepare('DELETE FROM ILLUSTRER WHERE ID_PHOTO = ?');
           $req3->execute(array($donnee['ID_PHOTO']));
           $req3->closeCursor();
      
           $req4 = $bdd->prepare('DELETE FROM PHOTO WHERE ID_PHOTO = ?');
           $req4->execute(array($donnee['ID_PHOTO']));
           $req4->closeCursor();

           header('Location: ../events.php?id_event=' . $donnee['ID_EVENTS']);
           email('Image', $donnee['ID_EVENTS'], $raison);
        }
        else
        {
          del_event($donnee2['ID_EVENTS'], $bdd);
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

        $req->closeCursor();
        $req = $bdd->prepare('DELETE FROM COMMENTAIRE WHERE ID_COMMENTAIRE = ?');
        $req->execute(array($id_com));

        header('Location: ../events.php?id_event=' . $donnee['ID_EVENTS'] . '&id_image=' . $donnee['ID_PHOTO']);
        email('Image', $donnee['ID_EVENTS'], $raison);
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
