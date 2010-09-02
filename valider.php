<?php
/*
GCourrier
Copyright (C) 2005, 2006  Cliss XXI

This file is part of GCourrier.

GCourrier is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

GCourrier is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

author VELU Jonathan
*/

require_once('init.php');

if(!isset($_GET['valider'])){
?>
<html>
	<head> <title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet"></head>
	<body>


<div id = pageTGd><br>
	<center>
	<img src = images/banniere2.jpg></img><br><br><br>
<?
echo "etes vous sur de vouloir archiver ce courrier ?<br>";
echo "<a href=valider.php?idCourrier=".$_GET['idCourrier']."&valider='o'&type=".$_GET['type'].">oui</a> &nbsp; &nbsp; <a href=index.php>non</a><br><br>";
}
else{

$type = $_GET['type'];
$idCourrier = $_GET['idCourrier'];
$date = date("Y-m-d");
$requete = "update courrier set validite = 1, dateArchivage='".$date."' where id = ".$idCourrier." ;";
$result = mysql_query( $requete ) or die (mysql_error( ));

header("Location:mail_list_my.php?type=".$type."");
}
//echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php\">";
