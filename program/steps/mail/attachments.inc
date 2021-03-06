<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/mail/attachments.inc                                    |
 |                                                                       |
 | This file is part of the Crystal Webmail client                       |
 | Copyright (C) 2005-2010, Crystal Dev Team - United States             |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Upload, remove, display attachments in compose form                 |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: compose.inc 2081 2008-11-23 12:38:44Z thomasb $

*/


if (!$_SESSION['compose']) {
  die("Invalid session var!");
}


// remove an attachment
if ($cmail->action=='remove-attachment')
{
  $id = 'undefined';
  if (preg_match('/^rcmfile(\w+)$/', $_POST['_file'], $regs))
    $id = $regs[1];
  if ($attachment = $_SESSION['compose']['attachments'][$id])
    $attachment = $cmail->plugins->exec_hook('remove_attachment', $attachment);
  if ($attachment['status']) {
    if (is_array($_SESSION['compose']['attachments'][$id])) {
      unset($_SESSION['compose']['attachments'][$id]);
      $OUTPUT->command('remove_from_attachment_list', "rcmfile$id");
    }
  }
  
  $OUTPUT->send();
  exit;
}

if ($cmail->action=='display-attachment')
{
  $id = 'undefined';
  if (preg_match('/^rcmfile(\w+)$/', $_GET['_file'], $regs))
    $id = $regs[1];
  if ($attachment = $_SESSION['compose']['attachments'][$id])
    $attachment = $cmail->plugins->exec_hook('display_attachment', $attachment);
    
  if ($attachment['status']) {
    $size = $attachment['data'] ? strlen($attachment['data']) : @filesize($attachment['path']);
    header('Content-Type: ' . $attachment['mimetype']);
    header('Content-Length: ' . $size);
    
    if ($attachment['data'])
      echo $attachment['data'];
    else if ($attachment['path'])
      readfile($attachment['path']);
  }
  exit;
}

// attachment upload action

if (!is_array($_SESSION['compose']['attachments'])) {
  $_SESSION['compose']['attachments'] = array();
}

// clear all stored output properties (like scripts and env vars)
$OUTPUT->reset();

$uploadid = get_input_value('_uploadid', cmail_INPUT_GET);

if (is_array($_FILES['_attachments']['tmp_name'])) {
  foreach ($_FILES['_attachments']['tmp_name'] as $i => $filepath) {
    $attachment = array(
      'path' => $filepath,
      'name' => $_FILES['_attachments']['name'][$i],
      'mimetype' => rc_mime_content_type($filepath, $_FILES['_attachments']['name'][$i], $_FILES['_attachments']['type'][$i])
    );

    $attachment = $cmail->plugins->exec_hook('upload_attachment', $attachment);

    if ($attachment['status'] && !$attachment['abort']) {
      $id = $attachment['id'];
      
      // store new attachment in session
      unset($attachment['status'], $attachment['abort']);
      $_SESSION['compose']['attachments'][$id] = $attachment;
      
      if (($icon = $_SESSION['compose']['deleteicon']) && is_file($icon)) {
        $button = html::img(array(
          'src' => $icon,
          'alt' => cmail_label('delete')
        ));
      }
      else {
        $button = Q(cmail_label('delete'));
      }

      $content = html::a(array(
        'href' => "#delete",
        'onclick' => sprintf("return %s.command('remove-attachment','rcmfile%s', this)", JS_OBJECT_NAME, $id),
        'title' => cmail_label('delete'),
      ), $button);

      $content .= Q($attachment['name']);
      
      $OUTPUT->command('add2attachment_list', "rcmfile$id", array(
        'html' => $content,
        'name' => $attachment['name'],
        'mimetype' => $attachment['mimetype'],
        'complete' => true), $uploadid);
    }
    else {  // upload failed
      $err = $_FILES['_attachments']['error'][$i];
      if ($err == UPLOAD_ERR_INI_SIZE || $err == UPLOAD_ERR_FORM_SIZE) {
        $msg = cmail_label(array('name' => 'filesizeerror', 'vars' => array('size' => show_bytes(parse_bytes(ini_get('upload_max_filesize'))))));
      }
      else if ($attachment['error']) {
        $msg = $attachment['error'];
      }
      else {
        $msg = cmail_label('fileuploaderror');
      }
    
      $OUTPUT->command('display_message', $msg, 'error');
      $OUTPUT->command('remove_from_attachment_list', $uploadid);
    }
  }
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // if filesize exceeds post_max_size then $_FILES array is empty,
  // show filesizeerror instead of fileuploaderror
  if ($maxsize = ini_get('post_max_size'))
    $msg = cmail_label(array('name' => 'filesizeerror', 'vars' => array('size' => show_bytes(parse_bytes($maxsize)))));
  else
    $msg = cmail_label('fileuploaderror');
  $OUTPUT->command('display_message', $msg, 'error');
  $OUTPUT->command('remove_from_attachment_list', $uploadid);
}

// send html page with JS calls as response
$OUTPUT->command('auto_save_start', false);
$OUTPUT->send('iframe');

?>
