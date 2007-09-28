<?php
/*
Functions to ease database access
Copyright (C) 2000-2006 John Lim (ADOdb)
Copyright (C) 2005,2006,2007 Cliss XXI
Copyright (C) 2007 Sylvain Beucler
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

define('DB_AUTOQUERY_INSERT', 1);
define('DB_AUTOQUERY_UPDATE', 2);

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
    exit("Query <code>$sql</code> failed: " .mysql_error());
  return $res;
}

if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
  $res = db_execute("SELECT * FROM utilisateur WHERE prenom=?", array("Gogol d'Algol"));
  print_r(mysql_fetch_array($res));
}

/* Like ADOConnection->AutoExecute, without ignoring non-existing
 fields (you'll get a nice mysql_error() instead) and with a modified
 argument list to allow variable binding in the where clause

eg: 

Check http://phplens.com/adodb/reference.functions.getupdatesql.html ,
http://phplens.com/adodb/tutorial.generating.update.and.insert.sql.html
and adodb.inc.php
*/
function db_autoexecute($table, $dict, $mode=DB_AUTOQUERY_INSERT,
			$where_condition=false, $where_inputarr=null)
{
  // table name validation
  if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]+$/', $table))
    die("db_autoexecute: invalid table name: " . htmlspecialchars($table));

  switch((string) $mode) {
  case 'INSERT':
  case '1':
    $fields = implode(',', array_keys($dict)); // date,summary,...
    $question_marks = implode(',', array_fill(0, count($dict), '?')); // ?,?,?,...
    return db_execute("INSERT INTO $table ($fields) VALUES ($question_marks)",
		     array_values($dict));
    break;
  case 'UPDATE':
  case '2':
    $sql_fields = '';
    $values = array();
    while (list($field,$value) = each($dict)) {
      $sql_fields .= "$field=?,";
      $values[] = $value;
    }
    $sql_fields = rtrim($sql_fields, ',');
    $values = array_merge($values, $where_inputarr);
    $where_sql = $where_clause ? "WHERE $where_clause" : '';
    return db_execute("UPDATE $table $sql_fields $where_sql", $values);
    break;
  default:
    // no default
  }
  die("db_autoexecute: unknown mode=$mode");
}
