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
<head>
<title>gCourrier</title>
<LINK HREF="styles.css" REL="stylesheet">
</head>
<body>
<center>
<img src = images/banniere.jpg><br><br>
</center>

<?php
$log = $_SESSION['login'];
$idCourrier =  $_GET['idCourrier'];
echo"<center><b><font color = white>HISTORIQUE DU COURRIER NUMERO : ".$idCourrier."</font></b></center><br><br>";

//faire un test afin de savoir le type de courrier 
$requeteType= "select idCourrier from facture,courrier
			where facture.idCourrier = '".$idCourrier."'
			and facture.idCourrier = courrier.id;";
$resultatType = mysql_query($requeteType) or die("erreur rtype".mysql_error() );
$ligne = mysql_fetch_array($resultatType);
	if( $ligne != NULL )
		$type="0";
	else $type=1;
	




if($type == 0){
$requete = "select service.libelle as libService,
		   service.designation as desService,
		   chemin.dateTransmission as dateModification,
		   chemin.id as idChemin
		from facture,chemin,courrier,aModifier,service
		where courrier.id = ".$idCourrier."
		and courrier.id = facture.idCourrier
		and courrier.id = aModifier.idCourrier
		and service.id = aModifier.idService
		and aModifier.idChemin= chemin.id
		order by chemin.id;";
}
else{
$requete = "select service.libelle as libService,
		   service.designation as desService,
		   chemin.dateTransmission as dateModification,
		   chemin.id as idChemin
		from chemin,courrier,aModifier,service
		where courrier.id = ".$idCourrier."
		and courrier.id = aModifier.idCourrier
		and service.id = aModifier.idService
		and aModifier.idChemin= chemin.id
		order by chemin.id;";
}

	
	$boul = 0;
	echo "<table align=center font-color ='white'>";
	echo "<tr>";
	echo "<tr>";
	echo "<td align=center><font color = white> service </font></td>";
	echo "<td align=center><font color = white> date de modification </font></td>";
	echo"</tr>";




$result = mysql_query( $requete ) or die (mysql_error() );
while ( $ligne = mysql_fetch_array($result) ){

	if($boul == 0){
		$couleur = lightblue;
		$boul = 1;
	}
	else{
		$couleur = white;
		$boul = 0;	
	}


		echo "<tr>";	
	$date = "";	
	$date .= substr($ligne['dateTransmission'],8,2);
	$date .= "-";
	$date .= substr($ligne['dateTransmission'],5,2);
	$date .= "-";
	$date .= substr($ligne['dateTransmission'],0,4);
	
	$service = $ligne['libService']." ".$ligne['desService'];
	$dateModif = $ligne['dateModification'];
	$login = $ligne['login'];
		
	echo "<td bgcolor=".$couleur."><font color='darkblue'>".$service."</font></td>";
	echo "<td bgcolor=".$couleur."><font color='darkblue'>".$dateModif."</font></td>";
}
echo "</table><br>";

echo"<center><b><a href = historiqueAvance.php>archives</b></a>";

$_SESSION['login'] = "admin";
?>
</body>
</html>
