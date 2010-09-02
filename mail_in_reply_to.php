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

include('templates/header.php');

$replies = mail_get_origins($_GET['object_id']);
if (count($replies) > 0)
  {
    echo "<h2>Ce courrier est en réponse à (courriers entrants)</h2>";
    mail_display_simple($replies);
  }
else
  {
    echo "<p>Ce courrier n'est pas une réponse à un courrier entrant.</p>";
  }

echo "<p><a href='javascript:history.go(-1)'>Retour</a></p>";

include('templates/footer.php');
