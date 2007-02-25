<?php
require_once('init.php');
require_once('functions/db.php');

$result = db_execute('SELECT id, nom, prenom FROM destinataire WHERE
                      CONCAT(nom, " ", prenom) LIKE ? ORDER BY nom', 
		     array("%{$_POST['autocomplete_parameter']}%"));
echo "<ul>";

while ($line = mysql_fetch_array($result)) {
  echo "<li id='{$line['id']}'>{$line['nom']} {$line['prenom']}</li>";
}
#foreach ($_POST as $key => $value) {
#  echo "<li>$key=$value</li>";
#}

echo "</ul>";
