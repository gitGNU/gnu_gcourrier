<?php
require_once(dirname(__FILE__) . '/../config.php');

mysql_connect($db_host, $db_user, $db_pass) or 
die("Connection MySQL impossible pour l'utilisateur " . $db_user . " sur l'hôte " . $db_host);
mysql_select_db($db_base) or
die("Connection impossible sur la base " . $db_base . "(" . $db_user . ", " . $db_host . ")");

$query = "SELECT id, libelle, designation, email FROM service";
// . "WHERE email IS NOT NULL";
$res_service = mysql_query($query) or die(mysql_error() . ": " . $query);;
$body = "";
while ($cur_service = mysql_fetch_array($res_service))
  {
    $delay = "((TO_DAYS(CURDATE()) - TO_DAYS(dateArrivee)) + nbJours)";
    // Courriers en retard, tout type
    $query = "SELECT COUNT(*) AS count"
      . " FROM courrier JOIN priorite ON courrier.idPriorite = priorite.id"
      . " WHERE validite=0 AND serviceCourant = {$cur_service['id']}"
      . " AND $delay > 0";
    $res = mysql_query($query) or die(mysql_error() . ": " . $query);
    $row = mysql_fetch_array($res);
    $nb = $row['count'];
    print "Service {$cur_service['libelle']}:\t$nb courriers en retard\n";

    // Courriers en retard, entrant
    $query = "SELECT courrier.id, courrier.libelle,"
      . " UNIX_TIMESTAMP(courrier.dateArrivee) as date_here,"
      . " type, $delay AS delay,"
      . " destinataire.nom, destinataire.prenom"
      . " FROM courrier JOIN priorite ON courrier.idPriorite = priorite.id"
      . "   JOIN destinataire ON idDestinataire = destinataire.id"
      . " WHERE validite=0 AND serviceCourant = {$cur_service['id']}"
      . " AND type=1"
      . " AND $delay > 0"
      . " ORDER BY $delay ASC LIMIT 50";
    $res = mysql_query($query) or die(mysql_error() . ": " . $query);
    while ($row = mysql_fetch_array($res))
      {
	$body .= "{$row['id']} - " . strftime("%x", $row['date_here'])
	  . " - {$row['libelle']} - {$row['nom']} {$row['prenom']} - {$row['delay']} j.\n";
      }
    if ($nb > 50)
      $body .= "...\n";
    print $body;
    print "\n";
    $body = '';
  }
