<?php
/*
Organization units management
Copyright (C) 2005, 2006, 2007  Cliss XXI

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
require_once('functions/service.php');
require_once('functions/grid.php');
require_once('functions/text.php');

include('templates/header.php');

if ($_SESSION['login'] != 'admin') {
  echo _("Vous n'êtes pas administrateur!");
  include('templates/footer.php');
  exit;
}

$form = new HTML_QuickForm_FR('modifyServiceForm');
$form->addElement('header', 'title', _('Créer un service'));

$form->addElement('text', 'label', _('Libellé'));
$form->addElement('text', 'description', _('Désignation'));
$form->addElement('text', 'email', _('Courriel'));
$form->addElement('hidden', 'mode', 'create');
$form->addElement('submit', 'save', _("Enregistrer"));

$form->applyFilter('label', 'trim');
$form->applyFilter('description', 'trim');
$form->applyFilter('email', 'trim');

$form->addRule('label', _("Ce champ est requis"), 'required');
$form->addRule('label', _("Entrez uniquement des lettres et des chiffres"),
	       'callback', 'ctype_alnum');
if ($form->exportValue('mode') == 'create')
     $form->addRule('label', _("Ce service existe déjà"), 'callback', 'service_exists_not');
else
     $form->addRule('label', _("Ce service n'existe pas"), 'callback', 'service_exists');
$form->addRule('mode', NULL, 'regex', '/^(create|modify)/');
if ($form->exportValue('mode') == 'create')
     $form->addRule('label', "Ce libellé existe déjà.", 'callback', 'service_exists_not');
else
     $form->addRule('label', "Cet identifiant n'existe pas.", 'callback', 'service_exists');

if ($form->exportValue('mode') == 'create')
     $display_mode = 'create';
else
     $display_mode = 'modify';


$param_user = new GPLQuickForm('modify_unit', 'get');
$param_user->addElement('text', 'id');
$param_user->addRule('id', NULL, 'required');
$param_user->addRule('id', NULL, 'callback', 'ctype_digit');
$param_user->addRule('id', NULL, 'nonzero');
if ($param_user->validate()) {
  $id = $param_user->exportValue('id');
  $unit = service_getbyid($id);
  if ($unit != NULL) {
    $form->setDefaults($unit);
    $display_mode = 'modify';
  }
}


if ($display_mode == 'modify') {
  $elt1 = $form->getElement('title');
  $elt1->setText("Modifier le service");
  $elt3 = $form->getElement('label');
  $elt3->freeze();
  $form->setConstants(array('mode' => 'modify'));
}

if ($form->validate()) {
  // Redisplay the form in 'modify' mode
  $display_mode = 'modify';

  // Insertion des données dans la table utilisateur
  $form_values = $form->exportValues();
  
  if ($form_values['mode'] == 'create') {
    $values = $form->exportValues();
    service_new($values['label'], $values['description'], $values['email']);
    text_notice(_("Service créé."));
  } else {
    $values = $form->exportValues();
    service_modify($values['label'], $values['description'], $values['email']);
    text_notice(_("Service modifié."));
  }
}

$form->display();



function printModify($params) {
  return "<a href='?id={$params['record']['id']}'>M</a>";
}

$sdg = new SQLDataGrid('SELECT id,
    libelle AS label,
    designation AS description,
    email
    FROM service',
    array(_('Libellé') => 'label',
	  _('Désignation') => 'description',
	  _('Courriel') => 'email',
	  _('Modifier') => array('style' => 'text-align: center',
			      'callback' => 'printModify')));
$sdg->setTitle(_("Services existants"));
$sdg->setPagerSize($_SESSION['pagersize']);
$sdg->setDefaultSort(array('label' => 'ASC'));
if (isset($_GET['id']))
{
  $sdg->setDefaultPageWhere(array('id' => $_GET['id']));
}
$sdg->display();


include('templates/footer.php');
