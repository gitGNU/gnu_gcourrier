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

function mail_get_replies($id) {
  $ret = array();

  $res = db_execute("SELECT mail_new_id FROM mail_reply WHERE mail_old_id = ?",
		    array($id));
  while ($row = mysql_fetch_array($res))
    array_push($ret, $row['mail_new_id']);

  return $ret;
}

function mail_exists($where, $where_params)
{
  $res = db_execute("SELECT COUNT(*) AS count FROM courrier WHERE $where", $where_params);
  $row = mysql_fetch_array($res);
  $count = $row['count'];
  return $count > 0;
}

function mail_reply_new($mail_old_id, $mail_new_id)
{
  $result = db_autoexecute('mail_reply',
    array('mail_old_id' => intval($mail_old_id),
          'mail_new_id' => intval($mail_new_id)),
    DB_AUTOQUERY_INSERT);
  return $result;
}
