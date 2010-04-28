<?php
/*
Queue of messages to display to the user
Copyright (C) 2010  Cliss XXI

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

function status_push($msg)
{
  if (empty($_SESSION['status_queue']))
    $_SESSION['status_queue'] = array();
  array_push($_SESSION['status_queue'], $msg);
}

function status_pop()
{
  if (empty($_SESSION['status_queue']))
    return null;
  return array_pop($_SESSION['status_queue']);
}
