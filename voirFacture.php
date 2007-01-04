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

$requeteInit = "Select id from facture Limit 1;";
$result = mysql_query($requeteInit) or die(mysql_error());
if(mysql_num_rows($result) == 0){
echo "<center><br>aucune facture pour le moment<br><br>";
echo "<a href = index.php>index</a></center>";
exit();
}


if(!isset( $_GET['order'] )){
$order = "facture.id";
}
else{
$order = $_GET['order'];
}

if(!isset( $_GET['id'] )){

$re = "select max(id) as id from facture;";
$res = mysql_query( $re ) or die (mysql_error() );
	while($ligne = mysql_fetch_array( $res ) ){
		$id = $ligne['id']; 
	}
$idTmp = $id;
}

else{
	$idTmp = $_GET['id'];
}


if(!isset( $_GET['nbAffiche'] )){
$requete = "select * from utilisateur where login = '".$_SESSION['login']."';";
$result = mysql_query($requete) or die(mysql_error());
while($ligne = mysql_fetch_array($result)){
$nbAffiche = $ligne['preferenceNbCourrier'];

}

}

else{
        $nbAffiche = $_GET['nbAffiche'];
}
?>
<form method = POST action=voirFactureAffiche.php>
<table align=center style="border:1px dotted black;"><tr><td>
<label>nombre de facture a afficher : </label>

<?php
//echo"<input type = hidden name=type value=".$_GET['type']." size=2></input>";
echo"<input type = text name=affiche value=".$nbAffiche." size=2></input>";
echo"<input type = hidden name=idTmp value=".$idTmp."></input>";
?>

<input type=submit name=ok value=ok></input>
</td></tr>
</table>
</form>

<form method = POST action=rechercheRapideFacture.php>
<table align=center style="border:1px dotted black;"><tr><td>
<label>rechercher la facture numero : </label>
<input type=text name=numero value=1 size=2></input>
<input type=submit name=ok value=ok></input>
<br><a href=rechercherFacture.php><font size=1px><center>rechercheAvancee</center></font></a>
</tr></td></table></form>

<?php



echo"<center><div id= titre>Factures  / <a href=copieFacture.php >Copies de Factures</a><br/><br/><i style=\"font-size:10px;font-weight:normal\">Note: Ceci est les factures de votre service uniquement</i><br/><br/></div></center>";


if(strcmp($_SESSION['login'] , 'admin') == 0){
$requeteFacture = "select facture.id as idFacture,
			  facture.histo as histo,
  			  refFacture as refFacture,
			  refuse as refuse,
			  montant as montant,
			  dateFacture as dateFacture,
			  dateFactureOrigine as dateFactureOrigine,
			  observation as observation,			  
			  destinataire.nom as nomFournisseur,
			  destinataire.id as idDest,
			  destinataire.prenom as prenomFournisseur,
			  priorite.nbJours as nbJours	
 		   from facture,destinataire,priorite
		   where facture.idFournisseur = destinataire.id
		   and validite=0 
		   and facture.idPriorite = priorite.id
		   order by ".$order." DESC
	           LIMIT ".$nbAffiche.";";


}
else{

if(!isset($_GET['idFactureRecherche'])){
$requeteFacture = "select facture.id as idFacture,
			  facture.histo as histo,
			  refuse as refuse,
  			  facture.refFacture as refFacture,
			  facture.dateFacture as dateFacture,
			  facture.dateFactureOrigine as dateFactureOrigine,
			  facture.observation as observation,			  
			  facture.montant as montant,
			  destinataire.nom as nomFournisseur,
			  destinataire.id as idDest,
			  destinataire.prenom as prenomFournisseur,
			  priorite.nbJours as nbJours
 		    from facture,destinataire,priorite
		    where facture.id<=".$idTmp." 
             		   and facture.validite = 0
			   and facture.idServiceCreation = ".$_SESSION['idService']."
			   and facture.idPriorite = priorite.id
			   and facture.idFournisseur = destinataire.id
		           order by ".$order." DESC
			   LIMIT ".$nbAffiche.";";
}
else{
$requeteFacture = "select facture.id as idFacture,
			  facture.histo as histo,
			  refuse as refuse,
  			  facture.refFacture as refFacture,
			  facture.dateFacture as dateFacture,
			  facture.dateFactureOrigine as dateFactureOrigine,
			  facture.observation as observation,			  
			  facture.montant as montant,
			  destinataire.nom as nomFournisseur,
			  destinataire.id as idDest,
			  destinataire.prenom as prenomFournisseur,
			  priorite.nbJours as nbJours
 		    from facture,destinataire,priorite
		    where facture.id=".$_GET['idFactureRecherche']." 
             		   and facture.validite = 0
			   and facture.idServiceCreation = ".$_SESSION['idService']."
			   and facture.idPriorite = priorite.id
			   and facture.idFournisseur = destinataire.id
		           order by ".$order." DESC
			   LIMIT ".$nbAffiche.";";
}


}

$resultatFacture = mysql_query($requeteFacture) or die("erreur facture ".mysql_error() );

echo "<table align=center font-color ='white'>";
	echo "<tr>";
	echo "<td align=center><a href=voirFacture?order=facture.id style=\"font-weight:normal\">numero</a> </td>";
	echo "<td align=center><a href=voirFacture?order=facture.idFournisseur style=\"font-weight:normal\">fournisseur</a></td>";
	echo "<td align=center><a href=voirFacture?order=facture.refFacture style=\"font-weight:normal\">refFacture</a></td>";
	echo "<td align=center><a href=voirFacture?order=facture.montant style=\"font-weight:normal\">montant</a></td>";
	echo "<td align=center><a href=voirFacture?order=facture.dateFacture style=\"font-weight:normal\">dateMairie</a></td>";
	echo "<td align=center><a href=voirFacture?order=facture.dateFactureOrigine style=\"font-weight:normal\">dateFacture</a></td>";
	echo "<td align=center>observation</td>";
	echo "<td align=center>historique</td>";
if(strcmp($_SESSION['login'] , 'admin') != 0){

	echo "<td align=center>transmettre</td>";
	echo "<td align=center>terminer</td>";
}
	echo "<td align=center>jours restant</td>";
	echo"</tr>";
$boul = 0;
while($ligne = mysql_fetch_array($resultatFacture)){
	$idTmp = $ligne['idFacture']-1;
	if($boul == 0){
		$couleur = 'lightblue';
		$boul = 1;
	}
	else{
		$couleur = 'white';
		$boul = 0;	
	}
		echo "<tr>";

//	echo "nbJours:".$ligne['nbJours'];
	$refuse=$ligne['refuse'];	
	$dest=$ligne['nomFournisseur'];
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
	
	if(strcmp($refFacture,"") ==0)
		$refFacture="modifier";
	if(strcmp($montant,"") ==0)
		$montant="modifier";
	if(strcmp($dest,"") ==0)
		$nomDestinataire="modifier";
	if(strcmp($observation,"")==0)
		$observation ="modifier";
	
	if($refuse==1)
		$couleur=red;

	echo "<td bgcolor=".$couleur.">".$idCourrier."</td>";
	echo "<td bgcolor=".$couleur."><a href=modifDestinataire.php?idCourrier=".$idCourrier." style=\"text-decoration :none;font-weight:normal\">".$nomDestinataire."</a></td>";
	echo "<td bgcolor=".$couleur." style=\"text-align:center\"><a href=modifRef.php?idCourrier=".$idCourrier." style=\"text-decoration :none;font-weight:normal\">".$refFacture."</a></td>";
	echo "<td bgcolor=".$couleur." style=\"text-align:right\"><a href=modifMontant.php?idCourrier=".$idCourrier." style=\"text-decoration :none;font-weight:normal\">".$montant."</a></td>";
	echo "<td bgcolor=".$couleur.">".$dateArrivee."</td>";
	echo "<td bgcolor=".$couleur.">".$dateFacture."</td>";

	echo "<td bgcolor=".$couleur."><a href=modifObservationFacture.php?idCourrier=".$idCourrier." style=\"text-decoration :none;font-weight:normal\">".$observation."</a></td>";



	echo"<td bgcolor=".$couleur."><a href=cheminFacture.php?idCourrier=".$idCourrier." style=\"text-decoration :none;font-weight:normal\">".$ligne['histo']."</center></a></td>";



if(strcmp($_SESSION['login'] , 'admin') != 0){

	echo"<td bgcolor=".$couleur."><a href=transmettreFacture.php?idCourrier=".$idCourrier.">transmettre</a></td>";
	echo"<td bgcolor=".$couleur."><a href=validerFacture.php?idCourrier=".$idCourrier.">terminer</a></td>";


}


	//test pour urgence du courrier
		$dateActuel = date("Y-m-d");
		$jourActuel = substr($dateActuel,8,2);
		$moisActuel = substr($dateActuel,5,2);
		$anneeActuel= substr($dateActuel,0,4);

		$tmpDateArrivee = $ligne['dateFacture'];
		$jourArrivee =substr($tmpDateArrivee,8,2);
		$moisArrivee =substr($tmpDateArrivee,5,2);
		$anneeArrivee =substr($tmpDateArrivee,0,4);
		
//		echo " feafaoho".$tmpdateArrivee;

		$nbJours = $ligne['nbJours'];
		
		$timestampActuel = mktime(0,0,0,$moisActuel,$jourActuel,$anneeActuel);
		$timestampArrivee= mktime(0,0,0,$moisArrivee,$jourArrivee,$anneeArrivee);
		$urgence = ($timestampActuel - $timestampArrivee ) / 86400;

		$nbJoursRestant = $nbJours - $urgence;

//		echo " fze ".$urgence." : ".$nbJours." :: ".$ligne['nbJours']."<br>";
		if ($nbJoursRestant >= 5)
			$alerte = "green";
		else
			$alerte = "red";
		

                   echo "<td bgcolor=".$couleur." style=\"color:".$alerte.";font-weight:bold\"><center>".$nbJoursRestant."</center></td></tr>";
	
}
	echo"</table>";
if(mysql_num_rows($resultatFacture) == $nbAffiche) 
	echo "<center><a href = voirFacture.php?id=".$idTmp."&nbAffiche=".$nbAffiche.">page suivante</a></center>";
?>	

<center><a href="javascript:history.go(-1)"> <b>page precedente </b></a> &nbsp/</center>

<center><br>
<a href = index.php>index</a>
</center>
<br><br>
</div>

</body>

</html>

