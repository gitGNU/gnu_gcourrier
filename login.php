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

if(!isset( $_POST['enregistrer'] ) ){
?>
<html>
<head>
<title>gCourrier</title>
<LINK HREF="styles.css" REL="stylesheet">
</head>

<body>

<div id = "login"><br>
<center> <img src = images/banniere2.jpg></img></center>

<table align=center>
	<br>
	<tr>
		<td>
		<table>
			<form name = log method = POST action = login.php>

			<tr>
				<td>login</td>
				<td><input type = text name = login></input></td>
			</tr>
			<tr>
				<td>password</td>
				<td><input type = password name = password></input></td>
			</tr>
		</table>
		</td>

		<td>
		<table>
			<tr>
				<td><img src = images/password.png></img></td>
			</tr>
		</table>
		</td>
	</tr>

</table>
	<center><br>
	<input type = submit name = enregistrer value = connexion> <br><br>
	</center>

	</form>
</div>
	
<center>
<div id="fin_page">
GCourrier 1.6
<a href="copyright.html">Licence</a></center>
</div>
</body>
</html>
<?php
}else{
	$login = $_POST['login'];
	$password = base64_encode($_POST['password']);
	
	$requete = "select id,idService
		 from utilisateur
		 where login = '".$login."' and passwd ='".$password."'; ";
	$result = mysql_query($requete) or die (mysql_error() );

	$ligne = mysql_fetch_array($result);

	if( $ligne != NULL ){
		$_SESSION['id'] = $ligne['id'];
		$_SESSION['login'] = $login;
		$_SESSION['idService'] = $ligne['idService'];
		echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";
	}else{
	echo "<meta http-equiv=\"refresh\" content=\"0;url=login.php\">";
	}		

}
?>
