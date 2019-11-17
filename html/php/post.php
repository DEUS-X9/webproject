<?php require('header.php');

if(!isset($_SESSION['id']))
{
  header('Location: ../events.php');
}
else if(!isset($_POST['e_id']))
{
  header('Location: ../events.php');
}
else
{
  $e_id = (int)$_POST['e_id'];
  if($e_id < 1)
  {
     header('Location: ../events.php');
  }
  else
  {
    $req = $bdd->prepare('SELECT ID_EVENTS FROM EVENEMENTS WHERE ID_EVENTS = ? AND E_DATE < NOW()');
    $req->execute(array($e_id));
    if(!$donnee = $req->fetch())
    {
      header('Location: ../events.php');
      $req->closeCursor();
    }
    else
    {
      $req_check = $bdd->prepare('SELECT ID_EVENTS FROM INSCRIRE WHERE ID_EVENTS = ? AND ID_MEMBRE = ?');
      $req_check->execute(array($e_id, $_SESSION['id']));

      if(!$check = $req_check->fetch())
      {
         $req_check->closeCursor();
         header('Location: ../events.php');
      } 
      else
      {
        $req_check->closeCursor();
        if(isset($_FILES['image']) AND $_FILES['image']['error'] == 0)
        {
          if ($_FILES['image']['size'] <= 5242880)
          {
                $infosfichier = pathinfo($_FILES['image']['name']);
                $extension_upload = $infosfichier['extension'];
                $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png');
                if(in_array($extension_upload, $extensions_autorisees))
                {
                   $nom_fichier = '' . time() . '.' . $infosfichier['extension'] . '';
                   move_uploaded_file($_FILES['image']['tmp_name'], '/home/webprojet/www/photos/' . $nom_fichier);
                   
                   $req = $bdd->prepare('INSERT INTO PHOTO(CHEMIN, ID_MEMBRE) VALUES(?, ?)');
                   $req->execute(array($nom_fichier, $_SESSION['id']));
                   $id_img = $bdd->lastInsertId();
                   $req->closeCursor();

                   $req = $bdd->prepare('INSERT INTO ILLUSTRER(ID_PHOTO, ID_EVENTS) VALUES(?, ?)');
                   $req->execute(array($id_img, $e_id));
                   $req->closeCursor();

                   header('Location: ../events.php?id_event=' . $e_id);
                }
                else
                {?>
                   <h1>Extension non autorisée</h1>
                   <p>Le fichier possède une extention non autorisée. Extention autorisé : jpg, jpeg, gif, png.<br />
                <?php   
                }
          }
          else
          {?>
             <h1>Fichier trop volumineux</h1>
             <p>Le fichier envoyé est trop gros. Taille max : 5 Mo<br />
           <?php   
          }
        }
        else
        {?>
          <h1>Erreur lors de l'upload</h1>
          <p>Une erreur s'est produite lors de l'upload de l'image. Veuillez contacter l'adminstrateur du site.<br />
        <?php  
        }
      }
    }
}
?>
         <a href="../events.php?id_event=<?php echo $e_id; ?>">Retour</a></p>
	</body>
</html>
