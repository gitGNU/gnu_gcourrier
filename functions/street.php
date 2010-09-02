<?php
/*
Street names
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

require_once(dirname(__FILE__) . '/db.php');

function street_new($label) {
  db_autoexecute('street',
		 array('label' => $label),
		 DB_AUTOQUERY_INSERT);
  return mysql_insert_id();
}

function street_modify($id, $label) {
  db_autoexecute('street',
		 array('label' => $label),
		 DB_AUTOQUERY_UPDATE,
		 'id = ?', array($id));
}

function street_getbyid($id) {
  $id = intval($id);
  $req = "SELECT id, label FROM street WHERE id = $id";
  $result = mysql_query($req) or die(mysql_error());
  $line = mysql_fetch_assoc($result);
  
  return $line;
}

function street_delete($id) {
  $id = intval($id);
  $res = db_execute("DELETE FROM street WHERE id = ?",
		    array($id));
  return $res;
}
