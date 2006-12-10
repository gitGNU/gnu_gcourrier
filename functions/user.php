<?php
/*
GCourrier
Copyright (C) 2005,2006 Cliss XXI

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

function user_getbyid($id) {
  $req = "SELECT id, login, idService FROM utilisateur
          WHERE id = '" . mysql_real_escape_string($id) . "'";
  $result = mysql_query($req) or die(mysql_error());
  $line = mysql_fetch_array($result);
  
  if ($line != NULL) {
    $id = $line['id'];
    $login = $line['login'];
    $idService = $line['idService'];
    return array($id, $login, $idService);
  } else {
    return array(-1, '', -1);
  }
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

function user_getbyid_assoc($id) {
  $req = "SELECT id, login, nom AS lastname, prenom AS firstname, idService FROM utilisateur
          WHERE id = '" . mysql_real_escape_string($id) . "'";
  $result = mysql_query($req) or die(mysql_error());
  $line = mysql_fetch_assoc($result);
  
  return $line;
}
