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
if(!isset($_SESSION['login'])){
echo "<meta http-equiv=\"refresh\" content=\"0;url=login.php\">";
}
else{

?>
<html>
	<head>
		<title>GCourrier</title>
		<LINK HREF="styles.css" REL="stylesheet">
	</head>
	<body>

<?php


if(strcmp($_SESSION['login'] ,'admin')==0){
?>

<div id = "page">
<br><br><br>

<table>

<tr>

<td>
<div id="logoimage"><img src = "images/logo.png"></div>
</td>
<div id="logo">
<td>
<a href = creerService.php>creer service</a><br>
<a href = creerCompte.php>creer compte</a><br>
<a href = creerPriorite.php>creer priorite</a><br>
</td>
</div>

<td>
<div id="logo2">
<a href= voirCourrier.php?type=1>voir entrant</a><br>
<a href= voirCourrierDepart.php?type=2>voir depart</a><br>
<a href= voirFacture.php>voir facture</a><br>

</div>
</td>



<td>
<div id="logo3">
<a href = modifierProfil.php>profil</a><br>
</div>
</td>


</table>

<br><br><br><div id="dco"><a href=login.php>deconnexion</a></div><br>
</div>


<?php
}//fin if admin
else{

?>


<div id = "page">
<br>
<table align = center  style="font-size:10px" >
<tr>
<td>rechercher :</td><td><a href="rechercher.php?type=1">entrant</a></td><td><a href="rechercher.php?type=2">depart</a></td></font></td>

<td> </td><td> </td><td> </td>
<td>archive :</td><td><a href="archive.php?type=1">entrant</a></td><td><a href="archive.php?type=2">depart</a></td></font></td>
</tr>

</table>
<br><br>


<table>
<tr>
<td>
<div id="logoimage"><img src = "images/logo.png"></div>
</td>
<div id="logo">
<td>
<a href=creerCourrier.php>creer courrier</a><br>
<a href=creerFacture.php>creer facture</a><br>
<a href=courrierDepart.php>creer depart</a><br>
<a href=creerDestinataire.php>creer individu</a><br>
</td>
</div>

<td>
<div id="logo2">

<a href= voirCourrier.php?type=1>voir entrant</a><br>
<a href= voirCourrier.php?type=2>voir depart</a><br>
<a href= voirFacture.php>voir facture</a><br>


</div>
</td>



<td>
<div id="logo3">

<a href = modifierProfil.php>profil</a><br>
</div>
</td>


</table>

<br><br><br><div id="dco"><a href=login.php>deconnexion</a></div><br>
</div>

<?php
}
}

?>
