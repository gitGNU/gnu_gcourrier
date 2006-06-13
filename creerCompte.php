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
session_start( );


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


			<form name = creerCompteForm method = POST action = creerCompte.php>
				<tr><td>login</td>
				<td><input type = text name = login></input></td></tr>
				<tr><td>nom</td>
				<td><input type = text name = nom></input></tr></td>
				<tr><td>prenom</td>
				<td><input type = text name = prenom></input></td></tr>
				<tr><td>service</td>
				<td><select name = service>
				<?php
					$requete = "select * from service order by libelle;";
					$result = mysql_query($requete) or die("erreur : ".mysql_error());
					while($ligne = mysql_fetch_array($result)){
					if($ligne['libelle'] !='ADMIN')
					echo "<option value = '".$ligne['id']."'>".$ligne['libelle']." ".$ligne['designation']." </option>";
					}
				?>
				</select></td></tr>
				<tr><td>password</td>
				<td><input type = password name = password1></input></td></tr>
				<tr><td>retaper le password</td>
				<td><input type = password name = password2></input></td></tr>
				
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
	$requeteVerif="select * from utilisateur where login = '".$_POST['login']."';";
	$result = mysql_query($requeteVerif) or die(mysql_error());
	if(mysql_num_rows($result) !=0 ){
		echo "Ce login existe deja !</br>";
		echo "<a href=index.php>index</a>";
		exit();
	}

	//test de verification des passwords
	if( $_POST['password1'] != $_POST['password2'] ){
		echo "<meta http-equiv=\"refresh\" content=\"0;url=creerCompte.php\">";
		exit();
	}



	else{

	//insertion des données dans la table utilisateur
		$login = $_POST['login'];

	if($login == "admin"){
			echo "<meta http-equiv=\"refresh\" content=\"0;url=creerCompte.php\">";
			exit();
	}

	$passwd =base64_encode( $_POST['password1'] );
	$nom = $_POST['nom'];
	$prenom = $_POST['prenom'];
	$service = $_POST['service'];

	$requete = "insert into utilisateur(login,passwd,nom,prenom,idService) values ('".$login."','". $passwd."' , '".$nom."' , '".$prenom."','".$service."')";
	$result = mysql_query($requete) or die(mysql_error());				
	echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";
	}
}
?>
