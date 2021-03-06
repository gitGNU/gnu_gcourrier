<?php
/*
GCourrier
Copyright (C) 2005, 2006, 2007, 2008, 2009, 2010  Cliss XXI

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
      <img src="images/enveloppe.png" /> <a href="mail_list_my.php?type=1">Voir entrant</a><br />
      <img src="images/enveloppeD.png" /> <a href="mail_list_my.php?type=2">Voir départ</a><br />
      <img src="images/euro.png" /> <a href="invoice_list.php">Voir facture</a><br />
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
  <a href="mail_list.php?type=1">Entrant</a>
  <a href="mail_list.php?type=2">Départ</a>
  <a href="rechercherFacture.php">Facture</a>
-
Archive:
  <a href="mail_list.php?archived=1&amp;type=1">Entrant</a>
  <a href="mail_list.php?archived=1&amp;type=2">Départ</a>
  <a href="archiveFacture.php">Facture</a>

<br />
<br />

<table id="menu_index">
  <tr>
    <td>
      <a href="creerCourrier.php">Créer courrier</a><br />
      <a href="creerFacture.php">Créer facture</a><br />
      <a href="courrierDepart.php">Créer départ</a><br />
      <a href="creerDestinataire.php">Créer un individu</a><br />
      <a href="modifierIndividu.php">Modifier un individu</a><br />
      <a href="streets.php">Gérer les noms des rues</a><br />
    </td>
    <td>
      <strong>Mon service</strong><br />
      <br />
      <img src="images/enveloppe.png" /> <a href="mail_list_my.php?type=1">Voir entrant</a><br />
      <img src="images/enveloppeD.png" /> <a href="mail_list_my.php?type=2">Voir départ</a><br />
      <img src="images/euro.png" /> <a href="invoice_list.php">Voir facture</a><br />
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
include('templates/footer.php');
