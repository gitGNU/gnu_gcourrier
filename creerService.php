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

if (!isset($_POST["envoyer"])) {
  include('templates/header.php');
?>
<form name="creerServiceForm" method="post" action="creerService.php">
  <table>
    <tr>
      <td>Libellé</td>
      <td><input type="text" name="libelle" /></td>
    </tr>
    <tr>
      <td>Désignation</td>
      <td><input type="text" name="designation" /></td>
    </tr>
  </table>
  <input type="submit" name="envoyer" value="enregistrer" />
</form>
<?php
} else {
  $libelle = $_POST['libelle'];
  $designation = $_POST['designation'];
  
  $requeteVerif="SELECT id FROM service WHERE libelle='$libelle'";
  $result = mysql_query($requeteVerif) or die(mysql_error());
  if(mysql_num_rows($result) !=0 ){
    echo "Ce service existe déjà!</br>";
    echo "<a href=index.php>index</a>";
    exit();
  }
  
  $requete = "INSERT INTO service(libelle, designation) VALUES ('$libelle','$designation')";
  $result = mysql_query($requete) or die("erreur: " . mysql_error( ));
  header('Location: index.php');
}
