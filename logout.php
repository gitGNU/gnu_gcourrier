<?php
/*
GCourrier
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

author Sylvain Beucler
*/

require_once('init.php');
require_once('functions/longsession.php');

// On supprime la session, y compris nos information de login
session_destroy();
longsession_delete();
setcookie('gcourrier_session', '', time() - 3600);
include('templates/header_login.php');
?>

<div class='status'>Vous êtes déconnecté(e) de GCourrier.</div>

<?php
include("templates/login.php");
include("templates/footer.php");
