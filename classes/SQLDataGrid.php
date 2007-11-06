<?php
/*
Display MySQL results in an HTML table
Copyright (C) 2007  Cliss XXI

This file is part of GCourrier.

GCourrier is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation, either version 3 of the License, or (at your
option) any later version.

GCourrier is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see http://www.gnu.org/licenses/.
*/

include(dirname(__FILE__) . '/../functions/db.php');

/* This data grid can also go to the page that contains a given
   value. */

class SQLDataGrid {
  private $query = '';
  private $cols = array();

  private $title = null;
  private $class = null;

  private $pager_size = null;
  private $default_page = 1;
  private $cur_page = null;
  private $total_rows = null;

  private $order_field = null;
  private $order_direction = null;

  private $order_sql = '';
  private $limit_sql = '';

  private $search_field = null;
  private $search_value = null;
  
  public function __construct($query, $cols)
  {
    $this->query = $query;

    // Make columns params tidy
    foreach($cols as $name => $params)
      if (!is_array($params))
	$cols[$name] = array('sqlcol' => $params);
    $this->cols = $cols;


    if (isset($_GET['page']))
      {
	$this->cur_page = $_GET['page'];
      }


    // ORDER BY..
    if (isset($_GET['orderBy']))
      {
	$this->order_field = $_GET['orderBy'];
	$this->order_sql = "ORDER BY $this->order_field";
	if (isset($_GET['direction']))
	  {
	    $this->order_direction = $_GET['direction'];
	    $this->order_sql .= " $this->order_direction";
	  }
      }
  }

  function setPagerSize($pager_size)
  {
    $this->pager_size = $pager_size;
  }

  function setTitle($title)
  {
    $this->title = $title;
  }

  function setDefaultPage($page)
  {
    $this->default_page = $page;
  }
  function setDefaultPageWhere($arr)
  {
    // $arr == ('field' => value)
    // False foreach loop just to get the params:
    foreach ($arr as $f => $v)
      {
	$location_field = $f;
	$location_value = $v;
      }

    // Store field/value for special processing during display
    $this->search_field = $location_field;
    $this->search_value = $location_value;

    if (!isset($this->cur_page) && isset($this->pager_size))
      {
	// Implementation 1: naive
	/* on y va bourin */
	$res = db_execute($this->query . " $this->order_sql");
	$pos = 0;
	$this->total_rows = mysql_num_rows($res);
	while($row = mysql_fetch_array($res))
	  {
	    if ($row[$location_field] == $location_value)
	      break;
	    $pos++;
	  }
	// $pos => ok
	
	// -------
	
	// Implementation 2: more efficient, but incorrect
	/* This requires $order_field to be the actual field name, while
      it could be an alias (select another_field AS order_field), or
      worse, a computation (select (field1 + field2) AS order_field */
	/*
      $res = db_execute("SELECT $order_field FROM utilisateur WHERE $location_field=$location_value");
      $target_row = mysql_fetch_array($res);
      preg_match('/select .* ( from .*)/is', $query, $matches);
      $query2 = "SELECT COUNT(*) AS pos {$matches[1]} AND $order_field < '{$target_row[$order_field]}'";
      $res = db_execute($query2);
      $row = mysql_fetch_array($res);
      $pos = $row['pos'];
	*/

	$this->default_page = floor($pos / $this->pager_size) + 1;
      }
  }

  static function GET2query_string($myget)
  {
    $first = true;
    foreach($myget as $name => $value)
      {
	if ($first)
	  {
	    $query_string = '?';
	    $first = false;
	  }
	else
	  {
	    $query_string .= '&amp;';
	  }
	$query_string .= "$name=$value";
      }
    return $query_string;
  }
  

  /* CSS class for the HTML table */
  function setClass($class)
  {
    $this->class = $class;
  }

  function setDefaultSort($default_sort)
  {
    if (!isset($this->order_field))
      {
	$first = true;
	foreach($default_sort AS $field => $direction)
	  {
	    if ($first)
	      {
		$this->order_field = $field;
		$this->order_direction = $direction;
		$this->order_sql = "ORDER BY";
		$first = false;
	      }
	    else
	      {
		$this->order_sql .= ",";
	      }
	    $this->order_sql .= " $field $direction";
	  }
      }
  }


  function display_pager()
  {
    $myget = $_GET;
    $myget['orderBy'] = $this->order_field;
    $myget['direction'] = $this->order_direction;
    $page_link = 0;

    if ($this->total_rows <=  $this->pager_size)
      return;

    if ($this->cur_page != 1)
      {
	$myget['page'] = $this->cur_page-1;
	$link = $this->GET2query_string($myget);
	print "<a href='$link'>&lt;&lt;</a>&nbsp;&nbsp;&nbsp;";
      }
    for ($i = 0; $i < $this->total_rows; $i += $this->pager_size)
      {
	$page_link++;
	if ($page_link != 1)
	  print "|";
	print "&nbsp;&nbsp;&nbsp;";
	if ($page_link != $this->cur_page)
	  {
	    $myget['page'] = $page_link;
	    $link = $this->GET2query_string($myget);
	    print "<a href='$link'>" . ($page_link) . "</a>";
	  }
	else
	  {
	    print "<strong>$page_link</strong>";;
	  }
	print "&nbsp;&nbsp;&nbsp;";
      }
    if ($this->cur_page != $page_link)
      {
	$myget['page'] = $this->cur_page+1;
	$link = $this->GET2query_string($myget);
	print "&nbsp;&nbsp;&nbsp;<a href='$link'>&gt;&gt;</a>";
      }
  }
  
  function display_data()
  {
    $res = db_execute("$this->query $this->order_sql $this->limit_sql");

    if (isset($this->class))
      print "<table class='$this->class'>";
    else
      print '<table>';
    
    if (isset($this->title))
      print "<caption>$this->title</caption>";

    // Titles
    print "<tr style='background: #CCCCCC'>";
    foreach($this->cols as $label => $params)
      {
	print "<th>";
	if (isset($params['sqlcol']))
	  {
	    $direction = 'ASC';
	    if (isset($params['sqlcol'])
		and $params['sqlcol'] == $this->order_field
		and $this->order_direction == 'ASC')
	      $direction = 'DESC';
	    
	    // Filter GET parameters
	    $myget = $_GET;
	    $myget['orderBy'] = $params['sqlcol'];
	    $myget['direction'] = $direction;
	    unset($myget['page']);
	    $link = $this->GET2query_string($myget);
	    
	    print "<a href='{$link}'>";
	    print "$label";
	    // up/down vertical arrow
	    if ($params['sqlcol'] == $this->order_field)
	      if ($this->order_direction == 'ASC')
		print '&uArr;';
	      else
		print '&dArr;';
	    print "</a>";
	  }
	else
	  {
	    print "$label";
	  }
	print "</th>";
      }
    print '</tr>';
    
    $i = 0;
    while($record = mysql_fetch_array($res))
      {
	if ($i % 2 == 0)
	  $row_class = 'even';
	else
	  $row_class = 'odd';
	$i++;
	
	print "<tr class='$row_class'>";
	foreach($this->cols as $label => $params)
	  {
	    $style = '';
	    if (isset($params['style']))
	      $style .= $params['style'] . ' ';
	    if (empty($style))
	      print '<td>';
	    else
	      print "<td style='$style'>";

	    if (!empty($params['sqlcol'])
		&& $params['sqlcol'] == $this->search_field
		&& $record[$params['sqlcol']] == $this->search_value)
	      print '<a name="result"></a>';
	    
	    if (isset($params['callback']))
	      print call_user_func($params['callback'],
				   array('record' => $record));
	    else if (isset($params['sqlcol']))
	      print $record[$params['sqlcol']];
	    print '</td>';
	  }
	print '</tr>';
      }
    
    print '</table>';
  }



  function display() {
    if (!isset($this->cur_page))
      $this->cur_page = $this->default_page;
    
    if (isset($this->pager_size))
      {
	$this->limit_sql = "LIMIT " . ($this->pager_size * ($this->cur_page-1)) . ",$this->pager_size";
	if (!isset($this->total_rows))
	  {
	    preg_match('/select .* ( from .*)/is', $this->query, $matches);
	    $query2 = "SELECT COUNT(*) AS total_rows {$matches[1]}";
	    $res = db_execute($query2);
	    $row = mysql_fetch_array($res);
	    $this->total_rows = $row['total_rows'];
	  }
      }
    
    $this->display_pager();
    $this->display_data();
    $this->display_pager();
  }
}
