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
session_start();



if(!isset($_POST["envoyer"])){
?>
<html>
<head>
	<title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
</head>
<body>
<div id="page">
	<br><center><img src=images/banniere2.jpg></img><br><br><br></center>
	<table align=center>
	<form name = creerServiceForm method = post action= creerService.php>
		<tr><td>libelle</td>
		<td><input type = text name = libelle ></input></td></tr>
		<tr><td>designation</td>
		<td><input type = text name = designation></input></td></tr>
	</table>
		<center><input type = submit name = envoyer value = enregistrer></input></center>
	</form>

<center><br>
<a href = index.php>index</a><br><br>
</div>
</center>
</body>
</html>
<?php
}else{
	$requeteVerif="select * from service where libelle = '".$_POST['libelle']."';";
	$result = mysql_query($requeteVerif) or die(mysql_error());
	if(mysql_num_rows($result) !=0 ){
		echo "Ce service existe deja !</br>";
		echo "<a href=index.php>index</a>";
		exit();
	}

	$libelle = $_POST['libelle'];
	$designation = $_POST['designation'];
	
	
	$requeteExist = "select count(*) as nb from service where designation ='".$designation."';";
	$resultExist = mysql_query($requeteExist) or die (mysql_error( ));
	$ligne = mysql_fetch_array( $resultExist );
	if($ligne['nb'] != 0){
		echo "<meta http-equiv=\"refresh\" content=\"0;url=creerService.php\">";
		exit();
	}


	$requete = "insert into service( libelle , designation ) values ('".$libelle."','".$designation."')";
	$result = mysql_query($requete) or die( "erreur :".mysql_error( ) );
	echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";
}

?>
