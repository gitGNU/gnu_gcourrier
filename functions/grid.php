<?php
/*
Standard presentation for results
Copyright (C) 2005,2006 Cliss XXI

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

#require_once('HTML/Table.php');
#require_once('Structures/DataGrid.php');
require_once('classes/SQLDataGrid.php');

function grid_table (&$dg, $caption=NULL) {
  $table = new HTML_Table();
  $rendererOptions =
    array('sortIconASC' => '&uArr;',
	  'sortIconDESC' => '&dArr;',
	  'headerAttributes' => array('style' => 'background: #CCCCCC;'),
	  'oddRowAttributes' => array('class' => 'odd'),
	  'evenRowAttributes' => array('class' => 'even')
	  );
  if ($caption) $table->setCaption($caption);
  $dg->fill($table, $rendererOptions);

  $test = $dg->render(DATAGRID_RENDER_PAGER);
  if (PEAR::isError($test)) echo $test->getMessage();

  echo $table->toHtml();

  $test = $dg->render(DATAGRID_RENDER_PAGER);
  if (PEAR::isError($test)) echo $test->getMessage();
}
