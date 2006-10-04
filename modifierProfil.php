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
require_once('connexion.php');
session_start();

if(!isset( $_POST['enregistrer'] ) ){
  include("templates/header2.php");
  include("templates/modifierProfil.php");
}

else{

if(strcmp($_POST['password1'],"")!=0){
//test de verification des passwords
if( $_POST['password1'] != $_POST['password2'] ){
//		echo "<meta http-equiv=\"refresh\" content=\"0;url=modifierProfil.php\">";
	    include("templates/header2.php");
	    echo "<div class='status'>Les mots de passes sont différents.</div>";
	    include("templates/modifierProfil.php");
	    exit();
	}


	else{

	//insertion des données dans la table utilisateur
		$login = $_POST['login'];
		$passwd = base64_encode( $_POST['password1'] );
		$nom = $_POST['nom'];
		$prenom = $_POST['prenom'];
		$nb = $_POST['nb'];
		
		$requete = "update utilisateur set login='".$login."' ,passwd='".$passwd."', nom='".$nom."',prenom='".$prenom."',preferenceNbCourrier=".$nb." where login='".$_SESSION['login']."';";
		$result = mysql_query($requete) or die("erreur: ".mysql_error() );		
		echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";
	
	}
}
else{
		$login = $_POST['login'];
		$nom = $_POST['nom'];
		$prenom = $_POST['prenom'];
		$nb = $_POST['nb'];
		
		$requete = "update utilisateur set login='".$login."', nom='".$nom."',prenom='".$prenom."',preferenceNbCourrier=".$nb." where login='".$_SESSION['login']."';";
		$result = mysql_query($requete) or die("erreur: ".mysql_error() );		
		echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";
}

}
?>
