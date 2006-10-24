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

require("connexion.php");

?>
<html>
<head><title>gCourrier</title>
<link rel="stylesheet" href=styles3.css type="text/css">
</head>
<body >
<br>
	<center>
		<img src= images/banniere2.jpg></img><br><br>
<table>
<tr>
<td>login</td>
<td>nom</td>
<td>prenom</td>
<td>service</td>
<td>modifier</td>
</tr>
<?php
$requete = "select utilisateur.login as login,utilisateur.id as idUser,utilisateur.nom as nomUser,utilisateur.prenom as prenomUser, service.designation as descService from utilisateur, service where utilisateur.idService = service.id;";
$result = mysql_query($requete) or die(mysql_error());
while($ligne = mysql_fetch_array($result)){
if($boul == 0){
		$couleur = lightblue;
		$boul = 1;
	}
	else{
		$couleur = white;
		$boul = 0;	
	}
	echo "<tr><td bgcolor=".$couleur.">".$ligne['login']."</td><td bgcolor=".$couleur.">".$ligne['nomUser']."</td><td bgcolor=".$couleur.">".$ligne['prenomUser']."</td><td bgcolor=".$couleur.">".$ligne['descService']."</td><td style='text-align:center' bgcolor=".$couleur."><a href=modifCompte.php?id=".$ligne['idUser'].">m</a></td></tr>";
}
?>
<table>
	</center>
<br>
<center><a href = index.php>index</a></center>
<br><br>

</center>
</body>

</html>

