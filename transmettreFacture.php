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
*/

require_once('init.php');


if(!isset( $_POST["enregistrer"])){
?>
<html>
<head><title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
</head>
<body>
<div id="page"><br />
	<center><img src="images/banniere2.jpg"><br><br>
	
	<i style="font-size: x-small">Note: les services sont crées par l'administrateur</i>
</center>
	<br>
	
	<table align = center>

	<form name = transmettreForm method="POST" action="transmettreFacture.php">
	<tr><td>service</td><td>
	<select name = service>
		<?php //'
		$requete = "select * from service order by libelle; ";
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
	</table>
	<center>
	<input type = submit name = enregistrer value = transmettre></input></center>
</form>

<center><br>
<?php
echo"<a href = voirFacture.php?id=".$idCourrier.">voir mes factures</a>";
?>
</center>
<br><br>
</div>
</body>
</html>
<?php
} else {
  $service = $_POST['service'];
  $idCourrier = $_POST['idCourrier'];
  
  $date = date("Y-m-d");
  
  $requete = "INSERT INTO estTransmisCopie(idFacture,idService,dateTransmission)
              VALUES ($idCourrier,$service,'$date')";
  $result = mysql_query($requete) or die(mysql_error());
  
  $requete = "SELECT service.libelle AS libService
              FROM service 
	      WHERE service.id=$service";
  
  $result = mysql_query($requete) or die(mysql_error());
  while ($ligne = mysql_fetch_array($result))
    $libService = $ligne['libService'];
  $requete = "UPDATE facture SET histo='$libService' WHERE id='$idCourrier'";
  $result = mysql_query($requete) or die(mysql_error());
  
  header("Location: voirFacture.php?id=$idCourrier");
}
