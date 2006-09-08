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

header("content-type: text/html; charset=UTF-8");

require_once('config.php');
require_once('include/session.php');
require_once('include/user.php');

$db = mysql_connect($hote, $user, $mdp) or 
die("Connection impossible pour l'utilisateur " . $user . " sur l'hôte " . $hote);

$se = mysql_select_db($base, $db) or
die("Connection impossible sur la base " . $base . "(" . $user . ", " . $hote . ")");

session_start();

$session_hash = $_COOKIES['gcourrier_session'];
if (!isset($_SESSION['id']) and isset($session_hash)) {
  $id = session_get($_COOKIES['gcourrier_session']);
  if ($id != -1) {
    session_renew($id, $session_hash);
    ($ignored, $login, $idService) = user_getbyid($id);
    $_SESSION['id'] = $id;
    $_SESSION['login'] = $login;
    $_SESSION['idService'] = $idService;
  }
}
