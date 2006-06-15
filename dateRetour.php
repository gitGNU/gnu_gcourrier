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
if(!isset($_POST['enregistrer'])){
?>
<html>
<head><title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
</head>
<body>
<div id = page><br>
<center>
<img src = images/banniere2.jpg><br><br>
</center>

<form name = dateRetourForm method = POST action = dateRetour.php>
<center>
<table align = center>

<tr><td>date retour</td><td>
<?php
	$dateToday = date("d-m-Y"); 
	echo "<input type = text name= date value ='".$dateToday."'></input>";
?></td></tr>
</table>
<?php
$idCourrier =  $_GET['idCourrier'];
echo "<input type = hidden name = idCourrier value =".$idCourrier."></input>";
?>
<input type = submit name = enregistrer value = enregistrer>
<br><?php
echo "<br><a href = index.php>index</a>";
?>
</center>
<br><br>
</div>
</body>
</html>
<?php
}else{
$date = $_POST['date'];
//$date1 = $date;

$tmp= substr($date, 6,4);
$tmp.='-';
$tmp.=substr($date, 3,2);
$tmp.='-';
$tmp.=substr($date, 0,2);

$date = $tmp;

$idCourrier = $_POST['idCourrier'];
/*
$requete ="select histo from facture where id =".$idCourrier.";";
$result = mysql_query($requete ) or die("1".mysql_error() );
while($ligne = mysql_fetch_array($result)){
	$histo = $ligne['histo'];
}
$tmpHisto = explode("-",$histo);
$tmpHisto2 = $tmpHisto[0];
$tmpHisto2 .= " - ".$date1; 


$requete = "update facture set histo ='".$tmpHisto2."' where id=".$idCourrier.";";
$result = mysql_query($requete) or die ("2".mysql_error( ) );
*/

$requete ="update estTransmisCopie set dateRetour = '".$date."' where id =".$idCourrier.";";
$result = mysql_query($requete) or die ("3".mysql_error( ) );

header("Location: voirFacture.php");

}//fin else
?>


