<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/mail/getunread.inc                                      |
 |                                                                       |
 | This file is part of the Crystal Webmail client                       |
 | Copyright (C) 2005-2007, Crystal Dev. - United States                 |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Check all mailboxes for unread messages and update GUI              |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: getunread.inc 2966 2010-09-18 10:15:17Z alec $

*/

$a_folders = $IMAP->list_mailboxes();

if (!empty($a_folders))
{
  $inbox = ($IMAP->get_mailbox_name() == 'INBOX');
  foreach ($a_folders as $mbox_row) {
    $unseen = $IMAP->messagecount($mbox_row, 'UNSEEN', !isset($_SESSION['unseen_count'][$mbox_row]));

    if ($unseen || !isset($_SESSION['unseen_count'][$mbox_row])) {
      $OUTPUT->command('set_unread_count', $mbox_row, $unseen, $inbox && $mbox_row == 'INBOX');
    }
    $_SESSION['unseen_count'][$mbox_row] = $unseen;
  }
}

$OUTPUT->send();

?>
