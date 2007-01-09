<?php
/*
Display bills so they can be reviewed and modified
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
*/

require_once('HTML/QuickForm.php');
require_once('Structures/DataGrid.php');

require_once('init.php');
require_once('functions/db.php');
require_once('functions/grid.php');


include('templates/header.php');


echo"<center><div id='titre'>Factures  / <a href=copieFacture.php >Copies de Factures</a><br/><br/></div></center>";

echo "<a href=rechercherFacture.php><font size=1px>";
echo "<center>" . _("Recherche avancée") . "</center>\n";
echo "</font></a>";

# Modify an existing element?
# Analyse the page parameters
$params = new HTML_QuickForm('browse', 'get');
$params->addElement('text', 'id', 'Rechercher la facture numéro');
$params->addElement('text', 'nbAffiche', 'Nombre de facture à afficher');
$params->addElement('submit', null, 'OK');

// id
$res = db_execute("SELECT MAX(id) AS id FROM facture");
$line = mysql_fetch_array($res);
$params->setDefaults(array('id' => $line['id']));

$params->addRule('id', NULL, 'callback', 'ctype_digit');
$params->addRule('id', NULL, 'nonzero');

// nbAffiche
$pagersize = $CURRENT_USER['pagersize'];
$params->setDefaults(array('nbAffiche' => $pagersize));

$params->addRule('nbAffiche', NULL, 'callback', 'ctype_digit');
$params->addRule('nbAffiche', NULL, 'nonzero');


if ($params->validate()) {
  $id = $params->exportValue('id');
  $nbAffiche = $params->exportValue('nbAffiche');
}
$params->display();

if (!isset($_GET['nbAffiche'])) {
  $req = "SELECT * FROM utilisateur WHERE login = '{$_SESSION['login']}'";
  $result = mysql_query($req) or die(mysql_error());
  while ($ligne = mysql_fetch_array($result)) {
    $nbAffiche = $ligne['preferenceNbCourrier'];
  }
} else {
  $nbAffiche = $_GET['nbAffiche'];
}


// Instantiate the DataGrid
$dg = new Structures_DataGrid($nbAffiche);
$dg->setDefaultSort(array('idFacture' => 'DESC'));

$filter_current_service = '';
if ($CURRENT_USER['login'] != 'admin') {
  $filter_current_service
    = " AND facture.idServiceCreation = {$CURRENT_USER['idService']} ";
}
$filter_open = " AND facture.validite=0 ";

$query = "SELECT facture.id AS idFacture,
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
                          priorite.nbJours AS nbJours
                    FROM facture,destinataire,priorite
                    WHERE
                      -- joins
                      facture.idFournisseur = destinataire.id
                      AND facture.idPriorite = priorite.id
                      -- filters
                      $filter_open
                      $filter_current_service";
$test = $dg->bind($query,
                  array('dsn' => $db_dsn));
if (PEAR::isError($test)) {
  echo $test->getMessage();
  exit;
}

$dg->addColumn(new Structures_DataGrid_Column('N°', 'idfacture', 'idfacture'));
$dg->addColumn(new Structures_DataGrid_Column('Fournisseur', 'nomfournisseur', 'nomfournisseur'));
$dg->addColumn(new Structures_DataGrid_Column('Réf.', 'reffacture', 'reffacture'));
$dg->addColumn(new Structures_DataGrid_Column('Montant', 'montant', 'montant'));
$dg->addColumn(new Structures_DataGrid_Column('Date mairie', 'datefacture', 'datefacture'));
$dg->addColumn(new Structures_DataGrid_Column('Date facture', 'datefactureorigine', 'datefactureorigine'));
$dg->addColumn(new Structures_DataGrid_Column('Observation', 'observation', 'observation'));
grid_table($dg, _("Factures en cours enregistrées par votre service"));


if ($_SESSION['login'] == 'admin') {
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
			  priorite.nbJours AS nbJours
 		   FROM facture,destinataire,priorite
		   WHERE facture.idFournisseur = destinataire.id
		   AND validite = 0
		   AND facture.idPriorite = priorite.id
		   ORDER BY facture.id DESC
	           LIMIT $nbAffiche";
} else {
  if(!isset($_GET['idFactureRecherche'])){
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
			  priorite.nbJours AS nbJours
 		    FROM facture,destinataire,priorite
		    WHERE facture.id <= $id
             		   AND facture.validite = 0
			   AND facture.idServiceCreation = {$_SESSION['idService']}
			   AND facture.idPriorite = priorite.id
			   AND facture.idFournisseur = destinataire.id
		           ORDER BY facture.id DESC
			   LIMIT $nbAffiche";
  } else {
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
			  priorite.nbJours AS nbJours
 		    FROM facture,destinataire,priorite
		    WHERE facture.id = {$_GET['idFactureRecherche']}
             		   AND facture.validite = 0
			   AND facture.idServiceCreation = {$_SESSION['idService']}
			   AND facture.idPriorite = priorite.id
			   AND facture.idFournisseur = destinataire.id
		           ORDER BY $order DESC
			   LIMIT $nbAffiche";
  }
}

$resultatFacture = mysql_query($requeteFacture) or die("erreur facture ".mysql_error() );

echo "<table align=center font-color ='white'>";
echo "<tr>";
echo "<td align=center><a href=voirFacture?order=facture.id style=\"font-weight:normal\">numero</a> </td>";
echo "<td align=center><a href=voirFacture?order=facture.idFournisseur style=\"font-weight:normal\">fournisseur</a></td>";
echo "<td align=center><a href=voirFacture?order=facture.refFacture style=\"font-weight:normal\">refFacture</a></td>";
echo "<td align=center><a href=voirFacture?order=facture.montant style=\"font-weight:normal\">montant</a></td>";
echo "<td align=center><a href=voirFacture?order=facture.dateFacture style=\"font-weight:normal\">dateMairie</a></td>";
echo "<td align=center><a href=voirFacture?order=facture.dateFactureOrigine style=\"font-weight:normal\">dateFacture</a></td>";
echo "<td align=center>observation</td>";
echo "<td align=center>historique</td>";
if (strcmp($_SESSION['login'] , 'admin') != 0) {
  echo "<td align=center>transmettre</td>";
  echo "<td align=center>terminer</td>";
}
echo "<td align=center>jours restant</td>";
echo"</tr>";

$boul = 0;
while ($ligne = mysql_fetch_array($resultatFacture)) {
  $id = $ligne['idFacture'] - 1;
  if ($boul == 0) {
    $couleur = 'lightblue';
    $boul = 1;
  } else {
    $couleur = 'white';
    $boul = 0;	
  }
  echo "<tr>";

  $refuse = $ligne['refuse'];	
  $dest = $ligne['nomFournisseur'];
  $idCourrier = $ligne['idFacture'];
  $nomDestinataire = $ligne['nomFournisseur']." ".$ligne['prenomFournisseur'];
  $refFacture = $ligne['refFacture'];
  $montant = $ligne['montant'];
  $tmpdateArrivee = $ligne['dateFacture']; 
  $dateArrivee = substr($tmpdateArrivee,8,2)."-".substr($tmpdateArrivee,5,2)."-".substr($tmpdateArrivee,0,4);
  $tmpdateFacture = $ligne['dateFactureOrigine'];
  $dateFacture=substr($tmpdateFacture,8,2)."-".substr($tmpdateFacture,5,2)."-".substr($tmpdateFacture,0,4);
  $observation = $ligne['observation'];
  
  $tmpMontant = $montant;
  $tmpMontant .= "00";
  $tmpMontant2 = $montant * 100;
  
  if(strcmp($tmpMontant,$tmpMontant2) == 0){
    $montant .= ",00";
  }
  
  if (strcmp($refFacture,"") == 0)
    $refFacture = "modifier";
  if (strcmp($montant,"") == 0)
    $montant = "modifier";
  if (strcmp($dest,"") == 0)
    $nomDestinataire = "modifier";
  if (strcmp($observation,"") == 0)
    $observation ="modifier";
  
  if ($refuse == 1)
    $couleur = 'red';
  
  echo "<td bgcolor='$couleur'>$idCourrier</td>";
  echo "<td bgcolor=".$couleur."><a href=modifDestinataire.php?idCourrier=".$idCourrier." style=\"text-decoration :none;font-weight:normal\">".$nomDestinataire."</a></td>";
  echo "<td bgcolor=".$couleur." style=\"text-align:center\"><a href=modifRef.php?idCourrier=".$idCourrier." style=\"text-decoration :none;font-weight:normal\">".$refFacture."</a></td>";
  echo "<td bgcolor=".$couleur." style=\"text-align:right\"><a href=modifMontant.php?idCourrier=".$idCourrier." style=\"text-decoration :none;font-weight:normal\">".$montant."</a></td>";
  echo "<td bgcolor=".$couleur.">".$dateArrivee."</td>";
  echo "<td bgcolor=".$couleur.">".$dateFacture."</td>";
  
  echo "<td bgcolor=".$couleur."><a href=modifObservationFacture.php?idCourrier=".$idCourrier." style=\"text-decoration :none;font-weight:normal\">".$observation."</a></td>";
  
  
  
  echo"<td bgcolor=".$couleur."><a href=cheminFacture.php?idCourrier=".$idCourrier." style=\"text-decoration :none;font-weight:normal\">".$ligne['histo']."</center></a></td>";

  
  
  if(strcmp($_SESSION['login'] , 'admin') != 0){
    
    echo"<td bgcolor=".$couleur."><a href=transmettreFacture.php?idCourrier=".$idCourrier.">transmettre</a></td>";
    echo"<td bgcolor=".$couleur."><a href=validerFacture.php?idCourrier=".$idCourrier.">terminer</a></td>";
  }

  //test pour urgence du courrier
  $dateActuel = date("Y-m-d");
  $jourActuel  = substr($dateActuel,8,2);
  $moisActuel  = substr($dateActuel,5,2);
  $anneeActuel = substr($dateActuel,0,4);
  
  $tmpDateArrivee = $ligne['dateFacture'];
  $jourArrivee  = substr($tmpDateArrivee,8,2);
  $moisArrivee  = substr($tmpDateArrivee,5,2);
  $anneeArrivee = substr($tmpDateArrivee,0,4);
  
  $nbJours = $ligne['nbJours'];
  
  $timestampActuel = mktime(0,0,0,$moisActuel,$jourActuel,$anneeActuel);
  $timestampArrivee= mktime(0,0,0,$moisArrivee,$jourArrivee,$anneeArrivee);
  $urgence = ($timestampActuel - $timestampArrivee ) / 86400;
  
  $nbJoursRestant = $nbJours - $urgence;
  
  if ($nbJoursRestant >= 5)
    $alerte = "green";
  else
    $alerte = "red";
  

  echo "<td bgcolor='$couleur' style='color: $alerte;font-weight:bold'><center>$nbJoursRestant</center></td></tr>";
}
echo"</table>";

if (mysql_num_rows($resultatFacture) == $nbAffiche) 
     echo "<center><a href = voirFacture.php?id=".$id."&nbAffiche=".$nbAffiche.">page suivante</a></center>";

include('templates/footer.php');
