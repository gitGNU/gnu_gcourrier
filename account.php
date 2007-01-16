<?php
/*
User accounts management
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
require_once('Structures/DataGrid.php');

require_once('init.php');
require_once('functions/db.php');
require_once('functions/user.php');
require_once('functions/grid.php');

include('templates/header.php');

function can_modify($login) {
  if ($_SESSION['login'] == 'admin')
    return true;
  if ($_SESSION['login'] == $login)
    return true;
  return false;
}

$form = new HTML_QuickForm_FR('creerCompteForm');
$form->addElement('header', 'title', 'Créer un compte');
$form->addElement('text', 'login', 'Identifiant');
$form->addElement('text', 'firstname', 'Prénom');
$form->addElement('text', 'lastname', 'Nom');

$services = array();
$req = "SELECT id, CONCAT(libelle, ' ', designation) AS description FROM service
        ORDER BY libelle";
$result = mysql_query($req) or die('erreur: '.mysql_error());
while($ligne = mysql_fetch_array($result))
     $services[$ligne['id']] = $ligne['description'];
$form->addElement('select', 'idService', 'Service', $services);

$form->addElement('password', 'password1', _("Mot de passe"));
$form->addElement('password', 'password2', _("Confirmez le mot de passe"));
$form->addElement('text', 'pagersize', _("Nombre d'éléments par page"));
$form->setDefaults(array('pagersize' => 50));
$form->addElement('submit', 'save', _("Enregistrer"));
$form->addElement('hidden', 'mode', 'create');

$form->applyFilter('login', 'trim');
$form->applyFilter('firstname', 'trim');
$form->applyFilter('lastname', 'trim');

$form->addRule('login', "Veuillez entrer un identifiant.", 'required', null, 'client');
$form->addRule('login', "L'identifiant ne peut contenir que des lettres.", 'lettersonly');
if ($form->exportValue('mode') == 'create')
     $form->addRule('login', "Cet identifiant existe déjà.", 'callback', 'user_exists_not');
else
     $form->addRule('login', "Cet identifiant n'existe pas.", 'callback', 'user_exists');
$form->addRule('login', "Vous ne pouvez pas modifier ce compte.", 'callback', 'can_modify');

$form->addRule('idService', NULL, 'required');
$form->addRule(array('password1', 'password2'),
	       'Les mots de passe ne correspondent pas.',
	       'compare', 'eq', 'client');
$form->addRule('mode', NULL, 'regex', '/^(create|modify)/');
$form->addRule('pagersize', _("Entrez un nombre entier."), 'required');
$form->addRule('pagersize', _("Entrez un nombre entier."), 'callback', 'ctype_digit');
$form->addRule('pagersize', _("Entrez un nombre entier."), 'nonzero');

if ($form->exportValue('mode') == 'create')
     $display_mode = 'create';
else
     $display_mode = 'modify';

if ($form->validate()) {
  // Redisplay the form in 'modify' mode
  $display_mode = 'modify';

  // Insertion des données dans la table utilisateur
  $form_values = $form->exportValues();
  
  if ($form_values['mode'] == 'create') {
    if ($_SESSION['login'] != 'admin') {
      echo "<div class='status'>"
	. _("Il faut être administrateur pour pouvoir créer un compte.") . "</div>";
    } else {
      db_execute("INSERT INTO utilisateur
                    (login, passwd,
                     prenom, nom,
                     idService, preferenceNbCourrier)
                  VALUES (?, ?,
                          ?, ?,
                          ?, ?)",
		 array($form_values['login'], $form_values['password1'],
		       $form_values['firstname'], $form_values['lastname'],
		       $form_values['idService'], $form_values['pagersize']));
      echo "<div class='status'>" . _("Compte créé.") . "</div>";
    }
  } else {
    $req = "UPDATE utilisateur SET
              prenom = ?,
              nom = ?,
              preferenceNbCourrier = ?";
    $sql_values = array($form_values['firstname'], $form_values['lastname'],
		    $form_values['pagersize']);
    // Don't change the password if it's left empty.
    if ($form_values['password1'] != '') {
      $pass = base64_encode($form_values['password1']);
      $req .= ", passwd = ?";
      $sql_values[] = $pass;
    }
    // Changing service only if admin
    if ($_SESSION['login'] == 'admin') {
      $req .= ", idService = ?";
      $sql_values[] = $form_values['idService'];
    }
    $req .= " WHERE login = ?";
    $sql_values[] = $form_values['login'];
    db_execute($req, $sql_values);
    echo "<div class='status'>" . _("Compte modifié.") . "</div>";    
  }
}

# Modify an existing user?
# Analyse the page parameters
$param_user = new HTML_QuickForm('modify_user', 'get');
$param_user->addElement('text', 'id');
$param_user->addRule('id', NULL, 'required');
$param_user->addRule('id', NULL, 'callback', 'ctype_digit');
$param_user->addRule('id', NULL, 'nonzero');
if ($param_user->validate()) {
  $id = $param_user->exportValue('id');
  $user = user_getbyid($id);
  if ($user != NULL) {
    $form->setDefaults($user);
    $display_mode = 'modify';
  }
}

if ($display_mode == 'modify') {
  $elt = $form->getElement('title');
  $elt->setText("Modifier le compte");
  $elt = $form->getElement('password1');
  $elt->setLabel("Changer de mot de passe");
  $elt = $form->getElement('login');
  $elt->freeze();
  $form->setConstants(array('mode' => 'modify'));
}
if ($_SESSION['login'] != 'admin') {
  $elt = $form->getElement('idService');
  $elt->freeze();
}

$form->display();

if ($_SESSION['login'] == 'admin') {
  if ($display_mode != 'create') {
    echo "<div><a href='?'>Nouveau Compte</a></div>";
  }
  
  
  function printModify($params) {
    return "<a href='?id={$params['record']['id']}'>M</a>";
  }
  
  // Instantiate the DataGrid
  $dg = new Structures_DataGrid($_SESSION['pagersize']);
  $dg->setDefaultSort(array('login' => 'ASC'));
  $test = $dg->bind('SELECT utilisateur.id AS id, login,
    nom AS lastname, prenom AS firstname,
    service.designation AS service,
    preferenceNbCourrier AS pagersize
    FROM utilisateur, service WHERE idService=service.id',
		   array('dsn' => $db_dsn));
  if (PEAR::isError($test)) {
    echo $test->getMessage();
    exit;
  }
  
  $dg->addColumn(new Structures_DataGrid_Column('Identifiant', 'login', 'login'));
  $dg->addColumn(new Structures_DataGrid_Column('Nom', 'lastname', 'lastname'));
  $dg->addColumn(new Structures_DataGrid_Column('Prénom', 'firstname', 'firstname'));
  $dg->addColumn(new Structures_DataGrid_Column('Service', 'service', 'service'));
  $dg->addColumn(new Structures_DataGrid_Column('Nb', 'pagersize', 'pagersize'));
  $dg->addColumn(new Structures_DataGrid_Column('Modifier', null,null,
						array('style' => 'text-align: center'),
						null, 'printModify'));
  grid_table($dg, _("Comptes existants"));
}

include('templates/footer.php');
