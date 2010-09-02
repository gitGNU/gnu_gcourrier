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
require_once('functions/status.php');

if (!empty($_POST['attachment_id'])) {
  mail_attachment_delete($_POST['attachment_id']);
  status_push("Pièce jointe supprimée");
  header('Location: ' . $_POST['next']);
  exit();
}
