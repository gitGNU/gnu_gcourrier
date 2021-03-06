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
*/

require_once('init.php');
require_once('classes/SQLDataGrid.php');
require_once('functions/db.php');
require_once('functions/text.php');
require_once('functions/mail.php');

if (!empty($_GET['idCourrierRecherche'])) {
  if (!mail_exists('id=? AND serviceCourant=?',
		   array($_GET['idCourrierRecherche'], $_SESSION['idService']))) {
    die("Ce courrier n'existe pas ou n'est pas actuellement dans votre service.");
  }
}

include('templates/header.php');

echo "<center><div id= titre>Courrier";
if ($_GET['type'] == 1)
  echo " Entrant";
else
  echo " Départ";

echo " <br/><i style=\"font-size:10px;font-weight:normal\">";
echo _("Note: Ne sont affichés que les courriers de votre service");
echo "</i></div></center><br/>";

?>
<form method='get' action='#result'>
<input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" />
<table align=center style="border:1px dotted black;"><tr><td>
<label>Rechercher le courrier numéro:</label>
<input type="text" name="idCourrierRecherche" value=1 size=2></input>
<input type="submit" value="OK"></input>
<br/><center><a href="mail_list.php?type=1"><font size="1">Recherche Avancée</font></a></center>
</td></table></form>


<?php
if ($_SESSION['login'] != 'admin')
  $where = "AND courrier.serviceCourant = {$_SESSION['idService']}";

$query = "SELECT courrier.id AS idCourrier,
                 priorite.nbJours AS nbJours,
                 UNIX_TIMESTAMP(courrier.dateArrivee) AS dateArrivee,
                 courrier.observation AS observation,
                 destinataire.nom AS nomDestinataire,
                 destinataire.prenom AS prenomDestinataire,
                 courrier.libelle AS libelle
          FROM courrier LEFT JOIN priorite ON courrier.idPriorite = priorite.id
               LEFT JOIN destinataire ON courrier.idDestinataire = destinataire.id
          WHERE courrier.validite = 0
            AND courrier.type = {$_GET['type']}
            $where";

function printId($params)
{
  extract($params);
  return $record['idCourrier'];
}
function printLabel($params)
{
  extract($params);
  if ($record[$fieldName] == "")
    $record[$fieldName] = '[modifier]';
  return "<a href='modifLibelleCourrier.php?idCourrier={$record['idCourrier']}&amp;type={$_GET['type']}'"
    . " style='text-decoration: none; font-weight: normal;'>{$record[$fieldName]}</a>";
}
function printContact($params)
{
  extract($params);
  $name = $record['nomDestinataire'] . ' ' . $record['prenomDestinataire'];
  if ($name == '')
    $name = '[modifier]';
  return "<a href='modifDestinataireCourrier.php?idCourrier={$record['idCourrier']}&amp;type={$_GET['type']}'"
    . " style='text-decoration: none; font-weight: normal;'>"
    . "$name</a>";
}
function printArrivalDate($params)
{
  extract($params);
  return strftime("%x", $record[$fieldName]);
}
function printComment($params)
{
  extract($params);
  if ($record[$fieldName] == "")
    $record[$fieldName] = '[modifier]';
  $comment_short = text_truncatewords($record[$fieldName], 10);
  return "<a href='modifObservationCourrier.php?idCourrier={$record['idCourrier']}&amp;type={$_GET['type']}'"
    . " style='text-decoration: none; font-weight: normal;'"
    . " title='" . htmlspecialchars($record[$fieldName], ENT_QUOTES) . "'>{$comment_short}</a>";
}
function printHistory($params)
{
  extract($params);
  return "<a href='mail_history.php?idCourrier={$record['idCourrier']}"
    . "&amp;type={$_GET['type']}'>Historique</a>";
}
function printTransmit($params)
{
  extract($params);
  return "<a href='mail_transmit.php?idCourrier={$record['idCourrier']}&amp;next="
    . urlencode($_SERVER['REQUEST_URI'])
    . "'>Transmettre</a>";
}
function printArchive($params)
{
  extract($params);
  return "<a href='valider.php?idCourrier={$record['idCourrier']}"
    . "&amp;type={$_GET['type']}'>Terminer</a>";
}
function printReceipt($params)
{
  extract($params);
  return "<a href='receipt_form.php?idCourrier={$record['idCourrier']}"
    . "&amp;type={$_GET['type']}'>Créer</a>";
}
function printPriority($params)
{
  extract($params);
  $delta_days = (time() - $record['dateArrivee']) / 86400;
  $nbJours = $record['nbJours'];
  $nbJoursRestant = intval($nbJours - $delta_days);

  $ret = '';
  $ret .= "<a href='mail_priority.php?object_id={$record['idCourrier']}&amp;next="
    . urlencode($_SERVER['REQUEST_URI'])
    . "'>";
  if ($nbJoursRestant <= 0)
    $ret .= "<img src=images/annuler.png title='dépassé depuis ".-$nbJoursRestant." jours' />";
  else
    $ret .= "<img src=images/ok.png title='reste $nbJoursRestant jours' />";
  $ret .= "</a>";
  return $ret;
}
function printReply($params)
{
  extract($params);
  return "<a href='mail_reply.php?object_id={$record['idCourrier']}'>Répondre</a>";
}
function printInReplyTo($params)
{
  extract($params);
  return "<a href='mail_in_reply_to.php?object_id={$record['idCourrier']}'>Voir</a>";
}
function printFiles($params)
{
  extract($params);
  return "<a href='mail_attachment.php?object_id={$record['idCourrier']}&amp;next="
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
$config['Observation'] = array('sqlcol' => 'observation',
			       'callback' => 'printComment');
$config['Historique'] = array('callback' => 'printHistory');
if ($_SESSION['login'] != 'admin')
  {
    $config['Transmettre'] = array('callback' => 'printTransmit');
    $config['Terminer'] = array('callback' => 'printArchive');
    if ($_GET['type'] == 1)
      $config['Accusé'] = array('callback' => 'printReceipt');
  }
$config['Urgence'] = array('callback' => 'printPriority');
if ($_GET['type'] == 1)
  $config['Réponses'] = array ('callback' => 'printReply');
else
  $config['En rép. à'] = array('callback' => 'printInReplyTo');
$config['Fichiers'] = array('callback' => 'printFiles');
$sdg = new SQLDataGrid($query, $config);
  
$sdg->setPagerSize($_SESSION['pagersize']);
$sdg->setDefaultSort(array('idCourrier' => 'DESC'));
$sdg->setClass('resultats');
if (!empty($_GET['idCourrierRecherche']))
  $sdg->setDefaultPageWhere(array('idCourrier' => $_GET['idCourrierRecherche']));
$sdg->display();

?>
<br/>

<?php
include('templates/footer.php');
