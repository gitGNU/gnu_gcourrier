<?php
/*
GCourrier
Copyright (C) 2005,2006 CLISS XXI

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

require_once("connexion.php");

if (!isset($_POST['login'])) {
?>
<html>
<head>
<title>gCourrier</title>
<LINK HREF="styles.css" REL="stylesheet">
</head>

<body>

<div id="login">
  <img src="images/banniere2.jpg" />

  <form method="POST" action="login.php">
    <table style="margin: 30px 0px 20px 0px; margin-left:auto; margin-right:auto;">
      <tr>
        <td>Identifiant: </td>
        <td><input type="text" name="login" /></td>
        <td rowspan="2"><img src="images/password.png" /></td>
      </tr>
      <tr>
        <td>Mot de passe: </td>
        <td><input type="password" name="password" /></td>
      </tr>
    </table>
    <input type="submit" value="Connexion">
  </form>
</div>

<div id="fin_page">
  GCourrier 1.6
  <a href="copyright.html">Licence</a></center>
</div>

</body>
</html>
<?php
} else {
  $login = $_POST['login'];
  $password = base64_encode($_POST['password']);
  
  $requete = "select id,idService
                from utilisateur
                where login = '".$login."' and passwd ='".$password."'; ";
  $result = mysql_query($requete) or die (mysql_error() );
  
  $ligne = mysql_fetch_array($result);
  
  if ($ligne != NULL) {
    $_SESSION['id'] = $ligne['id'];
    $_SESSION['login'] = $login;
    $_SESSION['idService'] = $ligne['idService'];
    if (isset($_POST['remember'])) {
      $session_hash = session_new();
      setcookie('gcourrier_session', $session_hash);
    }
    echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";
  } else {
    echo "<meta http-equiv=\"refresh\" content=\"0;url=login.php\">";
  }
}
?>
