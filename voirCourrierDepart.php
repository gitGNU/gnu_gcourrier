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
echo "<center><div id= titre>Courrier Depart</div></center><br>";


if(!isset( $_GET['id'] )){

$re = "select max(id)as id from depart;";
$res = mysql_query( $re ) or die (mysql_error() );
while($ligne = mysql_fetch_array( $res ) ){
	$id = $ligne['id']; 
}

$idTmp = $id;
}

else{
	$idTmp = $_GET['id'];
}





if(strcmp($_SESSION['login'] , 'admin') == 0){
	$requeteEntrant ="select  depart.id as idCourrier,
                                  priorite.nbJours as nbJours,
				  depart.dateArrivee,
				  depart.observation as observation,
				  destinataire.nom as nomDestinataire,
				  destinataire.prenom as prenomDestinataire,
				  depart.libelle as libelleCourrier
		   	   from depart,priorite,destinataire		          
			   where depart.validite = 0			 
			   and depart.idPriorite = priorite.id
			   and depart.idDestinataire = destinataire.id
		           order by depart.id DESC
			   LIMIT 5;";
}
else{

	$requeteEntrant = "select depart.id as idCourrier,
                                  priorite.nbJours as nbJours,
				  depart.dateArrivee,
				  depart.observation as observation,
				  destinataire.nom as nomDestinataire,
				  destinataire.prenom as prenomDestinataire,
				  depart.libelle as libelleCourrier
		   	   from depart,priorite,destinataire
		           where depart.id<=".$idTmp." 
             		   and depart.validite = 0
			   and depart.serviceCourant = ".$_SESSION['idService']."
			   and depart.idPriorite = priorite.id
			   and depart.idDestinataire = destinataire.id
		           order by depart.id DESC
			   LIMIT 5;";
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
	
	
	echo"<td bgcolor=".$couleur."><a href=cheminDepart.php?idCourrier=".$idCourrier.">historique</a></td>";

	if(strcmp($_SESSION['login'] , 'admin') != 0){
		echo"<td bgcolor=".$couleur."><a href=transmettreDepart.php?idCourrier=".$idCourrier.">transmettre</a></td>";
		echo"<td bgcolor=".$couleur."><a href=validerDepart.php?idCourrier=".$idCourrier.">terminer</a></td>";
	}

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
if(mysql_num_rows($resultatEntrant) == 5)
	echo "<center><a href = voirCourrierDepart.php?id=".$idTmp."><b>page suivante</b></a></center>";


?>	


<center><br>
<a href = index.php>index</a><br><br>

</center>
</body>

</html>

