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


require_once('connexion.php');
require_once('functions/user.php');
require_once('HTML/QuickForm.php');

function can_modify($login) {
  if ($_SESSION['login'] == 'admin')
    return true;
  if ($_SESSION['login'] == $login)
    return true;
  return false;
}

$form = new HTML_QuickForm('creerCompteForm');
$form->setRequiredNote('<span style="font-size:80%; color:#ff0000;">*</span><span style="font-size:80%;"> champ requis</span>');
$form->setJsWarnings("Le formulaire n'est pas valide:", '');
$form->addElement('header', 'title', 'Créer un compte');
$form->addElement('text', 'login', 'Identifiant');
$form->addElement('text', 'firstname', 'Nom');
$form->addElement('text', 'lastname', 'Prénom');

$services = array();
$req = "SELECT id, CONCAT(libelle, ' ', designation) AS description FROM service
        ORDER BY libelle";
$result = mysql_query($req) or die('erreur: '.mysql_error());
while($ligne = mysql_fetch_array($result))
     $services[$ligne['id']] = $ligne['description'];
$form->addElement('select', 'idService', 'Service', $services);

$form->addElement('password', 'password1', 'Mot de passe');
$form->addElement('password', 'password2', 'Confirmez le mot de passe');
$form->addElement('submit', 'enregistrer', 'Enregistrer');
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
$form->addRule('mode', NULL, 'regexp', 'create|update');

include('templates/header.php');

if ($form->exportValue('mode') == 'create')
     $display_mode = 'create';
else
     $display_mode = 'modify';

if ($form->validate()) {
  // Redisplay the form in 'modify' mode
  $display_mode = 'modify';

  // Insertion des données dans la table utilisateur
  $form->applyFilter('__ALL__', 'mysql_real_escape_string');
  $values = $form->exportValues();
  
  if ($values['mode'] == 'create') {
    if ($_SESSION['login'] != 'admin') {
      echo "<div class='status'>"
	. _("Il faut être administrateur pour pouvoir créer un compte.") . "</div>";
    } else {
      $requete = "INSERT INTO utilisateur (login, passwd, nom, prenom, idService)
                    VALUES ('{$values['login']}', '{$values['password1']}',
                            '{$values['lastname']}', '{$values['firstname']}',
                            '{$values['idService']}')";
      $result = mysql_query($requete) or die(mysql_error());
      echo "<div class='status'>" . _("Compte créé.") . "</div>";
    }
  } else {
    $req = "UPDATE utilisateur SET
              nom = '{$values['lastname']}',
              prenom = '{$values['firstname']}',
              idService = '{$values['idService']}'";
    # Don't change the password if it's left empty.
    if ($values['password1'] != '') {
      $pass = base64_encode($values['password1']);
      $req .= ", passwd = '$pass'";
    }
    $req .= " WHERE login = '{$values['login']}'";
    mysql_query($req) or die(mysql_error());
    echo "<div class='status'>" . _("Compte modifié.") . "</div>";    
  }
}

# Modify an existing user?
# Analyse the page parameters
$param_user = new HTML_QuickForm('modify_user', 'get');
$param_user->addElement('text', 'id');
$param_user->addRule('id', NULL, 'numeric');
if ($param_user->validate()) {
  $id = $param_user->exportValue('id');
  $form->setDefaults(user_getbyid_assoc($id));
  $display_mode = 'modify';
}

if ($display_mode == 'modify') {
  $form->getElement('title')->setText("Modifier un compte");
  $form->getElement('password1')->setLabel("Changer de mot de passe");
  $form->getElement('login')->freeze();
  $form->setConstants(array('mode' => 'modify'));
}

$form->display();

if ($_SESSION['login'] == 'admin') {
 if ($display_mode != 'create') {
   echo "<a href='?'>Nouveau Compte</a>";
 }
?>

<table class="userlist">
<tr><th colspan="5">Comptes existants</th></tr>
<tr>
<th>Login</th>
<th>Nom</th>
<th>Prénom</th>
<th>Service</th>
<th>Modifier</th>
</tr>

<?php
$req = "SELECT utilisateur.login AS login, utilisateur.id AS idUser,
               utilisateur.nom AS nomUser, utilisateur.prenom AS prenomUser,
               service.designation AS descService
        FROM utilisateur, service WHERE utilisateur.idService=service.id";
$result = mysql_query($req) or die(mysql_error());
$boul = 0;
while($ligne = mysql_fetch_array($result)){
if($boul == 0) {
  $class = 'odd';
  $boul = 1;
} else {
  $class = 'even';
  $boul = 0;	
}
 echo "<tr><td class='$class'>{$ligne['login']}</td>
           <td class='$class'>{$ligne['nomUser']}</td>
           <td class='$class'>{$ligne['prenomUser']}</td>
           <td class='$class'>{$ligne['descService']}</td>
           <td style='text-align:center' class='$class'><a href='?id={$ligne['idUser']}'>m</a></td>
       </tr>";
}
 echo "</table>"; 
}
include('templates/footer.php');
