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


if(!isset($_POST["enregistrer"])){
?>
<html>
<head> <title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet"></head>
<body>
<div id =pageGd><br>
	<center><img src = images/banniere2.jpg></img></center><br><br><br>
	<table align = center>
	<form name = creerDestForm method = POST action = creerDestinataire.php>
		<tr><td>nom</td>
		<td><input type = text name = nom ></input></td></tr>
		<tr><td>prenom</td>
		<td><input type = text name = prenom></input></td></tr>
		<tr><td>adresse</td>
		<td><input type = text name = adresse ></input></td></tr>
		<tr><td>code postal</td>
		<td><input type = text name =codePostal></input></td></tr>
		<tr><td>ville</td>
		<td><input type = text name =ville></input></td></tr>

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
	
	$requeteExist = "select count(*) as nb from destinataire where nom ='".$nom."' and prenom ='".$prenom."';";
	$resultExist = mysql_query($requeteExist) or die (mysql_error( ));
	$ligne = mysql_fetch_array( $resultExist );
	if($ligne['nb'] != 0){
		echo "<meta http-equiv=\"refresh\" content=\"0;url=creerDestinataire.php\">";
		exit();
	}

	$requete= "insert into destinataire(nom,prenom,adresse,codePostal,ville) values('".$nom."','".$prenom."','".$adresse."','".$codePostal."','".$ville."');";
	$resultat = mysql_query($requete ) or die ("erreur requete ".mysql_error( ) );
	
	echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";


}
?>
