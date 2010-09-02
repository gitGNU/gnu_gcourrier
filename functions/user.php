<?php
/*
GCourrier
Copyright (C) 2005,2006 Cliss XXI

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

function user_getbyid($id) {
  $req = "SELECT id, login, nom AS lastname, prenom AS firstname,
            idService, preferenceNbCourrier AS pagersize FROM utilisateur
          WHERE id = '" . mysql_real_escape_string($id) . "'";
  $result = mysql_query($req) or die(mysql_error());
  $line = mysql_fetch_assoc($result);
  
  return $line;
}

function user_exists($login) {
  $req = "SELECT * FROM utilisateur
          WHERE login='" . mysql_real_escape_string($login) . "'";
  $result = mysql_query($req) or die(mysql_error());
  return (mysql_num_rows($result) > 0);
}
/* Variant to be used as QuickForm callback */
function user_exists_not($login) {
  return !user_exists($login);
}

// doesn't work, need to rename fields, see account.php
function user_create($params) {
  die("unfinished function");
  $insert_fields = utils_array_filter_fields($params,
    array('login', 'passwd', 'prenom', 'nom', 'idService',
	  'preferenceNbCourrier'));
  db_autoexecute('utilisateur',
		 $insert_fields,
		 DB_AUTOQUERY_UPDATE);
}
