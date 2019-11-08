<?php require 'php/header.php';

if(isset($_SESSION['id']))
{
  header('Location: index.php');
}

if(isset($_POST['nom']) AND isset($_POST['fnom']) AND isset($_POST['email']) AND isset($_POST['password']) AND isset($_POST['cpassword']))
  {
     $nom = htmlspecialchars((string)$_POST['nom']);
     $fnom = htmlspecialchars((string)$_POST['fnom']);
     $email = htmlspecialchars((string)$_POST['email']);
     $password = $_POST['password'];
     $cpassword = $_POST['cpassword'];

     try
     {
        if($nom == '' OR $fnom == '' OR $email == '' OR $password == '' OR $cpassword == '')
        {
           $id = "empty";
        }
        else if(!isset($_POST['rgpd']))
        {
          $id = "rgpd";
        }
        else if(strlen($password) < 6)
        {
          $id = "iPass";
        }
        else
        {
          if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email))
          {
             if($password == $cpassword)
             {

               $password = sha1($password . '3fd8e4aac9812cb');
               $bdd = new PDO('mysql:host=localhost;dbname=sitegmg;charset=utf8', 'sitegmg', 'jdp2Xf44p0t84AOF');

               $req = $bdd->prepare('SELECT id FROM users WHERE email = ?');
               $req->execute(array($email));

               if(!$donnees = $req->fetch())
               {
                 $req2 = $bdd->prepare('INSERT INTO users(email, password, nom, prenom, adresse, CP, city, tel) VALUES(:email, :password, :nom, :fnom, :adresse, :CP, :city, :tel)');
                 $req2->execute(array(
                 'email' => $email,
                 'password' => $password,
                 'nom' => $nom,
                 'fnom'=> $fnom));

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

        		if($id == "empty") { echo "Un ou plusieurs champ(s) sont vide(s)"; } else if($id == "pass") { echo "Mot de passe différent !"; } else if($id == "email"){ echo "Email incorrecte!"; } else if($id == "rgpd"){ echo "Vous devez accepter l'utilisation de vos données personnelles"; } else if($id == "cgv"){ echo "Vous devez accepter les CGV"; } else if($id == "iPass") { echo "Mot de passe inférieur à 6 caractères"; } else if($id == "users") { echo "Utilisateur déjà existant"; }
			$_SESSION['prem'] = false;
		}
		else
		{
		  $_SESSION['prem'] = true;
		}?>
        </span><br />

           	 <label for="nom">Votre nom : </label>
	    	<input type="text" name="nom" id="nom" placeholder="Durand" required/><br />
           	 <label for="fnom">Votre prénom : </label>
	   	 <input type="text" name="fnom" id="fnom" placeholder="Hubert" required/><br />
           	 <label for="email">Votre email : </label>
	   	 <input type="email" name="email" id="email" placeholder="vous@domain.tld" required/><br />
           	 <label for="password">Votre mot de passe : </label>
           	 <input type="password" name="password" id="password" placeholder="Minimum 6 caractères alphanumériques" required/><br />
            	<label for="cpassword">Confirmer votre mot de passe : </label>
            	<input type="password" name="cpassword" id="password" placeholder="Minimum 6 caractères alphanumériques" required/><br />
           	<input type="checkbox" name="rgpd" id="rgpd" />
           	<label class="info" for="rgpd">J'accepte d'envoyer mes données personnelles à l'association BDE CESI ROUEN à des fins de communication, de gestion de compte et de livraison.<br/>Les données ne seront ni vendues, ni louées ni distribuées pour toute autres raisons que nécessaire à l'exécution de la commande.</label><br />
            	<input type="submit" value="Envoyer" />
           	<input type="reset" value="Remettre les valeurs à zéro" />
               </form>

	
	</body>
</html>
