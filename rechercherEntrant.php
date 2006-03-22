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
<center><b>RECHERCHE COURRIER ENTRANT</b><br><br>
<form name = rechercheAvanceeForm method = POST action = rechercherEntrant.php>
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
<td>emmeteur</td>
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
$from ="    FROM courrier ";
$where =" WHERE courrier.validite = 0 and courrier.type=1";


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
$requete = $requetetmp;


$result = mysql_query( $requete ) or die ( mysql_error() ) ;
echo "<table align=center font-color ='white'>";
echo "<tr>";
echo "<td align=center>numero</td>";
echo "<td align=center>libelle</td>";
echo "<td align=center>date arrivee</td>";
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

echo "<td bgcolor = ".$couleur.">".$ligne['idCourrier']."</td><td bgcolor = ".$couleur.">".$ligne['libelle']."</td><td bgcolor = ".$couleur.">".$tmp."</td><td bgcolor=".$couleur."><a href=rechercherEntrantHistorique.php?idCourrier=".$ligne['idCourrier'].">historique</a></td></tr>";
}//fin while
echo "</table>";

echo "<br><a href = rechercherEntrant.php>nouvelle recherche</a>";
echo "<br><a href = index.php>index</a>";

echo "</center>";
}//fin du premier else
?> 
