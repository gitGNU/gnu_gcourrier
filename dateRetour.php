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

if (!isset($_POST['enregistrer']) and !isset($_POST['save_and_archive'])) {
?>
<html>
<head><title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
</head>
<body>
<div id="page"><br>
<center>
<img src="images/banniere2.jpg"><br /><br />
</center>

<form name="dateRetourForm" method="post" action="dateRetour.php">
<center>
<table align="center">

<tr><td>Date retour</td><td>
<?php
	$dateToday = date("d-m-Y"); 
	echo "<input type = text name= date value ='".$dateToday."'></input>";
?></td></tr>
</table>
<?php
$idCourrier =  $_GET['idCourrier'];
echo "<input type = hidden name = idCourrier value =".$idCourrier."></input>";
if(isset($_GET['flag']))
echo "<input type = hidden name = flag value = 1></input>";
?>
<input type="submit" name="enregistrer" value="Enregistrer">
<input type="submit" name="save_and_archive" value="Enregistrer et archiver la facture">
</form>
<br><?php
echo "<br><a href='index.php'>Index</a>";
?>
</center>
<br><br>
</div>
</body>
</html>
<?php
} else {
$date = $_POST['date'];

$tmp = substr($date, 6,4);
$tmp .= '-';
$tmp .= substr($date, 3,2);
$tmp .= '-';
$tmp .= substr($date, 0,2);

$date = $tmp;

$idCourrier = $_POST['idCourrier'];

db_execute("UPDATE estTransmisCopie SET dateRetour=? WHERE id=?",
	   array($date, $idCourrier));

/* Also archive the invoice if requested */
if (isset($_POST['save_and_archive']))
{
  $res = db_execute("SELECT idFacture FROM estTransmisCopie WHERE id=?", array($idCourrier));
  while ($row = mysql_fetch_array($res))
    $idFacture = $row['idFacture'];

  db_execute("UPDATE facture SET validite=1, dateArchivage=?
    WHERE id=? AND idServiceCreation=? AND validite=0",
    array($date, $idFacture, $_SESSION['idService']));
}

if(!isset($_POST['flag']))
header("Location: voirFacture.php");
else
header("Location: rechercherFacture.php");

}//fin else
