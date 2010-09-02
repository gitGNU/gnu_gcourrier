<?php
/*
GCourrier
Copyright (C) 2005, 2006  Cliss XXI

This file is part of GCourrier.

GCourrier is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

GCourrier is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

author VELU Jonathan
*/

require_once('init.php');
require_once('functions/longsession.php');

if (isset($_SESSION['id'])) {
  header("Location: index.php");
}

if (!isset($_POST['login'])) {
  include("templates/header_login.php");
  include("templates/login.php");
  include("templates/footer.php");
?>
<?php
} else {
  $login = $_POST['login'];
  $password = base64_encode($_POST['password']);
  
  $requete = "SELECT id,idService, preferenceNbCourrier AS pagersize
                FROM utilisateur
                WHERE login = '".$login."' AND passwd ='".$password."'; ";
  $result = mysql_query($requete) or die (mysql_error() );
  
  $ligne = mysql_fetch_array($result);
  
  if ($ligne != NULL) {
    $_SESSION['id'] = $ligne['id'];
    $_SESSION['login'] = $login;
    $_SESSION['idService'] = $ligne['idService'];
    $_SESSION['pagersize'] = $ligne['pagersize'];
    if (isset($_POST['remember'])) {
      $session_hash = longsession_new();
      setcookie('gcourrier_session', $session_hash, strtotime("now + 1 week"));
    }
    header('Location: index.php');
  } else {
    include('templates/header_login.php');
    echo "<div class='status'>Vous vous êtes trompé(e) dans "
      . "le nom d'utilisateur ou dans le mot de passe.</div>";
    include('templates/login.php');
    include('templates/footer.php');
  }
}
