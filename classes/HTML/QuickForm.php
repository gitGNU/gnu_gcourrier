<?php
class GPLQuickForm_Element
{
  private $name = '';
  private $type = NULL;
  private $constant = NULL;
  private $value = NULL;

  private $frozen = false;
  private $params = array();

  private $label = '';

  private $select_options = array();

  public function __construct($type, $name, $params)
  {
    $this->type = $type;
    $this->name = $name;
    $this->params = $params;
    switch($this->type)
      {
      case "date":
	break;
      case "header":
	$this->label = $params[0];
	break;
      case "hidden":
	$this->value = $params[0];
	break;
      case "password":
	$this->label = $params[0];
	break;
      case "text":
	if (isset($params[0]))
	  $this->label = $params[0];
	break;
      case "select":
	$this->label = $params[0];
	$this->select_options = $params[1];
	break;
      case "submit":
	$this->label = $params[0];
	break;
      }
  }

  public function setLabel($text)
  {
    $this->label = $text;
  }
  public function setText($text)
  {
    $this->setLabel($text);
  }
  public function getValue()
  {
    if (isset($this->constant))
      return $this->constant;
    return $this->value;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function setConstant($value)
  {
    $this->constant = $value;
  }
  public function freeze()
  {
    $this->frozen = true;
  }
  public function display()
  {
    if ($this->type == 'header')
      {
	print "<tr><td colspan=2 style='background: lightgrey;"
	  . " text-align: left; font-weight: bold'>"
	  . "{$this->label}</td></tr>";
      }
    else if ($this->type == 'submit')
      {
	print "<tr><td></td><td class='element'>";
	print "<input type='submit'"
	  . " name='{$this->name}' value='{$this->label}' />";
	print '</td></tr>';
      }
    else
      {
	print '<tr>';
	print "<td class='label'>{$this->label}</td>";
	print '<td>';
	switch($this->type)
	  {

	  case "date":
	    break;

	  case "header":
	    print "";
	    break;

	  case "hidden":
	    print "<input type='hidden' name='{$this->name}' value='{$this->value}' />";
	    break;

	  case "password":
	    if ($this->frozen)
	      print "****";
	    else
	      print "<input type='password' name='{$this->name}' value='{$this->value}' />";
	    break;

	  case "text":
	    if ($this->frozen)
	      print "{$this->value}";
	    else
	      print "<input type='text' name='{$this->name}' value='{$this->value}' />";
	    break;

	  case "select":
	    if ($this->frozen)
	      {
		if ($this->value != NULL)
		  print $this->select_options[$this->value];
	      }
	    else
	      {
		print "<select name='{$this->name}'>";
		foreach($this->select_options as $id => $text)
		  {
		    $selected = '';
		    if ($id == $this->value)
		      $selected = "selected='selected'";
		    print "<option value='$id' $selected>$text</option>";
		  }
		print "</select>";
	      }
	    break;

	  }
	print '</td>';
	print '</tr>';
      }
  }
}

class GPLQuickForm
{
  private $name = '';
  private $method = '';
  private $in = array();

  private $requiredNote = '<span style="font-size:80%; color:#ff0000;">*</span><span style="font-size:80%;"> required field</span>';
  private $jsWarnings_pref = 'The form is not valid';
  private $jsWarnings_post = '';
  private $elements = array();
  private $elements_debug = array();

  // [string $formName = ''], [string $method = 'post'], [string $action = ''], [string $target = ''], [mixed $attributes = null], [bool $trackSubmit = false]
  public function __construct($name='', $method='post')
  {
    $this->name = $name;
    $this->method = $method;
    
    switch ($method)
      {
      case "get":
	$this->in = $_GET;
	break;
      case 'post':
      default:
	$this->in = $_POST;
	break;
      }
  }
  
  public function setRequiredNote($html_text)
  {
    $this->requiredNote = $html_text;
  }
  
  public function setJsWarnings($pref, $post)
  {
    $this->jsWarnings_pref = $pref;
    $this->jsWarnings_post = $post;
  }

  public function addElement()
  {
    $arg_list = func_get_args();

    $type = array_shift($arg_list);
    $name = array_shift($arg_list);
    $params = $arg_list;

    if (!is_string($type))
      throw new Exception("Adding elements as objects not supported");

    $this->elements_debug[$name] = array($type, $arg_list);

    $this->elements[$name] = new GPLQuickForm_Element($type, $name, $params);
  }

  public function getElement($name)
  {
    return $this->elements[$name];
  }

  // [array $defaultValues = null], [mixed $filter = null]
  public function setDefaults($defaults)
  {
    foreach($defaults as $name => $value)
      {
	if (isset($this->elements[$name]))
	  $this->elements[$name]->setValue($value);
      }
  }

  // mixed $element, mixed $filter
  public function applyFilter()
  {
  }

  // string $element, string $message, string $type, [string $format = null], [string $validation = 'server'], [boolean $reset = false], [boolean $force = false]
  public function addRule()
  {
  }

  public function exportValue($name)
  {
    return $this->elements[$name]->getValue();
  }

  public function validate()
  {
    if (empty($this->in))
      return false;

    foreach ($this->elements as $name => $object)
      {
	if (isset($this->in[$name]))
	  $object->setValue($this->in[$name]);
      }
    return true;
  }

  public function setConstants($constants)
  {
    foreach($constants as $name => $value)
      {
	if (isset($this->elements[$name]))
	  $this->elements[$name]->setConstant($value);
      }
  }

  public function display()
  {
//     print '<pre style="text-align: left">';
//     var_dump($this->elements);
//     print '</pre>';
    print "<form id='$this->name' action='{$_SERVER['PHP_SELF']}' method='$this->method'>";
    print "
<style type='text/css'><!--
#$this->name .label {
  text-align: right;
  font-weight: bold;
}
#$this->name .element {
  text-align: left;
}
// -->
</style>
";
    print '<table>';
    foreach ($this->elements as $element)
      $element->display();
    print "<tr><td /><td>$this->requiredNote</td></tr>";
    print '</table>';
    print "</form>";
  }

  public function exportValues()
  {
    $retval = array();
    foreach ($this->elements as $name => $object)
      $retval[$name] = $object->getValue();
    return $retval;
  }
}
