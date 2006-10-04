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
?>
<div id = pageGd><br>
<center> <img src = images/banniere2.jpg></img></center>
<br><br><br>
<table align=center>
<?php
$requete = "SELECT nom, prenom, login, idService, libelle, designation, preferenceNbCourrier as nb
              FROM utilisateur, service
              WHERE utilisateur.idService = service.id
              AND login='{$_SESSION['login']}'";
$result = mysql_query( $requete ) or die (mysql_error() );
while($ligne = mysql_fetch_array( $result ) ){
  $nb = $ligne['nb'];
  $id = $ligne['id'];
  $nom = $ligne['nom'];
  $prenom = $ligne['prenom'];
  $login = $ligne['login'];
  $service = $ligne['libelle'] . ' - ' . $ligne['designation'];
}

?>


			<form name = creerCompteForm method = POST action = modifierProfil.php>
				<tr>
				
				<td>Identifiant</td><td><?php echo $_SESSION['login']?></td></tr>
				<input type="hidden" name="login" value='<?php echo $_SESSION['login']?>'></input>
				<tr><td>Nom</td>
				<td>
				<?php
				echo"<input type = text name = nom value='".$nom."'></input></tr></td>";
				?>
				<tr><td>Prénom</td>
				<?php
				echo"<td><input type = text name = prenom value='".$prenom."'></input></td></tr>";
				?>
				<tr><td>Nombre de courrier a afficher</td><?php 				echo"<td><input type = text name = nb value='".$nb."'></input></td></tr>";
 ?>
				<tr><td>Mot de passe</td>
				<td><input type = password name = password1></input></td></tr>
				<tr><td>Confirmez le mot de passe</td>
				<td><input type = password name = password2></input></td></tr>
				<tr><td>Service</td><td><?php echo $service ?></td></tr>


				</table>
				
				<center>
				<input type="submit" name="enregistrer" value="Enregistrer" />
				<center>
			</form> 

<center><br>
<a href = index.php>index</a><br><br>
</center>
</div>
</body>
</html>
