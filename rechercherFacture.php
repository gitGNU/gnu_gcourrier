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

require("connexion.php");
session_start();

?>
<html>
<head><title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
</head>
<body>




<?php
if(!isset( $_POST["rechercher"] ) ){

?>
<div id =pageTGd><br>
<center><img src = images/banniere2.jpg></center><br><br>
<center><b>RECHERCHE FACTURE</b><br><br>
<?php
echo"<form method = POST action = rechercherFacture.php>";
?>
<table align = center>
<tr>
<td>numero</td>
<td><input type = text name = numero></input></td>
</tr>
<tr>
<td>refFacture</td>
<td><input type = text name = refFacture></input></td>
</tr>
<tr>
<td>date arrivee</td>
<td><input type = text name = dateArrivee value ="jj-mm-aaaa"></input></td>
</tr>
<td>date origine</td>
<td><input type = text name = dateOrigine value ="jj-mm-aaaa"></input></td>
</tr>
<tr>
<td>enregistrer entre</td>
<td><input type = text name = eDate1 value="jj-mm-aaaa"></input>
 et <input type = text name = eDate2 value="jj-mm-aaaa"></input></td>
</tr>


<tr>
<td>fournisseur</td>
<td><select name = fournisseur>
	<option value = "rien"></option>
		<?php
		$requete = "select * from destinataire order by nom ; ";
		$result = mysql_query($requete) or die( mysql_error() );
		while( $ligne = mysql_fetch_array( $result ) ){
		    echo "<option value = '".$ligne['id']."'>".$ligne['nom']." ".$ligne['prenom']."</option>";
		}
		?></select></td>
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
echo"<html>";
echo"<head><title>gCourrier</title>";
echo"<LINK HREF=styles3.css REL=stylesheet>";
echo"</head>";
echo"<body>"; 


echo"<center><img src = images/banniere2.jpg></center><br><br>";
echo"<center>";
echo "<div id = titre>RESULTAT DE LA RECHERCHE</div><br></b>";



$numero = $_POST['numero'];
$refFacture = $_POST['refFacture'];
$dateArrivee = $_POST['dateArrivee'];
$dateOrigine = $_POST['dateOrigine'];
$eDate1 = $_POST['eDate1'];
$eDate2 = $_POST['eDate2'];
$fournisseur = $_POST['fournisseur'];


$requetetmp = 	"SELECT	facture.id as idCourrier,
			facture.refFacture as refFacture,
			facture.dateFacture as dateArrivee,
			facture.dateFactureOrigine as dateOrigine,
			destinataire.nom as nomDest,
			destinataire.prenom as prenomDest
		";

$from ="    FROM facture,destinataire ";
$where =" WHERE facture.validite = 0 and facture.idServiceCreation=".$_SESSION['idService']."";


if(strcmp($numero,"")!=0){
	$requete.= " and facture.id = '".$numero."' ";

}

if(strcmp($refFacture,"")!=0){
	$requete.= " and facture.refFacture = '".$refFacture."' ";

}


if(strcmp($dateArrivee,"jj-mm-aaaa")!=0){
	$tmpdatearrivee= substr($dateArrivee, 6,4);
	$tmpdatearrivee.='-';
	$tmpdatearrivee.=substr($dateArrivee, 3,2);
	$tmpdatearrivee.='-';
	$tmpdatearrivee.=substr($dateArrivee, 0,2);
	$dateArrivee = $tmpdatearrivee;

	$requete.= " and facture.dateArrivee = '".$dateArrivee."' ";

}

if(strcmp($dateOrigine,"jj-mm-aaaa")!=0){
	$tmpdateorigine= substr($dateOrigine, 6,4);
	$tmpdateorigine.='-';
	$tmpdateorigine.=substr($dateOrigine, 3,2);
	$tmpdateorigine.='-';
	$tmpdateorigine.=substr($dateOrigine, 0,2);
	$dateOrigine = $tmpdateorigine;

	$requete.= " and facture.dateOrigine = '".$dateOrigine."' ";

}


if(strcmp($fournisseur,"rien")!=0){
	$requete.=" and facture.idFournisseur = destinataire.id and destinataire.id = ".$fournisseur." ";
}

else{
	$requete.=" and facture.idFournisseur = destinataire.id ";
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

$requete.=" and facture.dateArrivee >='".$eDate1."' and facture.dateArrivee<='".$eDate2."' ";
		
}


$requetetmp .= " ".$from." ".$where." ".$requete." ";
$requete = $requetetmp;


$result = mysql_query( $requete ) or die ( mysql_error() ) ;
echo "<table align=center font-color ='white'>";
echo "<tr>";
echo "<td align=center>numero</td>";
echo "<td aling=center>fournisseur</td>";
echo "<td align=center>refFacture</td>";
echo "<td align=center>date arrivee</td>";
echo "<td align=center>date origine</td>";
echo "<td align=center>historique</td>";
echo "</tr>";

$boul = 0;

while($ligne = mysql_fetch_array( $result ) ){

if($boul == 0){
		$couleur = lightblue;
		$boul = 1;
	}
	else{
		$couleur = white;
		$boul = 0;	
	}


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

echo "<td bgcolor = ".$couleur.">".$ligne['idCourrier']."</td>";
echo "<td bgcolor = ".$couleur.">".$ligne['nomDest']." ".$ligne['prenomDest']."</td>";
echo "<td bgcolor = ".$couleur.">".$ligne['refFacture']."</td>";
echo "<td bgcolor = ".$couleur.">".$tmp."</td>";
echo "<td bgcolor = ".$couleur.">".$tmp2."</td>";
echo "<td bgcolor=".$couleur."><a href=rechercherFactureHistorique.php?idCourrier=".$ligne['idCourrier'].">historique</a></td>";
}//fin while
echo "</table>";

echo "<br><a href = rechercherFacture.php>nouvelle recherche</a>";
echo "<br><a href = index.php>index</a>";

echo "</center>";
}//fin du premier else
?> 

