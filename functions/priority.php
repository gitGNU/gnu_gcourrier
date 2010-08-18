<?php
/*
Priority input form
Copyright (C) 2007, 2009, 2010  Cliss XXI

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

require_once(dirname(__FILE__) . '/db.php');

function priority_display($default_index=NULL) {
  $requete = "SELECT id, designation, nbJours FROM priorite";
  $result = mysql_query($requete) or die(mysql_error());
  
  if (mysql_num_rows($result) > 1) {
    echo "<select name='priority'>";
    
    while($ligne = mysql_fetch_array($result)){
      $selected = '';
      if (isset($default_index) and $ligne['id'] == $default_index)
	$selected = 'selected="selected"';
      echo "<option value='{$ligne['id']}' $selected>
                    {$ligne['designation']} ({$ligne['nbJours']} j.)
                  </option>";
    }
    
    echo "</select>";
  } else if (mysql_num_rows($result) == 0) {
    echo "Veuillez contacter l'admin pour ajouter une priorité";
  } else { /* Only one result */
    $ligne = mysql_fetch_array($result);
    echo "<input type='hidden' name='priorite' value='{$ligne['id']}' />";
    echo "{$ligne['designation']} ({$ligne['nbJours']} j.)";
  }
}

function priority_new($designation, $nbJours, $defautCourrier, $defautFacture) {
  db_autoexecute('priorite',
		 array('designation' => $designation,
		       'nbJours' => $nbJours,
		       'defautCourrier' => $defautCourrier ? 1 : 0,
		       'defautFacture' => $defautFacture ? 1 : 0,
		       ),
		 DB_AUTOQUERY_INSERT);
  return mysql_insert_id();
}

function priority_modify($id, $designation, $nbJours, $defautCourrier, $defautFacture) {
  db_autoexecute('priorite',
		 array('designation' => $designation,
		       'nbJours' => $nbJours,
		       'defautCourrier' => $defautCourrier ? 1 : 0,
		       'defautFacture' => $defautFacture ? 1 : 0,
		       ),
		 DB_AUTOQUERY_UPDATE,
		 'id = ?', array($id));
}

function priority_getbyid($id) {
  $req = "SELECT id, designation, nbJours, defautCourrier, defautFacture
          FROM priorite
          WHERE id = '" . mysql_real_escape_string($id) . "'";
  $result = mysql_query($req) or die(mysql_error());
  $line = mysql_fetch_assoc($result);
  
  return $line;
}

function priority_exists($id) {
  $res = db_execute("SELECT id FROM priorite WHERE id = ?",
		    array(intval($id)));
  return mysql_num_rows($res) > 0;
}

function priority_getdefaultmail()
{
  $req = "SELECT id
          FROM priorite
          WHERE defautCourrier = 1;";
  $result = mysql_query($req) or die(mysql_error());
  $line = mysql_fetch_assoc($result);
  return @$line['id'];
}

function priority_getdefaultinvoice()
{
  $req = "SELECT id
          FROM priorite
          WHERE defautFacture = 1;";
  $result = mysql_query($req) or die(mysql_error());
  $line = mysql_fetch_assoc($result);
  return @$line['id'];
}
