<?php
/*
GCourrier
Copyright (C) 2010  Cliss XXI

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
require_once('functions/db.php');
require_once('functions/mail.php');
require_once('functions/priority.php');
require_once('functions/status.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  mail_set_priority($_POST['object_id'], $_POST['priority']);
  status_push('Priorité modifiée');
  header('Location: ' . $_POST['next']);
  exit();
}

include('templates/header.php');

$archived = mail_is_archived($_GET['object_id']);
$cur_priority = mail_get_priority($_GET['object_id']);

if ($archived) {
  "<p>Vous ne pouvez pas changer la priorité d'un courrier archivé.</p>";
} else {
  echo "<h2>Changer la priorité</h2>";
  echo "<form action='?' method='post' >";
  priority_display($cur_priority);
  echo "<input type='hidden' name='object_id' value='{$_GET['object_id']}'/>";
  echo "<input type='hidden' name='next' value='{$_GET['next']}'/>";
  echo "<br />";
  echo "<input type='submit' value='Modifier' />";
  echo "</form>";
}

$history = mail_get_priority_history($_GET['object_id']);
echo "<p>Historique</p>";
echo "<table>";
echo "<tr><th>Date</th><th>Valeur</th></tr>";
foreach ($history as $timestamp => $id) {
  $priority = priority_getbyid($id);
  print "<tr><td>";
  print strftime('%x', $timestamp);
  print "</td><td>";
  print "{$priority['designation']} ({$priority['nbJours']} j.)";
  print "</td>";
}
echo "</table>";

echo "<p><a href='{$_REQUEST['next']}'>Retour</a></p>";

include('templates/footer.php');
