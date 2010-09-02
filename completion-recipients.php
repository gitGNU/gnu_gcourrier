<?php
/*
GCourrier
Copyright (C) 2007, 2010  Cliss XXI

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

author Sylvain Beucler
*/
require_once('init.php');
require_once('functions/db.php');

$result = db_execute('SELECT id, nom, prenom, adresse, codePostal, ville FROM destinataire WHERE
                      CONCAT(nom, " ", prenom) LIKE ? ORDER BY nom LIMIT 50',
		     array("%{$_POST['autocomplete_parameter']}%"));
echo "<ul>";

while ($line = mysql_fetch_array($result)) {
  echo "<li id='{$line['id']}'>{$line['nom']} {$line['prenom']}"
    . "<br /><span style='font-size: smaller;'>"
    . "{$line['adresse']} {$line['codePostal']} {$line['ville']}"
    . "</span></li>";
}
#foreach ($_POST as $key => $value) {
#  echo "<li>$key=$value</li>";
#}

echo "</ul>";
