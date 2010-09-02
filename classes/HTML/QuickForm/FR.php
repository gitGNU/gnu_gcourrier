<?php
/*
HTML_QuickForm, but with a couple translated texts
Copyright (C) 2005, 2006  Cliss XXI

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

require_once(dirname(__FILE__).'/../../../classes/HTML/QuickForm.php');

class HTML_QuickForm_FR extends GPLQuickForm {
  function HTML_QuickForm_FR($formName='', $method='post', $action='', $target='', $attributes=null, $trackSubmit = false) {
    parent::__construct($formName, $method, $action, $target, $attributes, $trackSubmit);
    $this->setRequiredNote('<span style="font-size: smaller"><span style="color: red">*</span> champ requis</span>');
    $this->setJsWarnings("Le formulaire n'est pas valide:", '');
  }
}
