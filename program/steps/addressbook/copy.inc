<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/addressbook/copy.inc                                    |
 |                                                                       |
 | This file is part of the Crystal Webmail client                       |
 | Copyright (C) 2007, Crystal Dev. - United States                      |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Copy a contact record from one direcotry to another                 |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: copy.inc 471 2007-02-09 21:25:50Z thomasb $

*/

// only process ajax requests
if (!$OUTPUT->ajax_call)
  return;

$cid = get_input_value('_cid', cmail_INPUT_POST);
$target = get_input_value('_to', cmail_INPUT_POST);

if ($cid && preg_match('/^[a-z0-9\-_=]+(,[a-z0-9\-_=]+)*$/i', $cid) && strlen($target) && $target !== $source)
{
  $success = 0;
  $TARGET = $cmail->get_address_book($target);

  if ($TARGET && $TARGET->ready && !$TARGET->readonly) {
    $arr_cids = split(',', $cid);
    foreach ($arr_cids as $cid) {
      $plugin = $cmail->plugins->exec_hook('create_contact', array('record' => $CONTACTS->get_record($cid, true), 'source' => $target));
    $a_record = $plugin['record'];

    if (!$plugin['abort'])
        if ($TARGET->insert($a_record, true))
          $success++;
    }
  }

  if ($success == 0)
    $OUTPUT->show_message('copyerror', 'error');
  else
    $OUTPUT->show_message('copysuccess', 'notice', array('nr' => $success));
    
  // close connection to second address directory
  $TARGET->close();
}

// send response
$OUTPUT->send();

?>
