<?php
/*
GCourrier
Copyright (C) 2010  Cliss XXI

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

author Sylvain BEUCLER
*/

require_once('init.php');
require_once('functions/db.php');
require_once('functions/contact.php');
require_once('functions/status.php');

if (isset($_POST['contact_id']) and contact_is_deletable($_POST['contact_id'])) {
  $res = db_execute("DELETE FROM destinataire WHERE id=?",
		    array($_POST['contact_id']));
  status_push("Contact {$_POST['contact_id']} supprimé");
}
header('Location: modifierIndividu.php');
