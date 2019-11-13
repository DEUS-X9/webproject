<?php 
require 'php/header.php';
function test_reg($arg1, $arg2)
{
  foreach($arg2 as $element)
  {
    if($element['ID_REGION'] == $arg1)
    {
      return true;
    }
  }

  return false;
}

function test_reg2($arg1, $arg2)
{
  foreach($arg2 as $element)
  {
    if($element['TYPE_UTILISATEUR'] == $arg1)
    {
      return true;
    }
  }

  return false;
}

$req_region = $bdd->query("SELECT * FROM REGION");
$regions = $req_region->fetchAll(); 
$req_droit = $bdd->query("SELECT * FROM TYPE_UTILISATEUR");
$droits = $req_droit->fetchAll(); 
$id = "";

if(!isset($_SESSION['id']))
{
  header('Location: index.php');
}
else if($_SESSION['droit'] != 2)
{
  header('Location: index.php');
} 
else if(isset($_POST['nom']) AND isset($_POST['fnom']) AND isset($_POST['email']) AND isset($_POST['region']) AND isset($_POST['fonction']) AND isset($_GET['compte']))
{
     $nom = htmlspecialchars((string)$_POST['nom']);
     $fnom = htmlspecialchars((string)$_POST['fnom']);
     $email = htmlspecialchars((string)$_POST['email']);
     $selectedRegion = (int)$_POST['region'];
     $selectedFct = (int)$_POST['fonction'];
     $user = (int)$_GET['compte'];

     try
     {
        if($nom == '' OR $fnom == '' OR $email == '' OR $selectedRegion == '' OR $selectedFct == '')
        {
           $id = "empty";
        }
	else if(!test_reg($selectedRegion, $regions))
	{
	  $id = "region";
	}
        else if(!test_reg2($selectedFct, $droits))
	{
	  $id = "fonction";
	}
        else
        {
          if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email))
          {

            $req = $bdd->prepare('SELECT * FROM MEMBRE WHERE ID_MEMBRE = ?');
            $req->execute(array($user));

               if($donnees = $req->fetch())
               {
                 $req2 = $bdd->prepare('UPDATE MEMBRE SET NOM = ?, PRENOM = ?, MAIL = ?, ID_REGION = ?, TYPE_UTILISATEUR = ? WHERE ID_MEMBRE = ?');
                 $req2->execute(array($nom, $fnom, $email, $selectedRegion, $selectedFct, $donnees['ID_MEMBRE']));
               }
               else
               {
                  $id = "users";
               }
               $req->closeCursor();
          }
          else
          {
            $id = "email";
          }
        }
     }
     catch(Exception $e)
     {
        die('Erreur : ' . $e->getMessage());
     }
}
else if(isset($_GET['compte']) AND isset($_GET['delete']))
{
  $user_del = (int)$_GET['compte'];
  $req = $bdd->prepare('SELECT * FROM MEMBRE WHERE ID_MEMBRE = ?');
  $req->execute(array($user_del));

  if($req->columnCount() != 0)
  {
    $req2 = $bdd->prepare('UPDATE MEMBRE SET actif = 0 WHERE ID_MEMBRE = ?');
    $req2->execute(array($user_del));
  }

  header('Location: bde.php');
}
?>
		<h1>Page d'administration</h1>

		<?php 
		if(!isset($_GET['compte']))
		{
		  $req = $bdd->query('SELECT * FROM MEMBRE');
		  
		  if($req->columnCount() == 0)
		  {
		    echo '<p id="result_compte">Aucune compte actif. Erreur BDD</p>';
		  }
		  else
		  { ?>
                  <table class="table table-striped table-dark">
  		  <thead>
    			<tr>
      			  <th scope="col">ID</th>
      			  <th scope="col">Compte</th>
			  <th scope="col">Nom</th>
      		          <th scope="col">Prénom</th>
      			  <th scope="col">Centre</th>
      			  <th scope="col">Rôle</th>
      			  <th scope="col">Gérer</th>
    			</tr>
  		  </thead>
                  <tbody>
                 <?php
		    while($donnees = $req->fetch())
		    {
                        if($donnees['actif'] == 1)
			{
				echo '    <tr><th scope="row"> ' . $donnees['ID_MEMBRE'] . '</th><td>' . $donnees['MAIL']. '</td><td>' . $donnees['NOM']. '</td><td>' . $donnees['PRENOM']. '</td><td>' . $donnees['ID_REGION']. '</td><td>' . $donnees['TYPE_UTILISATEUR']. '</td><td><a href="bde.php?compte=' . $donnees['ID_MEMBRE'] . '">Gérer</a></td></tr>' ;
                        }
		    }
                    echo '</tbody>';
                  }
		  $req->closeCursor();
		  
		}
		else
		{?>
		  <p><a href="bde.php">Retour</a></p><br />
		<?php
		  $compte = (int)htmlspecialchars($_GET['compte']);
		  $req = $bdd->prepare('SELECT * FROM MEMBRE WHERE ID_MEMBRE = ?');
		  $req->execute(array($compte));

                  if($req->columnCount() == 0)
		  {
		    echo '<p>Compte invalide</p>';
		  }
		  else if($donnee = $req->fetch() AND $donnee['actif'] == 1)
		  {
                ?>
		  <form method="post">
			<h3>Compte <?php echo $compte ?></h3><br />
                        <span class="error">
			<?php
        		if($id == "empty") { echo "Un ou plusieurs champ(s) sont vide(s)"; } else if($id == "email"){ echo "Email incorrecte!"; } else if($id == "users") { echo "Utilisateur invalide"; } else if($id == "region") { echo "Region invalide"; } else if($id == "fonction") { echo "Fonction invalide"; }
			?>
       	 		</span><br />

			<label for="name">Nom : </label>
			<input type="text" name="nom" id="nom" value="<?php echo $donnee['NOM']; ?>" required/><br />
			<label for="fname">Prenom : </label>
			<input type="text" name="fnom" id="fnom" value="<?php echo $donnee['PRENOM']; ?>" required/><br />
			<label for="email">Mail : </label>
			<input type="email" name="email" id="email" value="<?php echo $donnee['MAIL']; ?>" required/><br />
                        <lablel for="region">Région : </lablel>
       			<select name="region" id="region" required>
			<?php  
		 	 foreach($regions as $element)
                  	 {
		     	  if($donnee['ID_REGION'] == $element['ID_REGION'])
                     	  {
		       		echo '<option value="' . $element['ID_REGION'] . '" selected>' . $element['REGION'] . '</option>';
                     	  }
		     	  else
		     	  {
		     		echo '<option value="' . $element['ID_REGION'] . '">' . $element['REGION'] . '</option>';
		     	  }
		         }
                        $req_region->closeCursor();
		       ?>
		       </select><br />
		       <lablel for="fonction">Fonction : </lablel>
       		       <select name="fonction" id="fonction" required>
		       <?php  
		 	 foreach($droits as $element)
                  	 {
		     	  if($donnee['TYPE_UTILISATEUR'] == $element['TYPE_UTILISATEUR'])
                     	  {
		       		echo '<option value="' . $element['TYPE_UTILISATEUR'] . '" selected>' . $element['NOM_TYPE'] . '</option>';
                     	  }
		     	  else
		     	  {
		     		echo '<option value="' . $element['TYPE_UTILISATEUR'] . '">' . $element['NOM_TYPE'] . '</option>';
		     	  }
		         }
                        $req_droit->closeCursor();
		       ?>
                       </select><br /><br />
		       <input type="submit" value="Modifier" /><br /><br />
                       <a href="bde.php?compte=<?php echo $compte; ?>&delete=1" class="warning">Supprimer le compte (définitif)</a>
                  </form>
               <?php
		 }
                 else
                 {
                    echo '<p>Compte désactivé</p>';
	         }
               }
	       $req->closeCursor();
               $req_region->closeCursor();
               $req_droit->closeCursor();
               ?>
	  
	  
	    </tbody>
	  </table>
	</body>
</html>
