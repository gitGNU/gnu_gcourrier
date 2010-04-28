<?php
/*
GCourrier
Copyright (C) 2005, 2006, 2010  CLISS XXI

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
require_once('functions/priority.php');
require_once('functions/status.php');
require_once('functions/db.php');

if (!isset($_POST["enregistrer"])) {

require_once('templates/header.php');

?>
	<table align = center>
		<form enctype="multipart/form-data" name = creerCourrier.php method= POST action = courrierDepart.php> 
		
		<tr>
		<td>Destinataire</td>
		<td><select name = destinataire>
		<?php
		$requete = "select * from destinataire order by nom ; ";
		$result = mysql_query($requete) or die( mysql_error() );
		while( $ligne = mysql_fetch_array( $result ) ){
		    echo "<option value = '".$ligne['id']."'>".$ligne['nom']." ".$ligne['prenom']."</option>";
		}
		?>
	</select><a href=creerDestinataire.php>creer</a></td></tr>
		
		<tr>
		<td>Emetteur</td>
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

		<tr><td>Libelle</td>
		<td><input type=text name = libelle></input></td></tr>
		<tr><td>Date Creation</td>
		<td>
			<?php
			$dateToday = date("d-m-Y"); 
			echo "<input type = text name= dateArrivee value ='".$dateToday."'></input>";
		?></td></tr>
		<tr><td>Observation</td>
		<td><textarea name=observation cols=30 rows=4></textarea></td></tr>
<?php
		priority_display();
?>
	<tr>
	<td><label>Joindre un fichier</label></td>
	<td><input type="file" name="fichier"></td>
	</tr>
	</table><br>
		<center>
		<input type = submit name = enregistrer value="Enregistrer"></input>
		</center>
		</form>

<center><br>
<a href = index.php>index</a>
<br><br>
</center>
</div>
</body>
</html>
<?php

} else {

$destinataire = $_POST['destinataire'];
$libelle = $_POST['libelle'];
$observation = $_POST['observation'];
$service = $_POST['serviceDest'];
$priorite = $_POST['priorite'];

$tmpDate = $_POST['dateArrivee'];
$date= substr($tmpDate, 6,4);
$date.='-';
$date.=substr($tmpDate, 3,2);
$date.='-';
$date.=substr($tmpDate, 0,2);
	
$requeteCourrier = "insert into courrier(libelle,dateArrivee,observation,idPriorite,idServiceCreation,idDestinataire,serviceCourant,type,url) values('".$libelle."','".$date."','".$observation."','".$priorite."','".$_SESSION['idService']."','".$destinataire."','".$service."',2,'".$url."');";
$resultatCourrier = mysql_query( $requeteCourrier ) or die ("erreur requete courrier :".mysql_error( ) );


//Recuperation de l'id du courrier cree
$idCourrier = mysql_insert_id();


//transmission du courrier
$requeteTransmis = "insert into estTransmis( idService, idCourrier,dateTransmission ) values('".$service."','".$idCourrier."','".date("Y-m-d")."');";
$resultatTransmis = mysql_query($requeteTransmis) or die ("erreur requete transmis ".mysql_error( ) );


// Pièce jointe
if ($_FILES['fichier']['error'] == UPLOAD_ERR_OK) {
  $old_umask = umask(0);

  $content_dir = "upload/courrier/$idCourrier"; // dossier où sera déplacé le fichier
  mkdir($content_dir, 0755, true) or die("Impossible de créer $content_dir");

  // on copie le fichier dans le dossier de destination
  $tmp_file = $_FILES['fichier']['tmp_name'];
  $dest_file = "$content_dir/{$_FILES['fichier']['name']}";
  if (!move_uploaded_file($tmp_file, $dest_file)) {
    exit("Impossible de copier $tmp_file dans $dest_file");
  } else {
    // Give permissions to other users, including Apache. This is
    // necessary in a suPHP setup.
    chmod($dest_file, 0644);
    db_autoexecute('courrier', array('url' => $dest_file), DB_AUTOQUERY_UPDATE,
                   'id=?', array($idCourrier));
  }

  umask($old_umask);
} elseif ($_FILES['fichier']['error'] != UPLOAD_ERR_NO_FILE) {
  exit("Erreur lors de l'envoi du fichier {$_FILES['userfile']['name']}"
       . " (erreur {$_FILES['fichier']['error']})");
}

status_push("Vous venez de créer le courrier numéro: $idCourrier");
header("Location: index.php");
}

include('templates/footer.php');
