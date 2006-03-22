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

session_start();
require("connexion.php");
?>
<html>
<head>
	<title>gCourrier</title>
</head>
<body bgcolor = darkblue>
<center><img src=images/banniere.jpg></img><br><br><br>

<?php
echo "<h3><font color = white><i>Installation en cours ...</i></font></h3>";
echo "<h3><font color = white><i>Verification de la base de donnees ...</i></font></h3>";

$requete = " DROP TABLE IF EXISTS courrier;";
$result = mysql_query( $requete ) or die ( mysql_error() );

$requete = " DROP TABLE IF EXISTS destinataire;";
$result = mysql_query( $requete ) or die ( mysql_error() );

$requete = " DROP TABLE IF EXISTS estTransmis;";
$result = mysql_query( $requete ) or die ( mysql_error() );

$requete = " DROP TABLE IF EXISTS facture;";
$result = mysql_query( $requete ) or die ( mysql_error() );

$requete = " DROP TABLE IF EXISTS priorite;";
$result = mysql_query( $requete ) or die ( mysql_error() );

$requete = " DROP TABLE IF EXISTS service;";
$result = mysql_query( $requete ) or die ( mysql_error() );

$requete = " DROP TABLE IF EXISTS utilisateur;";
$result = mysql_query( $requete ) or die ( mysql_error() );


echo "<h3><font color = white><i>Creation des tables ...</i></font></h3>";

$requete = "CREATE TABLE `estTransmisCopie` (
  `id` int(11) NOT NULL auto_increment,
  `idFacture` int(11) NOT NULL default '0',
  `idService` int(11) NOT NULL default '0',
  `dateTransmission` date NOT NULL default '0000-00-00',
  `dateRetour` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
);";
$result = mysql_query( $requete ) or die ( mysql_error() );


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
  PRIMARY KEY  (`id`)
);";
$result = mysql_query( $requete ) or die ( mysql_error() );

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


$requete = " CREATE TABLE `estTransmis` (
  `id` int(11) NOT NULL auto_increment,
  `idCourrier` int(11) NOT NULL default '0',
  `idService` int(11) NOT NULL default '0',
  `dateTransmission` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
);";
$result = mysql_query( $requete ) or die ( mysql_error() );


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

$result = mysql_query( $requete ) or die ( mysql_error() );

$requete = "
CREATE TABLE `service` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(60) NOT NULL default '',
  `designation` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`id`)
);";
$result = mysql_query( $requete ) or die ( mysql_error() );


$requete = "
CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(32) NOT NULL default '',
  `nom` varchar(32) NOT NULL default '',
  `prenom` varchar(32) NOT NULL default '',
  `passwd` varchar(60) NOT NULL default '',
  `idService` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
);";
$result = mysql_query( $requete ) or die ( mysql_error() );

$requete = "
CREATE TABLE `priorite` (
  `id` int(11) NOT NULL auto_increment,
  `designation` varchar(50) NOT NULL default '',
  `nbJours` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);";

$result = mysql_query( $requete ) or die ( mysql_error() );

echo "<h3><font color = white><i>Creation du service ADMIN ...</i></font></h3>";
$requete = "INSERT INTO service(libelle,designation) VALUES ('ADMIN', 'admin');";
$result = mysql_query( $requete ) or die ( mysql_error() );


echo "<h3><font color = b27e5e><i>Creation du compte admin ...</i></font></h3>";
$requete="INSERT INTO utilisateur(login,nom,prenom,idService) VALUES('admin','administrateur','gCourrier',1);";
$result = mysql_query( $requete ) or die ( mysql_error() );

?>

<br><a href = login.php><font color = white>se connecter</font></a>
</center>

</body>
</html>
