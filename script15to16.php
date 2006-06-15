<?php
include("connexion.php");
/*$requete = "ALTER TABLE `courrier` ADD `url` VARCHAR( 255 ) NOT NULL ;";
$result = mysql_query($requete) or die mysql_error();
echo "modification de la base de donnees effectuee";*/
$requete ="ALTER TABLE `facture` ADD `h` VARCHAR( 255 ) DEFAULT 'consulter' NOT NULL ;";
$result = mysql_query($requete) or die mysql_error();

$requete ="ALTER TABLE `facture` ADD `refuse` INT NOT NULL;";
$result = mysql_query($requete) or die mysql_error();

echo "modification de la base de donnees effectuee";
?>
