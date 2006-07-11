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


if(!isset($_POST["enregistrer"])){
?>
<html>
<head> <title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet"></head>
<body>
<div id =pageGd><br>
	<center><img src = images/banniere2.jpg></img></center><br><br><br>
	<table align = center>
	<form name = creerDestForm method = POST action = modifierIndividu2.php>
<?php
$requete="select * from destinataire where id=".$_POST['fournisseur'].";";
$result=mysql_query($requete) or die (mysql_error());
while($ligne = mysql_fetch_array($result)){
  $nom = $ligne['nom'];
  $prenom = $ligne['prenom'];
  $adresse= $ligne['adresse'];
  $codePostal= $ligne['codePostal'];
  $ville= $ligne['ville'];
}
?>
		<tr><td>nom</td>
<?php
echo"		<td><input type = text name = nom value='".$nom."' ></input></td></tr>";
?>
        	<tr><td>prenom</td>
<?php
echo"		<td><input type = text name = prenom value='".$prenom."'></input></td></tr>";
?>
		<tr><td>adresse</td>
<?php
echo"		<td><input type = text name = adresse value='".$adresse."'></input></td></tr>";
?>
		<tr><td>code postal</td>
<?php
echo"		<td><input type = text name =codePostal value='".$codePostal."'></input></td></tr>";
?>
		<tr><td>ville</td>
<?php
echo"		<td><input type = text name =ville value='".$ville."'></input></td></tr>";
echo"<input type=hidden name=id value=".$_POST['fournisseur'].">";
?>
	</table>
		<center><input type = submit name = enregistrer value = enregistrer></input></center>
	</form>

<center><br>
<a href = index.php>index</a>
</center><br><br>
</div>
</body>
</html>
<?php
}else{
	$nom = $_POST['nom'];
	$prenom = $_POST['prenom'];
	$adresse = $_POST['adresse'];
	$codePostal = $_POST['codePostal'];
	$ville = $_POST['ville'];
	
	$requete= "update destinataire set nom='".$nom."', prenom='".$prenom."', adresse='".$adresse."', codePostal='".$codePostal."', ville='".$ville."' where id=".$_POST['id']."";
	$resultat = mysql_query($requete ) or die ("erreur requete ".mysql_error( ) );
	
	echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";


}
?>
