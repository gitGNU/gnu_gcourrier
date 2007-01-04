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

if(!isset($_POST['modifier'])){
$type=$_GET['type'];
$idCourrier = $_GET['idCourrier'];
$requete = "select observation from courrier where id = ".$idCourrier.";";
$result = mysql_query($requete) or die(mysql_error( ));
while($ligne = mysql_fetch_array($result) ){
	$obs = $ligne["observation"];
}
?>

<html>
<head><title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
</head>
<body>
<div id = pageGd><br>
<center>
<img src = images/banniere2.jpg><br><br>
</center>
<form method=POST action=modifObservationCourrier.php>
<?php
echo"<center>";
echo"observation<br>";
echo"<textarea name=observation cols=30 rows=4>".$obs."</textarea>";
echo"<br/><br/>";
echo"<input type=hidden name=type value=".$type."></input>";
echo"<input type=hidden name=idCourrier value=".$idCourrier."></input>";
echo"<input type=submit name=modifier value=modifier><br/><br/>";
echo"</center>";
?>
</form>
</div>

</body>
</html>
<?php
}
else{
$obs = $_POST['observation'];
$idCourrier = $_POST['idCourrier'];
$type=$_POST['type'];
$requete = "update courrier set observation='".$obs."' where id=".$idCourrier.";";
$result = mysql_query($requete) or die(mysql_error());

header("LOCATION:voirCourrier.php?type=".$type."");
}
?>
