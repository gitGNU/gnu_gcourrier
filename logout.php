<?php
/*
GCourrier
Copyright (C) 2005,2006 CLISS XXI

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

author Sylvain Beucler
*/

// Boilerplate (UTF8...)
include_once("connexion.php");

// On appelle session_start avant d'envoyer du HTML au navigateur
session_start();

// On supprime la session, y compris nos information de login
session_destroy();

echo "<div class='status'>Vous êtes déconnecté(e) de GCourrier.</div>";

include_once("login.php");
