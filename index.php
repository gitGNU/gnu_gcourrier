<?php
/*
GCourrier
Copyright (C) 2005, 2006, 2007, 2008, 2009  CLISS XXI

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
include('templates/header.php');

if ($_SESSION['login'] == 'admin')
{
?>
<table id="menu_index">
  <tr>
    <td>
      <a href="units.php">Gérer les services</a><br />
      <a href="account.php">Gérer les comptes</a><br />
      <a href="priorities.php">Gérer les priorités</a><br />
      <a href="modifierAccuse.php">Gérer l'accusé</a><br />
    </td>

    <td>
      <img src="images/enveloppe.png" /> <a href="voirCourrier.php?type=1">Voir entrant</a><br />
      <img src="images/enveloppeD.png" /> <a href="voirCourrier.php?type=2">Voir départ</a><br />
      <img src="images/euro.png" /> <a href="voirFacture.php">Voir facture</a><br />
    </td>
</table>

<div id="dco"><a href="logout.php">Déconnexion</a></div>


<?php
#'
} //fin if admin
else
{
?>
Rechercher:
  <a href="rechercher.php?type=1">Entrant</a>
  <a href="rechercher.php?type=2">Départ</a>
  <a href="rechercherFacture.php">Facture</a>
-
Archive:
  <a href="archive.php?type=1">Entrant</a>
  <a href="archive.php?type=2">Départ</a>
  <a href="archiveFacture.php">Facture</a>

<table id="menu_index">
  <tr>
    <td>
      <a href=creerCourrier.php>Créer courrier</a><br />
      <a href=creerFacture.php>Créer facture</a><br />
      <a href=courrierDepart.php>Créer depart</a><br />
      <a href=creerDestinataire.php>Créer un individu</a><br />
      <a href=modifierIndividu.php>Modifier un individu</a>
    </td>
    <td>
      <img src="images/enveloppe.png" /> <a href= voirCourrier.php?type=1>Voir entrant</a><br />
      <img src="images/enveloppeD.png" /> <a href= voirCourrier.php?type=2>Voir départ</a><br />
      <img src="images/euro.png" /> <a href= voirFacture.php>Voir facture</a><br />
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

<?php
}
