<?php require('header.php');

if(!isset($_SESSION['id']))
{
  header('Location: ../events.php');
}
else if(!isset($_POST['name']) OR !isset($_FILES['image']) OR !isset($_POST['desc']) OR !isset($_POST['date']))
{
  header('Location: ../events.php');
}
else
{
   $name = (string)htmlspecialchars($_POST['name']);
   $desc = (string)htmlspecialchars($_POST['desc']);
   $date = (string)htmlspecialchars($_POST['date']);
   
 
   if($name == '' OR $desc == '' OR !preg_match('#^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])(?:( [0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$#', $date))
   {?>
       <h1>Champ vide ou invalide</h1>
       <p>Des champs sont vides ou invalides<br />
   <?php
   }
   else
   { 
     if($_FILES['image']['error'] == 0)
     {
        if($_FILES['image']['size'] <= 5242880)
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

             $req = $bdd->prepare('INSERT INTO EVENEMENTS(EVENTS, E_DESCRIPTION, E_DATE, ID_REGION, ID_PHOTO) VALUES(?, ?, ?, ?, ?)');
             if($_SESSION['droit'] == 4 AND isset($_POST['region']) AND (int)$_POST['region'] > 0)
             {
               $req->execute(array($name, $desc, $date, (int)$_POST['region'], $id_img));
             } 
             else
             {
               $req->execute(array($name, $desc, $date, $_SESSION['id_region'], $id_img));
             }
             $req->closeCursor();

             header('Location: ../events.php?id_event=' . $bdd->lastInsertId());
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
?>
         <a href="../events.php?ajout=1">Retour</a></p>
	</body>
</html>
