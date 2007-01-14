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
*/

require_once('classes/HTML/QuickForm/FR.php');
require_once('functions/db.php');
require_once('functions/service.php');
require_once('functions/text.php');

require_once('init.php');
include('templates/header.php');

$form = new HTML_QuickForm_FR('creerService');
$form->addElement('header', 'title', 'Créer un service');
$form->addElement('text', 'libelle', 'Libellé/Abréviation');
$form->addElement('text', 'designation', 'Désignation');
$form->addElement('submit', null, 'Créer');

$form->addRule('libelle', _("Ce champ est requis"), 'required');
$form->addRule('libelle', _("Entrez uniquement des lettres et des chiffres"),
	       'callback', 'ctype_alphanum');
$form->addRule('libelle', _("Ce service existe déjà"), 'callback', 'service_exists_not');

if ($form->validate()) {
  $values = $form->exportValues();
  service_new($values['libelle'], $values['designation']);
  text_notice(_("Service créé."));
  // empty the form
  $form->setConstants(array('libelle' => '', 'designation' => ''));
}

$form->display();
include('templates/footer.php');
