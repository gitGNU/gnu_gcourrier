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

require_once("init.php");


if (isset($_POST["enregistrer"])) {
    $content_dir = 'upload/'; // dossier où sera déplacé le fichier

    $tmp_file = $_FILES['fichier']['tmp_name'];

    if( !is_uploaded_file($tmp_file) )
    {
	$url = "";
    }

    else{
        $url = "upload/".$_FILES['fichier']['name'];
    }
    // on copie le fichier dans le dossier de destination
    $name_file = $_FILES['fichier']['name'];

    if( !move_uploaded_file($tmp_file, $content_dir . $name_file) )
    {
//        exit("Impossible de copier le fichier dans $content_dir");
    }

  //  echo "Le fichier a bien été uploade";



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
	
$requeteCourrier = "insert into courrier(libelle,dateArrivee,observation,idPriorite,idServiceCreation,idDestinataire,serviceCourant,type,url) values('".$libelle."','".$date."','".$observation."','".$priorite."','".$_SESSION['idService']."','".$destinataire."','".$service."',1,'".$url."');";
$resultatCourrier = mysql_query( $requeteCourrier ) or die ("erreur requete courrier :".mysql_error( ) );


//Recuperation de l'id du courrier cree


$requeteIdCourrier = "select id from courrier where idServiceCreation =".$_SESSION['idService']." order by id;";
$resultatIdCourrier = mysql_query( $requeteIdCourrier ) or die ("erreur requete idCourrier".mysql_error( ) );
while($ligne = mysql_fetch_array($resultatIdCourrier ) )
	$idCourrier = $ligne['id'];



//transmission du courrier


$requeteTransmis = "insert into estTransmis( idService, idCourrier,dateTransmission ) values('".$service."','".$idCourrier."','".date("Y-m-d")."');";
$resultatTransmis = mysql_query( $requeteTransmis ) or die ("erreur requete transmis ".mysql_error( ) );


$adresse ="infoCourrier.php?idCourrier=".$idCourrier;

$status = "Vous venez de créer le courrier numéro: <strong>$idCourrier</strong>.";
}

?>

<html>
<head>
  <title>gCourrier</title>
  <link href="styles.css"  rel="stylesheet" />
  <link href="styles2.css" rel="stylesheet" />
</head>

<body>
<?php
if (isset($status)) {
  echo "<div class='status'>$status</div>";
}
?>

<div id = pageTGd><br>
	<center>
	<img src = images/banniere2.jpg></img><br><br><br>
	</center>
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
		<td>Emetteur</td>
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
		<td>Service Destinataire</td>
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
		<tr><td>Date Arrivee</td>
		<td>
			<?php
			$dateToday = date("d-m-Y"); 
			echo "<input type = text name= dateArrivee value ='".$dateToday."'></input>";
		?></td></tr>
		<tr><td>Observation</td>
		<td><textarea name=observation cols=30 rows=4></textarea></td></tr>

	<td>Priorite</td>
		<td><select name = priorite>
		<?php
		$requete = "select * from priorite ; ";
		$result = mysql_query($requete) or die( mysql_error() );
		while( $ligne = mysql_fetch_array( $result ) ){
		    echo "<option value = '".$ligne['id']."'>".$ligne['libelle']." ".$ligne['designation']."</option>";
		}
		?>
	</select></td></tr>
	<tr>
	<td><label>Attacher un fichier</label></td>
	<td><input type="file" name="fichier"></td>
	</tr>
	</table>
		<center>
		<input type = submit name = enregistrer value = enregistrer></input>
		</center>
		</form>

<center><br>
<a href = index.php>index</a>
<br><br>
</center>
</div>
</body>
</html>
