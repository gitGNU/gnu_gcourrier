<?php
/*
GCourrier
Copyright (C) 2005, 2006, 2009, 2010  CLISS XXI

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

author VELU Jonathan, Sylvain BEUCLER
*/

require_once('init.php');
require_once('functions/mail.php');
require_once('functions/priority.php');

if (isset($_POST["enregistrer"])) {
  $destinataire = $_POST['destinataire'];
  $libelle = $_POST['libelle'];
  $observation = $_POST['observation'];
  $service = $_POST['serviceDest'];
  $priorite = $_POST['priority'];
  
  $tmpDate = $_POST['dateArrivee'];
  $date= substr($tmpDate, 6,4);
  $date.='-';
  $date.=substr($tmpDate, 3,2);
  $date.='-';
  $date.=substr($tmpDate, 0,2);
  
  $requeteCourrier = "insert into courrier(libelle,dateArrivee,observation,idPriorite,idServiceCreation,idDestinataire,serviceCourant,type,url) values('".$libelle."','".$date."','".$observation."','".$priorite."','".$_SESSION['idService']."','".$destinataire."','".$service."',1,'".$url."');";
  $resultatCourrier = mysql_query( $requeteCourrier ) or die ("erreur requete courrier :".mysql_error( ) );
  
  
  //Recuperation de l'id du courrier cree
  $idCourrier = mysql_insert_id();  
  status_push("Vous venez de créer le courrier numéro: $idCourrier");

  
  //transmission du courrier
  $requeteTransmis = "insert into estTransmis( idService, idCourrier,dateTransmission ) values('".$service."','".$idCourrier."','".date("Y-m-d")."');";
  $resultatTransmis = mysql_query( $requeteTransmis ) or die ("erreur requete transmis ".mysql_error( ) );
  
  
  //
  // Pièce jointe
  //
  mail_handle_attachment($idCourrier);
}


include('templates/header.php');
?>
	<table align = center>
		<form  enctype="multipart/form-data"  name = creerCourrier.php method= POST action = creerCourrier.php> 
<?/*		
		<tr>
		<td>numero courrier: </td>
		<td>
		<?php
		$requete = "select max(id) as idTmp from courrier ;";
		$result = mysql_query( $requete ) or die(mysql_error () ) ;
		while($ligne= mysql_fetch_array( $result ) ){
			$idtmp = $ligne['idTmp'] + 1;
		}
		echo $idtmp;
		?>
		</td>
		</tr>
*/
?>
		<tr>
		<td>Émetteur</td>
		<td><select name = destinataire>
		<?php
		$requete = "select * from destinataire order by nom ; ";
		$result = mysql_query($requete) or die( mysql_error() );
		while( $ligne = mysql_fetch_array( $result ) ){
		    echo "<option value = '".$ligne['id']."'>".$ligne['nom']." ".$ligne['prenom']."</option>";
		}
		?>
	</select> <a href="creerDestinataire.php">[créer]</a></td></tr>
		
		<tr>
		<td>Service destinataire</td>
		<td><select name = serviceDest>
		<?php
			$requete = "select * from service where libelle <>'admin' order by libelle;";
			$result = mysql_query($requete) or die ( mysql_error() );
			while( $ligne = mysql_fetch_array( $result ) ){
				 echo "<option value = '".$ligne['id']."'>".$ligne['libelle']." ".$ligne['designation']."</option>";
			}
		?>
		</td>
		</tr>

		<tr><td>Libellé</td>
		<td><input type=text name = libelle></input></td></tr>
		<tr><td>Date arrivée</td>
		<td>
			<?php
			$dateToday = date("d-m-Y"); 
			echo "<input type = text name= dateArrivee value ='".$dateToday."'></input>";
		?></td></tr>
		<tr><td>Observation</td>
		<td><textarea name=observation cols=30 rows=4></textarea></td></tr>
	        <tr><td>Priorité</td><td>
                  <?php
                    $id = priority_getdefaultmail();
                    priority_display($id);
                  ?>
                </td></tr>
	<tr>
	<td><label>Attacher un fichier</label></td>
	<td><input type="file" name="mail_file"></td>
	</tr>
	</table>
		<center>
		<input type="submit" name="enregistrer" value="Enregistrer"></input>
		</center>
		</form>
<?php

include('templates/footer.php');
