<?php 
require 'php/header.php';
if(!isset($_SESSION['id']))
{
  header('Location: index.php');
}
else if($_SESSION['droit'] != 2)
{
  header('Location: index.php');
} 

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

if(isset($_POST['nom']) AND isset($_POST['fnom']) AND isset($_POST['email']) AND isset($_POST['region']) AND isset($_POST['fonction']))
{
     $nom = htmlspecialchars((string)$_POST['nom']);
     $fnom = htmlspecialchars((string)$_POST['fnom']);
     $email = htmlspecialchars((string)$_POST['email']);
     $password = htmlspecialchars((string)$_POST['password']);
     $cpassword = htmlspecialchars((string)$_POST['cpassword']);
     $selectedRegion = (int) $_POST['region'];

     try
     {
        if($nom == '' OR $fnom == '' OR $email == '' OR $password == '' OR $cpassword == '')
        {
           $id = "empty";
        }
        else if(strlen($password) < 6 OR !preg_match("#[A-Z0-9]{1,}#", $password))
        {
          $id = "iPass";
        }
	else if(!test_reg($selectedRegion, $regions))
	{
	  $id = "region";
	}
	else if(!isset($_POST['rgpd']))
        {
          $id = "rgpd";
        }
        else
        {
          if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email))
          {
             if($password == $cpassword)
             {

               $password = md5($password . 'fuizyehcdbskuyfz!e');

               $req = $bdd->prepare('SELECT * FROM MEMBRE WHERE MAIL = ?');
               $req->execute(array($email));

               if(!$donnees = $req->fetch())
               {
                 $req2 = $bdd->prepare('INSERT INTO MEMBRE(NOM, PRENOM, MAIL, PASSWORD, ID_REGION) VALUES(:nom, :prenom, :mail, :password, :id_region)');
                 $req2->execute(array(
                 'nom' => $nom,
                 'prenom' => $fnom,
                 'mail' => $email,
                 'password'=> $password,
                 'id_region' => $selectedRegion));
		
		 $_SESSION['prem'] = false;
                 header('Location: login.php');
               }
               else
               {
                  $id = "users";
               }
               $req->closeCursor();
             }
             else
             {
                $id = "pass";
             }
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
      			  <th scope="col">Rõle</th>
      			  <th scope="col">Gérer</th>
    			</tr>
  		  </thead>
                  <tbody>
                 <?php
		    while($donnees = $req->fetch())
		    {
			echo '    <tr><th scope="row"> ' . $donnees['ID_MEMBRE'] . '</th><td>' . $donnees['MAIL']. '</td><td>' . $donnees['NOM']. '</td><td>' . $donnees['PRENOM']. '</td><td>' . $donnees['ID_REGION']. '</td><td>' . $donnees['TYPE_UTILISATEUR']. '</td><td><a href="bde.php?compte=" ' . $donnees['MAIL'] . ' ">Gérer</a></td></tr>' ;
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
		  else
		  {
		    $donnee = $req->fetch();
                ?>
		  <form method="post">
			<h3>Compte <?php echo $compte ?></h3><br />
			<label for="name">Nom : </label>
			<input type="text" name="nom" id="nom" value="<?php echo $donnee['NOM']; ?>" required/><br />
			<label for="fname">Prenom : </label>
			<input type="text" name="fnom" id="fnom" value="<?php echo $donnee['PRENOM']; ?>" required/><br />
			<label for="email">Mail : </label>
			<input type="email" name="email" id="email" value="<?php echo $donnee['MAIL']; ?>" required/><br />
                        <lablel for="region">Région : </lablel>
       			<select name="region" id="region" required>
			<?php  
                         $req_region = $bdd->query("SELECT * FROM REGION");
			 $regions = $req_region->fetchAll(); 
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
                         $req_droit = $bdd->query("SELECT * FROM TYPE_UTILISATEUR");
			 $droits = $req_droit->fetchAll(); 
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
		       <input type="submit" value="Modifier" />
                  </form>
               <?php
		 }
               }
	       $req->closeCursor();
               ?>
	  
	  
	    </tbody>
	  </table>
	</body>
</html>
