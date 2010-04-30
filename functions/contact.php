<?php
/*
Contact input form with auto-completion
Copyright (C) 2007, 2010  Cliss XXI

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

require_once(dirname(__FILE__) . '/db.php');

function contact_display($default_id=NULL) {
  $default_name = "";
  if ($default_id != NULL)
    {
      $result = db_execute('SELECT nom, prenom FROM destinataire'
			   . ' WHERE id=?', array($default_id));
      $row = mysql_fetch_array($result);
      $default_name = $row['nom'] . ' ' . $row['prenom'];
    }
?>
<input type="hidden" name="contact_id" id="contact_id" value="<?php echo $default_id; ?>" />
<div style="font-size: smaller; position: relative;">
  <a href="creerDestinataire.php">Créer un nouveau</a>
  - Utilisez % comme joker
  <div style="position:absolute; top: 0; right: 0; display: none;" id="indicator1">En cours...</div>
</div>
<input type="text" id="autocomplete" name="autocomplete_parameter" size=50 value="<?php echo $default_name; ?>"/>
<div id="autocomplete_choices" class="autocomplete"></div>
<script type="text/javascript" language="javascript">
// <![CDATA[
  new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "completion-recipients.php", {
    indicator: 'indicator1', afterUpdateElement : getSelectionId});

function getSelectionId(text, li) {
  document.getElementById('contact_id').value = li.id;
}
// ]]>
</script>
<?php
}
