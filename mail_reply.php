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

include('templates/header.php');

$replies = mail_get_replies($_GET['object_id']);
if (count($replies) > 0)
  {
    echo "<h2>Réponses à ce courrier (courriers départ)</h2>";
    mail_display_simple($replies);
  }
else
  {
    echo "<p>Aucune réponse liée à ce courrier</p>";
  }


echo "<h2>Actions</h2>";
echo "<p>";
echo "<a href='courrierDepart.php?reply_to={$_GET['object_id']}'>Créer un courrier départ en réponse à ce courrier</a>";
echo "</p>";

include('templates/footer.php');
