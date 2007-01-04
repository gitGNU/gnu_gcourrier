<?php
/*
Functions to ease database access
Copyright (C) 2000-2006 John Lim (ADOdb)
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

require_once(dirname(__FILE__) . '/../init.php');

/* Like ADOConnection->Execute, with variables binding emulation for
MySQL, but simpler (not 2D-array, namely). Example:

db_execute("SELECT * FROM utilisateur WHERE name=?", array("Gogol d'Algol"));

Check http://phplens.com/adodb/reference.functions.execute.html and
adodb.inc.php
*/
function db_execute($sql, $inputarr=null)
{
  if ($inputarr) {
    $sqlarr = explode('?', $sql);
    
    $sql = '';
    $i = 0;
    //Use each() instead of foreach to reduce memory usage -mikefedyk
    while(list(, $v) = each($inputarr)) {
      $sql .= $sqlarr[$i];
      // from Ron Baldwin <ron.baldwin#sourceprose.com>
      // Only quote string types
      $typ = gettype($v);
      if ($typ == 'string')
	$sql .= "'" . mysql_real_escape_string($v) . "'";
      else if ($typ == 'double')
	$sql .= str_replace(',','.',$v); // locales fix so 1.1 does not get converted to 1,1
      else if ($typ == 'boolean')
	$sql .= $v ? '1' : '0';
      else if ($typ == 'object')
	exit("Don't due db_execute with objects.");
      else if ($v === null)
	$sql .= 'NULL';
      else
	$sql .= $v;
      $i += 1;
    }
    if (isset($sqlarr[$i])) {
      $sql .= $sqlarr[$i];
      if ($i+1 != sizeof($sqlarr))
	exit("db_execute: input array does not match query: ".htmlspecialchars($sql));
    } else if ($i != sizeof($sqlarr))
      exit("db_execute: input array does not match query: ".htmlspecialchars($sql));
  }

#  print "<pre>";
#  print_r($sql);
#  print "</pre>";
  $res = mysql_query($sql);
  if ($res === false)
#    throw new Exception(mysql_error());
    exit(mysql_error());
  return $res;
}

if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
  $res = db_execute("SELECT * FROM utilisateur WHERE prenom=?", array("Gogol d'Algol"));
  print_r(mysql_fetch_array($res));
}
