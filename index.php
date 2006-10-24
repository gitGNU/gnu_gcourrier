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

require_once('connexion.php');

include('templates/header.php');

if ($_SESSION['login'] == 'admin')
  {
?>

<div id="page">
<br><br><br>

<table>
<tr>
<td>
<div id="logoimage"><img src="images/logo.png"></div>
</td>
<div id="logo">
<td>
<a href="creerService.php">Créer service</a><br>
<a href="creerCompte.php">Créer compte</a><br>
<a href="creerPriorite.php">Créer priorite</a><br>
<a href="modifierAccuse.php">Gérer l'accusé</a><br>

</td>
</div>

<td>
<div id="logo2">
<img src="images/enveloppe.png"></img>&nbsp;&nbsp;<a href="voirCourrier.php?type=1">Voir entrant</a><br>
<img src="images/enveloppeD.png"></img>&nbsp;&nbsp;<a href="voirCourrier.php?type=2">Voir départ</a><br>
<img src="images/euro.png"></img>&nbsp;&nbsp;<a href="voirFacture.php">Voir facture</a><br>

</div>
</td>



<td>
<div id="logo3">
<a href="modifierProfil.php">Profil</a><br>
<a href="voirCompte.php">Comptes</a><br>
<a href="rechercherCompteService.php">CompServ</a>
</div>
</td>


</table>

<br><br><br><div id="dco"><a href="logout.php">Déconnexion</a></div><br>
</div>


<?php
  } //fin if admin
else
  {
?>
<div id="page">
Rechercher:
  <a href="rechercher.php?type=1">Entrant</a>
  <a href="rechercher.php?type=2">Départ</a>
  <a href="rechercherFacture.php">Facture</a>
-
Archive:
  <a href="archive.php?type=1">Entrant</a>
  <a href="archive.php?type=2">Départ</a>
  <a href="archiveFacture.php">Facture</a>

<table style="width: 100%">
  <tr>
    <td id="logoimage"><img src = "images/logo.png"></td>
    <td id="logo1">
      <a href=creerCourrier.php>Créer courrier</a><br />
      <a href=creerFacture.php>Créer facture</a><br />
      <a href=courrierDepart.php>Créer depart</a><br />
      <a href=creerDestinataire.php>Créer un individu</a><br />
      <a href=modifierIndividu.php>Modifier un individu</a>
    </td>
    <td id="logo2">
      <img src="images/enveloppe.png"></img>&nbsp&nbsp<a href= voirCourrier.php?type=1>Voir entrant</a><br />
      <img src="images/enveloppeD.png"></img>&nbsp&nbsp<a href= voirCourrier.php?type=2>Voir départ</a><br />
      <img src="images/euro.png"></img>&nbsp&nbsp<a href= voirFacture.php>Voir facture</a><br />
    </td>
    <td id="logo3">
      <a href = modifierProfil.php>Profil</a><br>
    </td>
  </tr>
</table>

<div id="stats">
<?php
$requete = "SELECT count(*) AS nbEntrant FROM courrier WHERE courrier.serviceCourant={$_SESSION['idService']} AND type=1 AND validite=0";
$result = mysql_query($requete) or die(mysql_error());
$ligne = mysql_fetch_array($result);
$nbEntrant = $ligne['nbEntrant'];

$requete = "SELECT count(*) AS nbDepart FROM courrier WHERE courrier.serviceCourant={$_SESSION['idService']} AND type=2 and validite=0";
$result = mysql_query($requete) or die(mysql_error());
$ligne = mysql_fetch_array($result);
$nbDepart = $ligne['nbDepart'];

$requete = "SELECT count(*) AS nbFacture FROM facture WHERE facture.idServiceCreation={$_SESSION['idService']} AND validite=0";
$result = mysql_query($requete) or die(mysql_error());
$ligne = mysql_fetch_array($result);
$nbFacture = $ligne['nbFacture'];

echo "Info : $nbEntrant courriers entrants - $nbDepart courriers départ - $nbFacture factures\n";
?>
</div>

<div><a href=logout.php>Déconnexion</a></div>

</div> <!-- id="page" -->

<?php
}
