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
require_once('functions/status.php');

if(!isset($_POST["enregistrer"])){
  include('templates/header.php');
?>
	<table align="center">
	<form name="creerDestForm" method="POST" action="creerDestinataire.php">
		<tr><td>Nom</td>
		<td><input type="text" name="nom"></input></td></tr>
		<tr><td>Prénom</td>
		<td><input type="text" name="prenom"></input></td></tr>
		<tr><td>Adresse</td>
		<td><input type="text" name="adresse"></input></td></tr>
		<tr><td>Code postal</td>
		<td><input type="text" name="codePostal"></input></td></tr>
		<tr><td>Ville</td>
		<td><input type="text" name="ville"></input></td></tr>

	</table>
		<center><input type="submit" name="enregistrer" value="Enregistrer"></input></center>
	</form>
<?php
  include('templates/footer.php');
}else{
	$nom = $_POST['nom'];
	$prenom = $_POST['prenom'];
	$adresse = $_POST['adresse'];
	$codePostal = $_POST['codePostal'];
	$ville = $_POST['ville'];
	
	if ($nom == "" and $prenom == "") {
	  status_push("Entrez un nom ou un prénom.");
	  header("Location: creerDestinataire.php");
	  exit();
	}

	$requeteExist = "SELECT count(*) AS nb FROM destinataire WHERE nom ='".$nom."' AND prenom ='".$prenom."';";
	$resultExist = mysql_query($requeteExist) or die (mysql_error( ));
	$ligne = mysql_fetch_array( $resultExist );
	if ($ligne['nb'] != 0) {
	  // Only warn user, several contacts may have the same name and a different address
	  status_push("Note: un destinataire de ce nom existe déjà.");
	}

	$requete= "INSERT INTO destinataire(nom,prenom,adresse,codePostal,ville) VALUES('".$nom."','".$prenom."','".$adresse."','".$codePostal."','".$ville."');";
	$resultat = mysql_query($requete ) or die ("erreur requete ".mysql_error( ) );
	
	status_push("Destinataire créé.");
	header("Location: index.php");
	exit();
}
