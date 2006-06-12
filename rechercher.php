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
<center><b>RECHERCHE COURRIER
<?php
if($_GET['type']==1){
	echo " ENTRANT";
	$emetteur = "emetteur";
}
else{
	echo " DEPART";
	$emetteur = "destinataire";
}
?>

</b><br><br>
<?php
echo"<form name = rechercheAvanceeForm method = POST action = rechercher.php?type=".$_GET['type'].">";
?>
<table align = center>
<tr>
<td>libelle</td>
<td><input type = text name = libelle></input></td>
</tr>
<tr>
<td>numero</td>
<td><input type = text name = numero></input></td>
</tr>

<tr>
<td>date arrivee</td>
<td><input type = text name = date value ="jj-mm-aaaa"></input></td>
</tr>


<tr>
<td>arrive entre</td>
<td><input type = text name = eDate1 value="jj-mm-aaaa"></input>
 et <input type = text name = eDate2 value="jj-mm-aaaa"></input></td>
</tr>


<tr>
<td>
<?php
echo $emetteur;
?>
</td>


<td><select name = ext>
	<option value = "rien"></option>
		<?php
		$requete = "select * from destinataire order by nom ; ";
		$result = mysql_query($requete) or die( mysql_error() );
		while( $ligne = mysql_fetch_array( $result ) ){
		    echo "<option value = '".$ligne['id']."'>".$ligne['nom']." ".$ligne['prenom']."</option>";
		}
		?></select></td>
</tr>
<tr>
<td><label>deja transmis</br> par le service</label></td>
<td><input type = "checkbox" name ="gTransmis"/></td>
</tr>
<tr>
<td><label>courrier retard</label></td>
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

if(isset($_POST['gTransmis'])){
$reqTmpTransmission = " and courrier.id = estTransmis.idCourrier
		       and estTransmis.idService =".$_SESSION['idService']." ";
$fromTransmission = ",estTransmis,service";
}
else{
$reqTmpTransmission =" ";
$fromTransmission = "";
}

if(isset($_POST['retard'])){
$fromRetard = ",priorite ";
$whereRetard = " and courrier.idPriorite = priorite.id";
}
else{
$whereRetard="";
$fromRetard = ""; 
}

echo"<html>";
echo"<head><title>gCourrier</title>";
echo"<LINK HREF=styles3.css REL=stylesheet>";
echo"</head>";
echo"<body>"; 


echo"<center><img src = images/banniere2.jpg></center><br><br>";
echo"<center>";
echo "<div id = titre>RESULTAT DE LA RECHERCHE</div><br></b>";


$libelle = $_POST['libelle'];
$numero = $_POST['numero'];

$date = $_POST['date'];

$dateResult = $date;

$eDate1 = $_POST['eDate1'];
$eDate2 = $_POST['eDate2'];
$ext = $_POST['ext'];


$requetetmp = "SELECT courrier.id as idCourrier,
		   courrier.libelle as libelle,
		   courrier.dateArrivee as dateArrivee,
		   courrier.dateArchivage as dateArchivage ";
$from =" FROM courrier ".$fromTransmission.$fromRetard;
$where =" WHERE courrier.validite = 0 and courrier.type=".$_GET['type']."";
$where .= $reqTmpTransmission.$whereRetard;
if(strcmp($libelle,"")!=0){
	$requete.= " and courrier.libelle = '".$libelle."' ";

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
	$couleur = lightblue;
	$boul = 1;	
}
else{
	$couleur = white;
	$boul = 0;	
}
if(isset($_POST['retard'])){
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
