<?php
include_once('init.php');
$requete = "select max(id) as idF from facture;";
$result=mysql_query($requete) or die(mysql_error());
while($ligne = mysql_fetch_array($result))
{
  $id = $ligne['idF'];
}
echo "vous venez de créer la facture numero ".$id;
?>
