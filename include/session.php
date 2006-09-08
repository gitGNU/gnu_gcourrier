<?php
/*
GCourrier
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

author Sylvain Beucler
*/

function session_get($session_hash) {
  // MySQL lookup
  if (at_least_one_row()) {
    return $id;
  } else {
    # No such session
    return -1;
  }
}

function session_register() {
  $id = $_SESSION['id'];
  // hash - id - expiration
  // hash = md5(rand(time));
  // expiration = now() + 1_week();
  return $session_hash
}

function session_renew() {
  // expiration = now() + 1_week();
}
