<?php
/*
Organization units management
Copyright (C) 2005, 2006, 2007, 2008, 2009  Cliss XXI

This file is part of GCourrier.

GCourrier is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation, either version 3 of the License, or (at your
option) any later version.

GCourrier is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see http://www.gnu.org/licenses/.
*/


require_once('classes/HTML/QuickForm/FR.php');
require_once('classes/SQLDataGrid.php');

require_once('init.php');
require_once('functions/db.php');
require_once('functions/priority.php');
require_once('functions/grid.php');
require_once('functions/text.php');

include('templates/header.php');

if ($_SESSION['login'] != 'admin') {
  echo _("Vous n'êtes pas administrateur!");
  include('templates/footer.php');
  exit;
}

$form = new HTML_QuickForm_FR('modifyPriorityForm');
$form->addElement('header', 'title', _('Créer une priorité'));

$form->addElement('text', 'designation', _('Désignation'));
$form->addElement('text', 'nbJours', _('Nombre de jours'));
$form->addElement('checkbox', 'defautCourrier', _('Par défaut pour les nouveaux courriers'));
$form->addElement('checkbox', 'defautFacture', _('Par défaut pour les nouvelles factures'));
$form->addElement('hidden', 'id');
$form->addElement('hidden', 'mode', 'create');
$form->addElement('submit', 'save', _("Enregistrer"));

$form->applyFilter('designation', 'trim');

$form->addRule('designation', _("Ce champ est requis"), 'required');
$form->addRule('nbJours', _("Ce champ est requis"), 'required');
$form->addRule('nbJours', _("Entrez un nombre"), 'callback', 'ctype_digit');
$form->addRule('id', _("Entrez un nombre"), 'nonzero');
$form->addRule('mode', NULL, 'regex', '/^(create|modify)/');

if ($form->exportValue('mode') == 'create')
     $display_mode = 'create';
else
     $display_mode = 'modify';


$param_user = new GPLQuickForm('modify_priority', 'get');
$param_user->addElement('text', 'id');
$param_user->addRule('id', NULL, 'required');
$param_user->addRule('id', NULL, 'callback', 'ctype_digit');
$param_user->addRule('id', NULL, 'nonzero');
if ($param_user->validate()) {
  $id = $param_user->exportValue('id');
  $priority = priority_getbyid($id);
  if ($priority != NULL) {
    $form->setDefaults($priority);
    $display_mode = 'modify';
  }
}


if ($display_mode == 'modify') {
  $elt1 = $form->getElement('title');
  $elt1->setText("Modifier la priorité");
  $form->setConstants(array('mode' => 'modify'));
}


// Apply the changes
if ($form->validate()) {
  // Insertion des données dans la table utilisateur
  $form_values = $form->exportValues();
  
  if ($form_values['mode'] == 'create') {
    $values = $form->exportValues();
    priority_new($values['designation'], $values['nbJours'],
		 $values['defautCourrier'], $values['defautFacture']);
    text_notice(_("Priorité créée."));
  } else {
    $values = $form->exportValues();
    priority_modify($values['id'], $values['designation'], $values['nbJours'],
		    $values['defautCourrier'], $values['defautFacture']);
    text_notice(_("Priorité modifiée."));
  }

  // Redisplay the form in 'modify' mode
  $display_mode = 'modify';
}

$form->display();




$requeteFacture = "SELECT id, designation, nbJours, defautCourrier, defautFacture
 		   FROM priorite";

$sdg = new SQLDataGrid($requeteFacture,
		       array('No' => array('sqlcol' => 'id'),
			     'Désignation' => array('sqlcol' => 'designation'),
			     'Nb.jours' => array('sqlcol' => 'nbJours'),
			     'Défaut courrier' => array('sqlcol' => 'defautCourrier',
							'callback' => 'printDefautCourrier'),
			     'Défaut facture' => array('sqlcol' => 'defautFacture',
						      'callback' => 'printDefautFacture'),
			     _('Modifier') => array('style' => 'text-align: center',
						    'callback' => 'printModify'),
			     ));

$sdg->setTitle(_("Priorités existantes"));
$sdg->setPagerSize($_SESSION['pagersize']);
$sdg->setDefaultSort(array('id' => 'ASC'));
if (isset($_GET['id']))
{
  $sdg->setDefaultPageWhere(array('id' => $_GET['id']));
}
$sdg->display();


function printDefautCourrier($params)
{
  $row = $params['record'];
  return "<input type='checkbox' disabled='1' " . ($row['defautCourrier'] ? "checked='checked' " : "") . "/>";
}

function printDefautFacture($params)
{
  $row = $params['record'];
  return "<input type='checkbox' disabled='1' " . ($row['defautFacture'] ? "checked='checked' " : "") . "/>";
}

function printModify($params) {
  return "<a href='?id={$params['record']['id']}'>M</a>";
}

include('templates/footer.php');
