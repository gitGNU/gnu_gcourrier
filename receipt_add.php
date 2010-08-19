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
require('functions/mail.php');
require('init.php');

//recuperation des donnees
if (get_magic_quotes_gpc()) {
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
} else {
  $expediteur = $_POST['expediteur'];
  $adresse = $_POST['adresse'];
  $codePostal = $_POST['codePostal']." ".$_POST['ville'];
  $telephone = $_POST['telephone'];
  
  $destinataire = $_POST['destinataire'];
  $adresseDest = $_POST['adresseDest'];
  $codePostalDest= $_POST['codePostalDest']." ".$_POST['villeDest'];
  
  $date = $_POST['date'];
  $objet = $_POST['objet'];
  $corps = $_POST['corps'];
}

$pdf = new pdffile;
$pdf->set_default('margin', 0);
$firstpage = $pdf->new_page("letter");

//information expediteur destinataire du document

$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(10, 750, utf8_decode($expediteur), $firstpage, $param);

$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(10, 730, utf8_decode($adresse), $firstpage, $param);

$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(10, 710, utf8_decode($codePostal), $firstpage, $param);

$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(10, 690, utf8_decode($telephone), $firstpage, $param);


$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(400, 670, utf8_decode($destinataire), $firstpage, $param);



$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(400, 650, utf8_decode($adresseDest), $firstpage, $param);


$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(400, 630, utf8_decode($codePostalDest), $firstpage, $param);


$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(400, 590, utf8_decode($date), $firstpage, $param);

$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text(10, 550, utf8_decode($objet), $firstpage, $param);



$MARGE_GAUCHE=30;
$x = $MARGE_GAUCHE;
$y = 490;
$debut = 0;
$fin = 0;

for ($i = 0; $i < strlen($corps); $i++) {
  if ($corps[$i] == "\r") {
    $corps[$i] = '';
    $i--;
  } elseif ($corps[$i] == '~' or $corps[$i] == "\n") {
    $param["height"] = 14;
    $param["fillcolor"] = $pdf->get_color('black');
    $param["font"] = "Helvetica";
    $param["rotation"] = 0;
    $pdf->draw_text($x, $y, utf8_decode(substr($corps,$debut,$fin-$debut-1)), $firstpage, $param);
    $y-=20;
    $x = $MARGE_GAUCHE;
    $fin++;
    $debut = $fin;
  } elseif ($corps[$i] == '|') {
    $x+=390;
    $corps[$i] = '';
    $i--;
  } elseif ($corps[$i] == '#') {
    $x+=10;
    $corps[$i] = '';
    $i--;
  } else {
    $fin++;
  }
}

$param["height"] = 14;
$param["fillcolor"] = $pdf->get_color('black');
$param["font"] = "Helvetica";
$param["rotation"] = 0;
$pdf->draw_text($x, $y, utf8_decode(substr($corps,$debut,$fin-$debut)), $firstpage, $param);

/*
header("Content-Disposition: filename=accuseReception.pdf");
header("Content-Type: application/pdf");
$temp = $pdf->generate();
header('Content-Length: ' . strlen($temp));
echo $temp;
*/

$mail_id = intval($_POST['mail_id']);
if ($mail_id == 0)
  exit("Identifiant de courrier invalide");
$filename = "accuse-courrier_{$mail_id}-" . strftime("%x-%X"). ".pdf";
$path = "accuse/$filename";
$inF = fopen($path, "wn");
fputs($inF, $pdf->generate(0));
fclose($inF);

$attachment_id = mail_attachment_new($mail_id, $path, $filename);

include('templates/header.php');
echo "<p>"
. "<a href='file_view.php/"
. $filename
. "?object=mail&attachment_id={$attachment_id}]'>"
. "Télécharger l'accusé de réception"
. "</a>"
. "</p>";

echo "<p><a href='mail_attachment.php?object_id={$mail_id}'>Voir les pièces jointes du courrier</a></p>";

include('templates/footer.php');
?>
