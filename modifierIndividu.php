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
require_once('functions/contact.php');

include('templates/header.php');
?>

<p>Modifier le destinataire ou fournisseur:</p>

<form name="creerFactureForm" action="modifierIndividu2.php">
<table>
<tr>
  <td valign="top">
    Rechercher:
  </td>
  <td>
<?php
  contact_display();
?>
  </td>
</tr>
</table>

  <br />

  <input type="submit" value="Modifier" />
</form>

<?php
include('templates/footer.php');
