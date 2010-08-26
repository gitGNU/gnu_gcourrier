<?php
/*
GCourrier
Copyright (C) 2005, 2006, 2008, 2010  Cliss XXI

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

//
// Get information
//
$idCourrier = intval($_REQUEST['idCourrier']);
$result = db_execute('
SELECT service.id AS old_service_id, service.libelle AS old_service_label
FROM courrier
  JOIN estTransmis T1 ON courrier.id = T1.idCourrier
  JOIN estTransmis T2 ON T1.idCourrier = T2.idCourrier
  JOIN service ON T1.idService = service.id
WHERE courrier.id = ?
GROUP BY T1.id HAVING T1.id >= MAX(T2.id)',
		     array($idCourrier));
$row = mysql_fetch_array($result);
$old_service_id = $row['old_service_id'];
$old_service_label = $row['old_service_label'];

if ($old_service_id == $_POST['service'])
  {
    exit("Choisissez un service différent.");
  }

if (!isset($_POST["enregistrer"])) {
  include ('templates/header.php');
?>
<form method="post" action="?">
<input type="hidden" name="next" value="<?php echo $_GET['next']; ?>" />
<table align="center">
  <tr>
    <td>Service</td>
    <td><select name="service">
    <?php
    $requete = "SELECT * FROM service ORDER BY libelle;";
    $result = mysql_query($requete) or die(mysql_error());
    while ($ligne = mysql_fetch_array($result)) {
      if ($ligne['libelle'] != "ADMIN")
	{
	  $selected = "";
	  if ($ligne['id'] == $old_service_id)
	    $selected = "selected='selected'";
	  echo "<option value='{$ligne['id']}' $selected>{$ligne['libelle']} {$ligne['designation']}</option>";
	}
    }
    ?></select></td></tr>
    <?php
    $idCourrier = $_GET["idCourrier"];
    echo "<input type='hidden' name='idCourrier' value='{$idCourrier}' />";
    ?>
  </tr>
  <tr>
    <td>Observation</td>
    <td><textarea name="observation" cols="30" rows="4"><?php
      $result = db_execute("SELECT observation FROM courrier WHERE id = ?", array($idCourrier));
      $ligne = mysql_fetch_array($result);
      echo $ligne['observation'];
    ?></textarea>
    </td>
  </tr>
</table>
<input type="submit" name="enregistrer" value="Transmettre" />
</form>
<?php
  echo "<p><a href='{$_REQUEST['next']}'>Retour</a></p>";
  include ('templates/footer.php');

} else {

  //
  // DB update
  //

  $new_service = $_POST['service'];
  $danger = ($old_service_id != $_SESSION['idService']);
  $result = db_autoexecute('estTransmis',
    array('dateTransmission' => date("Y-m-d"),
	  'idCourrier' => $idCourrier,
	  'idService' => $new_service,
	  'danger' => $danger),
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
  if (false) { // désactivé pour le moment
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
  }
  header("Location: {$_POST['next']}");
  exit();
}
