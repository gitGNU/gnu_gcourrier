<?php
/*
GCourrier
Copyright (C) 2005,2006 CLISS XXI

This file is part of GCourrier.

GCourrier is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

GCourrier is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with GCourrier; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

author VELU Jonathan
*/

require_once('init.php');
require_once('functions/db.php');

if(!isset($_POST['modifier'])){
  $idCourrier = $_GET['idCourrier'];

?>

<html>
<head><title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
</head>
<body>
<div id = pageGd><br>
<center>
<img src = images/banniere2.jpg><br><br>
</center>
<form method=POST action=modifDestinataire.php>
<?php
echo"<center>";
echo"<label>fournisseur : </label>";


$res = db_execute("select * from facture where id=?", array($idCourrier));
$row = mysql_fetch_array($res);
$cur_provider = $row['idFournisseur'];

echo "<select name = fournisseur>";

$requete = "select * from destinataire order by nom; ";
$result = mysql_query($requete) or die( mysql_error() );

while( $ligne = mysql_fetch_array( $result ) ){
  $selected = '';
  if ($ligne['id'] == $cur_provider)
    $selected = "selected='selected'";
  echo "<option $selected value='{$ligne['id']}'>{$ligne['nom']} {$ligne['prenom']}</option>";
}

echo "</select>";
echo"<br/><br/>";
echo"<input type=hidden name=idCourrier value=".$idCourrier."></input>";
echo"<input type=submit name=modifier value=modifier><br/><br/>";
echo"</center>";
?>
</form>
</div>

</body>
</html>
<?php
}else{
$fournisseur = $_POST['fournisseur'];
$idCourrier = $_POST['idCourrier'];
$requete = "update facture set idFournisseur='".$fournisseur."' where id=".$idCourrier.";";
$result = mysql_query($requete) or die(mysql_error());

header("LOCATION:voirFacture.php");
}
?>
