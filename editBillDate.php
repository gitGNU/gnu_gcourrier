<?php
/*
Change the date for a bill if issued this day.
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
*/


require_once('init.php');
require_once('functions/db.php');
require_once('classes/HTML/QuickForm/FR.php');

include('templates/header.php');

function cur_dates() {
  $res = db_execute("SELECT dateFactureOrigine AS date_facture,
                            dateFacture AS date_mairie,
			    unix_timestamp(datesysteme) AS internal_timestamp
                     FROM facture WHERE id=?",
		    array((int)$_REQUEST['id']));
  return mysql_fetch_array($res);
}

if(empty($_REQUEST['id']) or !ctype_digit($_REQUEST['id'])) {
  echo "<div class='status'>" . _("Identifiant de facture invalide.") . "</div>";      
} else {
  $form = new HTML_QuickForm_FR('change_date');
  $cur_dates = cur_dates();

  $form->addElement('hidden', 'id', 'identifiant facture');
  $form->addElement('header', 'title', _('Modifier la date de la facture n°') . $_REQUEST['id']);
  $form->addElement('date', 'date_mairie', "Date de saisie en mairie",
		    array('language' => 'fr'));
  $form->addElement('date', 'date_facture', "Date d'émission de la facture",
		    array('language' => 'fr'));
  $form->addElement('submit', null, _("OK"));
  
  $form->addRule('id', null, 'nonzero');
  $form->addRule('id', null, 'callback', 'ctype_digit');
  
  $form->addRule('date_mairie', _("La date n'est pas valide."), 'callback', 'date_isvalid');
  $form->addRule('date_facture', _("La date n'est pas valide."), 'callback', 'date_isvalid');

  $form->setDefaults(array('id' => $_REQUEST['id'],
			   'date_facture' => $cur_dates['date_facture'],
			   'date_mairie'  => $cur_dates['date_mairie']));

  if ((time() - $cur_dates['internal_timestamp']) > 86400)
    $form->freeze('date_mairie');

  function date_isvalid($arr) {
    extract($arr);
    return checkdate($M, $d, $Y);
  }

  $form_values = $form->exportValues();
  if ($form->validate()) {
    extract($form_values['date_facture']);
    $date_facture = "$Y-$M-$d";
    $timestamp_facture=strtotime($date_facture);
    
    extract($form_values['date_mairie']);
    $date_mairie = "$Y-$M-$d";
    $timestamp_mairie=strtotime($date_mairie);

    $changed = false;
    if ($timestamp_mairie != strtotime($cur_dates['date_mairie'])) {
      if ((time() - $cur_dates['internal_timestamp']) > 86400) {
	echo "<div class='status'>"
	  . _("Facture saisie depuis plus de 24H. Date de saisie non modifiée.")
	  . "</div>";
	$date_mairie = $cur_dates['date_mairie'];
      } else {
	$changed = true;
      }
    }
    if ($timestamp_facture != strtotime($cur_dates['date_facture'])) {
      $changed = true;
    }
    db_execute("UPDATE facture SET dateFacture=?, dateFactureOrigine=?
                WHERE id=?",
	       array($date_mairie, $date_facture, $_REQUEST['id']));
    if ($changed) {
      echo "<div class='status'>" . _("Date modifiée.") . "</div>";
    }
    $form->setConstants(cur_dates());
  }
  $form->display();
}

include('templates/footer.php');
