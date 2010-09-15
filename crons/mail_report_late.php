<?php
/*
GCourrier
Copyright (C) 2010  Cliss XXI

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
require_once(dirname(__FILE__) . '/../config.php');

mysql_connect($db_host, $db_user, $db_pass) or 
die("Erreur GCourrier: connection MySQL impossible pour l'utilisateur " . $db_user . " sur l'hôte " . $db_host);
mysql_select_db($db_base) or
die("Erreur GCourrier: connection impossible sur la base " . $db_base . "(" . $db_user . ", " . $db_host . ")");

if (!isset($url_base)) {
  die("Erreur GCourrier: il faut définir le paramètre \$url_base dans 'config.php'.");
}

$query = "SELECT id, libelle, designation, email FROM service";
// . "WHERE email IS NOT NULL";
$res_service = mysql_query($query) or die(mysql_error() . ": " . $query);;
$body = "";
while ($cur_service = mysql_fetch_array($res_service)) {
  if (empty($cur_service['email']))
    continue;
  
  $delay = "((TO_DAYS(CURDATE()) - TO_DAYS(dateArrivee)) + nbJours)";
  // Courriers en retard, tout type
  $query = "SELECT COUNT(*) AS count"
    . " FROM courrier JOIN priorite ON courrier.idPriorite = priorite.id"
    . " WHERE validite=0 AND serviceCourant = {$cur_service['id']}"
    . " AND $delay > 0";
  //die($query);
  $res = mysql_query($query) or die(mysql_error() . ": " . $query);
  $row = mysql_fetch_array($res);
  $nb = $row['count'];
  
  if ($nb == 0)
    continue;
  
  $body = '';
  $body = "<html><head><title></title></head><body>\n";
  $body .= "<p><strong>Service {$cur_service['libelle']}: $nb courriers non archivés hors délai</strong></p>\n";
  
  foreach (array('entrant' => 1, 'sortant' => 2) as $type => $type_id) {
    // Courriers en retard, entrant
    $query = "SELECT courrier.id, courrier.libelle,"
      . " UNIX_TIMESTAMP(courrier.dateArrivee) as date_here,"
      . " type, $delay AS delay,"
      . " destinataire.nom, destinataire.prenom"
      . " FROM courrier JOIN priorite ON courrier.idPriorite = priorite.id"
      . "   JOIN destinataire ON idDestinataire = destinataire.id"
      . " WHERE validite=0 AND serviceCourant = {$cur_service['id']}"
      . " AND type=$type_id"
      . " AND $delay > 0"
      . " ORDER BY $delay ASC LIMIT 50";
    //die($query);
    $res = mysql_query($query) or die(mysql_error() . ": " . $query);
    
    $body .= "<p><strong>Courrier $type</strong></p>\n";
    $body .= "<table>\n";
    $body .= "<tr><th>N°</th><th>Date</th><th>Libellé</th><th>Destinataire</th><th>Retard</th></tr>\n";
    while ($row = mysql_fetch_array($res))
      {
	$body .= "<tr>";
	$body .= "<td><a href='{$url_base}mail_list_my.php?type=1&idCourrierRecherche={$row['id']}#result'>"
	  . "{$row['id']}</a></td>";
	$body .= "<td>" . strftime("%x", $row['date_here']) . "</td>";
	$body .= "<td>" . htmlspecialchars($row['libelle']) . "</td>";
	$body .= "<td>" . htmlspecialchars($row['nom'] . ' ' . $row['prenom']) . "</td>";
	$body .= "<td>" . htmlspecialchars($row['delay']) . " j.</td>";
	$body .= "</tr>\n";
      }
    if (mysql_num_rows($res) > 50)
      $body .= "<tr><td colspan=5>...</td></tr>\n";
    $body .= "</table>\n";
    $body .= "</body></html>";
  } 
  
  $to      = $cur_service['email'];
  $subject = 'Courriers en retard pour le service ' . $cur_service['designation'];
  $headers = "Content-type: text/html;charset=UTF-8\r\n";
  mail($to, $subject, $body, $headers);
}
