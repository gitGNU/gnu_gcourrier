<?php
/*
GCourrier
Copyright (C) 2009  Cliss XXI

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

require_once('classes/HTML/QuickForm/FR.php');

require_once('init.php');
include('functions/invoice.php');

include('templates/header.php');

$param_id = new HTML_QuickForm_FR('bill_id', 'get');
$param_id->addElement('text', 'id');
$param_id->addRule('id', "ID is required.", 'required');
$param_id->addRule('id', "ID must be a digit.", 'callback', 'ctype_digit');
$param_id->addRule('id', "ID must be positive.", 'nonzero');
if (!$param_id->validate()) {
  print $param_id->getElementError('id');
  exit;
}
$id = $param_id->exportValue('id');

$invoice = invoice_getbyid($id);

if (empty($invoice['internal_timestamp']) or $invoice['internal_timestamp'] < (time()-60*60*24))
  {
    echo _("Cette facture a été saisie il y a plus de 24h.");
    echo "<br />";
    echo ("Il n'est plus possible de l'annuler.");
  }
else if ($invoice['idServiceCreation'] != $_SESSION['idService'])
  {
    echo _("Cette facture n'a pas été émise par votre service.");
  }
else
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
      {
	// supprimer la facture
	invoice_delete($id);
	printf(_("La facture numéro %d a bien été annulée."), $id);
      }
    else
      {
	echo "<p>";
	echo _("Êtes-vous sûr(e) de vouloir annuler cette facture et l'effacer définitivement?");
	echo "</p><p>";
	echo $invoice['observation'] . " (" . $invoice['montant'] . "€)";
	echo "</p>";
?>
<br />
<form method="POST" action="?id=<?php echo $id;?>">
  <input type="submit" value="Confirmer" />
</form>
<?php
      }
  }

include('templates/footer.php');
