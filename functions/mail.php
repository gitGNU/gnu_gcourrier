<?php
/*
Mail encapsulation
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

require_once(dirname(__FILE__) . '/db.php');
require_once(dirname(__FILE__) . '/../classes/SQLDataGrid.php');
require_once('functions/status.php');

function mail_get_replies($id) {
  $ret = array();

  $res = db_execute("SELECT mail_new_id FROM mail_reply WHERE mail_old_id = ?",
		    array($id));
  while ($row = mysql_fetch_array($res))
    array_push($ret, intval($row['mail_new_id']));

  return $ret;
}

function mail_get_origins($id) {
  $ret = array();

  $res = db_execute("SELECT mail_old_id FROM mail_reply WHERE mail_new_id = ?",
		    array($id));
  while ($row = mysql_fetch_array($res))
    array_push($ret, intval($row['mail_old_id']));

  return $ret;
}

function mail_exists($where, $where_params)
{
  $res = db_execute("SELECT id FROM courrier WHERE $where", $where_params);
  $row = mysql_fetch_array($res);
  return mysql_num_rows($count) > 0;
}

function mail_is_archived($id)
{
  $res = db_execute("SELECT validite AS archived FROM courrier WHERE id=?",
		    array(intval($id)));
  $row = mysql_fetch_array($res);
  $count = $row['archived'];
  return $row['archived'] == 1;
}

function mail_reply_new($mail_old_id, $mail_new_id)
{
  $result = db_autoexecute('mail_reply',
    array('mail_old_id' => intval($mail_old_id),
          'mail_new_id' => intval($mail_new_id)),
    DB_AUTOQUERY_INSERT);
  return $result;
}

function mail_display_simple($ids)
{
    $query = "SELECT courrier.id AS mail_id, libelle AS label, CONCAT(nom, ' ', prenom) AS contact_name,"
      . " UNIX_TIMESTAMP(dateArrivee) AS date_here,"
      . " type, validite AS archived "
      . " FROM courrier JOIN destinataire ON courrier.idDestinataire = destinataire.id"
      . " WHERE courrier.id IN (" . join(',', $ids) . ")";

    function printId($params)
    {
      extract($params);
      $archived = '';
      if ($record['archived'] == 1)
	$archived = "type=archived=1&";
      return "<a href='mail_list.php?{$archived}type={$record['type']}"
	. "&idCourrierRecherche={$record[$fieldName]}"
	. "&rechercher=1#result'>{$record[$fieldName]}</a>";
    }

    $config = array();
    $config['No'] =
      array('sqlcol' => 'mail_id',
	    'callback' => 'printId');
    $config['Libellé'] =
      array('sqlcol' => 'label',
	    'callback' => 'printText');
    $config['Destinataire'] =
      array('sqlcol' => 'contact_name',
	    'callback' => 'printText');
    $config['Date Mairie'] =
      array('sqlcol' => 'date_here',
	    'callback' => 'printDate');

    $sdg = new SQLDataGrid($query, $config);
    $sdg->setDefaultSort(array('mail_id' => 'ASC'));
    $sdg->setClass('resultats');
    $sdg->display();
}

function mail_query_attachments($id) {
  $res = db_execute("SELECT id, filename FROM mail_attachment WHERE mail_id = ?",
		    array($id));
  return $res;
}

function mail_get_upload_dir($id) {
  $id = intval($id); // avoid '..' (for example)
  if ($id == 0)
    die('mail_get_upload_dir: invalid mail id');
  return "upload/courrier/$id";
}

function mail_attachment_get_path($attachment_id) {
  $res = db_execute("SELECT id, mail_id, filename FROM mail_attachment WHERE id = ?",
		    array($attachment_id));
  $row = mysql_fetch_array($res);
  if (strpos($row['filename'], '/') !== false)
    exit('Nom de fichier invalide');

  return mail_get_upload_dir($row['mail_id']) . '/' . $row['filename'];
}

function mail_attachment_delete($attachment_id) {
  $res = db_execute("SELECT mail_id FROM mail_attachment WHERE id = ?",
		    array($attachment_id));
  $row = mysql_fetch_array($res);
  if (mail_is_archived($row['mail_id']))
    exit('Cette pièce jointe est rattachée à un courrier archivé');

  $attachment_id = intval($attachment_id);
  $path = mail_attachment_get_path($attachment_id);
  @unlink($path);

  $res = db_execute("DELETE FROM mail_attachment WHERE id = ?",
		    array($attachment_id));  
  return $res;
}

function mail_attachment_new($id, $tmp_file, $filename)
{
  $old_umask = umask(0);
	
  $content_dir = mail_get_upload_dir($id); // dossier où sera déplacé le mail_file
  if (!file_exists($content_dir))
    mkdir($content_dir, 0755, true) or die("Impossible de créer $content_dir");
	
  // on copie le mail_file dans le dossier de destination
  if (strpos($filename, '/') !== false)
    exit('Nom de fichier invalide');
  $dest_file = "$content_dir/$filename";
  if (!rename($tmp_file, $dest_file)) {
    exit("Impossible de copier $tmp_file dans $dest_file");
  } else {
    // Give permissions to other users, including Apache. This is
    // necessary in a suPHP setup.
    chmod($dest_file, 0644);
    
    $res = db_execute('SELECT id FROM mail_attachment WHERE mail_id=? AND filename=?',
		      array($id, $filename));
    
    if (mysql_num_rows($res) == 0) {
      db_autoexecute('mail_attachment',
		     array('mail_id' => intval($id),
			   'filename' => $filename,
			   ), DB_AUTOQUERY_INSERT);
      status_push('Fichier joint au courrier');
    } else {
      status_push('Pièce jointe écrasée');
    }
  }
  
  umask($old_umask);
}

function mail_handle_attachment($id) {
  if (mail_is_archived($id))
    exit("L'ajout de pièces jointes est désactivé pour les courriers archivés");

  // If a file was uploaded
  if (isset($_FILES['mail_file'])) {
    if ($_FILES['mail_file']['error'] == UPLOAD_ERR_OK) {
      mail_attachment_new($id,
			  $_FILES['mail_file']['tmp_name'],
			  $_FILES['mail_file']['name']);
    } elseif ($_FILES['mail_file']['error'] != UPLOAD_ERR_NO_FILE) {
      exit("Erreur lors de l'envoi du mail_file {$_FILES['userfile']['name']}"
	   . " (erreur {$_FILES['mail_file']['error']})");
    }
  }
}
