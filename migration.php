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
require("../connexion.php");


//----------------------------------
//  MIGRATION DE LA TABLE ENTRANT
//----------------------------------

$requete = " DROP TABLE IF EXISTS courrierMigration;";
$result = mysql_query( $requete ) or die ( mysql_error() );

$requete = " CREATE TABLE `courrierMigration` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(32) NOT NULL default '',
  `dateArrivee` date NOT NULL default '0000-00-00',
  `observation` text NOT NULL,
  `validite` tinyint(4) NOT NULL default '0',
  `dateArchivage` date NOT NULL default '0000-00-00',
  `idDestinataire` int(11) NOT NULL default '0',
  `idServiceCreation` int(11) NOT NULL default '0',
  `idPriorite` int(11) NOT NULL default '0',
  `serviceCourant` int(11) NOT NULL default '0',
  `type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);";
$result = mysql_query( $requete ) or die ( mysql_error() );



$recupIdEntrant = "select entrant.id as idEntrant,
		   entrant.idCourrier as idCourrier,
                   courrier.libelle as lib,
		   courrier.dateArrivee as dateArrivee,
		   courrier.observation as observation,
		   courrier.validite as validite,
		   courrier.dateArchivage as dateArchivage
		   from entrant,courrier
                   where courrier.id = entrant.idCourrier;";

$resultIdEntrant = mysql_query($recupIdEntrant) or die("erreur recuperation idEntrant : ".mysql_error() );

$idCourrier;
$idEntrant;
$libelle;
$dateArrivee;
$observation;
$validite;
$dateArchivage;
//$idDestinataire;
//$idService;

$i = 0;
while($ligne = mysql_fetch_array( $resultIdEntrant ) ){
	$idEntrant[$i] = $ligne['idEntrant'];
	$idCourrier[$i] = $ligne['idCourrier'];
	$libelle[$i]=$ligne['lib'];
	$dateArrivee[$i]=$ligne['dateArrivee'];
	$observation[$i]=str_replace( '"' ," ",$ligne['observation']);
	$validite[$i]=$ligne['validite'];
	$dateArchivage[$i]=$ligne['dateArchivage'];
	$i++;
}

//Recuperation du destinataire
$idDestinataire;
for($cpt=0;$cpt<$i;$cpt++){
	$requeteIdDestinataire = "select envoyeA.idDestinataire as idDest
				  from envoyeA,entrant
				  where entrant.idCourrier = ".$idCourrier[$cpt]."
				  and envoyeA.idCourrier = entrant.idCourrier;";
	//echo $requeteIdDestinataire."<br>";
	$resultIdDestinataire = mysql_query($requeteIdDestinataire) or die("destinataire");
	while($ligne=mysql_fetch_array($resultIdDestinataire)){
		$idDestinataire[$cpt] = $ligne['idDest'];
	}
}

//Recuperation du createur

$idCreateur;
for($cpt=0;$cpt<$i;$cpt++){
	$requeteIdCreateur = "select aCreer.idService as idServ
			      from aCreer,entrant
			      where entrant.idCourrier = ".$idCourrier[$cpt]."
			      and aCreer.idCourrier = entrant.idCourrier;";
	$resultIdCreateur = mysql_query($requeteIdCreateur) or die("createur");
	while($ligne=mysql_fetch_array($resultIdCreateur)){
		$idCreateur[$cpt] = $ligne['idServ'];

	}
}


//Recuperation de la priorite

$idPriorite;
for($cpt=0;$cpt<$i;$cpt++){
	$requeteIdPriorite = "select estImportant.idPriorite as idPrio
			      from estImportant,entrant
			      where entrant.idCourrier = ".$idCourrier[$cpt]."
			      and estImportant.idCourrier = entrant.idCourrier;";
	$resultIdPriorite = mysql_query($requeteIdPriorite) or die("priorite");
	while($ligne=mysql_fetch_array($resultIdPriorite)){
		$idPriorite[$cpt] = $ligne['idPrio'];

	}
}


//Recuperation du serviceCourant

$serviceCourant;
for($cpt=0;$cpt<$i;$cpt++){
	$requeteIdServiceCourant = "select estTransmis.idService as courant
			      from estTransmis,entrant
			      where entrant.idCourrier = ".$idCourrier[$cpt]."
			      and estTransmis.idCourrier = entrant.idCourrier
			      LIMIT 1;";
	$resultIdServiceCourant = mysql_query($requeteIdServiceCourant) or die("service courant");
	while($ligne=mysql_fetch_array($resultIdServiceCourant)){
		$serviceCourant[$cpt] = $ligne['courant'];

	}
}

//echo $i;

$tmp = 0;
for( $tmp ; $tmp<$i ; $tmp ++ ){

//echo $idEntrant[$tmp]." ".$idCourrier[$tmp]."<br>";

	$requeteInsertion = "insert into courrierMigration(id, libelle, dateArrivee, observation, validite, dateArchivage, idDestinataire, idServiceCreation, idPriorite, serviceCourant,type) values(".$idCourrier[$tmp].",\"".$libelle[$tmp]."\",\"".$dateArrivee[$tmp]."\",\"".$observation[$tmp]."\",".$validite[$tmp].",\"".$dateArchivage[$tmp]."\",".$idDestinataire[$tmp].",".$idCreateur[$tmp].",".$idPriorite[$tmp].",".$serviceCourant[$tmp].",1);";

//echo $requeteInsertion."<br><br>";
	$resultInsertion = mysql_query($requeteInsertion) or die("erreur".mysql_error());
}



//----------------------------------
//  MIGRATION DE LA TABLE DEPART
//----------------------------------


$recupIdDepart =  "select depart.id as idDepart,
		   depart.idCourrier as idCourrier,
                   courrier.libelle as lib,
		   courrier.dateArrivee as dateArrivee,
		   courrier.observation as observation,
		   courrier.validite as validite,
		   courrier.dateArchivage as dateArchivage
		   from depart,courrier
                   where courrier.id = depart.idCourrier;";

$resultIdDepart = mysql_query($recupIdDepart) or die("erreur recuperation idDepart : ".mysql_error() );

$idCourrier;
$idDepart;
$libelle;
$dateArrivee;
$observation;
$validite;
$dateArchivage;

$i = 0;
while($ligne = mysql_fetch_array( $resultIdDepart ) ){
	$idDepart[$i] = $ligne['idDepart'];
	$idCourrier[$i] = $ligne['idCourrier'];
	$libelle[$i]=$ligne['lib'];
	$dateArrivee[$i]=$ligne['dateArrivee'];
	$observation[$i]=str_replace( '"' ," ",$ligne['observation']);
	$validite[$i]=$ligne['validite'];
	$dateArchivage[$i]=$ligne['dateArchivage'];
	$i++;
}

//Recuperation du destinataire
$idDestinataire;
for($cpt=0;$cpt<$i;$cpt++){
	$requeteIdDestinataire = "select envoyeA.idDestinataire as idDest
				  from envoyeA,depart
				  where depart.idCourrier = ".$idCourrier[$cpt]."
				  and envoyeA.idCourrier = depart.idCourrier;";

	$resultIdDestinataire = mysql_query($requeteIdDestinataire) or die("destinataireDepart");
	while($ligne=mysql_fetch_array($resultIdDestinataire)){
		$idDestinataire[$cpt] = $ligne['idDest'];
	}
}

//Recuperation du createur

$idCreateur;
for($cpt=0;$cpt<$i;$cpt++){
	$requeteIdCreateur = "select aCreer.idService as idServ
			      from aCreer,depart
			      where depart.idCourrier = ".$idCourrier[$cpt]."
			      and aCreer.idCourrier = depart.idCourrier;";
	$resultIdCreateur = mysql_query($requeteIdCreateur) or die("createur depart");
	while($ligne=mysql_fetch_array($resultIdCreateur)){
		$idCreateur[$cpt] = $ligne['idServ'];

	}
}


//Recuperation de la priorite

$idPriorite;
for($cpt=0;$cpt<$i;$cpt++){
	$requeteIdPriorite = "select estImportant.idPriorite as idPrio
			      from estImportant,depart
			      where depart.idCourrier = ".$idCourrier[$cpt]."
			      and estImportant.idCourrier = depart.idCourrier;";
	$resultIdPriorite = mysql_query($requeteIdPriorite) or die("priorite depart");
	while($ligne=mysql_fetch_array($resultIdPriorite)){
		$idPriorite[$cpt] = $ligne['idPrio'];

	}
}


//Recuperation du serviceCourant

$serviceCourant;
for($cpt=0;$cpt<$i;$cpt++){
	$requeteIdServiceCourant = "select estTransmis.idService as courant
			      from estTransmis,depart
			      where depart.idCourrier = ".$idCourrier[$cpt]."
			      and estTransmis.idCourrier = depart.idCourrier
			      LIMIT 1;";
	$resultIdServiceCourant = mysql_query($requeteIdServiceCourant) or die("service courant");
	while($ligne=mysql_fetch_array($resultIdServiceCourant)){
		$serviceCourant[$cpt] = $ligne['courant'];

	}
}

//echo $i;

$tmp = 0;
for( $tmp ; $tmp<$i ; $tmp ++ ){

//echo $idDepart[$tmp]." ".$idCourrier[$tmp]."<br>";

	$requeteInsertion = "insert into courrierMigration(id, libelle, dateArrivee, observation, validite, dateArchivage, idDestinataire, idServiceCreation, idPriorite, serviceCourant,type) values(".$idCourrier[$tmp].",\"".$libelle[$tmp]."\",\"".$dateArrivee[$tmp]."\",\"".$observation[$tmp]."\",".$validite[$tmp].",\"".$dateArchivage[$tmp]."\",".$idDestinataire[$tmp].",".$idCreateur[$tmp].",".$idPriorite[$tmp].",".$serviceCourant[$tmp].",2);";

	$resultInsertion = mysql_query($requeteInsertion) or die("erreur".mysql_error());
}





//----------------------------------
//  MIGRATION DE LA TABLE UTILISATEUR
//----------------------------------

$requete = " DROP TABLE IF EXISTS utilisateurMigration;";
$result = mysql_query( $requete ) or die ( mysql_error() );

$requete = " CREATE TABLE `utilisateurMigration` (
    `id` int(11) NOT NULL auto_increment,
    `login` varchar(32) NOT NULL default '',
    `nom` varchar(32) NOT NULL default '',
    `prenom` varchar(32) NOT NULL default '',
    `passwd` varchar(60) NOT NULL default '',
    `idService` int(11) NOT NULL,
     PRIMARY KEY  (`id`)
) ;";

$result = mysql_query( $requete ) or die ( mysql_error() );


$requeteUser = "select 	utilisateur.id as idUtilisateur,
		     	utilisateur.login as login,
			utilisateur.nom as nom,
			utilisateur.prenom as prenom,
			utilisateur.passwd as passwd,
			appartient.idService as idService
		from utilisateur, appartient
		where utilisateur.id = appartient.idUtilisateur;";
$resultUser = mysql_query($requeteUser) or die("utilisateur");

$idUser;
$login;
$nom;
$prenom;
$passwd;
$idService;

$i=0;

while($ligne = mysql_fetch_array($resultUser) ){
	$idUser[$i]=$ligne['idUtilisateur'];
	$login[$i]=$ligne['login'];
	$nom[$i]=$ligne['nom'];
	$prenom[$i]=$ligne['prenom'];
	$passwd[$i]=$ligne['passwd'];
	$idService[$i]=$ligne['idService'];
	$i++;
}
for($tmp=0;$tmp<$i;$tmp++){
$requeteInsertUser = "insert into utilisateurMigration values(".$idUser[$tmp].",'".$login[$tmp]."','".$nom[$tmp]."','".$prenom[$tmp]."','".$passwd[$tmp]."',".$idService[$tmp].");";
$resultInsertUser = mysql_query($requeteInsertUser) or die("insertUser");
}

//FINALISATION
$requete = "DROP TABLE facture;";
$result = mysql_query($requete) or die("erreur finalisation 1");
$requete = "DROP TABLE courrier;";
$result = mysql_query($requete) or die("erreur finalisation 1");
$requete = "DROP TABLE entrant;";
$result = mysql_query($requete) or die("erreur finalisation 2");
$requete = "DROP TABLE utilisateur;";
$result = mysql_query($requete) or die("erreur finalisation 3");
$requete = "DROP TABLE depart;";
$result = mysql_query($requete) or die("erreur finalisation 4");
$requete = "ALTER TABLE courrierMigration RENAME courrier ;";
$result = mysql_query($requete) or die("erreur finalisation 5");
$requete = "ALTER TABLE utilisateurMigration RENAME utilisateur ;";
$result = mysql_query($requete) or die("erreur finalisation 7");



$requete = "DROP TABLE aCreer;";
$result = mysql_query($requete) or die("erreur finalisation 8");
$requete = "DROP TABLE aMailer;";
$result = mysql_query($requete) or die("erreur finalisation 9");
$requete = "DROP TABLE aModifier;";
$result = mysql_query($requete) or die("erreur finalisation 10");
$requete = "DROP TABLE appartient;";
$result = mysql_query($requete) or die("erreur finalisation 11");
$requete = "DROP TABLE chemin;";
$result = mysql_query($requete) or die("erreur finalisation 12");
$requete = "DROP TABLE compteMail;";
$result = mysql_query($requete) or die("erreur finalisation 13");
$requete = "DROP TABLE envoyeA;";
$result = mysql_query($requete) or die("erreur finalisation 14");
$requete = "DROP TABLE estAffecte;";
$result = mysql_query($requete) or die("erreur finalisation 15");
$requete = "DROP TABLE estImportant;";
$result = mysql_query($requete) or die("erreur finalisation 16");
$requete = "DROP TABLE mail;";
$result = mysql_query($requete) or die("erreur finalisation 17");
$requete = "DROP TABLE estTransmisCopie;";
$result = mysql_query($requete) or die("erreur finalisation 18");
$requete = "DROP TABLE retour;";
$result = mysql_query($requete) or die("erreur finalisation 19");
$requete = "DROP TABLE retourTmp;";
$result = mysql_query($requete) or die("erreur finalisation 20");


$requete = "CREATE TABLE `estTransmisCopie` (
  `id` int(11) NOT NULL auto_increment,
  `idFacture` int(11) NOT NULL default '0',
  `idService` int(11) NOT NULL default '0',
  `dateTransmission` date NOT NULL default '0000-00-00',
  `dateRetour` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";
$result = mysql_query($requete) or die("erreur finalisation 21");

$requete = "UPDATE utilisateur set passwd=\"\" where id=1;";
$result = mysql_query($requete) or die("erreur finalisation 22");
/*
$requete ="CREATE TABLE `estTransmisDepart` (
  `id` int(11) NOT NULL auto_increment,
  `idDepart` int(11) NOT NULL default '0',
  `idService` int(11) NOT NULL default '0',
  `dateTransmission` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM ;";
$result = mysql_query($requete) or die("erreur finalisation 22");
*/


$requete = " CREATE TABLE `facture` (
  `id` int(11) NOT NULL auto_increment,
  `montant` varchar(32) NOT NULL default '',
  `refFacture` varchar(32) NOT NULL default '',
  `dateFacture` date NOT NULL default '0000-00-00',
  `dateFactureOrigine` date NOT NULL default '0000-00-00',
  `observation` text NOT NULL,
  `validite` tinyint(4) NOT NULL default '0',
  `dateArchivage` date NOT NULL default '0000-00-00',
  `idFournisseur` int(11) NOT NULL default '0',
  `idServiceCreation` int(11) NOT NULL default '0',
  `idPriorite` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);";
$result = mysql_query( $requete ) or die ( mysql_error() );

$requete = "ALTER TABLE `estTransmis` ADD `danger` int(11) NOT NULL ;";
$result = mysql_query( $requete ) or die ( mysql_error() );

$requete = "CREATE TABLE `accuse` (
  `id` int(11) NOT NULL auto_increment,
  `expediteur` varchar(50) NOT NULL default '',
  `adresse` varchar(255) NOT NULL default '',
  `codePostal` varchar(5) NOT NULL default '',
  `ville` varchar(50) NOT NULL default '',
  `telephone` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$result = mysql_query( $requete ) or die ( mysql_error() );

$requete="INSERT INTO accuse(id) VALUES(1);";
$result = mysql_query( $requete ) or die ( mysql_error() );


echo"MIGRATION TERMINEE";

?>















