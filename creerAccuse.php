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
require('phppdflib/phppdflib.class.php');
require('connexion.php');


//recuperation des donnees
$expediteur = stripcslashes($_POST['expediteur']);
$adresse = stripcslashes($_POST['adresse']);
$codePostal = stripcslashes($_POST['codePostal']." ".$_POST['ville']);
$telephone = stripcslashes($_POST['telephone']);

$destinataire = stripcslashes($_POST['destinataire']);
$adresseDest =stripcslashes( $_POST['adresseDest']);
$codePostalDest= stripcslashes($_POST['codePostalDest']." ".$_POST['villeDest']);

$date = stripcslashes($_POST['date']);
$objet = stripcslashes($_POST['objet']);
$corps = stripcslashes($_POST['corps']);


$pdf = new pdffile;
$pdf->set_default('margin', 0);
$firstpage = $pdf->new_page("letter");

//information expediteur destinataire du document

$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(10, 750, $expediteur, $firstpage, $param);

$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(10, 730, $adresse, $firstpage, $param);

$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(10, 710, $codePostal, $firstpage, $param);

$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(10, 690, $telephone, $firstpage, $param);


$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(400, 670, $destinataire, $firstpage, $param);



$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(400, 650, $adresseDest, $firstpage, $param);


$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(400, 630, $codePostalDest, $firstpage, $param);


$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(400, 590, $date, $firstpage, $param);

$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(10, 550, $objet, $firstpage, $param);



$x=10;
$y=490;
$debut=0;
$fin = 0;

for($i=0; $i<strlen($corps); $i++){
  if(strcmp($corps[$i],'~') == 0){
    $param["height"] = 14;
    $param["fillcolor"] = $pdf->get_color('black');
    $param["font"] = "Helvetica";
    $param["rotation"] = 0;
    $pdf->draw_text($x, $y, substr($corps,$debut,$fin-$debut), $firstpage, $param);
    $y-=40;
    $x=10;
    $fin++;
    $debut = $fin;
  }

 else if(strcmp($corps[$i],'|') == 0){
    $x+=390;
    $corps[$i] = '';  
    $fin++;
  }

 else if(strcmp($corps[$i],'#') == 0){
    $x+=10;
    $corps[$i] = '';  
    $fin++;
  }

  else
    $fin++;
}

$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text($x, $y, substr($corps,$debut,$fin-$debut-1), $firstpage, $param);

/*
header("Content-Disposition: filename=accuseReception.pdf");
header("Content-Type: application/pdf");
$temp = $pdf->generate();
header('Content-Length: ' . strlen($temp));
echo $temp;
*/

$inF = fopen("accuse/".$objet.".pdf", "w");
fputs($inF, $pdf->generate(0));
fclose($inF);
/*
header("Content-Disposition: filename=accuseReception.pdf");
header("Content-Type: application/pdf");
header('Content-Length:'. filesize("accuse/".$objet.".pdf").' ');

$outF = fopen("accuse/".$objet.".pdf", "r");
while (!feof($outF )) {
  fread($outF , 8192);
}
fclose($outF );
*/
echo "accuse reception enregistre sur le serveur";
echo "<br><br><a href='accuse/".$objet.".pdf'>telecharger l'accuse reception</a>";
echo "<br><a href=index.php>index</a>";
?>
