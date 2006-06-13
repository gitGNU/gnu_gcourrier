<?php
include("connexion.php");
$requete = "ALTER TABLE `courrier` ADD `url` VARCHAR( 255 ) NOT NULL ;";
$result = mysql_query($requete) or die mysql_error();
echo "modification de la base de donnees effectuee";

?>
