<?php require 'php/header.php'; ?>
		<h1>Inscription</h1>
		
		<form method="POST">
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
           	<label for="rgpd">J'accepte d'envoyer mes données personnelles à l'association BDE CESI ROUEN à des fins de communication, de gestion de compte et de livraison.<br/>Les données ne seront ni vendues, ni louées ni distribuées pour toute autres raisons que nécessaire à l'exécution de la commande.</label><br />
            	<input type="submit" value="Envoyer" />
           	<input type="reset" value="Remettre les valeurs à zéro" />
               </form>

	
	</body>
</html>
