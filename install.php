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
<head>
	<title>gCourrier</title>
	
</head>
<body style="text-align: center">
<img src="images/banniere2.jpg" />

<?php
echo "<h3><i>Installation en cours ...</i></h3>";
echo "<h3><i>Verification de la base de donnees ...</i></h3>";

$requete = " DROP TABLE IF EXISTS courrier;";
$result = mysql_query( $requete ) or die ("erreur1".mysql_error() );

$requete = " DROP TABLE IF EXISTS accuse;";
$result = mysql_query( $requete ) or die ("erreur2". mysql_error() );

$requete = " DROP TABLE IF EXISTS destinataire;";
$result = mysql_query( $requete ) or die ( "erreur3".mysql_error() );

$requete = " DROP TABLE IF EXISTS estTransmis;";
$result = mysql_query( $requete ) or die ("erreur4". mysql_error() );


$requete = " DROP TABLE IF EXISTS estTransmisCopie;";
$result = mysql_query( $requete ) or die ("erreur5". mysql_error() );


$requete = " DROP TABLE IF EXISTS facture;";
$result = mysql_query( $requete ) or die ( "erreur6".mysql_error() );

$requete = " DROP TABLE IF EXISTS priorite;";
$result = mysql_query( $requete ) or die ("erreur7". mysql_error() );

$requete = " DROP TABLE IF EXISTS service;";
$result = mysql_query( $requete ) or die ("erreur8". mysql_error() );

$requete = " DROP TABLE IF EXISTS utilisateur;";
$result = mysql_query( $requete ) or die ("erreur9". mysql_error() );


echo "<h3><i>Creation des tables ...</i></h3>";

$requete = "CREATE TABLE `estTransmisCopie` (
  `id` int(11) NOT NULL auto_increment,
  `idFacture` int(11) NOT NULL default '0',
  `idService` int(11) NOT NULL default '0',
  `dateTransmission` date NOT NULL default '0000-00-00',
  `dateRetour` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
);";
$result = mysql_query( $requete ) or die ( "erreur10".mysql_error() );

$requete = "CREATE TABLE `accuse` (
  `id` int(11) NOT NULL auto_increment,
  `expediteur` varchar(50) NOT NULL default '',
  `adresse` varchar(255) NOT NULL default '',
  `codePostal` varchar(5) NOT NULL default '',
  `ville` varchar(50) NOT NULL default '',
  `telephone` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$result = mysql_query( $requete ) or die ( "erreur11".mysql_error() );


$requete = " CREATE TABLE `courrier` (
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
  `url` varchar(255) NOT NULL default '', 
  PRIMARY KEY  (`id`)
);";
$result = mysql_query( $requete ) or die ("erreur12". mysql_error() );

$requete = " CREATE TABLE `facture` (
  `id` int(11) NOT NULL auto_increment,
  `montant` decimal(10,2) NOT NULL,
  `refFacture` varchar(32) NOT NULL default '',
  `dateFacture` date NOT NULL default '0000-00-00',
  `dateFactureOrigine` date NOT NULL default '0000-00-00',
  `observation` text NOT NULL,
  `validite` tinyint(4) NOT NULL default '0',
  `dateArchivage` date NOT NULL default '0000-00-00',
  `idFournisseur` int(11) NOT NULL default '0',
  `idServiceCreation` int(11) NOT NULL default '0',
  `idPriorite` int(11) NOT NULL default '0',
  `histo` varchar( 255 ) DEFAULT 'consulter' NOT NULL,
  `refuse` int(11) NOT NULL default '0',
  `dateSysteme` timestamp default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
);";
$result = mysql_query( $requete ) or die ("erreur13". mysql_error() );


$requete = " CREATE TABLE `estTransmis` (
  `id` int(11) NOT NULL auto_increment,
  `idCourrier` int(11) NOT NULL default '0',
  `idService` int(11) NOT NULL default '0',
  `dateTransmission` date NOT NULL default '0000-00-00',
  `danger` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);";
$result = mysql_query( $requete ) or die ("erreur14". mysql_error() );


$requete = "
CREATE TABLE `destinataire` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(32) NOT NULL default '',
  `prenom` varchar(32) NOT NULL default '',
  `adresse` varchar(200) NOT NULL default '',
  `codePostal` varchar(5) NOT NULL default '',
  `ville` varchar(60) NOT NULL default '',
  `telephone` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
);";

$result = mysql_query( $requete ) or die ("erreur15". mysql_error() );

$requete = "
CREATE TABLE `service` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(60) NOT NULL default '',
  `designation` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`id`)
);";
$result = mysql_query( $requete ) or die ("erreur16". mysql_error() );


$requete = "
CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(32) NOT NULL default '',
  `nom` varchar(32) NOT NULL default '',
  `prenom` varchar(32) NOT NULL default '',
  `passwd` varchar(60) NOT NULL default '',
  `idService` int(11) NOT NULL,
  `preferenceNbCourrier` int(11) NOT NULL default '50',
  PRIMARY KEY  (`id`)
);";
$result = mysql_query( $requete ) or die ( "erreur17".mysql_error() );

$requete = "
CREATE TABLE `priorite` (
  `id` int(11) NOT NULL auto_increment,
  `designation` varchar(50) NOT NULL default '',
  `nbJours` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);";

$result = mysql_query( $requete ) or die ( "erreur18".mysql_error() );

# User sessions, to stay connected even if the browser is closed
$requete = "
CREATE TABLE `sessions` (
  `hash` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expiration` date NOT NULL,
  PRIMARY KEY (`hash`)
);";
$result = mysql_query($requete) or die("erreur en créant la table 'session': ".mysql_error());

# Completed upgrades
$requete = "
CREATE TABLE `upgrades` (
  `version` varchar(255) NOT NULL,
  `upgrade_name` varchar(255) NOT NULL,
  PRIMARY KEY (`version`, `upgrade_name`)
);";
$result = mysql_query($requete) or die("erreur en créant la table 'upgrades': ".mysql_error());


##
# Initial values
##

echo "<h3><i>Creation du service ADMIN ...</i></h3>";
$requete = "INSERT INTO service(libelle,designation) VALUES ('ADMIN', 'admin');";
$result = mysql_query( $requete ) or die ( "erreur19".mysql_error() );


echo "<h3><i>Creation du compte admin ...</i></h3>";
$requete="INSERT INTO utilisateur(login,nom,prenom,idService) VALUES('admin','admin','admin',1);";
$result = mysql_query( $requete ) or die ( "erreur20".mysql_error() );

$requete="INSERT INTO accuse(id) VALUES(1);";
$result = mysql_query( $requete ) or die ("erreur21". mysql_error() );


$requete="Insert into priorite(designation,nbJours) values('normal',10)";
$result = mysql_query( $requete ) or die ( "erreur22".mysql_error() );

?>

<h3>OK!</h3>

<p><a href="login.php">Se connecter</a></p>

</body>
</html>
