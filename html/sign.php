<?php require 'php/header.php';

if(isset($_SESSION['id']))
{
  header('Location: index.php');
}

try
{
	$req_region = $bdd->query("SELECT * FROM REGION");
	$regions = $req_region->fetchAll();
}
catch(Exception $e)
{
	die('Erreur : ' . $e->getMessage());
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

if(isset($_POST['nom']) AND isset($_POST['fnom']) AND isset($_POST['email']) AND isset($_POST['password']) AND isset($_POST['cpassword']) AND isset($_POST['region']))
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
                 'nom' => strtoupper($nom),
                 'prenom' => ucfirst($fnom),
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
  else
  {
    $id = "empty";
  }
?>

		<h1>Inscription</h1>
		
		<form method="POST">
		 <span class="error">
		<?php
		if(isset($_SESSION['prem']) AND $_SESSION['prem'] == true)
		{

        		if($id == "empty") { echo "Un ou plusieurs champ(s) sont vide(s)"; } else if($id == "pass") { echo "Mot de passe différent !"; } else if($id == "email"){ echo "Email incorrecte!"; } else if($id == "rgpd"){ echo "Vous devez accepter l'utilisation de vos données personnelles"; } else if($id == "iPass") { echo "Mot de passe inférieur à 6 caractères. Il doit contenir au moins une majuscule et un chiffre"; } else if($id == "users") { echo "Utilisateur déjà existant"; } else if($id == "region") { echo "Region invalide"; }
		}
		else
		{
		  $_SESSION['prem'] = true;
		}?>
        </span><br />

           	 <label for="nom">Votre nom : </label>
	    	<input type="text" name="nom" id="nom" placeholder="Durand" <?php if(isset($_SESSION['prem']) AND $_SESSION['prem'] == true AND isset($_POST['nom'])) { echo 'value="' . $_POST['nom'] . '"';} ?>  required autofocus/><br />
           	 <label for="fnom">Votre prénom : </label>
	   	 <input type="text" name="fnom" id="fnom" placeholder="Hubert" <?php if(isset($_SESSION['prem']) AND $_SESSION['prem'] == true AND isset($_POST['fnom'])) { echo 'value="' . $_POST['fnom'] . '"';} ?> required/><br />
           	 <label for="email">Votre email : </label>
	   	 <input type="email" name="email" id="email" placeholder="vous@domain.tld" <?php if(isset($_SESSION['prem']) AND $_SESSION['prem'] == true AND isset($_POST['email'])) { echo 'value="' . $_POST['email'] . '"';} ?> required/><br />
           	 <label for="password">Votre mot de passe : </label>
           	 <input type="password" name="password" id="password" placeholder="Minimum 6 caractères alphanumériques" required/><br />
            	<label for="cpassword">Confirmer votre mot de passe : </label>
            	<input type="password" name="cpassword" id="password" placeholder="Minimum 6 caractères alphanumériques" required/><br />
                <lablel for="region">Votre région : </lablel>
       		<select name="region" id="region" required>
		<?php                  
		  foreach($regions as $element)
                  {
		     if(isset($_SESSION['prem']) AND $_SESSION['prem'] == true AND isset($_POST['region']) AND $_POST['region'] == $element['ID_REGION'])
                     {
		     	echo '<option value="' . $element['ID_REGION'] . '" selected>' . $element['REGION'] . '</option>';
                     }
		     else
		     {
			echo '<option value="' . $element['ID_REGION'] . '">' . $element['REGION'] . '</option>';
		     }
		  }
		?>
      		</select><br /><br />
           	<input type="checkbox" name="rgpd" id="rgpd" <?php if(isset($_SESSION['prem']) AND $_SESSION['prem'] == true AND isset($_POST['rgpd'])) { echo 'checked';} ?> />
           	<label class="info" for="rgpd">J'accepte d'envoyer mes données personnelles à l'association BDE CESI ROUEN à des fins de communication, de gestion de compte et de livraison.<br/>Les données ne seront ni vendues, ni louées ni distribuées pour toute autres raisons que nécessaire à l'exécution de la commande.</label><br />
            	<input type="submit" value="Envoyer" />
           	<input type="reset" value="Remettre les valeurs à zéro" />
               </form>

	       <?php require 'php/footer.php'; ?>
	</body>
</html>
