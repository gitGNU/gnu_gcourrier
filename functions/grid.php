<?php
/* Standard presentation for results */
function grid_table (&$dg, $caption=NULL) {
  $table = new HTML_Table();
  $rendererOptions = array('sortIconASC' => '&uArr;',
			   'sortIconDESC' => '&dArr;');
  $dg->fill($table, $rendererOptions);
  
  if ($caption) $table->setCaption($caption);
  $tableHeader =& $table->getHeader();
  $tableBody =& $table->getBody();
  $tableHeader->setRowAttributes(0, array('style' => 'background: #CCCCCC;'));
  $tableBody->altRowAttributes(0, array('class' => 'odd'), array('class' => 'even'), true);

  echo $table->toHtml();
  
  $test = $dg->render(DATAGRID_RENDER_PAGER);
  if (PEAR::isError($test)) echo $test->getMessage();
}
