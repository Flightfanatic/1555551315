<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/addressbook/delete.inc                                  |
 |                                                                       |
 | This file is part of the Crystal Webmail client                       |
 | Copyright (C) 2005-2010, Crystal Dev Team - United States             |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Delete the submitted contacts (CIDs) from the users address book    |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: delete.inc 2758 2010-07-16 15:01:05Z thomasb $

*/

if ($OUTPUT->ajax_call &&
    ($cid = get_input_value('_cid', cmail_INPUT_POST)) &&
    (preg_match('/^[0-9]+(,[0-9]+)*$/', $cid) ||
     preg_match('/^[a-zA-Z0-9=]+(,[a-zA-Z0-9=]+)*$/', $cid))
   )
  {
  $plugin = $cmail->plugins->exec_hook('delete_contact', array('id' => $cid, 'source' => get_input_value('_source', cmail_INPUT_GPC)));
  
  $deleted = !$plugin['abort'] ? $CONTACTS->delete($cid) : false;
  if (!$deleted)
    {
    // send error message
    exit;
    }

  // count contacts for this user
  $result = $CONTACTS->count();

  // update message count display
  $OUTPUT->set_env('pagecount', ceil($result->count / $CONTACTS->page_size));
  $OUTPUT->command('set_rowcount', cmail_get_rowcount_text($result->count));

  // add new rows from next page (if any)
  $pages = ceil(($result->count + $deleted) / $CONTACTS->page_size);
  if ($_GET['_from'] != 'show' && $pages > 1 && $CONTACTS->list_page < $pages)
    cmail_js_contacts_list($CONTACTS->list_records(null, -$deleted));

  // send response
  $OUTPUT->send();
  }

exit;
?>
