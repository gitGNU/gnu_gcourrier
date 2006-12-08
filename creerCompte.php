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


if(!isset($_POST['enregistrer'])) {
?>
<html>
  <head>
    <title>gCourrier</title>
    <link href="styles.css" rel="stylesheet">
    </head>
  <body>
    <div id="page">
      <img src="images/banniere2.jpg">

      <form name="creerCompteForm" method="POST" action="creerCompte.php">
        <table>
          <tr><td>Identifiant</td>
              <td><input type="text" name="login" /></td></tr>
          <tr><td>Nom</td>
              <td><input type="text" name="firstname"></input></tr></td>
          <tr><td>Prénom</td>
              <td><input type="text" name="lastname"></input></td></tr>
          <tr><td>Service</td>
              <td><select name="service">
	  <?php
 $requete = "SELECT * FROM service WHERE libelle <> 'ADMIN' ORDER BY libelle";
 $result = mysql_query($requete) or die('erreur : '.mysql_error());
 while($ligne = mysql_fetch_array($result))
   echo "<option value = '{$ligne['id']}'>{$ligne['libelle']} {$ligne['designation']}</option>";
?>
                </select>
              </td>
          </tr>
          <tr><td>Mot de passe</td>
	      <td><input type="password" name="password1"></input></td></tr>
          <tr><td>Retapez le mot de passe</td>
	      <td><input type="password" name="password2"></input></td></tr>
	</table>

        <input type="submit" name="enregistrer" value="Enregistrer">
      </form> 

      <a href="index.php">Retour à l'index</a>
    </div>
  </body>
</html>
<?php
//'
}
else
{
  $login = $_POST['login'];
  $requeteVerif = "SELECT * FROM utilisateur WHERE login='$login';";
  $result = mysql_query($requeteVerif) or die(mysql_error());
  if(mysql_num_rows($result) !=0 ){
    echo 'Ce login existe déjà!<br />';
    echo '<a href="index.php">Index</a>';
    exit();
  }
  
  // Test de vérification des mots de passe
  if ($_POST['password1'] != $_POST['password2'])
    {
      header('Location: creerCompte.php');
      exit();
    }
  else
    {
      // Insertion des données dans la table utilisateur
      $passwd = base64_encode($_POST['password1']);
      $nom = $_POST['nom'];
      $prenom = $_POST['prenom'];
      $service = $_POST['service'];
      
      $requete = "INSERT INTO utilisateur (login, passwd, nom, prenom, idService)
                    VALUES ('$login','$passwd', '$nom', '$prenom','$service')";
      $result = mysql_query($requete) or die(mysql_error());				
      header('Location: index.php');
    }
}
