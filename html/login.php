<?php require 'php/header.php'; 

if(isset($_SESSION['id']))
{
  header('Location: index.php');
}

if(isset($_POST['email']) AND isset($_POST['mdp']))
{
     $email = (String)htmlspecialchars($_POST['email']);
     $password = (String)htmlspecialchars($_POST['mdp']);

     $password = md5($password . 'fuizyehcdbskuyfz!e');
     $req = $bdd->prepare('SELECT * FROM MEMBRE WHERE MAIL = ? AND PASSWORD = ?');
     $req->execute(array($email, $password));

     $req2 = $bdd->prepare('INSERT INTO loging(ip, email, login) VALUES(:ip, :email, :login)');

     if(!$donnee = $req->fetch())
     {
         $req2->execute(array(
               'ip' => $_SERVER['REMOTE_ADDR'],
               'email' => $email,
               'login' => false ));
     }
    else
    {
	if(isset($_POST['auto']))
        {
          setcookie("mail", $email, time() + 365*24*3600, '/webprojet/', 'www.webfacile76.fr', true, true);
          setcookie("mdp", $password, time() + 365*24*3600, '/webprojet/', 'www.webfacile76.fr', true, true);
        }
          
        $_SESSION['id'] = $donnee['ID_MEMBRE'];
	$_SESSION['nom'] = $donnee['NOM'];
	$_SESSION['prenom'] = $donnee['PRENOM'];
	$_SESSION['mail'] = $donnee['MAIL'];
	$_SESSION['droit'] = $donnee['DROIT'];
	$_SESSION['id_region'] = $donnee['ID_REGION'];

        if(isset($_SESSION['location']))
        {
           header('Location: ' . $_SESSION['location']);
           unset($_SESSION['location']);
        }
        else
        {
           header('Location: index.php');
        }

        $req2->execute(array(
               'ip' => $_SERVER['REMOTE_ADDR'],
               'email' => $_POST['email'],
               'login' => true ));
      }

      $req->closeCursor();
      $req2->closeCursor();
}
?>
		<h1>Connexion</h1>
		<span id="info">

		</span><br />
		<form method="post">
		  <label for="email">Votre email :</label>
                  <input type="email" id="email" name="email" placeholder="votrenom@domain.tld" autofocus required/><br />
		  <label for="mdp">Votre mot de passe :</label>
                  <input type="password" id="mdp" name="mdp" required/><br />
		  <label for="auto">Connection automatique</label>
                  <input type="checkbox" id="auto" name="auto" checked/><br />
                  <input type="submit" value="Connection" /><br />
		</form>
	</body>
</html>
