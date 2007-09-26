<?php
/*
HTML_QuickForm, but with a couple translated texts
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

require_once(dirname(__FILE__).'/../../../classes/HTML/QuickForm.php');

class HTML_QuickForm_FR extends GPLQuickForm {
  function HTML_QuickForm_FR($formName='', $method='post', $action='', $target='', $attributes=null, $trackSubmit = false) {
    parent::__construct($formName, $method, $action, $target, $attributes, $trackSubmit);
    $this->setRequiredNote('<span style="font-size:80%; color:#ff0000;">*</span><span style="font-size:80%;"> champ requis</span>');
    $this->setJsWarnings("Le formulaire n'est pas valide:", '');
  }
}
