<?php
session_start();

try
{
  $bdd = new PDO('mysql:host=localhost;dbname=webprojet;charset=utf8', 'webprojet', '7Ydeuzdb52:!');
}
catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage());
}

if(!isset($_SESSION['id']) OR $_SESSION['droit'] < 3)
{
  header('Location: ../events.php');
}
else
{
       $nom_fichier = sys_get_temp_dir() . '/' . time() . '.zip';
       
       $zip = new ZipArchive;
       $res = $zip->open($nom_fichier, ZipArchive::CREATE);
       $zip->setArchiveComment('ZIP contenant toute les photos postées dans la catégorie "événement" à ' . date("H:i:s") . ' le ' . date("d/m/Y"));

       $req = $bdd->query('SELECT CHEMIN FROM PHOTO WHERE USED_FOR_EVENT = 1');
       $fichiers = $req->fetchAll();

       foreach($fichiers as $fichier)
       {
         $zip->addFile('../photos/' . $fichier['CHEMIN'], $fichier['CHEMIN']);
       }

       $zip->close();

       header("Cache-Control: no-cache, must-revalidate");
       header("Cache-Control: post-check=0,pre-check=0");
       header("Cache-Control: max-age=0");
       header("Pragma: no-cache");
       header("Expires: 0");
       header("Content-Type: application/force-download");
       header('Content-Disposition: attachment; filename="Image_a_'. date("H_i_s") . '_le_' . date("d_m_Y") .'.zip"');
       header("Content-Length: " . filesize($nom_fichier));
       readfile($nom_fichier);
       unlink($nom_fichier);
}
?>
