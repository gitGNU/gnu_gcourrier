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
session_start();
require("connexion.php");
$idCourrier = $_GET['idCourrier'];

?>

<html>
<head><title>GCourrier</title></head>
<LINK HREF="styles3.css" REL="stylesheet">
<body>

	
<?php
	echo"<form name = accuse method = POST action = creerAccuse.php>";

$requete = "select destinataire.* 
	    from courrier,destinataire
	    where courrier.id = ".$idCourrier."
	    and courrier.idDestinataire = destinataire.id;";
$resultat = mysql_query( $requete ) or die (mysql_error( ) );
while( $ligne = mysql_fetch_array($resultat) ){
$nom = $ligne['nom'];
$prenom = $ligne['prenom'];
$adresse = $ligne['adresse'];
$codePostal = $ligne['codePostal'];
$ville = $ligne['ville'];
}
$date = "Harnes, le ";
$date.=date("d-m-Y");
$objet = "objet : accuse reception courrier num ".$idCourrier;

?>
	<table align = left>
		<tr><td>Expediteur</td>
		<td><input type = text name = expediteur value = 'Mairie de harnes'></input></td>
		</tr>
		<tr><td>Adresse</td>
		<td><input type = text name = adresse value = '35, rue des Fusilles'></input></td>
		</tr>
		<tr><td>Code postal</td>
		<td><input type = text name = codePostal value = '62440'></input></td>
		</tr>
		<tr><td>Ville</td>
		<td><input type = text name = ville value = 'Harnes'></input></td>
		</tr>
		<tr><td>Telephone</td>
		<td><input type=text name = telephone value = '0321794279'></input></td>
		</tr>
	</table><br><br><br><br><br><br>
	<table align = right>
		<tr><td>Destinataire</td>
		<td>
		<?php
		echo"<input type = text name = destinataire value ='".$nom." ".$prenom."'></input></td>";
		?>
		</tr>
		<tr><td>Adresse</td>
		<?php
		echo"<td><input type = text name=adresseDest value = '".$adresse."'></input></td>";
		?>
		</tr>
		<tr><td>Code postal</td>
		<?php
		echo"<td><input type = text name = codePostalDest value=".$codePostal."></input></td>";
		?>
		</tr>
		<tr><td>Ville</td>
		<?php
		echo"<td><input type = text name = villeDest value =".$ville."></input></td>";
		?>
		</tr>
		<tr><td>Date</td>
		<?php
		echo"<td><input type = text name = date value='".$date."'></input></td>";
		?>
		</tr>
	</table><br><br><br><br><br><br>
	<table align = left>
		<tr><td>Objet</td>
		<?php
		echo"<td><input type = text name = objet size=50 value ='".$objet."'></td>";
		?>
		</tr>
		<tr><td>corps</td>
		<?php
		echo"<td><textarea name=corps cols=100 rows=20 wrap=virtual>
Madame,Monsieur,~

Nous avons bien recu votre courrier qui est actuellement en cours de traitement.~

Nous vous prions d'agreer, Madame, Monsieur, l'expression de nos salutations distinguees.~~

|La Mairie de Harnes.
		</textarea></td>";
		?>
		</tr>
	</table><br><br><br><br><br><br><br><br><br><br>
	
	
	<i>~ retour chariot</i><br>
	<i>| alignement a droite</i><br>
	<i># tabulation</i><br><br>
	
	<center>
	<input type = submit name = creer value = creer></input>
	</center><br>
<?php
echo"<b><a href = voirCourrier.php?id=".$idCourrier.">voir mon courrier</a></b>";
?>

	</form>

</body>
</html>
