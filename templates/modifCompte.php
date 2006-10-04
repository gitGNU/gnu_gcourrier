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
if(isset($_GET['mdp'])){
include("templates/header2.php");
echo "<div class='status'>Les mots de passes sont différents.</div>";
}

?>
<div id = pageGd><br>
<center> <img src = images/banniere2.jpg></img></center>
<br><br><br>
<table align=center>
<?php

$idUser = $_GET['id'];
$requete = "select utilisateur.nom as nomUser,
		   utilisateur.prenom as prenomUser,
		   utilisateur.login as loginUser
		   from utilisateur,service where utilisateur.id=".$idUser.";";
$result = mysql_query( $requete ) or die ("1".mysql_error() );
while($ligne = mysql_fetch_array( $result ) ){
$nom = $ligne['nomUser'];
$prenom = $ligne['prenomUser'];
$login = $ligne['loginUser'];
}
?>


			<form name = creerCompteForm method = POST action = modifCompte.php>
				<tr>
				
				<?php
				if($login != 'admin')
				echo"<td>login</td><td><input type = text name = login value ='".$login."'></input></td></tr>";
				else
				echo"<input type=hidden name=login value='".$_SESSION['login']."'></input>";
				?>
				<tr><td>nom</td>
				<td>
				<?php
				echo"<input type = text name = nom value='".$nom."'></input></tr></td>";
				?>
				<tr><td>prenom</td>
				<?php
				echo"<td><input type = text name = prenom value='".$prenom."'></input></td></tr>";
if($login != 'admin'){
				?>
				<tr><td>service</td>
				<td><select name = service>
				<?php
					$requete = "select libelle as libelle,
						    id as idService,
						    designation as designation
						    from service 
					   	    order by libelle;";
					$result = mysql_query($requete) or die("2 : ".mysql_error());
					while($ligne = mysql_fetch_array($result)){
					if($ligne['libelle'] !='ADMIN')
					echo "<option value = '".$ligne['idService']."'>".$ligne['libelle']." ".$ligne['designation']." </option>";
					}
				?>
				</select></td></tr>
<?
}
?>
				<tr><td>password</td>
				<td><input type = password name = password1></input></td></tr>
				<tr><td>retaper le password</td>
				<td><input type = password name = password2></input></td></tr>
				
				</table>
<?php
echo"<input type = hidden name=idUser value=".$idUser.">";
?>
				<center>
				<input type = submit name = enregistrer value=enregistrer>
				<center>
			</form> 

<center><br>
<a href = index.php>index</a><br><br>
</center>
</div>
</body>
</html>
