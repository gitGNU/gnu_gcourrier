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
?>

<html>
<head><title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
</head>
<body>
<div id = pageGd><br>
<center>
<img src = images/banniere2.jpg><br><br>
</center>

<?php
$log = $_SESSION['login'];
$idCourrier =  $_GET['idCourrier'];
echo"<center>HISTORIQUE DU COURRIER NUMERO : ".$idCourrier."</center><br><br>";

$requete = "select service.libelle as libService,
		   service.designation as desService,
		   estTransmis.dateTransmission as dateTransmission,
		   estTransmis.danger as danger
	    from courrier, estTransmis,service
	    where courrier.id = estTransmis.idCourrier
	    and courrier.id = ".$idCourrier."
	    and estTransmis.idService = service.id
	    order by estTransmis.id ASC;";
	
	$boul = 0;
	echo "<table align=center>";
	echo "<tr>";
	echo "<td align=center>service</td>";
	echo "<td align=center>date de modification</td>";
	echo "<td></td>";
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

	echo "<td bgcolor=".$couleur.">".$service."</td>";
	echo "<td bgcolor=".$couleur.">".$date."</td>";
if($ligne['danger']==1)
		echo "<td ><img src=\"images/attention.png\"></img></td>";
	else 
		echo "<td></td>";

}
echo "</table><br>";


?>

<br/>
<center><a href="javascript:history.go(-1)"> <b>retourner au resultat</b></a>

<br><br>
</div>
</body>
</html>
