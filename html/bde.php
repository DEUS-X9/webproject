<?php 
require 'php/header.php';
if(!isset($_SESSION['id']))
{
  header('Location: index.php');
}
else if($_SESSION['droit'] != 2)
{
  header('Location: index.php');
} ?>
		<h1>Page d'administration</h1>

<table class="table table-striped table-dark">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Compte</th>
      <th scope="col">Gérer</th>

    </tr>
  </thead>
  <tbody>

		<?php 
		if(!isset($_GET['compte']))
		{
		  $req = $bdd->query('SELECT * FROM MEMBRE');
		  
		  if($req->columnCount() == 0)
		  {
		    echo '<p id="result_compte">Aucune compte actif. Erreur BDD</p>';
		  }
		  else
		  {
 		    echo '<p id="result_compte">';
		    while($donnees = $req->fetch())
		    {
			echo '    <tr> <th scope="row"> ' . $donnees['ID_MEMBRE'] . '</th><td>' . $donnees['MAIL']. '</td>  <td><a href="bde.php?compte=" ' . $donnees['MAIL'] . ' ">Gérer</a></td></tr>' ;
		    }
                    echo '</p>';
                  }
		  $req->closeCursor();
		  
		}
		else
		{ 
		  $compte = htmlspecialchars((string)$_GET['compte']);
		  $req = $bdd->prepare('SELECT * FROM MEMBRE WHERE MAIL = ?');
		  $req->execute(array($compte));
		  $donnee = $req->fetch();
                ?>
		  <form method="post">
                  </form>
               <?php
               }
	       $req->closeCursor();
               ?>
	  
	  
	    </tbody>
	  </table>
	</body>
</html>
