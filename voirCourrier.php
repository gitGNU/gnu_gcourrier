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

if($_GET['type']==1){
	$tmpCouleur ="white";
}
else{
	$tmpCouleur="white";
}

?>
<html>
<head><title>GCourrier</title>
<link rel="stylesheet" href=styles3.css type="text/css">
</head>
<?php
echo "<body style='background:".$tmpCouleur."' >";
?>
<br>
	<center>
		<img src= images/banniere2.jpg></img>
	</center>
		<br>

<?php
echo "<center><div id= titre>Courrier";
if($_GET['type']==1){
	echo " Entrant";
	$affDate="arrivee";
}
else{
	echo " Depart";
	$affDate="depart";
}

$requeteInit = "Select id from courrier Limit 1;";
$result = mysql_query($requeteInit) or die(mysql_error());
if(mysql_num_rows($result) == 0){
echo "</div><br>aucun courrier pour le moment<br><br>";
echo "<a href = index.php>index</a>";
exit();
}

echo " <br/><i style=\"font-size:10px;font-weight:normal\">";
echo _("Note: Ne sont affichés que les courriers de votre service");
echo "</i></div></center><br>";

if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
  $res = db_execute("SELECT MAX(id) AS id FROM courrier WHERE type=?", array($_GET["type"]));
  while ($ligne = mysql_fetch_array($res)) {
    $id = $ligne['id']; 
  }
  $idTmp = $id;
} else {
  $idTmp = $_GET['id'];
}

if (!isset($_GET['nbAffiche'])) {
  $result = db_execute('SELECT * FROM utilisateur WHERE login=?', array($_SESSION['login']));
  while($ligne = mysql_fetch_array($result)){
    $nbAffiche = $ligne['preferenceNbCourrier'];
  }
} else {
  $nbAffiche = $_GET['nbAffiche'];
}

?>
<form method = POST action=voirCourrierAffiche.php>
<table align=center style="border:1px dotted black;"><tr><td>
<label>Nombre de courrier à afficher:</label>

<?php 
echo"<input type = hidden name=type value=".$_GET['type']." size=2></input>";
echo"<input type = text name=affiche value=".$nbAffiche." size=2></input>";
echo"<input type = hidden name=idTmp value=".$idTmp."></input>";
?>

<input type=submit name=ok value=ok></input>
</td></tr></table>
</form>

<?php
echo "<form method='POST' action='rechercheRapideCourrier.php?type={$_GET['type']}'>";
?>
<table align=center style="border:1px dotted black;"><tr><td>
<label>Rechercher le courrier numéro:</label>
<input type=text name=numero value=1 size=2></input>
<input type=submit name=ok value=ok></input>
<br><a href="rechercher.php?type=1"><font size=1px><center>Recherche Avancée</center></font></a>
</tr></td></table></form>





<?php
if(strcmp($_SESSION['login'] , 'admin') == 0){
	$requeteEntrant ="select courrier.id as idCourrier,
                                  priorite.nbJours as nbJours,
				  courrier.dateArrivee,
				  courrier.observation as observation,
				  destinataire.nom as nomDestinataire,
				  destinataire.prenom as prenomDestinataire,
				  courrier.libelle as libelleCourrier,
				  courrier.url as url
		   	   from courrier,priorite,destinataire		          
			   where courrier.id<=".$idTmp." 
			   and courrier.validite = 0			 
			   and courrier.idPriorite = priorite.id
			   and courrier.idDestinataire = destinataire.id
			   and courrier.type = ".$_GET['type']."
		           order by courrier.id DESC
			   LIMIT ".$nbAffiche.";";}
else{

	if(!isset($_GET['idCourrierRecherche'])){
	$requeteEntrant = "select courrier.id as idCourrier,
                                  priorite.nbJours as nbJours,
				  courrier.dateArrivee,
				  courrier.observation as observation,
				  destinataire.nom as nomDestinataire,
				  destinataire.prenom as prenomDestinataire,
				  courrier.libelle as libelleCourrier,
				  courrier.url as url
		   	   from courrier,priorite,destinataire
		           where courrier.id<=".$idTmp." 
             		   and courrier.validite = 0
			   and courrier.serviceCourant = ".$_SESSION['idService']."
			   and courrier.idPriorite = priorite.id
			   and courrier.idDestinataire = destinataire.id
			   and courrier.type = ".$_GET['type']."
		           order by courrier.id DESC
			   LIMIT ".$nbAffiche.";";
	}
	else{
	$requeteEntrant = "select courrier.id as idCourrier,
                                  priorite.nbJours as nbJours,
				  courrier.dateArrivee,
				  courrier.observation as observation,
				  destinataire.nom as nomDestinataire,
				  destinataire.prenom as prenomDestinataire,
				  courrier.libelle as libelleCourrier,
				  courrier.url as url
		   	   from courrier,priorite,destinataire
		           where courrier.id=".$_GET['idCourrierRecherche']." 
             		   and courrier.validite = 0
			   and courrier.serviceCourant = ".$_SESSION['idService']."
			   and courrier.idPriorite = priorite.id
			   and courrier.idDestinataire = destinataire.id
			   and courrier.type = ".$_GET['type']."
		           order by courrier.id DESC
			   LIMIT ".$nbAffiche.";";
	
	}

}

$resultatEntrant = mysql_query($requeteEntrant) or die("erreur rEntrant ".mysql_error());


echo "<table align=center>";
echo "<tr>";
echo "<td align=center>num</td>";
echo "<td align=center>libelle</td>";
echo "<td align=center>emetteur </td>";
echo "<td align=center>".$affDate."</td>";
echo "<td align=center>observation</td>";
echo "<td align=center>historique</td>";
	
if(strcmp($_SESSION['login'] , 'admin') != 0){	
	echo "<td align=center>transmettre</td>";
	echo "<td align=center>terminer </td>";
if($_GET['type'] ==1)
	echo "<td align=center>accuse </td>";
}

	
echo "<td align=center> urgence</td>";
echo"<td>fichier</td>";
echo"</tr>";


$boul = 0;
while( $ligne = mysql_fetch_array($resultatEntrant) ){
	if($boul == 0){
		$couleur = 'lightblue';
		$boul = 1;
	}
	else{
		$couleur = 'white';
		$boul = 0;	
	}


	echo"<tr>";
	$idCourrier = $ligne['idCourrier'];
	$idTmp = $idCourrier;
	$destinataire = $ligne['nomDestinataire']." ".$ligne['prenomDestinataire'];
	$tmpdateArrivee = $ligne['dateArrivee']; 
	$dateArrivee=substr($tmpdateArrivee,8,2)."-".substr($tmpdateArrivee,5,2)."-".substr($tmpdateArrivee,0,4);
	$observation = $ligne['observation'];
	$libelle = $ligne['libelleCourrier'];
	
	if(strcmp($observation,"") ==0)
		$observation="modifier";
	if(strcmp($libelle,"") ==0)
		$libelle="modifier";
	if(strcmp($destinataire,"") ==0)
		$destinataire="modifier";

	echo "<td bgcolor=".$couleur.">".$idCourrier."</td>";	
	echo "<td bgcolor=".$couleur."><a href=modifLibelleCourrier.php?idCourrier=".$idCourrier."&type=".$_GET['type']." style=\"text-decoration :none;font-weight:normal\">".$libelle."</td>";	
	echo "<td bgcolor=".$couleur."><a href=modifDestinataireCourrier.php?idCourrier=".$idCourrier."&type=".$_GET['type']." style=\"text-decoration :none;font-weight:normal\">".$destinataire."</td>";	
	echo "<td bgcolor=".$couleur.">".$dateArrivee."</td>";	
	echo "<td bgcolor=".$couleur."><a href=modifObservationCourrier.php?idCourrier=".$idCourrier."&type=".$_GET['type']." style=\"text-decoration :none;font-weight:normal\">".$observation."</td>";	
	
	
	echo"<td bgcolor=".$couleur."><a href=chemin.php?idCourrier=".$idCourrier."&affiche=".$nbAffiche."&type=".$_GET['type'].">historique</a></td>";

	if(strcmp($_SESSION['login'] , 'admin') != 0){
		echo"<td bgcolor=".$couleur."><a href=transmettre.php?idCourrier=".$idCourrier."&affiche=".$nbAffiche."&type=".$_GET['type'].">transmettre</a></td>";
		echo"<td bgcolor=".$couleur."><a href=valider.php?idCourrier=".$idCourrier."&affiche=".$nbAffiche."&type=".$_GET['type'].">terminer</a></td>";
	}

	if($_GET['type'] == 1)
		echo"<td bgcolor=".$couleur."><a href=receipt_form.php?idCourrier=".$idCourrier."&nbAffiche=".$nbAffiche."&type=".$_GET['type'].">creer</a></td>";

//test pour urgence du courrier
		$dateActuel = date("Y-m-d");
		$jourActuel = substr($dateActuel,8,2);
		$moisActuel = substr($dateActuel,5,2);
		$anneeActuel= substr($dateActuel,0,4);

		$tmpDateArrivee = $ligne['dateArrivee'];
		$jourArrivee =substr($tmpDateArrivee,8,2);
		$moisArrivee =substr($tmpDateArrivee,5,2);
		$anneeArrivee =substr($tmpDateArrivee,0,4);
		

		$nbJours = $ligne['nbJours'];
		
		$timestampActuel = mktime(0,0,0,$moisActuel,$jourActuel,$anneeActuel);
		$timestampArrivee= mktime(0,0,0,$moisArrivee,$jourArrivee,$anneeArrivee);
		$urgence = ($timestampActuel - $timestampArrivee ) / 86400;

		$nbJoursRestant = $nbJours - $urgence;

		if($urgence >= $nbJours)
                   echo "<td bgcolor=".$couleur."><center><img src=images/annuler.png title = \"reste ".$nbJoursRestant." jours\"></img></center></td>";
		else	
                   echo "<td bgcolor=".$couleur."><center><img src=images/ok.png title = \"reste ".$nbJoursRestant." jours\"></img></center></td>";

echo "<td style='text-align:center' bgcolor='$couleur'>";
if ($ligne['url'] != "")
  echo "<a href='file_view.php/".basename($ligne['url'])."?object=courrier&object_id={$ligne['idCourrier']}'><img src='images/download.gif' style='border: 0'></a>";
echo "</td>";
echo "</tr>";
}//fin while
echo "</table>";
$idTmp--;
?>
<br/>
<center><a href="javascript:history.go(-1)"> <b>page precedente </b></a> &nbsp/

<?php
if(mysql_num_rows($resultatEntrant) == $nbAffiche){
	echo "<a href = voirCourrier.php?id=".$idTmp."&nbAffiche=".$nbAffiche."&type=".$_GET['type']."><b>page suivante</b></a></center>";
}

?>	
<center><br>
<a href="index.php">Index</a><br /><br />

</center>
</body>

</html>

