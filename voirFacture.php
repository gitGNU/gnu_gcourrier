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
require_once('classes/SQLDataGrid.php');

include('templates/header.php');

$requeteInit = "Select id from facture Limit 1;";
$result = mysql_query($requeteInit) or die(mysql_error());
if(mysql_num_rows($result) == 0){
echo "<div style='text-align: center'>" . _("Aucune facture pour le moment.") . "</div>";
echo "<div style='text-align: center'><a href='index.php'>Index</a></div>";
exit();
}
?>

<form method="post" action="rechercheRapideFacture.php#result">
<table align=center style="border: 1px dotted black;"><tr><td>
<label>Aller à la facture numéro:</label>
<input type="text" name="numero" value="1" size="2"></input>
<input type="submit" name="ok" value="OK"></input>
<br /><center><span style="font-size: x-small"><a href="rechercherFacture.php">Recherche avancée</a></span></center>
</td></tr></table></form>

<?php

echo"<center><div id= titre>Factures / <a href='copieFacture.php'>Copies de Factures</a>";
echo "<br/><br/>";
echo "<i style='font-size:10px; font-weight:normal'>";
echo _("Voici les les factures en cours de votre service.");
echo "</i><br/><br/></div></center>";


if ($_SESSION['login'] == 'admin')
{
  $requeteFacture = "SELECT facture.id AS idFacture,
			  facture.histo AS histo,
  			  refFacture AS refFacture,
			  refuse AS refuse,
			  montant AS montant,
			  dateFacture AS dateFacture,
			  dateFactureOrigine AS dateFactureOrigine,
			  observation AS observation,
			  destinataire.nom AS nomFournisseur,
			  destinataire.id AS idDest,
			  destinataire.prenom AS prenomFournisseur,
			  priorite.nbJours AS nbJours,
			  unix_timestamp(datesaisie) AS internal_timestamp
 		   FROM facture, destinataire, priorite
		   WHERE facture.idFournisseur = destinataire.id
		   AND validite = 0
		   AND facture.idPriorite = priorite.id";
}
else
{
  $requeteFacture = "SELECT facture.id AS idFacture,
			  facture.histo AS histo,
			  refuse AS refuse,
  			  facture.refFacture AS refFacture,
			  facture.dateFacture AS dateFacture,
			  facture.dateFactureOrigine AS dateFactureOrigine,
			  facture.observation AS observation,			  
			  facture.montant AS montant,
			  destinataire.nom AS nomFournisseur,
			  destinataire.id AS idDest,
			  destinataire.prenom AS prenomFournisseur,
			  priorite.nbJours AS nbJours,
			  unix_timestamp(datesaisie) AS internal_timestamp
 		    FROM facture,destinataire,priorite
		    WHERE facture.validite = 0
			  AND facture.idServiceCreation = ".$_SESSION['idService']."
			  AND facture.idPriorite = priorite.id
			  AND facture.idFournisseur = destinataire.id";
}

$sdg = new SQLDataGrid($requeteFacture,
		       array('No' => array('sqlcol' => 'idFacture',
					   'callback' => 'printId'),
			     'Fournisseur' => array('sqlcol' => 'nomFournisseur',
						    'callback' => 'printProvider'),
			     'Ref.' => array('sqlcol' => 'refFacture',
					     'callback' => 'printReference'),
			     'Mont.' => array('sqlcol' => 'montant',
						'callback' => 'printAmount'),
			     'Date Mairie' => array('sqlcol' => 'dateFacture',
						    'callback' => 'printArrivalDate'),
			     'Date Facture' => array('sqlcol' => 'dateFactureOrigine',
						     'callback' => 'printEmissionDate'),
			     'Observation' => array('sqlcol' => 'observation',
						    'callback' => 'printComments'),
			     'Historique' => array('sqlcol' => 'histo',
						   'callback' => 'printHistory'),
			     'Transmettre' => array('callback' => 'printTransmit'),
			     'Archiver' => array('callback' => 'printArchive'),
			     'Jours restant' => array('style' => 'text-align: center',
						      'callback' => 'printRemainingDays'),
			     ));

$sdg->setPagerSize($_SESSION['pagersize']);
$sdg->setDefaultSort(array('idFacture' => 'DESC'));
$sdg->setClass('resultats');
if (!empty($_GET['idFactureRecherche']))
  $sdg->setDefaultPageWhere(array('idFacture' => $_GET['idFactureRecherche']));
if (!empty($_GET['id']))
  $sdg->setDefaultPageWhere(array('idFacture' => $_GET['id']));
$sdg->display();



function printId($params)
{
  $row = $params['record'];
  if ($row['refuse'] == 1)
    return "<span style='background: red'>{$row['idFacture']}</span>";
  else
    return $row['idFacture'];
}

function printProvider($params)
{
  $row = $params['record'];
  return "<a href='modifDestinataire.php?idCourrier={$row['idFacture']}'>"
    . "{$row['nomFournisseur']}"
    . " {$row['prenomFournisseur']}"
    . "</a>";
}

function printReference($params)
{
  $row = $params['record'];
  $ref = $row['refFacture'];
  if ($ref == '')
    $ref = _('Modifier');
  return "<a href='modifRef.php?idCourrier={$row['idFacture']}'>$ref</a>";
}

function printAmount($params)
{
  $row = $params['record'];
  $amount = $row['montant'];
  if ($amount == '')
    $amount = _('Modifier');
  return "<a href='modifMontant.php?idCourrier={$row['idFacture']}'>$amount</a>";
}

function printArrivalDate($params)
{
  $row = $params['record'];
  $tmpdateArrivee = $row['dateFacture'];
  $dateArrivee = substr($tmpdateArrivee,8,2)
    . '/' . substr($tmpdateArrivee,5,2)
    . '/' . substr($tmpdateArrivee,0,4);
  return
    ((time() - $row['internal_timestamp']) < (24 * 60 * 60))
    ? "<a href='editBillDate.php?id={$row['idFacture']}'>$dateArrivee</a>"
    : $dateArrivee;
}

function printEmissionDate($params)
{
  $row = $params['record'];
  $emission_date = $row['dateFactureOrigine'];
  $emission_date = substr($emission_date, 8, 2)
    . '/' . substr($emission_date, 5, 2)
    . '/' . substr($emission_date, 0, 4);
  return "<a href='editBillDate.php?id={$row['idFacture']}'>$emission_date</a>";

}

function printComments($params)
{
  $row = $params['record'];
  $comments = $row['observation'];
  if ($comments == '')
    $comments = _('Modifier');
  return "<a href='modifObservationFacture.php?idCourrier={$row['idFacture']}'".
    " style='font-size: smaller'>$comments</a>";
}

function printHistory($params)
{
  $row = $params['record'];
  $histo = $row['histo'];
  return "<a href='cheminFacture.php?idCourrier={$row['idFacture']}'>$histo</a>";
}

function printTransmit($params)
{
  $row = $params['record'];
  return "<a href='transmettreFacture.php?idCourrier={$row['idFacture']}'>Transmettre</a>";
}

function printArchive($params)
{
  $row = $params['record'];
  return "<a href='validerFacture.php?idCourrier={$row['idFacture']}'>Archiver</a>";
}

function printRemainingDays($params)
{
  // test pour urgence du courrier
  $dateActuel = date("Y-m-d");
  $jourActuel = substr($dateActuel,8,2);
  $moisActuel = substr($dateActuel,5,2);
  $anneeActuel= substr($dateActuel,0,4);
  
  $tmpDateArrivee = $params['record']['dateFacture'];
  $jourArrivee =substr($tmpDateArrivee,8,2);
  $moisArrivee =substr($tmpDateArrivee,5,2);
  $anneeArrivee =substr($tmpDateArrivee,0,4);
  
  //		echo " feafaoho".$tmpdateArrivee;
  
  $nbJours = $params['record']['nbJours'];
  
  $timestampActuel = mktime(0,0,0,$moisActuel,$jourActuel,$anneeActuel);
  $timestampArrivee= mktime(0,0,0,$moisArrivee,$jourArrivee,$anneeArrivee);
  $urgence = ($timestampActuel - $timestampArrivee ) / 86400;
  
  // round value to take 'dailight saving time' into account (=> no float)
  $nbJoursRestant = (int) ($nbJours - $urgence);
  
  //		echo " fze ".$urgence." : ".$nbJours." :: ".$ligne['nbJours']."<br>";
  if ($nbJoursRestant >= 5)
    $alerte = "green";
  else
    $alerte = "red";
  
  
  return "<span style='color:$alerte;font-weight:bold'>$nbJoursRestant</span>";
}

include('templates/footer.php');
