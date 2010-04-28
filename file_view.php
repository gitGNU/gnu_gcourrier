<?php
/*
Filter access to attachments
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
require_once('functions/mimetype.php');

$object = $_GET['object'];
if ($object != 'facture' and $object != 'courrier') exit("Mauvais paramètre 'object'");

$object_id = intval($_GET['object_id']);

if ($object == 'courrier') {
  $result = db_execute('SELECT url FROM courrier WHERE id=?', array($object_id));
  $row = mysql_fetch_array($result);
  $path = $row['url'];

  $f = fopen($path, "rb") or die("Ne peut ouvrir $path");

  #header('Content-type: application/octet-stream' . mime_content_type($path));
  header('Content-type: ' . mimetype_get_by_ext($path));
  header('Content-Disposition: inline; filename=' . basename($path));
  header('Content-Length: ' . filesize($path));

  fpassthru($f);
  fclose($f);
}
