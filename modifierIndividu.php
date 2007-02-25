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

require_once('init.php');
require_once('functions/db.php');

include('templates/header.php');
?>
<script src="javascripts/prototype.js" type="text/javascript"></script>
<script src="javascripts/scriptaculous.js?load=effects,controls" type="text/javascript"></script>

Modifier le destinataire ou fournisseur:
<form name="creerFactureForm" action="modifierIndividu2.php">
  <select name="fournisseur" id="fournisseur">

<?php
$req = "SELECT * FROM destinataire ORDER BY nom";
$result = db_execute($req) or die(mysql_error());

while ($ligne = mysql_fetch_array($result)) {
  echo "<option value='{$ligne['id']}'>";
  echo htmlspecialchars("{$ligne['nom']} {$ligne['prenom']}");
  echo "</option>\n";
}
?>

  </select>
<br />

Rechercher: <input type="text" id="autocomplete" name="autocomplete_parameter" size=50 />
<span id="indicator1" style="display: none">En cours...</span>
<div id="autocomplete_choices" class="autocomplete"></div>

<script type="text/javascript" language="javascript">
// <![CDATA[
  new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "completion-recipients.php",
    {indicator: 'indicator1', afterUpdateElement : getSelectionId});

function getSelectionId(text, li) {
  options = document.getElementById('fournisseur').options;
  for (i = 0; i < options.length; i++)
    if (options[i].value == li.id)
      document.getElementById('fournisseur').selectedIndex = i;
}
// ]]>
</script>
  <br />

  <input type="submit" value="Modifier" />
</form>

<?php
include('templates/footer.php');

