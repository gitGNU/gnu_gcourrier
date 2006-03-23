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

$affiche = $_GET['affiche'];
if(!isset( $_POST["enregistrer"])){
?>
<html>
<head><title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
</head>
<body>
<div id=pageGd><br>
	<center><img src =images/banniere2.jpg></center><br><br><br>

	<table align = center>

	<form name = transmettreForm method = POST action = transmettre.php>
	<tr><td>service</td><td>
	<select name = service>
		<?php
		$requete = "select * from service order by libelle ; ";
		$result = mysql_query($requete) or die( mysql_error() );
		while( $ligne = mysql_fetch_array( $result ) ){
		  if($ligne['libelle'] != "ADMIN")
		  echo "<option value = '".$ligne['id']."'>".$ligne['libelle']." ".$ligne['designation']."</option>";
		}
		?>
	</select></td></tr>
	<?php
	$idCourrier = $_GET["idCourrier"];
	echo"<input type = hidden name = idCourrier value=".$idCourrier."></input>";
	?>
	<tr><td>Observation</td>
	<td><textarea name=observation cols=30 rows=4>
	<?php
	$requete = "Select observation from courrier where id = ".$idCourrier." ; ";
	$result = mysql_query( $requete ) or die (mysql_error( ) );
	$ligne = mysql_fetch_array( $result );
echo $ligne['observation'];
	?>

	</textarea></td></tr>
	</table>
	<center>
<?php
	echo "<input type = hidden name=nbAffiche value=".$affiche."></input>";
	echo "<input type = hidden name=type value=".$_GET['type']."></input>";
?>
	<input type = submit name = enregistrer value = transmettre></input></center>
</form>

<center><br>
<?php
echo"<a href = voirCourrier.php?id=".$idCourrier."&nbAffiche=".$affiche."&type=".$_GET['type'].">voir mon courrier</a><br><br>";
?>
</center>
</div>
</body>
</html>
<?php
}else{
$service = $_POST["service"];
$idCourrier = $_POST["idCourrier"];

$requete = "select * from estTransmis where idCourrier = ".$idCourrier.";";
$result = mysql_query($requete) or die(mysql_error() );


$requete="insert into estTransmis(dateTransmission,idCourrier,idService) values('".date("Y-m-d")."','".$idCourrier."','".$service."');";


$result=mysql_query($requete) or die(mysql_error());

$observation = $_POST['observation'];
$requete = "update courrier set observation ='".$observation."',serviceCourant=".$service." where id =".$idCourrier.";";
$result = mysql_query($requete) or die( mysql_error() );

header("Location: voirCourrier.php?id=".$idCourrier."&nbAffiche=".$_POST['nbAffiche']."&type=".$_POST['type']." ");
}
?>
