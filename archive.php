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

if (!isset($_GET["rechercher"])) {
?>
<center><b>ARCHIVE COURRIER
<?php
if($_GET['type'] == 1) {
	echo " ENTRANT";
	$emetteur = "Émetteur";
} else {
	echo " DEPART";
	$emetteur = "Destinataire";
}

?>
</b><br><br>
<?
echo "<form name = rechercheAvanceeForm action='archive.php'>";
echo "<input type='hidden' name='type' value='{$_GET['type']}' />";
?>
<table align = center>
<tr>
<td>Libellé</td>
<td><input type="text" name="libelle" /> (contient cette phrase)</td>
</tr>
<tr>
<td>Numéro</td>
<td><input type="text" name="numero" /></td>
</tr>

<tr>
<td>En date du</td>
<td><input type="text" name="date" value="jj-mm-aaaa" /></td>
</tr>


<tr>
<td>Date entre</td>
<td><input type="text" name="eDate1" value="jj-mm-aaaa" />
 et <input type="text" name="eDate2" value="jj-mm-aaaa" /></td>
</tr>


<tr>
<td><?php echo $emetteur; ?></td>
<td><select name="ext">
	<option value="rien">(tous)</option>
		<?php
		$requete = "SELECT * FROM destinataire ORDER BY nom";
		$result = mysql_query($requete) or die( mysql_error() );
		while( $ligne = mysql_fetch_array( $result ) ){
		    echo "<option value = '".$ligne['id']."'>".$ligne['nom']." ".$ligne['prenom']."</option>";
		}
		?></select></td>
</tr>


</table>

<br><input type="submit" name="rechercher" value="Rechercher" />
</form>
<?php
}
else{
echo"<center>";
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
		   destinataire.nom as nomDestinataire,
		   destinataire.prenom as prenomDestinataire,
		   courrier.dateArrivee as dateArrivee,
		   courrier.dateArchivage as dateArchivage,
		   courrier.url as url ";
$from ="    FROM courrier, destinataire ";
$where =" WHERE courrier.validite = 1 and courrier.type=".$_GET['type']
  . " AND courrier.idDestinataire = destinataire.id";

if ($libelle != '') {
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


if ($ext != "rien") {
  $requete.=" AND destinataire.id = ".$ext." ";
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


$result = mysql_query($requete) or die(mysql_error());
echo "<table align=center font-color ='white'>";
echo "<tr>";
echo "<td align=center>Numéro</td>";
echo "<td align=center>Libellé</td>";
echo "<td align=center>";
if($_GET['type'] == 1) {
  echo "Émetteur";
} else {
  echo "Destinataire";
}
echo "</td>";
echo "<td align=center>Date</td>";
echo "<td align=center>Historique</td>";
echo "<td align=center>Fichier</td>";
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

$destinataire = $ligne['nomDestinataire']." ".$ligne['prenomDestinataire'];

echo "<td bgcolor = ".$couleur.">".$ligne['idCourrier']."</td>";
echo "<td bgcolor = ".$couleur.">".$ligne['libelle']."</td>";
echo "<td bgcolor = ".$couleur.">$destinataire</td>";
echo "<td bgcolor = ".$couleur.">".$tmp."</td>";
echo "<td bgcolor=".$couleur."><a href=archiveHistorique.php?idCourrier=".$ligne['idCourrier']."&type=".$_GET['type'].">historique</a></td>";

echo "<td style='text-align:center' bgcolor='$couleur'>";
if ($ligne['url'] != "")
  echo "<a href='file_view.php/".basename($ligne['url'])."?object=courrier&object_id={$ligne['idCourrier']}'><img src='images/download.gif' style='border: 0'></a>";
echo "</td>";

echo "</tr>";
}//fin while
echo "</table>";

echo "<br><a href = archive.php?type=".$_GET['type'].">nouvelle recherche</a>";
echo "</center>";
}//fin du premier else
include('templates/footer.php');
