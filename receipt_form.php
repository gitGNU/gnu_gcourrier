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
include('templates/header.php');
$idCourrier = intval($_GET['idCourrier']);
?>

<?php
	echo '<form name="accuse" method="post" action="receipt_add.php">';

$requete = "select destinataire.* 
	    from courrier,destinataire
	    where courrier.id = ".$idCourrier."
	    and courrier.idDestinataire = destinataire.id;";
$resultat = mysql_query( $requete ) or die (mysql_error( ) );
while( $ligne = mysql_fetch_array($resultat) ){
$nom = $ligne['nom'];
$prenom = $ligne['prenom'];
$adresse = $ligne["adresse"];
$codePostal = $ligne['codePostal'];
$ville = $ligne['ville'];
}

$objet = "Objet : accusé réception courrier n°".$idCourrier;

$requete = "select * from accuse where id=1;";
$result = mysql_query($requete) or die(mysql_error());
while($ligne = mysql_fetch_array($result)){
	$expediteurAccuse = $ligne['expediteur'];
	$adresseAccuse = $ligne['adresse'];
	$codePostalAccuse = $ligne['codePostal'];
	$villeAccuse = $ligne['ville'];
	$telephoneAccuse = $ligne['telephone'];
}
$date = $villeAccuse.", le ";
$date.=date("d-m-Y");

?>
	<table align = left>
		<tr><td>Expéditeur</td>
<?php
echo"		<td><input type = text name = expediteur value = \"".$expediteurAccuse."\" ></input></td> ";
?>
		</tr>
		<tr><td>Adresse</td>
<?php
echo"		<td><input type = text name = adresse value = \"".$adresseAccuse."\" ></input></td>";
?>
		</tr>
		<tr><td>Code postal</td>
<?php
echo"		<td><input type = text name = codePostal value=\"".$codePostalAccuse."\" ></input></td>";
?>
		</tr>
		<tr><td>Ville</td>
<?php
echo"		<td><input type = text name = ville value=\"".$villeAccuse."\"></input></td>";
?>
		</tr>
		<tr><td>Téléphone</td>
<?php
echo"		<td><input type=text name = telephone value=\"".$telephoneAccuse."\"></input></td>";
?>
		</tr>
	</table><br><br><br><br><br><br>
	<table align = right>
		<tr><td>Destinataire</td>
		<td>
		<?php
		echo"<input type = text name = destinataire value =\"".$nom." ".$prenom."\"></input></td>";
		?>
		</tr>
		<tr><td>Adresse</td>
		<?php
		echo"<td><input type = text name=adresseDest value = \"".$adresse."\"></input></td>";
		?>
		</tr>
		<tr><td>Code postal</td>
		<?php
		echo"<td><input type = text name = codePostalDest value=".$codePostal."></input></td>";
		?>
		</tr>
		<tr><td>Ville</td>
		<?php
		echo"<td><input type = text name = villeDest value =\"".$ville."\"></input></td>";
		?>
		</tr>
		<tr><td>Date</td>
		<?php
		echo"<td><input type = text name = date value=\"".$date."\"></input></td>";
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

Nous avons bien reçu votre courrier qui est actuellement en cours de traitement.~

Nous vous prions d'agréer, Madame, Monsieur, l'expression de nos salutations distinguées.~~

|".$expediteurAccuse.".
		</textarea></td>";
		?>
		</tr>
	</table><br><br><br><br><br><br><br><br><br><br>
	
	
	<i>~ retour chariot</i><br>
	<i>| alignement à droite</i><br>
	<i># tabulation</i><br><br>
	
	<center>
	<input type="hidden" name="mail_id" value="<?php echo $idCourrier; ?>" />
	<input type="submit" name="creer" value="Créer" />
	</center><br>
<?php
echo"<b><a href = mail_list_my.php?id=".$idCourrier."&type=".$_GET['type'].">Retour au courrier</a></b>";
?>

	</form>
<br style="clear: both;" />
<?php
include('templates/footer.php');
