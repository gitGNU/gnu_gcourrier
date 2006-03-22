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

session_start();
require("connexion.php");


if(!isset($_POST["envoyer"])){
?>
<html>
<head>
	<title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
</head>
<body>
<div id = page><br>
<center><img src=images/banniere2.jpg></img><br><br><br></center>
	<table align = center>

	<form name = creerPrioriteForm method = post action = creerPriorite.php>
		<tr><td>Designation</td>
		<td><input type = text name = designation ></input></td></tr>
		<tr><td>NbJours</td>
		<td><input type = text name = nbJours ></input></td></tr>
	</table>
		<center>
		<input type = submit name = envoyer value = envoyer>
		</center>

<center><br>
<a href = index.php>index</a><br><br>
</center>
</div>
</body>
</html>
<?php
}else{
	$designation = $_POST['designation'];
	$nbJours = $_POST['nbJours'];

	
	$requeteExist = "select count(*) as nb from priorite where designation ='".$designation."';";
	$resultExist = mysql_query($requeteExist) or die (mysql_error( ));
	$ligne = mysql_fetch_array( $resultExist );
	if($ligne['nb'] != 0){
		echo "<meta http-equiv=\"refresh\" content=\"0;url=creerPriorite.php\">";
		exit();
	}
	
	$requete = "insert into priorite(designation, nbJours) values( '".$designation."','".$nbJours."');";
	$result = mysql_query($requete) or die( mysql_error( ));
	echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";
}
?>
