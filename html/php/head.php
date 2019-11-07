<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

try
{
  $bdd = new PDO('mysql:host=localhost;dbname=webprojet;charset=utf8', 'webprojet', '7Ydeuzdb52:!');
}
catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage());
}

if(isset($_COOKIE('Pseudo')) && isset($_COOKIE('Mdp'))
{
	$req = $bdd->prepare("SELECT * ")
}
