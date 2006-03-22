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
session_start();
?>

<html>
<head>
<title>gCourrier</title>
<LINK HREF="styles2.css" REL="stylesheet">
</head>
<body>
<div id = pageGd><br>
<center>
<img src = images/banniere2.jpg><br><br>
</center>

<?php


$idMail =  $_GET['idMail'];


$requete = "select mail.id as mailid,objet,message,service.libelle, service.designation
	    from mail,aMailer,service
	    where mail.id = '".$idMail."'
	    and service.id = aMailer.idServiceDest
	    group by mail.id;";
$resultat = mysql_query( $requete ) or die (mysql_error( ) );
while($ligne = mysql_fetch_array( $resultat ) ){
echo "<table align = center>";

echo "<tr><td><font size = 4px><b>objet : </b></font></td><td><font size = 3px>".$ligne['objet']."</font></td></tr>";
echo "<tr><td><font size = 4px ><b>a : </b></font></td><td><font size = 3px>".$ligne['libelle']." ".$ligne['designation']."</font></center></td></tr></table>";
echo "<br><center><br><font size=3px >".$ligne['message']."</font></center>";

echo"<br><br><center><a href = voirMessageEnvoyer.php>voir mes messages envoyes</a></center><br><br>";
}
?>
</div>
</body>
</html>
