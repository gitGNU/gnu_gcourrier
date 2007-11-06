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

if (!isset($_GET['valider'])) {
  include('templates/header2.php');

$idCourrier = $_GET['idCourrier'];
?>
  <div id="pageTGd"><br />
  <center>
  <img src="images/banniere2.jpg" /><br /><br /><br />
<?php
     echo _("Êtes-vous sûr(e) de vouloir archiver cette facture?");
?>
  <br />
  <a href="validerFacture.php?idCourrier=<?php echo $idCourrier?>&valider=o">Oui</a>
  &nbsp; &nbsp;
<?php
  echo "<a href='voirFacture.php?id=$idCourrier#result'>Non</a>";
?>
  <br /><br />
<?php
# include('templates/footer.php');
} else {
  $idCourrier = $_GET['idCourrier'];
  $date = date('Y-m-d');
  $requete = "UPDATE facture SET validite=1, dateArchivage='$date'
    WHERE id='$idCourrier' AND idServiceCreation='{$_SESSION['idService']}'
          AND validite=0";
  $result = mysql_query($requete) or die(mysql_error());
  header('Location: voirFacture.php');
}
