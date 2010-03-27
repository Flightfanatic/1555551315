<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/mail/check_recent.inc                                   |
 |                                                                       |
 | This file is part of the Crystal Webmail client                       |
 | Copyright (C) 2005-2010, Crystal Dev Team - United States             |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Check for recent messages, in all mailboxes                         |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: check_recent.inc 233 2006-06-26 17:31:20Z richs $

*/

$a_mailboxes = $IMAP->list_mailboxes();
$check_all = (bool)$cmail->config->get('check_all_folders');

foreach ($a_mailboxes as $mbox_name) {
  if ($mbox_name == $IMAP->get_mailbox_name()) {
    if ($recent_count = $IMAP->messagecount(NULL, 'RECENT', TRUE)) {
      // refresh saved search set
      if (($search_request = get_input_value('_search', cmail_INPUT_GPC)) && isset($_SESSION['search'][$search_request])) {
        $_SESSION['search'][$search_request] = $IMAP->refresh_search();
        $all_count = $IMAP->messagecount();
      } else {
        $all_count = $IMAP->messagecount(NULL, 'ALL', TRUE);
      }
      
      $unread_count = $IMAP->messagecount(NULL, 'UNSEEN', TRUE);
      $_SESSION['unseen_count'][$mbox_name] = $unread_count;

      $OUTPUT->set_env('messagecount', $all_count);
      $OUTPUT->set_env('pagesize', $IMAP->page_size);
      $OUTPUT->set_env('pagecount', ceil($all_count/$IMAP->page_size));
      $OUTPUT->command('set_unread_count', $mbox_name, $unread_count, ($mbox_name == 'INBOX'));
      $OUTPUT->command('set_rowcount', cmail_get_messagecount_text($all_count));

      if (cmail::get_instance()->config->get('focus_on_new_message',true))
        $OUTPUT->command('new_message_focus');

      if (!empty($_GET['_quota']))
        $OUTPUT->command('set_quota', cmail_quota_content($IMAP->get_quota()));

      // trigger plugin hook
      $cmail->plugins->exec_hook('new_messages', array('mailbox' => $mbox_name, 'count' => $unread_count));

      // "No-list" mode, don't get messages
      if (empty($_GET['_list']))
        continue;

      // use SEARCH/SORT to find recent messages
      $search_str = 'RECENT';
      if ($search_request)
        $search_str .= ' '.$IMAP->search_string;

      $result = $IMAP->search($mbox_name, $search_str, NULL, 'date');

      if ($result) {
        // get the headers
        $result_h = $IMAP->list_headers($mbox_name, 1, 'date', 'DESC');
        // add to the list
        cmail_js_message_list($result_h, true, false);
      }
    }
    else {
      cmail_send_unread_count($mbox_name, true);
    }
  }
  else if ($check_all) {
    cmail_send_unread_count($mbox_name, true);
  }
}

$OUTPUT->send();

?>
