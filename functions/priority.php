<?php
/*
Priority input form
Copyright (C) 2007  Cliss XXI

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

function priority_display() {
?>
      <tr>
        <td>Priorité</td>
	<td>
          <?php
          $requete = "SELECT id, designation, nbJours FROM priorite";
          $result = mysql_query($requete) or die(mysql_error());

          if (mysql_num_rows($result) > 1) {
	    echo "<select name='priorite'>";

	    while($ligne = mysql_fetch_array($result)){
	      echo "<option value='{$ligne['id']}'>
                    {$ligne['designation']} ({$ligne['nbJours']} j.)
                  </option>";
	    }

	    echo "</select>";
          } else if (mysql_num_rows($result) == 0) {
	    echo "Veuillez contacter l'admin pour ajouter une priorité";
	  } else { /* Only one result */
	    $ligne = mysql_fetch_array($result);
	    echo "<input type='hidden' name='priorite' value='{$ligne['id']}' />";
	    echo "{$ligne['designation']} ({$ligne['nbJours']} j.)";
	  }
          ?>
        </td>
      </tr>
<?php
}
