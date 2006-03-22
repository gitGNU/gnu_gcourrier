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
<link rel="stylesheet" href=styles3.css type="text/css">
</head>
<body >
<br>
	<center>
		<img src= images/banniere2.jpg></img>
	</center>
		<br>

<?php
echo "<center><div id= titre>Courrier Entrant</div></center><br>";

if(!isset( $_GET['id'] )){

$re = "select max(id)as id from courrier where type = 1;";
$res = mysql_query( $re ) or die (mysql_error() );
while($ligne = mysql_fetch_array( $res ) ){
	$id = $ligne['id']; 
}

$idTmp = $id;
}

else{
	$idTmp = $_GET['id'];
}

if(!isset( $_GET['nbAffiche'] )){
	$nbAffiche=5; 
}

else{
	$nbAffiche = $_GET['nbAffiche'];
}

?>
<form method = POST action=voirCourrierAffiche.php>
<table align=center style="border:1px dotted black;"><tr><td>
<label>nombre de courrier a afficher : </label>

<?php 
echo"<input type = text name=affiche value=".$nbAffiche." size=2></input>";
echo"<input type = hidden name=idTmp value=".$idTmp."></input>";
?>

<input type=submit name=ok value=ok></input>
</td></tr></table>
</form>




<?php
if(strcmp($_SESSION['login'] , 'admin') == 0){
	$requeteEntrant ="select courrier.id as idCourrier,
                                  priorite.nbJours as nbJours,
				  courrier.dateArrivee,
				  courrier.observation as observation,
				  destinataire.nom as nomDestinataire,
				  destinataire.prenom as prenomDestinataire,
				  courrier.libelle as libelleCourrier
		   	   from courrier,priorite,destinataire		          
			   where courrier.id<=".$idTmp." 
			   and courrier.validite = 0			 
			   and courrier.idPriorite = priorite.id
			   and courrier.idDestinataire = destinataire.id
			   and courrier.type = 1
		           order by courrier.id DESC
			   LIMIT ".$nbAffiche.";";}
else{




	$requeteEntrant = "select courrier.id as idCourrier,
                                  priorite.nbJours as nbJours,
				  courrier.dateArrivee,
				  courrier.observation as observation,
				  destinataire.nom as nomDestinataire,
				  destinataire.prenom as prenomDestinataire,
				  courrier.libelle as libelleCourrier
		   	   from courrier,priorite,destinataire
		           where courrier.id<=".$idTmp." 
             		   and courrier.validite = 0
			   and courrier.serviceCourant = ".$_SESSION['idService']."
			   and courrier.idPriorite = priorite.id
			   and courrier.idDestinataire = destinataire.id
			   and courrier.type = 1
		           order by courrier.id DESC
			   LIMIT ".$nbAffiche.";";

}

$resultatEntrant = mysql_query($requeteEntrant) or die("erreur rEntrant ".mysql_error());


echo "<table align=center>";
echo "<tr>";
echo "<td align=center>num</td>";
echo "<td align=center>libelle</td>";
echo "<td align=center>emetteur </td>";
echo "<td align=center>arrivee</td>";
echo "<td align=center>observation</td>";
echo "<td align=center>historique</td>";
	
if(strcmp($_SESSION['login'] , 'admin') != 0){	
	echo "<td align=center>transmettre</td>";
	echo "<td align=center>terminer </td>";
	echo "<td align=center>accuse </td>";
}
	
echo "<td align=center> urgence</td>";
echo"</tr>";


$boul = 0;
while( $ligne = mysql_fetch_array($resultatEntrant) ){
	if($boul == 0){
		$couleur = lightblue;
		$boul = 1;
	}
	else{
		$couleur = white;
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

	echo "<td bgcolor=".$couleur.">".$idCourrier."</td>";	
	echo "<td bgcolor=".$couleur.">".$libelle."</td>";	
	echo "<td bgcolor=".$couleur.">".$destinataire."</td>";	
	echo "<td bgcolor=".$couleur.">".$dateArrivee."</td>";	
	echo "<td bgcolor=".$couleur.">".$observation."</td>";	
	
	
	echo"<td bgcolor=".$couleur."><a href=chemin.php?idCourrier=".$idCourrier."&affiche=".$nbAffiche.">historique</a></td>";

	if(strcmp($_SESSION['login'] , 'admin') != 0){
		echo"<td bgcolor=".$couleur."><a href=transmettre.php?idCourrier=".$idCourrier."&affiche=".$nbAffiche.">transmettre</a></td>";
		echo"<td bgcolor=".$couleur."><a href=valider.php?idCourrier=".$idCourrier.">terminer</a></td>";
	}

	if($ack == 0)
		echo"<td bgcolor=".$couleur."><a href=avtAccuse.php?idCourrier=".$idCourrier."&nbAffiche=".$nbAffiche.">creer</a></td>";
	
	else
		echo"<td bgcolor=".$couleur.">X</td>";

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
                   echo "<td bgcolor=".$couleur."><center><img src=images/annuler.png title = \"reste ".$nbJoursRestant." jours\"></img></center></td></tr>";
		else	
                   echo "<td bgcolor=".$couleur."><center><img src=images/ok.png title = \"reste ".$nbJoursRestant." jours\"></img></center></td>";

echo "</tr>";
}//fin while
echo "</table>";
$idTmp--;
?>
<br/>
<center><a href="javascript:history.go(-1)"> <b>page precedente </b></a> &nbsp/

<?php
if(mysql_num_rows($resultatEntrant) == $nbAffiche){
	echo "<a href = voirCourrier.php?id=".$idTmp."&nbAffiche=".$nbAffiche."><b>page suivante</b></a></center>";
}

?>	
<center><br>
<a href = index.php>index</a><br><br>

</center>
</body>

</html>

