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

include('templates/header.php');

if(!isset( $_GET["rechercher"] ) ){

?>
<center><b>Recherche facture archivée</b><br><br>
<?php
echo"<form action=archiveFacture.php>";
?>
<table>
<tr>
<td>Numéro</td>
<td><input type="text" name="numero" /></td>
</tr>
<tr>
<td>Réf. facture</td>
<td><input type="text" name="refFacture" /></td>
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
<td>Date arrivée</td>
<td><input type="text" name="dateArrivee" value="jj-mm-aaaa" /></td>
</tr>
<td>Date fournisseur</td>
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
		$requete = "select * from destinataire order by nom ; ";
		$result = mysql_query($requete) or die( mysql_error() );
		while( $ligne = mysql_fetch_array( $result ) ){
		    echo "<option value = '".$ligne['id']."'>".$ligne['nom']." ".$ligne['prenom']."</option>";
		}
		?></select></td>
</tr>


</table>

<br><input type="submit" name="rechercher" value="Rechercher">
</form>

<?php
}
else{
echo "<div id = titre>RESULTAT DE LA RECHERCHE</div><br></b>";



$numero = $_GET['numero'];
$refFacture = $_GET['refFacture'];
$dateArrivee = $_GET['dateArrivee'];
$dateOrigine = $_GET['dateOrigine'];
$eDate1 = $_GET['eDate1'];
$eDate2 = $_GET['eDate2'];
$fournisseur = $_GET['fournisseur'];
$montant = $_GET['montant'];
$montant_op = $_GET['montant_op'];


$requetetmp = 	"SELECT	facture.refuse as refuse,
			facture.observation as observation,
			facture.id as idCourrier,
			facture.refFacture as refFacture,
			facture.dateFacture as dateArrivee,
			facture.dateFactureOrigine as dateOrigine,
			destinataire.nom as nomDest,
			destinataire.prenom as prenomDest,
                        montant
		";

$from ="    FROM facture,destinataire ";
$where =" WHERE facture.validite = 1 and facture.idServiceCreation=".$_SESSION['idService']."";

$requete = '';
if(strcmp($numero,"")!=0){
	$requete.= " and facture.id = '".$numero."' ";

}


if ($montant != '' and ctype_digit($montant)) {
  if ($montant_op == '<')
    $requete.= " and montant < $montant ";
  else if ($montant_op == '=')
    $requete.= " and montant = $montant ";
  else if ($montant_op == '>')
    $requete.= " and montant > $montant ";
}


if (strcmp($refFacture,"") != 0) {
	$requete.= " and facture.refFacture = '".$refFacture."' ";
}

if (strcmp($dateArrivee,"jj-mm-aaaa") != 0) {
	$tmpdatearrivee= substr($dateArrivee, 6,4);
	$tmpdatearrivee.='-';
	$tmpdatearrivee.=substr($dateArrivee, 3,2);
	$tmpdatearrivee.='-';
	$tmpdatearrivee.=substr($dateArrivee, 0,2);
	$dateArrivee = $tmpdatearrivee;

	$requete.= " and facture.dateFacture = '".$dateArrivee."' ";

}

if (strcmp($dateOrigine,"jj-mm-aaaa") != 0) {
	$tmpdateorigine= substr($dateOrigine, 6,4);
	$tmpdateorigine.='-';
	$tmpdateorigine.=substr($dateOrigine, 3,2);
	$tmpdateorigine.='-';
	$tmpdateorigine.=substr($dateOrigine, 0,2);
	$dateOrigine = $tmpdateorigine;

	$requete.= " and facture.dateOrigine = '".$dateOrigine."' ";

}


if (strcmp($fournisseur, 'rien') != 0) {
//	$from .= " ,destinataire";
	$requete.=" and facture.idFournisseur = destinataire.id and destinataire.id = ".$fournisseur." ";
} else {
	$requete.=" and facture.idFournisseur = destinataire.id ";
}




if (strcmp($eDate1,"jj-mm-aaaa") != 0 && strcmp($eDate2, "jj-mm-aaaa") != 0) {
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

$requete.=" and facture.dateFacture >='".$eDate1."' and facture.dateFacture<='".$eDate2."' ";
		
}


$requetetmp .= " ".$from." ".$where." ".$requete." ";
$requete = $requetetmp;

$result = mysql_query( $requete ) or die ( mysql_error() ) ;
echo '<table align=center font-color="white" class="resultats">';
echo '<tr>';
echo '<td align="center">numero</td>';
echo '<td align="center">fournisseur</td>';
echo '<td align="center">refFacture</td>';
echo '<td align="center">date arrivee</td>';
echo '<td align="center">date origine</td>';
echo '<td align="center">montant</td>';
echo '<td align="center">observation</td>';
echo '<td align="center">historique</td>';
echo '<td align="center">refuser</td>';
echo '</tr>';

$boul = 0;

while($ligne = mysql_fetch_array( $result ) ){

if($boul == 0){
		$couleur = 'lightblue';
		$boul = 1;
	}
	else{
		$couleur = 'white';
		$boul = 0;	
	}

if($ligne["refuse"] == 1)
	$couleurTmp = 'red';
else
	$couleurTmp = $couleur;


echo "<tr>";	

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

if(strcmp($ligne['observation'],"")==0){
$obs = "modifier";
}
else
$obs = $ligne['observation'];

echo "<td bgcolor = ".$couleurTmp.">".$ligne['idCourrier']."</td>";
echo "<td bgcolor = ".$couleurTmp.">".$ligne['nomDest']." ".$ligne['prenomDest']."</td>";
echo "<td bgcolor = ".$couleurTmp.">".$ligne['refFacture']."</td>";
echo "<td bgcolor = ".$couleurTmp.">".$tmp."</td>";
echo "<td bgcolor = ".$couleurTmp.">".$tmp2."</td>";
echo "<td bgcolor = ".$couleurTmp.">".$ligne['montant']."</td>";
echo "<td bgcolor = ".$couleurTmp."><a href=modifObservationFacture.php?idCourrier=".$ligne['idCourrier']."  style=\"text-decoration :none;font-weight:normal\">".$obs."</a></td>";
echo "<td bgcolor=".$couleurTmp."><a href=rechercherFactureHistorique.php?idCourrier=".$ligne['idCourrier'].">historique</a></td>";


echo "<td bgcolor = ".$couleurTmp."><a href=refuse.php?idCourrier=".$ligne['idCourrier'].">refuse</a></td></tr>";

}//fin while
echo "</table>";

echo "<br><a href='archiveFacture.php'>Nouvelle Recherche</a>";

echo "</center>";
}//fin du premier else

include('templates/footer.php');
