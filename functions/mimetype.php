<?php
/*
Determine mime type by extension
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

/* Cf. http://stackoverflow.com/questions/1147931/how-do-i-determine-the-extensions-associated-with-a-mime-type-in-php/1147952#1147952 */

function system_extension_mime_types() {
  // Returns the system MIME type mapping of extensions to MIME types, as defined in /etc/mime.types.
  $out = array();
  $file = fopen('/etc/mime.types', 'r');
  while(($line = fgets($file)) !== false) {
    $line = trim(preg_replace('/#.*/', '', $line));
    if(!$line)
      continue;
    $parts = preg_split('/\s+/', $line);
    if(count($parts) == 1)
      continue;
    $type = array_shift($parts);
    foreach($parts as $part)
      $out[$part] = $type;
  }
  fclose($file);
  return $out;
}

function mimetype_get_by_ext($file) {
  // Returns the system MIME type (as defined in /etc/mime.types) for the filename specified.
  //
  // $file - the filename to examine
  static $types;
  if(!isset($types))
    $types = system_extension_mime_types();
  $ext = pathinfo($file, PATHINFO_EXTENSION);
  if(!$ext)
    $ext = $file;
  $ext = strtolower($ext);
  return isset($types[$ext]) ? $types[$ext] : null;
}
