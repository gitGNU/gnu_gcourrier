<?php
/*
Streets name selection
Copyright (C) 2010  Cliss XXI

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
*/

require_once('init.php');
require_once('classes/SQLDataGrid.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head><title>Rues</title></head>
<body>

<?php
function printLabel($params) {
  extract($params);
  return "<a href=\"javascript:select('" . addslashes($record[$fieldName])
    . "');\">$record[$fieldName]</a>";
}

$sdg = new SQLDataGrid("SELECT label FROM street",
		       array('Nom des rues' => array('sqlcol' => 'label',
						     'callback' => 'printLabel')));
$sdg->setDefaultSort(array('label' => 'ASC'));
$sdg->display();
?>

<script type="text/javascript">
// <![CDATA[
function select(text) {
  w = window.opener.document.getElementById('address')
  v = w.getValue();
  if (v != '' && v[v.length-1] != ' ')
    v += ' ';
  w.setValue(v + text);
  window.close();
}
// ]]>
</script>

</body></html>
