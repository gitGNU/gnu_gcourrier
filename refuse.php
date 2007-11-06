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

/** Reject a facture means "unarchive" here **/

require_once('init.php');
require_once('functions/db.php');

if(!isset($_GET['valider'])){
?>
<html>
	<head> <title>gCourrier</title>
<link href="styles2.css" rel="stylesheet"></head>
	<body>


<div id="pageTGd"><br>
	<center>
	<img src="images/banniere2.jpg"></img><br><br><br>
<?
echo _("Êtes-vous sûr(e) de vouloir refuser cette facture?");
echo "<br />";
echo _("La facture repartira dans la liste des factures en cours et ne sera plus marquée comme archivée.");
echo "<br />";
echo "<a href='refuse.php?idCourrier={$_GET['idCourrier']}&valider=o'>Oui</a> &nbsp; &nbsp; <a href='index.php'>Non</a><br /><br />";
}
else{

$idCourrier = $_GET['idCourrier'];

db_execute("UPDATE facture SET refuse=1, validite=0 WHERE id=?", array($idCourrier));

header("LOCATION:index.php");
}
