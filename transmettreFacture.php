<?php
/*
GCourrier
Copyright (C) 2005, 2006  Cliss XXI

This file is part of GCourrier.

GCourrier is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

GCourrier is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

	<form name = transmettreForm method="post" action="transmettreFacture.php">
	<tr><td>service</td><td>
	<select name = service>
		<?php //'
		$requete = "SELECT * FROM service ORDER BY libelle; ";
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
	<input type="submit" name="enregistrer" value="Transmettre"></input></center>
</form>

<center><br>
<?php
echo "<a href='invoice_list.php?id=$idCourrier'>Voir mes factures</a>";
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
  
  header("Location: invoice_list.php?id=$idCourrier");
}
