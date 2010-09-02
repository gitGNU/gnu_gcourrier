<?php
/*
Filter access to attachments
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
require_once('functions/mimetype.php');
require_once('functions/mail.php');

$object = $_GET['object'];
if ($object != 'mail' and $object != 'invoice') exit("Mauvais paramètre 'object'");

$object_id = intval($_GET['object_id']);

if ($object == 'mail') {
  $path = mail_attachment_get_path($_GET['attachment_id']);

  $f = fopen($path, "rb") or die("Ne peut ouvrir $path");

  #header('Content-type: application/octet-stream' . mime_content_type($path));
  header('Content-type: ' . mimetype_get_by_ext($path));
  header('Content-Disposition: inline; filename=' . basename($path));
  header('Content-Length: ' . filesize($path));

  fpassthru($f);
  fclose($f);
}
