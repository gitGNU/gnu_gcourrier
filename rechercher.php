<?php
/*
GCourrier
Copyright (C) 2005, 2006, 2010  Cliss XXI

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

author VELU Jonathan, Sylvain BEUCLER
*/

require_once('init.php');
require_once('classes/SQLDataGrid.php');
require_once('functions/contact.php');

include('templates/header.php');

if (!isset($_GET['type']))
  {
    $_GET['type'] = 1;
  }

if(!isset( $_GET["rechercher"] ) ){

?>
<center><b>RECHERCHE COURRIER
<?php

if($_GET['type'] == 1) {
	echo " ENTRANT";
	$emetteur = "Émetteur";
} else {
	echo " DEPART";
	$emetteur = "Destinataire";
}
?>

</b><br><br>
<?php
echo "<form name = rechercheAvanceeForm action='rechercher.php'>";
echo "<input type='hidden' name='type' value='{$_GET['type']}' />";
?>
<table>
<tr>
<td>Libellé</td>
<td><input type="text" name="libelle" /> (contient cette phrase)</td>
</tr>
<tr>
<td>Numéro</td>
<td><input type="text" name="numero" /></td>
</tr>

<tr>
<td>En date du</td>
<td><input type="text" name="date" value ="jj-mm-aaaa" /></td>
</tr>


<tr>
<td>Date entre</td>
<td><input type = text name = eDate1 value="jj-mm-aaaa" />
 et <input type = text name = eDate2 value="jj-mm-aaaa" /></td>
</tr>


<tr>
<td>
<?php
echo $emetteur;
?>
</td>


<td>
<?php contact_display(); ?>
</td>
</tr>
<tr>
<td><label>Déjà transmis</br> par le service</label></td>
<td><input type = "checkbox" name ="gTransmis"/></td>
</tr>
<tr>
<td><label>Courrier retard</label></td>
<td><input type = "checkbox" name ="retard"/></td>
</tr>
</table>

<br><input type="submit" name="rechercher" value="Rechercher" />
</form>
<?php
}
else{

if(isset($_GET['gTransmis'])){
$reqTmpTransmission = " and courrier.id = estTransmis.idCourrier
		       and estTransmis.idService =".$_SESSION['idService']." ";
$fromTransmission = ",estTransmis,service";
}
else{
$reqTmpTransmission =" ";
$fromTransmission = "";
}

if(isset($_GET['retard'])){
$fromRetard = ",priorite ";
$whereRetard = " AND courrier.idPriorite = priorite.id"
  . " AND (dateArrivee + INTERVAL priorite.nbJours DAY) < CURDATE()";
}
else{
$whereRetard="";
$fromRetard = ""; 
}

echo "<div id = titre>RESULTAT DE LA RECHERCHE</div><br></b>";


$libelle = $_GET['libelle'];
$numero = $_GET['numero'];

$date = $_GET['date'];

$dateResult = $date;

$eDate1 = $_GET['eDate1'];
$eDate2 = $_GET['eDate2'];
$contact_id = $_GET['contact_id'];


$requetetmp = "SELECT courrier.id as idCourrier,
		   courrier.libelle as libelle,
		   destinataire.nom as nomDestinataire,
		   destinataire.prenom as prenomDestinataire,
		   UNIX_TIMESTAMP(courrier.dateArrivee) as dateArrivee,
		   courrier.url as url ";
$from =" FROM courrier, destinataire ".$fromTransmission.$fromRetard;
$where =" WHERE courrier.validite = 0 and courrier.type=".intval($_GET['type'])
  . " AND courrier.idDestinataire = destinataire.id";
$where .= $reqTmpTransmission.$whereRetard;
$requete = '';

if ($libelle != "") {
  $libelle = mysql_real_escape_string($libelle);
  $requete .= " AND courrier.libelle LIKE '%$libelle%' ";
}

if(strcmp($numero,"")!=0){
	$requete.= " and courrier.id = '".$numero."' ";

}


if(strcmp($date,"jj-mm-aaaa")!=0){
	$tmpdate= substr($date, 6,4);
	$tmpdate.='-';
	$tmpdate.=substr($date, 3,2);
	$tmpdate.='-';
	$tmpdate.=substr($date, 0,2);
	$date = $tmpdate;

	$requete.= " and courrier.dateArrivee = '".$date."' ";

}


if (!empty($contact_id)) {
  $requete .= " AND destinataire.id = ".$contact_id." ";
}



if(strcmp($eDate1,"jj-mm-aaaa")!=0 && strcmp($eDate2,"jj-mm-aaaa")!=0){
$tmpdate= substr($eDate1, 6,4);
$tmpdate.='-';
$tmpdate.=substr($eDate1, 3,2);
$tmpdate.='-';
$tmpdate.=substr($eDate1, 0,2);
$eDate1 = $tmpdate;

$tmpdate= substr($eDate2, 6,4);
$tmpdate.='-';
$tmpdate.=substr($eDate2, 3,2);
$tmpdate.='-';
$tmpdate.=substr($eDate2, 0,2);
$eDate2 = $tmpdate;

$requete.=" and courrier.dateArrivee >='".$eDate1."' and courrier.dateArrivee<='".$eDate2."' ";
		
}


$requete .= " ".$from." ".$where." ".$requete." ";
$requetetmp.=$requete;
//$requete.=$requetetmp." group by courrier.id;";
$requete = $requetetmp;
//echo $requete."<br><br>";
$result = mysql_query( $requete ) or die ( mysql_error() ) ;


function printId($params)
{
  extract($params);
  return $record['idCourrier'];
}
function printLabel($params)
{
  extract($params);
  return $record['libelle'];
}
function printContact($params)
{
  extract($params);
  return $record['nomDestinataire'];
}
function printArrivalDate($params)
{
  extract($params);
  return strftime("%x", $record[$fieldName]);
}
function printHistory($params)
{
  extract($params);
  return "<a href='rechercherHistorique.php?idCourrier={$record['idCourrier']}"
    . "&type={$_GET['type']}'>Historique</a>";
}
function printTransmit($params)
{
  extract($params);
  return "<a href='transmettreRecherche.php?idCourrier={$record['idCourrier']}"
    . "&type={$_GET['type']}'>Transmettre</a>";
}
function printFiles($params)
{
  extract($params);
  if (!empty($record['url']))
    {
      return "<a href='file_view.php/" . basename($record['url'])
	. "?object=courrier&object_id={$record['idCourrier']}'>"
	. "<img src='images/download.gif' style='border: 0;'></a>";
    }
}

$sdg = new SQLDataGrid($requete,
		       array('No' => array('sqlcol' => 'idCourrier',
					   'callback' => 'printId'),
			     'Libellé' => array('sqlcol' => 'libelle',
					     'callback' => 'printLabel'),
			     (($_GET['type'] == 1) ? 'Émetteur' : 'Destinataire')
                                       => array('sqlcol' => 'nomDestinataire',
						 'callback' => 'printContact'),
			     'Date Mairie' => array('sqlcol' => 'dateArrivee',
						    'callback' => 'printArrivalDate'),
			     'Historique' => array('callback' => 'printHistory'),
			     'Transmettre' => array('callback' => 'printTransmit'),
			     'Fichiers' => array('callback' => 'printFiles'),
			     ));

$sdg->setPagerSize($_SESSION['pagersize']);
$sdg->setDefaultSort(array('idCourrier' => 'DESC'));
$sdg->setClass('resultats');
#if (!empty($_GET['idFactureRecherche']))
#  $sdg->setDefaultPageWhere(array('idFacture' => $_GET['idFactureRecherche']));
#if (!empty($_GET['id']))
#  $sdg->setDefaultPageWhere(array('idFacture' => $_GET['id']));
$sdg->display();


echo "<p><a href='rechercher.php?type={$_GET['type']}'>Nouvelle recherche</a></p>";

echo "</center>";

}//fin du premier else
include('templates/footer.php');
