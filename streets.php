<?php
/*
Streets name management
Copyright (C) 2005, 2006, 2007, 2008, 2009, 2010  Cliss XXI

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
*/


require_once('classes/HTML/QuickForm/FR.php');
require_once('classes/SQLDataGrid.php');

require_once('init.php');
require_once('functions/db.php');
require_once('functions/street.php');
require_once('functions/grid.php');
require_once('functions/text.php');

include('templates/header.php');

#if ($_SESSION['login'] != 'admin') {
#  echo _("Vous n'êtes pas administrateur!");
#  include('templates/footer.php');
#  exit;
#}

$form = new HTML_QuickForm_FR('StreetModifyForm');
$form->addElement('header', 'title', _('Ajouter une rue'));

$form->addElement('text', 'label', _('Nom'));
$form->addElement('hidden', 'id');
$form->addElement('hidden', 'mode', 'create');
$form->addElement('submit', 'save', _("Enregistrer"));

$form->applyFilter('label', 'trim');

$form->addRule('label', _("Ce champ est requis"), 'required');
$form->addRule('id', _("Entrez un nombre"), 'nonzero');
$form->addRule('mode', NULL, 'regex', '/^(create|modify)/');

if ($form->exportValue('mode') == 'create')
     $display_mode = 'create';
else
     $display_mode = 'modify';


$param_user = new GPLQuickForm('street_modify', 'get');
$param_user->addElement('text', 'id');
$param_user->addRule('id', NULL, 'required');
$param_user->addRule('id', NULL, 'callback', 'ctype_digit');
$param_user->addRule('id', NULL, 'nonzero');
if ($param_user->validate()) {
  $id = $param_user->exportValue('id');
  $street = street_getbyid($id);
  if ($street != NULL) {
    $form->setDefaults($street);
    $display_mode = 'modify';
  }
}


if ($display_mode == 'modify') {
  $elt1 = $form->getElement('title');
  $elt1->setText("Modifier la rue");
  $form->setConstants(array('mode' => 'modify'));
}


// Apply the changes
if ($form->validate()) {
  // Insertion des données dans la table utilisateur
  $form_values = $form->exportValues();
  
  if ($form_values['mode'] == 'create') {
    $values = $form->exportValues();
    street_new($values['label']);
    text_notice(_("Rue créée."));
  } else {
    $values = $form->exportValues();
    street_modify($values['id'], $values['label']);
    text_notice(_("Rue modifiée."));
    header('Location: ?');
    exit();
  }

  // Redisplay the form in 'modify' mode
  $display_mode = 'modify';
}

$form->display();




$requeteFacture = "SELECT id, label FROM street";

$sdg = new SQLDataGrid($requeteFacture,
		       array('No' => array('sqlcol' => 'id'),
			     'Nom' => array('sqlcol' => 'label'),
			     _('Modifier') => array('style' => 'text-align: center',
						    'callback' => 'printModify'),
			     _('Suppr.') => array('style' => 'text-align: center',
						    'callback' => 'printDelete'),
			     ));

$sdg->setTitle(_("Rues existantes"));
$sdg->setPagerSize($_SESSION['pagersize']);
$sdg->setDefaultSort(array('id' => 'ASC'));
if (isset($_GET['id']))
{
  $sdg->setDefaultPageWhere(array('id' => $_GET['id']));
}
$sdg->display();


function printModify($params) {
  return "<a href='?id={$params['record']['id']}'>M</a>";
}

function printDelete($params) {
  return "<form action='street_delete.php' method='post'>"
    . "<input type='hidden' name='id' value='{$params['record']['id']}' />"
    . "<input type='submit' value='"._("Supprimer")."' />"
    . "</form>";
}

include('templates/footer.php');
