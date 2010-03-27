<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/settings/delete_identity.inc                            |
 |                                                                       |
 | This file is part of the Crystal Webmail client                       |
 | Copyright (C) 2005-2010, Crystal Dev Team - United States             |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Delete the submitted identities (IIDs) from the database            |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: delete_identity.inc 2779 2010-07-21 16:13:42Z thomasb $

*/

$iid = get_input_value('_iid', cmail_INPUT_GPC);

// check request token
if (!$OUTPUT->ajax_call && !$cmail->check_request(cmail_INPUT_GPC)) {
  $OUTPUT->show_message('invalidrequest', 'error');
  cmail_overwrite_action('identities');
  return;
}

if ($iid && preg_match('/^[0-9]+(,[0-9]+)*$/', $iid))
{
  $plugin = $cmail->plugins->exec_hook('delete_identity', array('id' => $iid));
  
  if (!$plugin['abort'] && $USER->delete_identity($iid)) {
    $OUTPUT->show_message('deletedsuccessfully', 'confirmation', null, false);
  }
  else {
    $OUTPUT->show_message('nodeletelastidentity', 'error', null, false);
  }
  // send response
  if ($OUTPUT->ajax_call)
    $OUTPUT->send();
}

if ($OUTPUT->ajax_call)
  exit;

// go to identities page
cmail_overwrite_action('identities');

?>
