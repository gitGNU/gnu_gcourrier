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

author VELU Jonathan
*/

header("Content-Type: text/html;charset=UTF-8");

require_once('config.php');
require_once('functions/longsession.php');
require_once('functions/user.php');

if (!extension_loaded('mysql')) {
  echo "Please install the MySQL extension for PHP:
    <ul>
      <li>Debian: <code>aptitude install php4-mysql</code></li>
      <li>FC5: <code>yum install php-mysql</code></li>
    </ul>";
  exit;
}

$db = mysql_connect($hote, $user, $mdp) or 
die("Connection MySQL impossible pour l'utilisateur " . $user . " sur l'hôte " . $hote);

$se = mysql_select_db($base, $db) or
die("Connection impossible sur la base " . $base . "(" . $user . ", " . $hote . ")");

session_start();

if (isset($_COOKIE['gcourrier_session'])) {
  $session_hash = $_COOKIE['gcourrier_session'];
} else {
  unset($session_hash);
}

if (!isset($_SESSION['id']) and isset($session_hash)) {
  if (ctype_alnum($session_hash)) {
    $id = longsession_getid($session_hash);
    if ($id != -1) {
      longsession_renew();
      list($id, $login, $idService) = user_getbyid($id);
      if ($id != -1) {
	$_SESSION['id'] = $id;
	$_SESSION['login'] = $login;
	$_SESSION['idService'] = $idService;
      } else {
	// No such user. Maybe it was deleted?
	// -> No login.
	// echo "No such user: $id";
      }
    } else {
      // No such session. It probably expired.
      // -> No login.
      // echo "No such session: $session_hash";
    }
  } else {
    // Invalid hash. Probably a crack attempt.
    // echo "Invalid hash: $session_hash";
  }
}

if (!isset($_SESSION['login']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
  header("Location: login.php");
  exit;
}
