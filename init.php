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

setlocale(LC_ALL, 'fr_FR.UTF-8');
header('Content-Type: text/html;charset=UTF-8');

if (!file_exists(dirname(__FILE__).'/config.php')) {
  echo "<code>config.php</code> not found!
Please create <code>config.php</code> using <code>config.php.dist</code>
as model.";
  exit(1);
} else {
  require_once('config.php');
}
require_once('functions/longsession.php');
require_once('functions/user.php');

if (!extension_loaded('mysql')) {
  echo "Please install the MySQL extension for PHP:
    <ul>
      <li>Debian: <code>aptitude install php4-mysql</code> or <code>aptitude install php5-mysql</code></li>
      <li>FC5: <code>yum install php-mysql</code></li>
    </ul>";
  exit;
}

mysql_connect($db_host, $db_user, $db_pass) or 
die("Connection MySQL impossible pour l'utilisateur " . $db_user . " sur l'hôte " . $db_host);

mysql_select_db($db_base) or
die("Connection impossible sur la base " . $db_base . "(" . $db_user . ", " . $db_host . ")");

// For PEAR::MDB2
$db_dsn = "mysql://$db_user:$db_pass@$db_host/$db_base";

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
      $user_params = user_getbyid($id);
      if ($user_params != NULL) {
	$_SESSION['id'] = $user_params['id'];
	// This is not good because this is cached for the duration of
	// the session - and this may change meanwhile. Use
	// $CURRENT_USER instead.
	$_SESSION['login'] = $user_params['login'];
	$_SESSION['idService'] = $user_params['idService'];
	$_SESSION['pagersize'] = $user_params['pagersize'];
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

if (isset($_SESSION['id'])) {
  // Cache current user's information
  $CURRENT_USER = user_getbyid($_SESSION['id']);
} else if (basename($_SERVER['PHP_SELF']) != 'login.php'
	   && basename($_SERVER['PHP_SELF']) != 'install.php') {
  header("Location: login.php");
  exit;
}
