<?php
/*
GCourrier
Copyright (C) 2005, 2006, 2010  Cliss XXI

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

author VELU Jonathan, Sylvain BEUCLER
*/

require_once('init.php');
require_once('classes/SQLDataGrid.php');
require_once('functions/contact.php');

include('templates/header.php');

if (!isset($_GET['type']))
  $_GET['type'] = 1;

if (!isset($_GET['archived']))
  $_GET['archived'] = 0;

if (!isset($_GET['rechercher'])) {
?>
<center><b>RECHERCHE COURRIER
<?php
if($_GET['archived'] == 1)
  echo " ARCHIVÉ";
?>
<?php
if($_GET['type'] == 1) {
	echo " ENTRANT";
	$emetteur = "Émetteur";
} else {
	echo " DEPART";
	$emetteur = "Destinataire";
}
?>
</b>
<br><br>

<?php
echo "<form action=''>";
echo "<input type='hidden' name='type' value='{$_GET['type']}' />";
echo "<input type='hidden' name='archived' value='{$_GET['archived']}' />";
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
    <td><?php echo $emetteur; ?></td>
    <td><?php contact_display(); ?></td>
  </tr>
<?php if (empty($_GET['archived'])) { ?>
  <tr>
    <td><label>Déjà transmis</br> par le service</label></td>
    <td><input type="checkbox" name="gTransmis" /></td>
  </tr>

  <tr>
    <td><label>Courrier retard</label></td>
    <td><input type="checkbox" name="retard" /></td>
  </tr>
<?php } ?>
</table>

<input type="submit" name="rechercher" value="Rechercher" />
</form>

<?php

} else {

  echo "<div id = titre>RESULTAT DE LA RECHERCHE</div><br></b>";

  $libelle = $_GET['libelle'];
  $numero = $_GET['numero'];

  $date = $_GET['date'];

  $eDate1 = $_GET['eDate1'];
  $eDate2 = $_GET['eDate2'];

  $contact_id = $_GET['contact_id'];
  
  $distinct = '';
  $select = "courrier.id as idCourrier,
	     courrier.libelle as libelle,
	     destinataire.nom as nomDestinataire,
	     destinataire.prenom as prenomDestinataire,
	     UNIX_TIMESTAMP(courrier.dateArrivee) as dateArrivee";
  $from = "courrier LEFT JOIN destinataire ON courrier.idDestinataire = destinataire.id"
    . " LEFT JOIN service ON service.id = courrier.serviceCourant";
  $where = "courrier.validite = ".intval($_GET['archived'])." and courrier.type=".intval($_GET['type']);

  if (isset($_GET['gTransmis'])) {
    $from .= ",estTransmis,service ST";
    $where .= ""
      . " AND estTransmis.idCourrier = courrier.id"
      . " AND estTransmis.idService = {$_SESSION['idService']} "
      . " AND estTransmis.idService = ST.id ";
    $distinct = 'DISTINCT';
  }

  if (isset($_GET['retard'])) {
    $from .= ",priorite";
    $where .= " AND courrier.idPriorite = priorite.id"
      . " AND (dateArrivee + INTERVAL priorite.nbJours DAY) < CURDATE()";
  }

  if ($libelle != "") {
    $libelle = mysql_real_escape_string($libelle);
    $where .= " AND courrier.libelle LIKE '%$libelle%' ";
  }

  if ($numero != "") {
    $numero = intval($numero);
    $where .= " AND courrier.id = $numero ";
  }

  if (!empty($date) and $date != "jj-mm-aaaa") {
    $tmpdate= substr($date, 6,4);
    $tmpdate.='-';
    $tmpdate.=substr($date, 3,2);
    $tmpdate.='-';
    $tmpdate.=substr($date, 0,2);
    $date = $tmpdate;
    
    $where .= " AND courrier.dateArrivee = '$date' ";
  }

  if (!empty($contact_id)) {
    $where .= " AND destinataire.id = ".$contact_id." ";
  }

  if ((!empty($eDate1) and $eDate1 != "jj-mm-aaaa")
      and (!empty($eDate2) and $eDate2 = "jj-mm-aaaa")) {
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
    
    $where .= " AND courrier.dateArrivee >='$eDate1' AND courrier.dateArrivee<='$eDate2' ";
  }

  $requete = "SELECT $distinct $select FROM $from WHERE $where";
  $result = mysql_query($requete) or die(mysql_error());

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
    return $record['nomDestinataire'] . " " . $record['prenomDestinataire'];
  }
  function printArrivalDate($params)
  {
    extract($params);
    return strftime("%x", $record[$fieldName]);
  }
  function printHistory($params)
  {
    extract($params);
    return "<a href='mail_history.php?idCourrier={$record['idCourrier']}"
      . "&type={$_GET['type']}'>Historique</a>";
  }
  function printTransmit($params)
  {
    extract($params);
    return "<a href='mail_transmit.php?idCourrier={$record['idCourrier']}&next="
      . urlencode($_SERVER['REQUEST_URI'])
      . "'>Transmettre</a>";
  }
  function printReply($params)
  {
    extract($params);
    return "<a href='mail_reply.php?object_id={$record['idCourrier']}'>Voir</a>";
  }
  function printInReplyTo($params)
  {
    extract($params);
    return "<a href='mail_in_reply_to.php?object_id={$record['idCourrier']}'>Voir</a>";
  }
  function printFiles($params)
  {
    extract($params);
    return "<a href='mail_attachment.php?object_id={$record['idCourrier']}&next="
      . urlencode($_SERVER['REQUEST_URI'])
      . "'>Voir</a>";
  }

  $config = array();
  $config['No'] = array('sqlcol' => 'idCourrier',
			'callback' => 'printId');
  $config['Libellé'] = array('sqlcol' => 'libelle',
			     'callback' => 'printLabel');
  $config[($_GET['type'] == 1) ? 'Émetteur' : 'Destinataire']
    = array('sqlcol' => 'nomDestinataire',
	    'callback' => 'printContact');
  $config['Date Mairie'] = array('sqlcol' => 'dateArrivee',
				 'callback' => 'printArrivalDate');
  $config['Historique'] = array('callback' => 'printHistory');
  if ($_GET['archived'] == 0)
    $config['Transmettre'] = array('callback' => 'printTransmit');
  if ($_GET['type'] == 1)
    $config['Réponses'] = array ('callback' => 'printReply');
  else
    $config['En rép. à'] = array('callback' => 'printInReplyTo');
  $config['Fichiers'] = array('callback' => 'printFiles');
  $sdg = new SQLDataGrid($requete, $config);
  
  $sdg->setPagerSize($_SESSION['pagersize']);
  $sdg->setDefaultSort(array('idCourrier' => 'DESC'));
  $sdg->setClass('resultats');
  if (!empty($_GET['idCourrierRecherche']))
    $sdg->setDefaultPageWhere(array('idCourrier' => $_GET['idCourrierRecherche']));
  $sdg->display();

  echo "<p><a href='?type={$_GET['type']}'>Nouvelle recherche</a></p>";

}//fin du premier else

echo "</center>";
include('templates/footer.php');
