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
require_once('functions/mail.php');
require_once('functions/priority.php');
require_once('functions/contact.php');
require_once('functions/status.php');
require_once('functions/db.php');

$copy = 0;
$destinataire = null;
if (isset($_POST["enregistrer"]) or isset($_POST["enregistrer_puis_copie"])) {
  $libelle = $_POST['libelle'];
  $observation = $_POST['observation'];
  $service = $_POST['serviceDest'];
  $priorite = $_POST['priority'];
  $destinataire = $_POST['contact_id'];
  if (empty($destinataire)) exit("Le contact est vide!");

  $tmpDate = $_POST['dateArrivee'];
  $date= substr($tmpDate, 6,4);
  $date.='-';
  $date.=substr($tmpDate, 3,2);
  $date.='-';
  $date.=substr($tmpDate, 0,2);
	
  $requeteCourrier = "INSERT INTO courrier (libelle,dateArrivee,observation,idPriorite,idServiceCreation,idDestinataire,serviceCourant,type) VALUES('".$libelle."','".$date."','".$observation."','".$priorite."','".$_SESSION['idService']."','".$destinataire."','".$service."',2);";
  $resultatCourrier = mysql_query( $requeteCourrier ) or die ("erreur requete courrier :".mysql_error( ) );


  //Recuperation de l'id du courrier cree
  $idCourrier = mysql_insert_id();
  status_push("Vous venez de créer le courrier numéro: $idCourrier");


  //transmission du courrier
  $requeteTransmis = "INSERT INTO estTransmis (idService, idCourrier,dateTransmission) VALUES ('".$service."','".$idCourrier."','".date("Y-m-d")."');";
  $resultatTransmis = mysql_query($requeteTransmis) or die ("erreur requete transmis ".mysql_error());


  // Reply
  if (!empty($_POST['reply_to']))
    {
      $_POST['reply_to'] = intval($_POST['reply_to']);
      if (mail_exists('id=?', array($_POST['reply_to'])))
	{
	  mail_reply_new($_POST['reply_to'],  $idCourrier);
	  status_push("Courrier $idCourrier en réponse au courrier {$_POST['reply_to']}");
	}
    }
  
  //
  // Pièce jointe
  //
  mail_handle_attachment($idCourrier);


  // header("Location: index.php");

  if (isset($_POST["enregistrer_puis_copie"]))
    {
      $copy = 1;
    }
}
require_once('templates/header.php');
?>
	<table align=center>
		<form enctype="multipart/form-data" name = creerCourrier.php method= POST action = courrierDepart.php> 
		
		<tr>
		<td>Destinataire</td>
		<td>
<?php
		  contact_display();
?>
                </td>
                </tr>
		
		<tr>
		<td>Émetteur</td>
		<td><select name="serviceDest">
		<?php
			$requete = "SELECT * FROM service WHERE libelle <> 'admin' ORDER BY libelle;";
			$result = mysql_query($requete) or die (mysql_error());
			while ($ligne = mysql_fetch_array($result)) {
			  $selected = "";
			  if ($ligne['id'] == $_SESSION['idService'])
			    $selected = 'selected="selected"';
			  echo "<option value='{$ligne['id']}' $selected>"
			    . "{$ligne['libelle']} {$ligne['designation']}"
			    . "</option>";
			}
		?>
		</td>
		</tr>

		<tr><td>Libellé</td>
		<td><input type="text" name="libelle"></input></td></tr>
		<tr><td>Date création</td>
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
	<td><label>Joindre un fichier</label></td>
	<td><input type="file" name="mail_file"></td>
	</tr>
	</table><br>
	<input type="hidden" name="reply_to" value="<?php echo $_GET['reply_to']; ?>" />
	<center>
	  <!--
	  <input type="submit" name="enregistrer_puis_copie"
	    value="Enregistrer puis commencer un courrier au même destinataire">
	  <input type="submit" name="enregistrer"
	    value ="Enregistrer puis commencer un courrier vierge">
	  -->
	  <input type="submit" name="enregistrer"
	    value ="Enregistrer">
	</center>
      </form>
<?php
include('templates/footer.php');
