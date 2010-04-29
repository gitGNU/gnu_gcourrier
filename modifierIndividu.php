<?php
/*
GCourrier
Copyright (C) 2005, 2006, 2007, 2010  Cliss XXI

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

author VELU Jonathan, Sylvain Beucler
*/

require_once('init.php');
require_once('functions/db.php');

include('templates/header.php');
?>

<p>Modifier le destinataire ou fournisseur:</p>

<form name="creerFactureForm" action="modifierIndividu2.php">
<input type="hidden" name="fournisseur" id="fournisseur" />
<table>
<tr>
  <td valign="top">
    Rechercher:<br /><span id="indicator1" style="display: none">En cours...</span>
  </td>
  <td>
    <input type="text" id="autocomplete" name="autocomplete_parameter" size=50 /><br />
    <div id="autocomplete_choices" class="autocomplete"></div>
    Utiliser % comme joker
  </td>
</tr>
</table>

<script type="text/javascript" language="javascript">
// <![CDATA[
  new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "completion-recipients.php", {
    indicator: 'indicator1', afterUpdateElement : getSelectionId});

function getSelectionId(text, li) {
  document.getElementById('fournisseur').value = li.id;
}
// ]]>
</script>
  <br />

  <input type="submit" value="Modifier" />
</form>

<?php
include('templates/footer.php');
