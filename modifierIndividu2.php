<?php
/*
GCourrier
Copyright (C) 2005,2006  Cliss XXI

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

if (!isset($_POST["enregistrer"])) {
  include('templates/header.php');
?>
	<table align="center">
	<form name="creerDestForm" method="POST" action="modifierIndividu2.php">
<?php
   if (isset($_GET['fournisseur'])
       and $_GET['fournisseur'] != ''
       and ctype_digit($_GET['fournisseur'])) {
     $requete = "SELECT * FROM destinataire WHERE id={$_GET['fournisseur']};";
     $result = mysql_query($requete) or die (mysql_error());
     
     while ($ligne = mysql_fetch_array($result)) {
       $nom = $ligne['nom'];
       $prenom = $ligne['prenom'];
       $adresse= $ligne['adresse'];
       $codePostal= $ligne['codePostal'];
       $ville= $ligne['ville'];
     }
   }
  echo "<input type=hidden name=id value=".$_GET['fournisseur'].">";
  echo "<tr><td>Numéro</td><td style='text-align: left'>{$_GET['fournisseur']}</td></tr>";
?>
		<tr><td>Nom</td>
<?php
echo"		<td><input type = text name = nom value='".htmlspecialchars($nom)."' ></input></td></tr>";
?>
        	<tr><td>Prénom</td>
<?php
echo"		<td><input type = text name = prenom value='".htmlspecialchars($prenom)."'></input></td></tr>";
?>
		<tr><td>Adresse</td>
<?php
echo"		<td><input type = text name = adresse value='".htmlspecialchars($adresse, ENT_QUOTES)."'></input></td></tr>";
?>
		<tr><td>Code postal</td>
<?php
echo"		<td><input type = text name =codePostal value='".htmlspecialchars($codePostal)."'></input></td></tr>";
?>
		<tr><td>Ville</td>
<?php
echo"		<td><input type = text name =ville value='".htmlspecialchars($ville)."'></input></td></tr>";
?>
	</table>
		<center><input type="submit" name="enregistrer" value="Enregistrer"></input></center>
	</form>
<?php
	    include('templates/footer.php');
} else {
	$nom = $_POST['nom'];
	$prenom = $_POST['prenom'];
	$adresse = $_POST['adresse'];
	$codePostal = $_POST['codePostal'];
	$ville = $_POST['ville'];
	$id = $_POST['id'];

	$requete= "UPDATE destinataire
                   SET nom='$nom', prenom='$prenom', adresse='$adresse',
                       codePostal='$codePostal', ville='$ville'
                   WHERE id=$id";

	$resultat = mysql_query($requete) or die ("erreur requete: " . mysql_error());
	
	header('Location: index.php');
}
