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
*/

require_once('init.php');

include('templates/header.php');

if (!isset($_GET["rechercher"])) {
?>
<div id =pageTGd><br>
<center><b>RECHERCHE FACTURE</b><br><br>
<form action='rechercherFacture.php'>
<table align = center>
<tr>
<td>Numéro</td>
<td><input type="text" name="numero"></input></td>
</tr>
<tr>
<td>Réf. Facture</td>
<td><input type="text" name="refFacture"></input></td>
</tr>

<tr>
  <td>Montant</td>
  <td colspan=>
    <select name="montant_op">
      <option value="<">&lt;</option>
      <option value="=">=</option>
      <option value=">">&gt;</option>
    </select>
    <input type="text" name="montant" /> €
  </td>
</td>

<tr>
<td>Observation</td>
<td><input type="text" name="observation"> (contient cette phrase)</td>
</tr>
<tr>
<td>Date arrivée mairie</td>
<td><input type="text" name="dateArrivee" value="jj-mm-aaaa" /></td>
</tr>
<td>Date origine fournisseur</td>
<td><input type="text" name="dateOrigine" value="jj-mm-aaaa" /></td>
</tr>
<tr>
<td>Enregistré entre</td>
<td><input type="text" name="eDate1" value="jj-mm-aaaa" />
 et <input type="text" name="eDate2" value="jj-mm-aaaa" /></td>
</tr>

<tr>
<td>Fournisseur</td>
<td><select name="fournisseur">
	<option value="rien">(tous)</option>
		<?php
		$requete = "SELECT * FROM destinataire ORDER BY nom";
		$result = mysql_query($requete) or die( mysql_error() );
		while( $ligne = mysql_fetch_array( $result ) ){
		    echo "<option value = '".$ligne['id']."'>".$ligne['nom']." ".$ligne['prenom']."</option>";
		}
		?></select></td>
</tr>

<tr>
<td>Service</td>
	<td><select name="serviceDest">
	<option value="rien">(tous)</option>
		<?php
			$requete = "SELECT * FROM service WHERE libelle <> 'admin' ORDER BY libelle";
			$result = mysql_query($requete) or die ( mysql_error() );
			while( $ligne = mysql_fetch_array( $result ) ){
				 echo "<option value = '".$ligne['id']."'>".$ligne['libelle']." ".$ligne['designation']."</option>";
			}
		?>
	</td>
</tr>

<td><label>Courrier retard</label></td>
<td><input type="checkbox" name="retard"/></td>
</tr>

</table>

<br><input type="submit" name="rechercher" value="Rechercher" />
</form>
</div>

<?php
} else {
//
// Results
//
echo"<center>";
echo "<div id = titre>RESULTAT DE LA RECHERCHE</div><br></b>";

$serviceDest = $_GET['serviceDest'];
$numero = $_GET['numero'];
$refFacture = $_GET['refFacture'];
$dateArrivee = $_GET['dateArrivee'];
$dateOrigine = $_GET['dateOrigine'];
$eDate1 = $_GET['eDate1'];
$eDate2 = $_GET['eDate2'];
$fournisseur = $_GET['fournisseur'];
$montant = $_GET['montant'];
$montant_op = $_GET['montant_op'];
$observation = $_GET['observation'];

$requetetmp = 	"SELECT	facture.id AS idCourrier,
			facture.montant AS montant,
			facture.refFacture AS refFacture,
			facture.dateFacture AS dateArrivee,
			facture.dateFactureOrigine AS dateOrigine,
			destinataire.nom AS nomDest,
			destinataire.prenom AS prenomDest,
			priorite.nbJours AS nbJours,
			facture.montant AS montant,
			facture.observation AS observation
		";

$from ="    FROM facture,destinataire,priorite ";
$where =" WHERE facture.validite = 0 and facture.idServiceCreation=".$_SESSION['idService']." and
	  priorite.id = facture.idPriorite ";

$requete = '';
if ($numero != "") {
  $requete.= " AND facture.id = '".$numero."' ";

}

if ($montant != '' and is_numeric($montant)) {
  if ($montant_op == '<')
    $requete.= " AND montant < $montant ";
  else if ($montant_op == '=')
    $requete.= " AND montant = $montant ";
  else if ($montant_op == '>')
    $requete.= " AND montant > $montant ";
}

if ($observation != '') {
  $observation = mysql_real_escape_string($observation);
  $requete .= " AND observation LIKE '%$observation%' ";
}



if($refFacture != '') {
	$requete.= " AND facture.refFacture = '".$refFacture."' ";

}


if(strcmp($dateArrivee,"jj-mm-aaaa")!=0){
	$tmpdatearrivee= substr($dateArrivee, 6,4);
	$tmpdatearrivee.='-';
	$tmpdatearrivee.=substr($dateArrivee, 3,2);
	$tmpdatearrivee.='-';
	$tmpdatearrivee.=substr($dateArrivee, 0,2);
	$dateArrivee = $tmpdatearrivee;

	$requete.= " AND facture.dateFacture = '".$dateArrivee."' ";

}

if(strcmp($dateOrigine,"jj-mm-aaaa")!=0){
	$tmpdateorigine= substr($dateOrigine, 6,4);
	$tmpdateorigine.='-';
	$tmpdateorigine.=substr($dateOrigine, 3,2);
	$tmpdateorigine.='-';
	$tmpdateorigine.=substr($dateOrigine, 0,2);
	$dateOrigine = $tmpdateorigine;

	$requete.= " AND facture.dateOrigine = '".$dateOrigine."' ";

}


if($serviceDest != "rien") {
  $requete .=" AND facture.id = estTransmisCopie.idFacture AND estTransmisCopie.idService = service.id AND service.id =".$serviceDest." ";
  $from.=" ,service,estTransmisCopie ";
}

if ($fournisseur != "rien") {
  $requete .= " AND facture.idFournisseur = destinataire.id AND destinataire.id = {$fournisseur}";
} else {
  $requete .= " AND facture.idFournisseur = destinataire.id ";
}





if(strcmp($eDate1,"jj-mm-aaaa")!=0 && strcmp($eDate2,"jj-mm-aaaa")!=0){
$tmpdate= substr($eDate1, 6,4);
$tmpdate.='-';
$tmpdate.=substr($eDate1, 3,2);
$tmpdate.='-';
$tmpdate.=substr($eDate1, 0,2);
$eDate1 = $tmpdate;

$tmpdate= substr($eDate2, 6,4);
$tmpdate.='-';
$tmpdate.=substr($eDate2, 3,2);
$tmpdate.='-';
$tmpdate.=substr($eDate2, 0,2);
$eDate2 = $tmpdate;

$requete.=" AND facture.dateFacture >='".$eDate1."' AND facture.dateFacture<='".$eDate2."' ";
		
}


$requetetmp .= " ".$from." ".$where." ".$requete." ";
$requete = $requetetmp;

$result = mysql_query( $requete ) or die ( mysql_error() ) ;
echo "<table align=center font-color ='white' class='resultats'>";
echo "<tr>";
echo "<td align=center>numero</td>";
echo "<td aling=center>fournisseur</td>";
echo "<td align=center>refFacture</td>";
echo "<td align=center>montant</td>";
echo "<td align=center>date mairie</td>";
echo "<td align=center>date facture</td>";
echo "<td align=center>observation</td>";
echo "<td align=center>historique</td>";
echo "<td align=center>archiver</td>";
echo "<td align=center>Jours restant</td>";
echo "</tr>";

$boul = 0;

while ($ligne = mysql_fetch_array($result)) {
  if ($boul == 0) {
    $couleur = 'lightblue';
    $boul = 1;
  } else {
    $couleur = 'white';
    $boul = 0;	
  }

  # remaining days
  $tmpDateArrivee = $ligne['dateArrivee'];
  $jourArrivee = substr($tmpDateArrivee,8,2);
  $moisArrivee = substr($tmpDateArrivee,5,2);
  $anneeArrivee = substr($tmpDateArrivee,0,4);
  
  $nbJours = $ligne['nbJours'];
  
  $timestampActuel = time();
  $timestampArrivee= mktime(0, 0, 0, $moisArrivee, $jourArrivee, $anneeArrivee);
  $urgence = ($timestampActuel - $timestampArrivee ) / 86400;
  
  $nbJoursRestant = $nbJours - (int)$urgence;


  $display_current = true;
  if (isset($_GET['retard']) && ($nbJoursRestant > 5))
      $display_current = false;
  
  if ($display_current)
    {
      echo "<tr>";	
      $id = $ligne['idCourrier'];
      
      $tmp= substr($ligne['dateArrivee'], 8,2);
      $tmp.='-';
      $tmp.=substr($ligne['dateArrivee'], 5,2);
      $tmp.='-';
      $tmp.=substr($ligne['dateArrivee'], 0,4);
      
      $tmp2= substr($ligne['dateOrigine'], 8,2);
      $tmp2.='-';
      $tmp2.=substr($ligne['dateOrigine'], 5,2);
      $tmp2.='-';
      $tmp2.=substr($ligne['dateOrigine'], 0,4);
      
      if ($ligne['observation'] == '')
	$obs = "modifier";
      else
	$obs = $ligne['observation'];
      
      echo "<td bgcolor='$couleur'>".$ligne['idCourrier']."</td>";
      echo "<td bgcolor='$couleur'>".$ligne['nomDest']." ".$ligne['prenomDest']."</td>";
      echo "<td bgcolor='$couleur'>".$ligne['refFacture']."</td>";
      echo "<td bgcolor='$couleur'>".$ligne['montant']."</td>";
      echo "<td bgcolor='$couleur'>".$tmp."</td>";
      echo "<td bgcolor='$couleur'>".$tmp2."</td>";
      echo "<td bgcolor='$couleur'><a href='modifObservationFacture.php?idCourrier=$id'>{$obs}</a></td>";
      echo "<td bgcolor='$couleur'><a href='rechercherFactureHistorique.php?idCourrier=$id'>historique</a></td>";
      
      echo "<td bgcolor='$couleur'><a href='validerFactureRecherche.php?idCourrier=$id'>archiver</a></td>";
      echo "<td bgcolor='$couleur'>$nbJoursRestant</td>";
      
      echo "</tr>";
    }
} // while
echo "</table>";

echo "<br><a href='rechercherFacture.php'>Nouvelle recherche</a>";

echo "</center>";
} // Results

include('templates/footer.php');
