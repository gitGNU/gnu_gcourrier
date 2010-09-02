<?php
/*
GCourrier
Copyright (C) 2005, 2006, 2010  Cliss XXI

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
require_once('classes/SQLDataGrid.php');

require_once('init.php');
include('functions/grid.php');

include('templates/header.php');

$param_id = new HTML_QuickForm_FR('invoice_id', 'get');
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
  if ($record[$fieldName] == '0000-00-00')
    return _("Non renseigné");
  else
    return strftime("%x", $record[$fieldName]);
}

function print_date_returned($params) {
  extract($params);
  if ($record[$fieldName] == '0')
    echo "<a href='dateRetour.php?idCourrier=".$record['idTransmis']."'>ajouter</a></td>";
  else
    return print_date($params);
}

echo '<p>' . _("Historique pour la facture n°") . $id . '</p>';

$query = "SELECT
    CONCAT(service.libelle, ' ', service.designation) AS service,
    UNIX_TIMESTAMP(estTransmisCopie.dateTransmission) AS date_transmitted,
    UNIX_TIMESTAMP(estTransmisCopie.dateRetour) AS date_returned,
    estTransmisCopie.id as idTransmis
  FROM facture, estTransmisCopie, service
  WHERE facture.id = '$id'
    AND facture.idServiceCreation = '{$_SESSION['idService']}'
    AND facture.id = estTransmisCopie.idFacture
    AND estTransmisCopie.idService = service.id";
$sdg = new SQLDataGrid($query,
		       array(_('Service') => array('sqlcol' => 'service'),
			     _('Date de transmission') => array('sqlcol' => 'date_transmitted',
								'callback' => 'print_date'),
			     _('Date de retour') => array('sqlcol' => 'date_returned',
							  'callback' => 'print_date_returned'),
			     ));
$sdg->setPagerSize($_SESSION['pagersize']);
$sdg->setDefaultSort(array('date_transmitted' => 'DESC'));
$sdg->display();

echo "<center><a href='invoice_list.php?id=$id#result'>Voir mes factures</a><center>";

include('templates/footer.php');
