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
$param_user = new GPLQuickForm('modify_user', 'get');
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
  $elt1 = $form->getElement('title');
  $elt1->setText("Modifier le compte");
  $elt2 = $form->getElement('password1');
  $elt2->setLabel("Changer de mot de passe");
  $elt3 = $form->getElement('login');
  $elt3->freeze();
  $form->setConstants(array('mode' => 'modify'));
}
if ($_SESSION['login'] != 'admin') {
  $elt4 = $form->getElement('idService');
  $elt4->freeze();
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


  
  $query = 'SELECT utilisateur.id AS id, login,
    nom AS lastname, prenom AS firstname,
    service.designation AS service,
    preferenceNbCourrier AS pagersize
    FROM utilisateur, service WHERE idService=service.id';
  $default_sort = array('login' => 'ASC', 'firstname' => 'DESC');
  $pager_size = $_SESSION['pagersize'];
  $page = 1;
  $cols = array('Identifiant' => 'login',
		'Nom' => 'lastname',
		'Prénom' => 'firstname',
		'Service' => 'service',
		'Nb' => 'pagersize',
		'Modifier' => array('style' => 'text-align: center',
				    'callback' => 'printModify'));
  
  
  // ORDER BY..
  $order = '';
  $order_field = null;
  $order_direction = null;
  if (isset($_GET['orderBy']))
    {
      $order_field = $_GET['orderBy'];
      $order = "ORDER BY $order_field";
      if (isset($_GET['direction']))
	{
	  $order_direction = $_GET['direction'];
	  $order .= " $order_direction";
	}
    }
  else if (isset($default_sort))
    {
      $first = true;
      foreach($default_sort AS $field => $direction)
	{
	  if ($first)
	    {
	      $order_field = $field;
	      $order_direction = $direction;
	      $order = "ORDER BY";
	      $first = false;
	    }
	  else
	    {
	      $order .= ",";
	    }
	  $order .= " $field $direction";
	}
    }


  // LIMIT
  $limit = '';


  if (isset($_GET['page']))
    {
      $page = $_GET['page'];
    }
  else
    {
      $location_field = 'id';
      $location_value = $_GET['id'];

      // Implementation 1: naive
      /* on y va bourin */
      $res = db_execute($query . " $order");
      $pos = 0;
      while($row = mysql_fetch_array($res))
	{
	  if ($row['id'] == $location_value)
	    break;
	  $pos++;
	}
      // $pos => ok

      // -------

      // Implementation 2: more efficient, but incorrect
      /* This requires $order_field to be the actual field name, while
      it could be an alias (select another_field AS order_field), or
      worse, a computation (select (field1 + field2) AS order_field */
      /*
      $res = db_execute("SELECT $order_field FROM utilisateur WHERE $location_field=$location_value");
      $target_row = mysql_fetch_array($res);
      preg_match('/select .* ( from .*)/is', $query, $matches);
      $query2 = "SELECT COUNT(*) AS pos {$matches[1]} AND $order_field < '{$target_row[$order_field]}'";
      $res = db_execute($query2);
      $row = mysql_fetch_array($res);
      $pos = $row['pos'];
      */

      $page = floor($pos / $pager_size) + 1;
    }

  if ($pager_size)
    $limit = "LIMIT " . ($pager_size * ($page-1)) . ",$pager_size";

  $res = db_execute($query . " $order $limit");


  // Make columns params tidy
  foreach($cols as $name => $params)
    if (!is_array($params))
      $cols[$name] = array('sqlcol' => $params);

  print '<table>';

  // Titles
  print "<tr style='background: #CCCCCC'>";
  foreach($cols as $label => $params)
    {
      print "<th>";
      if (isset($params['sqlcol']))
	{
	  $direction = 'ASC';
	  if ($params['sqlcol'] == $order_field
	      and $order_direction == 'ASC')
	    $direction = 'DESC';

	  // Filter GET parameters
	  $myget = $_GET;
	  $myget['orderBy'] = $params['sqlcol'];
	  $myget['direction'] = $direction;
	  $myget['page'] = 1;
	  $first = true;
	  foreach($myget as $name => $value)
	    {
	      if ($first)
		{
		  $link = '?';
		  $first = false;
		}
	      else
		{
		  $link .= '&';
		}
	      $link .= "$name=$value";
	    }

	  print "<a href='{$link}'>";
	  print "$label";
	  // up/down vertical arrow
	  if ($params['sqlcol'] == $order_field)
	    if ($order_direction == 'ASC')
	      print '&uArr;';
	    else
	      print '&dArr;';
	  print "</a>";
	}
      else
	{
	  print "$label";
	}
      print "</th>";
    }
  print '</tr>';

  $i = 0;
  while($record = mysql_fetch_array($res))
    {
      if ($i % 2 == 0)
	$row_class = 'even';
      else
	$row_class = 'odd';
      $i++;
      
      print "<tr class='$row_class'>";
      foreach($cols as $label => $params)
	{
	  $style = '';
	  if (isset($params['style']))
	    $style .= $params['style'] . ' ';
	  if (empty($style))
	    print '<td>';
	  else
	    print "<td style='$style'>";

	  if (isset($params['callback']))
	    print call_user_func($params['callback'],
				 array('record' => $record));
	  else if (isset($params['sqlcol']))
	    print $record[$params['sqlcol']];
	  print '</td>';
	}
      print '</tr>';
    }
  
  print '</table>';
}

include('templates/footer.php');
