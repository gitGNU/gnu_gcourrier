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
<center><b>RECHERCHE COURRIER
<?php
if($_GET['type']==1){
	echo " ENTRANT";
	$emetteur = "Émetteur";
}
else{
	echo " DEPART";
	$emetteur = "Destinataire";
}
?>

</b><br><br>
<?php
echo "<form name = rechercheAvanceeForm action='rechercher.php'>";
echo "<input type='hidden' name='type' value='{$_GET['type']}' />";
?>
<table>
<tr>
<td>Libellé</td>
<td><input type="text" name="libelle" /> (contient cette phrase)</td>
</tr>
<tr>
<td>Numéro</td>
<td><input type="text" name="numero" /></td>
</tr>

<tr>
<td>Date arrivée</td>
<td><input type="text" name="date" value ="jj-mm-aaaa" /></td>
</tr>


<tr>
<td>Arrivé entre</td>
<td><input type = text name = eDate1 value="jj-mm-aaaa" />
 et <input type = text name = eDate2 value="jj-mm-aaaa" /></td>
</tr>


<tr>
<td>
<?php
echo $emetteur;
?>
</td>


<td><select name = ext>
	<option value = "rien">(tous)</option>
		<?php
		$requete = "SELECT * FROM destinataire ORDER BY nom";
		$result = mysql_query($requete) or die( mysql_error() );
		while ($ligne = mysql_fetch_array($result)) {
		    echo "<option value = '".$ligne['id']."'>".$ligne['nom']." ".$ligne['prenom']."</option>";
		}
		?></select></td>
</tr>
<tr>
<td><label>Déjà transmis</br> par le service</label></td>
<td><input type = "checkbox" name ="gTransmis"/></td>
</tr>
<tr>
<td><label>Courrier retard</label></td>
<td><input type = "checkbox" name ="retard"/></td>
</tr>
</table>

<br><input type = submit name = rechercher value = rechercher>
</form>
<br><a href = index.php>index</a><br><br></div>
</center>
</body>
</html>
<?php
}
else{

if(isset($_GET['gTransmis'])){
$reqTmpTransmission = " and courrier.id = estTransmis.idCourrier
		       and estTransmis.idService =".$_SESSION['idService']." ";
$fromTransmission = ",estTransmis,service";
}
else{
$reqTmpTransmission =" ";
$fromTransmission = "";
}

if(isset($_GET['retard'])){
$fromRetard = ",priorite ";
$whereRetard = " and courrier.idPriorite = priorite.id";
}
else{
$whereRetard="";
$fromRetard = ""; 
}

echo "<div id = titre>RESULTAT DE LA RECHERCHE</div><br></b>";


$libelle = $_GET['libelle'];
$numero = $_GET['numero'];

$date = $_GET['date'];

$dateResult = $date;

$eDate1 = $_GET['eDate1'];
$eDate2 = $_GET['eDate2'];
$ext = $_GET['ext'];


$requetetmp = "SELECT courrier.id as idCourrier,
		   courrier.libelle as libelle,
		   courrier.dateArrivee as dateArrivee,
		   courrier.dateArchivage as dateArchivage ";
$from =" FROM courrier ".$fromTransmission.$fromRetard;
$where =" WHERE courrier.validite = 0 and courrier.type=".$_GET['type']."";
$where .= $reqTmpTransmission.$whereRetard;
$requete = '';

if ($libelle != "") {
  $libelle = mysql_real_escape_string($libelle);
  $requete .= " AND courrier.libelle LIKE '%$libelle%' ";
}

if(strcmp($numero,"")!=0){
	$requete.= " and courrier.id = '".$numero."' ";

}


if(strcmp($date,"jj-mm-aaaa")!=0){
	$tmpdate= substr($date, 6,4);
	$tmpdate.='-';
	$tmpdate.=substr($date, 3,2);
	$tmpdate.='-';
	$tmpdate.=substr($date, 0,2);
	$date = $tmpdate;

	$requete.= " and courrier.dateArrivee = '".$date."' ";

}


if(strcmp($ext,"rien")!=0){
	$from .= " ,destinataire";
	$requete.=" and courrier.idDestinataire = destinataire.id and destinataire.id = ".$ext." ";

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

$requete.=" and courrier.dateArrivee >='".$eDate1."' and courrier.dateArrivee<='".$eDate2."' ";
		
}


$requetetmp .= " ".$from." ".$where." ".$requete." ";
$requetetmp.=$requete."group by courrier.id";
//$requete.=$requetetmp." group by courrier.id;";
$requete = $requetetmp;
//echo $requete."<br><br>";
$result = mysql_query( $requete ) or die ( mysql_error() ) ;
echo "<table align=center font-color ='white'>";
echo "<tr>";
echo "<td align=center>numero</td>";
echo "<td align=center>libelle</td>";
echo "<td align=center>date arrivee</td>";
echo "<td align=center>historique</td>";
echo "<td align=center>transmettre</td>";
echo "</tr>";

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
if(isset($_GET['retard'])){
//test pour urgence du courrier
		$dateActuel = date("Y-m-d");
		$jourActuel = substr($dateActuel,8,2);
		$moisActuel = substr($dateActuel,5,2);
		$anneeActuel= substr($dateActuel,0,4);

		$tmpDateArrivee = $ligne['dateArrivee'];
		$jourArrivee =substr($tmpDateArrivee,8,2);
		$moisArrivee =substr($tmpDateArrivee,5,2);
		$anneeArrivee =substr($tmpDateArrivee,0,4);
		

		$nbJours = $ligne['nbJours'];
		
		$timestampActuel = mktime(0,0,0,$moisActuel,$jourActuel,$anneeActuel);
		$timestampArrivee= mktime(0,0,0,$moisArrivee,$jourArrivee,$anneeArrivee);
		$urgence = ($timestampActuel - $timestampArrivee ) / 86400;

		$nbJoursRestant = $nbJours - $urgence;

		if($urgence <= $nbJours){
			echo "<tr>";	
			$tmp= substr($ligne['dateArrivee'], 8,2);
			$tmp.='-';
			$tmp.=substr($ligne['dateArrivee'], 5,2);
			$tmp.='-';
			$tmp.=substr($ligne['dateArrivee'], 0,4);

			echo "<td bgcolor = ".$couleur.">".$ligne['idCourrier']."</td><td bgcolor = ".$couleur.">".$ligne['libelle']."</td><td bgcolor = ".$couleur.">".$tmp."</td><td bgcolor=".$couleur."><a href=rechercherHistorique.php?idCourrier=".$ligne['idCourrier']."&type=".$_GET['type'].">historique</a></td>
<td bgcolor = ".$couleur."><a href=transmettreRecherche.php?idCourrier=".$ligne['idCourrier']."&type=".$_GET['type'].">transmettre</a></td></tr>";
		}//fin if urgence
}//fin if retard

else{

	echo "<tr>";	
	$tmp= substr($ligne['dateArrivee'], 8,2);
	$tmp.='-';
	$tmp.=substr($ligne['dateArrivee'], 5,2);
	$tmp.='-';
	$tmp.=substr($ligne['dateArrivee'], 0,4);

	echo "<td bgcolor = ".$couleur.">".$ligne['idCourrier']."</td><td bgcolor = ".$couleur.">".$ligne['libelle']."</td><td bgcolor = ".$couleur.">".$tmp."</td><td bgcolor=".$couleur."><a href=rechercherHistorique.php?idCourrier=".$ligne['idCourrier']."&type=".$_GET['type'].">historique</a></td>
<td bgcolor = ".$couleur."><a href=transmettreRecherche.php?idCourrier=".$ligne['idCourrier']."&type=".$_GET['type'].">transmettre</a></td></tr>";
	
	}//fin else
}//fin while

echo "</table>";

echo "<br><a href = rechercher.php?type=".$_GET['type'].">nouvelle recherche</a>";
echo "<br><a href = index.php>index</a>";

echo "</center>";

}//fin du premier else
?> 
