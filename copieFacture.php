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

?>
<html>
<head><title>gCourrier</title></head>
<link rel="stylesheet" href=styles3.css type="text/css">

<body>

	<center>
		<img src= images/banniere2.jpg></img>
	</center>
		<br>


<?php

if(!isset( $_GET['id'] )){
  $re = "SELECT max(id) AS id FROM facture";
  $res = mysql_query( $re ) or die (mysql_error() );
  while($ligne = mysql_fetch_array( $res ) ){
    $id = $ligne['id']; 
  }
  
  $idTmp = $id;
} else {
  $idTmp = $_GET['id'];
}


echo"<center><div id= titre><a href=voirFacture.php>Factures</a>  / Copies de Factures<br/><br/><i style=\"font-size:10px;font-weight:normal\">Note: Ceci est les factures de votre service uniquement</i><br/><br/></div></center>";


if(strcmp($_SESSION['login'], 'admin') == 0) {
$requeteFacture = "select facture.id as idFacture,
  			  refFacture as refFacture,
			  montant as montant,
			  dateFacture as dateFacture,
			  dateFactureOrigine as dateFactureOrigine,
			  observation as observation,			  
			  destinataire.nom as nomFournisseur,
			  destinataire.prenom as prenomFournisseur,
			  priorite.nbJours as nbJours	
 		   from facture,destinataire,priorite
		   where facture.idFournisseur = destinataire.id
		   and validite=0 
		   and facture.idPriorite = priorite.id
		   order by facture.id DESC LIMIT 5;";


} else {
$requeteFacture = "select facture.id as idFacture,
  			  facture.refFacture as refFacture,
			  facture.dateFacture as dateFacture,
			  facture.dateFactureOrigine as dateFactureOrigine,
			  facture.observation as observation,			  
			  facture.montant as montant,
			  estTransmisCopie.dateTransmission as dateTransmission,
			  destinataire.nom as nomFournisseur,
			  destinataire.prenom as prenomFournisseur
 		    from facture,destinataire,estTransmisCopie
		    where facture.id<=".$idTmp." 
             		   and facture.validite = 0
			   and facture.idFournisseur = destinataire.id
			   and facture.id = estTransmisCopie.idFacture
			   and estTransmisCopie.idService = ".$_SESSION['idService']."
		           order by facture.id DESC
			   LIMIT 5;";
}



$resultatFacture = mysql_query($requeteFacture) or die("erreur facture ".mysql_error());

echo "<table align=center font-color ='white'>";
	echo "<tr>";
	echo "<td align=center>numero </td>";
	echo "<td align=center>fournisseur</td>";
	echo "<td align=center>refFacture</td>";
	echo "<td align=center>montant</td>";
	echo "<td align=center>dateMairie</td>";
	echo "<td align=center>dateFacture</td>";
	echo "<td align=center>observation</td>";
	echo "<td align=center>date reception</td>";
	echo"</tr>";


$boul = 0;
while($ligne = mysql_fetch_array($resultatFacture)){
  $idTmp = $ligne['idFacture'];
  if($boul == 0){
    $couleur = lightblue;
    $boul = 1;
  }
  else{
    $couleur = white;
    $boul = 0;	
  }
  echo "<tr>";
  
  //	echo "nbJours:".$ligne['nbJours'];
  
  $transmission = $ligne['dateTransmission'];
  $idCourrier = $ligne['idFacture'];
  $nomDestinataire = $ligne['nomFournisseur']." ".$ligne['prenomFournisseur'];
  $refFacture = $ligne['refFacture'];
  $montant = $ligne['montant'];
  $tmpdateArrivee = $ligne['dateFacture']; 
  $dateArrivee=substr($tmpdateArrivee,8,2)."-".substr($tmpdateArrivee,5,2)."-".substr($tmpdateArrivee,0,4);
  $tmpdateFacture = $ligne['dateFactureOrigine'];
  $dateFacture=substr($tmpdateFacture,8,2)."-".substr($tmpdateFacture,5,2)."-".substr($tmpdateFacture,0,4);
  $observation = $ligne['observation'];
  
  $tmpMontant = $montant;
  $tmpMontant.="00";
  $tmpMontant2 = $montant * 100;
  
  if(strcmp($tmpMontant,$tmpMontant2) == 0){
    $montant.=",00";
  }
	
  
  echo "<td bgcolor=".$couleur.">".$idCourrier."</td>";
  echo "<td bgcolor=".$couleur.">".$nomDestinataire."</td>";
  echo "<td bgcolor=".$couleur." style=\"text-align:center\">".$refFacture."</td>";
  echo "<td bgcolor=".$couleur." style=\"text-align:right\">".$montant."</td>";
  echo "<td bgcolor=".$couleur.">".$dateArrivee."</td>";
  echo "<td bgcolor=".$couleur.">".$dateFacture."</td>";
  echo "<td bgcolor=".$couleur.">".$observation."</td>";
  echo "<td bgcolor=".$couleur.">".$transmission."</td>";
}

echo"</table>";
if(mysql_num_rows($resultatFacture) == 5) {
  echo "<center><a href='copieFacture.php?id=".$idTmp."'>page suivante</a></center>";
}
     
?>	


<center><br>
<a href = index.php>index</a>
</center>
<br><br>
</div>

</body>

</html>
