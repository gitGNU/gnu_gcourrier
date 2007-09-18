<?php
/*
Convert the bill amount field from string to decimal value
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

/*
  INSTALL: place this temporarily at the top-level of your
  installation and run it from a web browser.
 */

include('functions/db.php');
$result = db_execute('SELECT id, montant FROM facture');
$num_errors_total = 0;
$num_errors_space = 0;
$num_errors_comma = 0;
$num_errors_doubledot = 0;
$num_errors_dotthousands = 0;
$num_errors_invalidchar = 0;
$num_errors_empty = 0;
$num_errors_unknown = 0;

function is_valid($amount)
{
  return is_numeric($amount) and $amount != '';
}


echo "<table border>";
echo "<tr><td>ID</td><td>Amount</td><td>Detected error</td><td>Fixed amount</td></tr>";
while($line = mysql_fetch_array($result))
{
  $id = $line['id'];
  $amount = $line['montant'];
  if (!is_valid($amount))
    {
      $num_errors_total++;
      echo "<tr>";
      echo "<td>{$line['id']}</td><td>{$line['montant']}</td>";
      echo "<td>";
      $amount = str_replace(' ', '', $amount);
      if (is_valid($amount))
	{
	  $num_errors_space++;
	  echo "Extra space";
	}
      else
	{
	  $amount = str_replace(',', '.', $amount);
	  $amount = str_replace(';', '.', $amount);
	  if (is_valid($amount))
	    {
	      $num_errors_comma++;
	      echo "Comma/semi-colon";
	    }
	  else
	    {
	      $amount = str_replace('..', '.', $amount);
	      if (is_valid($amount))
		{
		  $num_errors_doubledot++;
		  echo "Double dot";
		}
	      else
		{
		  $matches = array();
		  preg_match('/(.*)\.([^.]*)/', $amount, $matches);
		  if (isset($matches[1]) and isset($matches[2]))
		    $amount = str_replace('.', '', $matches[1]) . '.' . $matches[2];
		  if (is_valid($amount))
		    {
		      $num_errors_dotthousands++;
		      echo "Dot used as thousands separator";
		    }
		  else
		    {
		      $amount = preg_replace('/[^0-9.+-]/', '', $amount);
		      if (is_valid($amount))
			{
			  $num_errors_invalidchar++;
			  echo "Invalid character";
			}
		      else if ($amount == '')
			{
			  $num_errors_empty++;
			  $amount = "0";
			  echo "Empty";
			}
		      else
			{
			  
			  $num_errors_unknown++;
			  echo "?";
			}
		    }
		}
	    }
	}
      echo "</td>";
      echo "<td>$amount</td>";
      echo "</tr>";
      db_execute('UPDATE facture SET montant=? WHERE id=?',
		 array($amount, $id));
    }
}
echo "</table>";
echo "<hr />";
echo "num_errors_total = $num_errors_total<br />";
echo "num_errors_space = $num_errors_space<br />";
echo "num_errors_empty = $num_errors_empty<br />";
echo "num_errors_comma = $num_errors_comma<br />";
echo "num_errors_doubledot = $num_errors_doubledot<br />";
echo "num_errors_dotthousands = $num_errors_dotthousands<br />";
echo "num_errors_invalidchar = $num_errors_invalidchar<br />";
echo "num_errors_unknown = $num_errors_unknown<br />";
