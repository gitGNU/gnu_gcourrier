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

require("connexion.php");
session_start();

?>
<html>
<head><title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
</head>
<body>

<?php
if(!isset( $_POST["rechercher"] ) ){

?>
<div id =pageTGd><br>
<center><img src = images/banniere2.jpg></center><br><br>
<center><b>CONSULTER LES MEMBRES D'UN SERVICE</<b>
<br><br>
<form method=POST action=rechercherCompteService.php>
<table>
<tr><td><label>service</label></td>
<td><select name = service>
<?php
$requete = "select * from service order by libelle;";
$result = mysql_query($requete) or die("erreur : ".mysql_error());
  while($ligne = mysql_fetch_array($result)){
    if($ligne['libelle'] !='ADMIN')
    echo "<option value = '".$ligne['id']."'>".$ligne['libelle']." ".$ligne['designation']." </option>";
  }

?>
</select></td>

<td><input type=submit value=rechercher name=rechercher></td></tr>
</table>
</form>
<a href=index.php>index</a><br><br>
<?php
}
else{
$service = $_POST['service'];
$requete = "select * from utilisateur where idService = '".$service."';";
$result = mysql_query($requete) or die (mysql_error());
echo "<div id =pageTGd><br>
<center><img src = images/banniere2.jpg></center><br><br>
<center><b>CONSULTER LES MEMBRES D'UN SERVICE</<b>
<br><br>
<table><tr><td>login</td><td>nom</td><td>prenom</td></tr>";
while($ligne = mysql_fetch_array($result)){
echo "<tr align=center><td>".$ligne['login']."</td><td>".$ligne['nom']."</td><td>".$ligne['prenom']."</td></tr>";
}

echo "</table><br><a href=index.php>index</a>";
}
?> 
