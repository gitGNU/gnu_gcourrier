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
require_once('functions/db.php');

if (isset($_GET['affiche']))
     $affiche = $_GET['affiche'];

if (!isset( $_POST["enregistrer"])) {
?>
<html>
<head><title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
</head>
<body>
<div id=pageGd><br>
	<center><img src =images/banniere2.jpg></center><br><br><br>

	<table align = center>

	<form name = transmettreForm method = POST action = transmettre.php>
	<tr><td>service</td><td>
	<select name = service>
		<?php
		$requete = "select * from service order by libelle ; ";
		$result = mysql_query($requete) or die( mysql_error() );
		while( $ligne = mysql_fetch_array( $result ) ){
		  if($ligne['libelle'] != "ADMIN")
		  echo "<option value = '".$ligne['id']."'>".$ligne['libelle']." ".$ligne['designation']."</option>";
		}
		?>
	</select></td></tr>
	<?php
	$idCourrier = $_GET["idCourrier"];
	echo"<input type = hidden name = idCourrier value=".$idCourrier."></input>";
	?>
	<tr><td>Observation</td>
	<td><textarea name=observation cols=30 rows=4><?php
	$result = db_execute("SELECT observation FROM courrier WHERE id = ?", array($idCourrier));
	$ligne = mysql_fetch_array($result);
	echo $ligne['observation'];
	?></textarea>
        </td></tr>
	</table>
	<center>
<?php
	echo "<input type = hidden name=nbAffiche value=".$affiche."></input>";
	echo "<input type = hidden name=type value=".$_GET['type']."></input>";
?>
	<input type = submit name = enregistrer value = transmettre></input></center>
</form>

<center><br>
<?php
echo"<a href = voirCourrier.php?id=".$idCourrier."&nbAffiche=".$affiche."&type=".$_GET['type'].">voir mon courrier</a><br><br>";
?>
</center>
</div>
</body>
</html>
<?php
} else {
  //
  // Get information
  //
  $new_service = $_POST['service'];
  $idCourrier = $_POST['idCourrier'];
  $result = db_execute('
SELECT service.libelle AS label
FROM courrier
  JOIN estTransmis T1 ON courrier.id = T1.idCourrier
  JOIN estTransmis T2 ON T1.idCourrier = T2.idCourrier
  JOIN service ON T1.idService = service.id
WHERE courrier.id = ?
GROUP BY T1.id HAVING T1.id >= MAX(T2.id)',
		     array($idCourrier));
  $row = mysql_fetch_array($result);
  $old_service_label = $row['label'];

  //
  // DB update
  //

  $result = db_autoexecute('estTransmis',
    array('dateTransmission' => date("Y-m-d"),
	  'idCourrier' => $idCourrier,
	  'idService' => $new_service),
    DB_AUTOQUERY_INSERT);

  $observation = $_POST['observation'];
  $result = db_autoexecute('courrier',
    array('observation' => $observation,
          'serviceCourant' => $new_service),
    DB_AUTOQUERY_UPDATE,
    'id=?', array($idCourrier));

  //
  // E-mail notification
  //

  $result = db_execute("SELECT libelle AS label, email FROM service WHERE id=?",
		       array($new_service));
  $row = mysql_fetch_array($result);
  $email = $row['email'];
  $new_service_label = $row['label'];
  // libelle emetteur arrivee observation
  if (!empty($email))
    {
      $result = db_execute("SELECT libelle AS label,
                              nom, prenom,
                              dateArrivee AS arrival_date,
                              observation AS observation
                            FROM courrier JOIN destinataire ON courrier.idDestinataire = destinataire.id
                            WHERE courrier.id=?",
			   array($idCourrier));
      $row = mysql_fetch_array($result);
      $label = 
      $observation = 
      // ...

      mail($email,
	   sprintf(_("Transmission du courrier %d: %s"),
		   $idCourrier, $row['label']),
	   sprintf(_("Bonjour,

Le courrier %d a été transféré du service %s à votre service %s
De: %s %s
Objet: %s
Observation: %s"),
		   $idCourrier, $old_service_label, $new_service_label,
		   $row['nom'], $row['prenom'],
		   $row['label'],
		   $row['observation']),
	   "Content-type: text/plain; charset=UTF-8");
    }
  header("Location: voirCourrier.php?id=$idCourrier&nbAffiche={$_POST['nbAffiche']}&type={$_POST['type']}");
}
