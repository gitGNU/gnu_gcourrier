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


if(!isset( $_POST['enregistrer'] ) ){
?>
<html>
	<head>
		<title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
	</head>
	<body >
		<div id = "pageGd"><br>

		<center><img src=images/banniere2.jpg></center><br><br>

		<table align=center>


			<form method = POST action = modifierAccuse.php>
				<tr><td><label>expediteur : </label></td>
				<td><input type = text name = expediteur></input></td></tr>
				<tr><td><label>adresse : </label></td>
				<td><input type = text name = adresse></input></td></tr>
				<tr><td><label>codePostal : </label></td>
				<td><input type = text name = codePostal></input></td></tr>
				<tr><td><label>ville : </label></td>
				<td><input type = text name = ville></input></td></tr>
				<tr><td><label>telephone : </label></td>
				<td><input type = text name = telephone></input></td></tr>
		</table>
				<center>
				<input type = submit name = enregistrer value=enregistrer>
				<center>
			</form> 
<center><br>
<a href = index.php>index</a><br><br></center>
</div>
				
	</body>
</html>
<?php
}
else{
	$expediteur = $_POST['expediteur'];
	$adresse = $_POST['adresse'];
	$codePostal = $_POST['codePostal'];
	$ville = $_POST['ville'];
	$telephone = $_POST['telephone'];

	$requete = "update accuse set expediteur = '".$expediteur."',
		    adresse = '".$adresse."',
		    codePostal = '".$codePostal."',
		    ville = '".$ville."',
		    telephone = '".$telephone."'
		    where id = 1;";
	$result = mysql_query($requete) or die(mysql_error() );
		
	echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";

}
?>
