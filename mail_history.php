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
require_once('functions/mail.php');
require_once('functions/service.php');

include('templates/header.php');

$log = $_SESSION['login'];
$idCourrier = intval($_GET['idCourrier']);
echo "<p>Historiquer du courrier numéro : $idCourrier</p>";
echo "<h2>Transmissions</h2>";

$requete = "SELECT service.libelle AS libService,
		   service.designation AS desService,
		   UNIX_TIMESTAMP(estTransmis.dateTransmission) AS dateTransmission,
		   estTransmis.danger AS danger
	    FROM courrier,estTransmis,service
	    WHERE courrier.id = estTransmis.idCourrier
	    AND courrier.id = ".$idCourrier."
	    AND estTransmis.idService = service.id
	    ORDER BY estTransmis.id ASC;";
	
	$boul = 0;
	echo "<table>";
	echo "<tr>";
	echo "<td>Service</td>";
	echo "<td>Date de modification</td>";
	echo "<td></td>";
	echo"</tr>";

$result = db_execute($requete);

$boul = true;
while ($ligne = mysql_fetch_array($result)) {
  if ($boul)
    $couleur = "lightblue";
  else
    $couleur = "white";
  $boul = !$boul;
  
  echo "<tr>";
  $date = strftime("%x", $ligne['dateTransmission']);
  $service = "{$ligne['desService']} ({$ligne['libService']})";
  
  echo "<td bgcolor=".$couleur.">".$service."</td>";
  echo "<td bgcolor=".$couleur.">".$date."</td>";
  
  if ($ligne['danger'] == 1)
    echo "<td ><img src=\"images/attention.png\"></img></td>";
  else
    echo "<td></td>";
}
echo "</table><br>";


echo "<h2>Autres changements</h2>";

$history = mail_get_history($idCourrier);
echo "<table>";
echo "<tr><th>Service</th><th>Date</th><th>Message</th></tr>";
$boul = true;
foreach ($history as $timestamp => $row) {
  if ($boul)
    $couleur = "lightblue";
  else
    $couleur = "white";
  $boul = !$boul;

  $service = service_getbyid($row['service_id']);
  print "<tr bgcolor='$couleur'><td>";
  print $service['description'] . "({$service['label']})";
  print "</td><td>";
  print strftime('%x', $timestamp);
  print "</td><td>";
  print $row['message'];
  print "</td></tr>";
}
echo "</table>";


?>
<p><a href="javascript:history.go(-1)">Retourner au résultat</p>
<?php
include('templates/footer.php');
