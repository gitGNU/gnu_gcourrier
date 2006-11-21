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

author Sylvain Beucler
*/

/* Returns the user id associated with the given session
   identifier */
function longsession_getid($session_hash) {
  // Clean-up old sessions
  $req = "DELETE FROM sessions WHERE NOW() > expiration";
  $result = mysql_query($req) or die(mysql_error());

  // Look for current session
  $req = "SELECT user_id
          FROM sessions
          WHERE hash = '$session_hash'";
  $result = mysql_query($req) or die(mysql_error());
  $line = mysql_fetch_array($result);

  if ($line != NULL) {
    return $line['user_id'];
  } else {
    # No such session
    return -1;
  }
}

/* Create a new one-week session for the current user */
function longsession_new() {
  $user_id = $_SESSION['id'];
  $expiration = strtotime("now + 1 week");

  // Find a unique session hash
  $req = '';
  do {
    // Compute a long, random, changing text:
    $session_hash = md5(mt_rand() . microtime() . $_SERVER['REMOTE_ADDR']);
    $req = "SELECT NULL FROM sessions WHERE hash='$session_hash'";
  } while (mysql_num_rows(mysql_query($req)) > 0);

  $req = "INSERT INTO sessions (hash, user_id, expiration)
          VALUES ('$session_hash', '$user_id', FROM_UNIXTIME('$expiration'))";
  $result = mysql_query($req) or die(mysql_error());
  return $session_hash;
}

function longsession_renew() {
  $session_hash = $_COOKIE['gcourrier_session'];
  $expiration = strtotime("now + 1 week");
  $req = "UPDATE sessions
          SET expiration = FROM_UNIXTIME($expiration)
          WHERE hash='$session_hash'";
  $result = mysql_query($req) or die(mysql_error());
}

function longsession_delete() {
  $session_hash = $_COOKIE['gcourrier_session'];
  if (isset($session_hash)) {
    $req = "DELETE FROM sessions WHERE hash='$session_hash'";
    $result = mysql_query($req) or die(mysql_error());
  }
}
