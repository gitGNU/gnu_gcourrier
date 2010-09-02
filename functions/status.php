<?php
/*
Queue of messages to display to the user
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
*/

function status_push($msg)
{
  if (empty($_SESSION['status_queue']))
    $_SESSION['status_queue'] = array();
  if ($msg !== null)
    array_push($_SESSION['status_queue'], $msg);
}

function status_shift()
{
  if (empty($_SESSION['status_queue']))
    return null;
  return array_shift($_SESSION['status_queue']);
}
