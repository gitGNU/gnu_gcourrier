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

if(!isset($_POST['modifier'])){
$type=$_GET['type'];
$idCourrier = $_GET['idCourrier'];
$requete = "select libelle from courrier where id = ".$idCourrier.";";
$result = mysql_query($requete) or die("1".mysql_error( ));
while($ligne = mysql_fetch_array($result) ){
	$refFacture = $ligne["libelle"];
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
<form method=POST action=modifLibelleCourrier.php>
<?php
echo"<center>";
echo"<label>Libelle : </label>";
echo"<input type=text name=refFacture value='".htmlspecialchars($refFacture, ENT_QUOTES)."'></input>";
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
$refFacture = $_POST['refFacture'];
$idCourrier = $_POST['idCourrier'];
$type=$_POST['type'];
$requete = "update courrier set libelle='".$refFacture."' where id=".$idCourrier.";";
$result = mysql_query($requete) or die("2".mysql_error());

header("LOCATION:mail_list_my.php?type=".$type."");
}
?>
