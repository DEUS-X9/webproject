<?php require('header.php');
if(!isset($_SESSION['id']))
{
  header('Location: ../events.php');
}
else if(!isset($_GET['e_id']))
{
  header('Location: ../events.php');
}
else
{
  $e_id = (int)$_GET['e_id'];
  if($e_id < 1)
  {
     header('Location: ../events.php');
  }
  else
  {
    $req = $bdd->prepare('SELECT ID_EVENTS, ID_REGION FROM EVENEMENTS WHERE ID_EVENTS = ? AND E_DATE > NOW()');
    $req->execute(array($e_id));
    if(!$donnee = $req->fetch())
    {
      header('Location: ../events.php');
      $req->closeCursor();
    }
    else
    {
       $req->closeCursor();
       
       if($_SESSION['id_region'] != $donnee['ID_REGION'] AND $_SESSION['droit'] != 4)
       {
         header('Location: ../events.php');
       }
       else
       {
         $req = $bdd->prepare('SELECT ID_EVENTS, ID_MEMBRE FROM INSCRIRE WHERE ID_EVENTS = ? AND ID_MEMBRE = ?');
         $req->execute(array($e_id, $_SESSION['id']));
       
         if(!$donnee = $req->fetch())
         {
            $req->closeCursor();
            $req = $bdd->prepare('INSERT INTO INSCRIRE(ID_EVENTS, ID_MEMBRE) VALUES(?, ?)');
            $req->execute(array($e_id, $_SESSION['id']));
            $req->closeCursor();
            header('Location: ../events.php?id_event=' . $e_id);
         }
         else
         {
            header('Location: ../events.php');
         }
      }
    }
  }
}
?>
