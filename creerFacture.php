<?php
/*
GCourrier
Copyright (C) 2005, 2006, 2007  Cliss XXI

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
require_once('functions/priority.php');

include('templates/header.php');

if (isset($_POST["enregistrer"]) or isset($_POST["enregistrer_puis_copie"])) {
  $service = $_POST['serviceDest'];
  $montant=$_POST["montant"];
  $refFacture=$_POST["numFacture"];
  $dateFacture=$_POST["dateFacture"];
  $dateFactureOrigine=$_POST["dateFactureOrigine"];
  $observation=$_POST["observation"];
  $idServiceCreation = $_SESSION["idService"];
  $idPriorite = $_POST["priorite"];
  $idFournisseur = $_POST["fournisseur"];
  
  $tmp= substr($dateFacture, 6,4);
  
  $tmp.='-';
  $tmp.=substr($dateFacture, 3,2);
  $tmp.='-';
  $tmp.=substr($dateFacture, 0,2);
  $dateFacture = $tmp;
  
  $tmp2= substr($dateFactureOrigine, 6,4);
  $tmp2.='-';
  $tmp2.=substr($dateFactureOrigine, 3,2);
  $tmp2.='-';
  $tmp2.=substr($dateFactureOrigine, 0,2);
  $dateFactureOrigine = $tmp2;
  
  $requete = "SELECT libelle FROM service WHERE id=".$service.";";
  $result = mysql_query($requete) or die(mysql_error() );
  while($ligne = mysql_fetch_array($result)){
    $serviceLib = $ligne['libelle'];
  }
  
  $requeteCourrier = "insert into facture(montant,refFacture,dateFacture,dateFactureOrigine, observation,idServiceCreation,idPriorite,idFournisseur,histo) values('".$montant."','".$refFacture."','".$dateFacture."','".$dateFactureOrigine."','".$observation."','".$idServiceCreation."','".$idPriorite."','".$idFournisseur."','".$serviceLib."');";
  $resultatCourrier = mysql_query( $requeteCourrier ) or die ("erreur requete courrier :".mysql_error( ) );
  
  $requeteIdCourrier = "select id from facture order by id;";
  $resultatIdCourrier = mysql_query( $requeteIdCourrier ) or die ("erreur requete idCourrier".mysql_error( ) );
  
  while($ligne = mysql_fetch_array($resultatIdCourrier ))
    $idCourrier = $ligne['id'];
  
  $date = date("Y-m-d");
  
  $requete = "insert into estTransmisCopie( idFacture, idService,dateTransmission ) values(".$idCourrier.",".$service.",'".$date."');";
  $result = mysql_query($requete ) or die(mysql_error() );
  
  $status = "Vous venez de créer la facture numéro: <strong>$idCourrier</strong>.";

  $copy = 0;
  if (isset($_POST["enregistrer_puis_copie"]))
    {
      $copy = 1;
    }
}


if (isset($status)) {
  echo "<div class='status'>$status</div>";
}
?>
<table align = center>
<form name = creerFactureForm method = POST action = creerFacture.php>
<tr>
<td>Fournisseur</td>
<td><select name = fournisseur>

<?php
$requete = "SELECT * FROM destinataire ORDER BY nom;";
$result = mysql_query($requete) or die(mysql_error());
while ($ligne = mysql_fetch_array($result)) {
  $selected = '';
  if ($copy and $ligne['id'] == $idFournisseur)
    $selected = 'SELECTED';

  echo "<option $selected value='{$ligne['id']}'>"
    . "{$ligne['nom']} {$ligne['prenom']}"
    . "</option>";
}
?>

</select>[<a href="creerDestinataire.php">Créer</a>]</td></tr>

<tr>
<td>Service Destinataire</td>
<td><select name="serviceDest">

<?php
$requete = "SELECT * FROM service WHERE libelle <> 'admin' ORDER BY libelle;";
$result = mysql_query($requete) or die (mysql_error());
while ($ligne = mysql_fetch_array( $result )) {
  $selected = '';
  if ($copy and $ligne['id'] == $service)
    $selected = 'SELECTED';
  
  echo "<option $selected value='{$ligne['id']}'>"
    . "{$ligne['libelle']} {$ligne['designation']}"
    . "</option>";
}
?>

</td>
</tr>


<tr>
<td>Référence facture</td>
<td><input type = text name = numFacture></input></td></tr>
<tr><td>Date mairie</td><td>

<?php
$date = date("d-m-Y"); // today
if ($copy)
     $date = $_POST['dateFacture'];
echo "<input type='text' name='dateFacture' value='{$date}' />";
?>

</td></tr>
<tr><td>Date facture</td><td>

<?php
$date = date("d-m-Y"); // today
if ($copy)
     $date = $_POST['dateFactureOrigine'];
echo "<input type='text' name='dateFactureOrigine' value='{$date}' />";
?>

</td></tr>		

<tr><td>Montant</td>
<td><input type = text name = montant></input></td></tr>

<?php
priority_display();
?>

<tr>
<td>Observation</td>
<td><textarea name=observation cols=30 rows=4></textarea></td></tr>
</table>

<center>
<input type="submit" name="enregistrer_puis_copie"
  value="Enregistrer puis commencer une facture du même fournisseur">
<input type="submit" name="enregistrer"
  value ="Enregistrer puis commencer une facture vierge">
</center>
</form>

<center>

<?php
include('templates/footer.php');
