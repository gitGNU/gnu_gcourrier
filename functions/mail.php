<?php
/*
Mail encapsulation
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

require_once(dirname(__FILE__) . '/db.php');
require_once(dirname(__FILE__) . '/../classes/SQLDataGrid.php');

function mail_get_replies($id) {
  $ret = array();

  $res = db_execute("SELECT mail_new_id FROM mail_reply WHERE mail_old_id = ?",
		    array($id));
  while ($row = mysql_fetch_array($res))
    array_push($ret, intval($row['mail_new_id']));

  return $ret;
}

function mail_get_origins($id) {
  $ret = array();

  $res = db_execute("SELECT mail_old_id FROM mail_reply WHERE mail_new_id = ?",
		    array($id));
  while ($row = mysql_fetch_array($res))
    array_push($ret, intval($row['mail_old_id']));

  return $ret;
}

function mail_exists($where, $where_params)
{
  $res = db_execute("SELECT COUNT(*) AS count FROM courrier WHERE $where", $where_params);
  $row = mysql_fetch_array($res);
  $count = $row['count'];
  return $count > 0;
}

function mail_is_archived($id)
{
  $res = db_execute("SELECT validite AS archived FROM courrier WHERE id=?",
		    array(intval($id)));
  $row = mysql_fetch_array($res);
  $count = $row['archived'];
  return $row['archived'] == 1;
}

function mail_reply_new($mail_old_id, $mail_new_id)
{
  $result = db_autoexecute('mail_reply',
    array('mail_old_id' => intval($mail_old_id),
          'mail_new_id' => intval($mail_new_id)),
    DB_AUTOQUERY_INSERT);
  return $result;
}

function mail_display_simple($ids)
{
    $query = "SELECT courrier.id AS mail_id, libelle AS label, CONCAT(nom, ' ', prenom) AS contact_name,"
      . " UNIX_TIMESTAMP(dateArrivee) AS date_here,"
      . " type, validite AS archived "
      . " FROM courrier JOIN destinataire ON courrier.idDestinataire = destinataire.id"
      . " WHERE courrier.id IN (" . join(',', $ids) . ")";

    function printId($params)
    {
      extract($params);
      $archived = '';
      if ($record['archived'] == 1)
	$archived = "type=archived=1&";
      return "<a href='mail_list.php?{$archived}type={$record['type']}"
	. "&idCourrierRecherche={$record[$fieldName]}"
	. "&rechercher=1#result'>{$record[$fieldName]}</a>";
    }

    $config = array();
    $config['No'] =
      array('sqlcol' => 'mail_id',
	    'callback' => 'printId');
    $config['Libellé'] =
      array('sqlcol' => 'label',
	    'callback' => 'printText');
    $config['Destinataire'] =
      array('sqlcol' => 'contact_name',
	    'callback' => 'printText');
    $config['Date Mairie'] =
      array('sqlcol' => 'date_here',
	    'callback' => 'printDate');

    $sdg = new SQLDataGrid($query, $config);
    $sdg->setDefaultSort(array('mail_id' => 'ASC'));
    $sdg->setClass('resultats');
    $sdg->display();
}