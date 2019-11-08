<?php
session_start();

session_unset();
session_destroy();

setcookie('mail', '', -1, '/webprojet/', 'www.webfacile76.fr', true, true);
setcookie('mdp', '', -1, '/webprojet/', 'www.webfacile76.fr', true, true); 

header('Location: index.php');
?>

