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
*/

require_once('HTML/QuickForm.php');
require_once('HTML/Table.php');
require_once('Structures/DataGrid.php');

require_once('connexion.php');
include('functions/grid.php');

include('templates/header.php');

$param_id = new HTML_QuickForm('bill_id', 'get');
$param_id->addElement('text', 'id');
$param_id->addRule('id', "ID is required.", 'required');
$param_id->addRule('id', "ID must be a digit.", 'callback', 'ctype_digit');
$param_id->addRule('id', "ID must be positive.", 'nonzero');
if (!$param_id->validate()) {
  print $param_id->getElementError('id');
  exit;
}
$id = $param_id->exportValue('id');


function print_date($params) {
  extract($params);
  if(strcmp($record[$fieldName], '0000-00-00') == 0)
    return _("Non renseigné");
  else
    return $record[$fieldName];
}

$dg = new Structures_DataGrid($_SESSION['pagersize']);
$test= $dg->bind("SELECT
    CONCAT(service.libelle, ' ', service.designation) AS service,
    estTransmisCopie.dateTransmission AS date_transmitted,
    estTransmisCopie.dateRetour AS date_returned
  FROM facture, estTransmisCopie, service
  WHERE facture.id = '$id'
    AND facture.idServiceCreation = '{$_SESSION['idService']}'
    AND facture.id = estTransmisCopie.idFacture
    AND estTransmisCopie.idService = service.id",
		 array('dsn' => 'mysql://root@localhost/gcourrier'));
if (PEAR::isError($test)) echo $test->getMessage();

$dg->addColumn(new Structures_DataGrid_Column('Service', 'service'));
$dg->addColumn(new Structures_DataGrid_Column('Date de transmission', 'date_transmitted',
					      null, null, null, 'print_date'));
$dg->addColumn(new Structures_DataGrid_Column('Date de retour', 'date_returned',
					      null, null, null, 'print_date'));
grid_table($dg, _("Historique pour la facture n°") . $id);

include('templates/footer.php');
