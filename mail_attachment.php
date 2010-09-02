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

require_once('init.php');
require_once('functions/db.php');
require_once('functions/mail.php');


if (isset($_FILES['mail_file'])) {
  mail_handle_attachment($_POST['object_id']);
  header('Location: ' . $_POST['next']);
  exit();
}


include('templates/header.php');

$archived = mail_is_archived($_GET['object_id']);
$res = mail_query_attachments($_GET['object_id']);
if (mysql_num_rows($res) > 0)
  {
    echo "<h2>Pièces jointes</h2>";
    print "<table>";
    while ($row = mysql_fetch_array($res))
      {
	echo "<tr>";
	echo "<td style='text-align: left;'>";
	echo "<a href='file_view.php/"
	  . $row['filename']
	  . "?object=mail&attachment_id={$row['id']}'>"
	  . "<img src='images/download.gif' style='border: 0;'>"
	  . $row['filename']
	  . "</a>";
	echo "</td>";
	if (!$archived) {
	  echo "<td>";
	  echo "<form action='mail_attachment_delete.php' method='post'>";
	  echo "<input type='hidden' name='attachment_id' value='{$row['id']}' />";
	  echo "<input type='hidden' name='next' value='{$_SERVER['REQUEST_URI']}' />";
	  echo "<input type='submit' value='Supprimer' />";
	  echo "</form>";
	  echo "</td>";
	}
	echo "</tr>";
      }
    echo "</table>";
  }
else
  {
    echo "<p>Aucune pièce jointe.</p>";
  }

if (!$archived) {
  echo "<h2>Ajouter une pièce jointe</h2>";
  echo "<form action='?' method='post' enctype='multipart/form-data' >";
  echo "<input type='hidden' name='object_id' value='{$_GET['object_id']}'/>";
  echo "<input type='hidden' name='next' value='{$_SERVER['REQUEST_URI']}'/>";
  echo "<input type='file' name='mail_file' />";
  echo "<br />";
  echo "<input type='submit' value='Envoyer' />";
  echo "</form>";
}

if (!empty($_REQUEST['next'])) {
  echo "<p><a href='{$_REQUEST['next']}'>Retour</a></p>";
}

include('templates/footer.php');
