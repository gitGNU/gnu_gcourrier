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

require_once('init.php');

if(!isset( $_POST['enregistrer'] ) ){
  include("templates/header2.php");
  include("templates/modifCompte.php");
}

else{
  $idUser = $_POST['idUser'];
  
  if(strcmp($_POST['password1'],"")!=0){
  
      if( $_POST['password1'] != $_POST['password2'] ){
	  
          echo "<meta http-equiv=\"refresh\" content=\"0;url=modifCompte.php?id=".$idUser."&mdp=1\">";	  
//	  include("templates/header2.php");
//	  echo "<div class='status'>Les mots de passes sont différents.</div>";
//	  include("templates/modifCompte.php?id=".$idUser."");
	  exit();
      }

      else{
        //insertion des données dans la table utilisateur
	$login = $_POST['login'];
	$passwd = base64_encode( $_POST['password1'] );
	$nom = $_POST['nom'];
	$prenom = $_POST['prenom'];
	
	if($login != 'admin'){
		$service = $_POST['service'];
		$requete = "update utilisateur set login='".$login."' ,passwd='".$passwd."', nom='".$nom."',prenom='".$prenom."', idService=".$service." where login ='".$login."';";
		$result = mysql_query($requete) or die("3: ".mysql_error() );		
	}

	else{
		$service = $_POST['service'];
		$requete = "update utilisateur set login='".$login."' ,passwd='".$passwd."', nom='".$nom."',prenom='".$prenom."' where login ='".$login."';";
		$result = mysql_query($requete) or die("4: ".mysql_error() );		
	}
      }//fin else si mauvais passwd
  }//fin if str

  else{
	//insertion des données dans la table utilisateur hors mdp
	$login = $_POST['login'];
	$nom = $_POST['nom'];
	$prenom = $_POST['prenom'];
	
	if($login != 'admin'){
		$service = $_POST['service'];
		$requete = "update utilisateur set login='".$login."', nom='".$nom."',prenom='".$prenom."', idService=".$service." where login ='".$login."';";
		$result = mysql_query($requete) or die("3: ".mysql_error() );		
	}

	else{
		$service = $_POST['service'];
		$requete = "update utilisateur set login='".$login."', nom='".$nom."',prenom='".$prenom."' where login ='".$login."';";
		$result = mysql_query($requete) or die("4: ".mysql_error() );		
  
       }
    }//fin else

echo "<meta http-equiv=\"refresh\" content=\"0;url=voirCompte.php\">";	
}//fin 1er else
?>
