<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/addressbook/list.inc                                    |
 |                                                                       |
 | This file is part of the Crystal Webmail client                       |
 | Copyright (C) 2005-2007, Crystal Dev. - United States                 |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Send contacts list to client (as remote response)                   |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: list.inc 543 2007-04-28 18:07:12Z thomasb $

*/

// get contacts for this user
$result = $CONTACTS->list_records();

// update message count display
$OUTPUT->set_env('pagecount', ceil($result->count / $CONTACTS->page_size));
$OUTPUT->command('set_rowcount', cmail_get_rowcount_text($rowcount));

// create javascript list
cmail_js_contacts_list($result);
  
// send response
$OUTPUT->send();

?>