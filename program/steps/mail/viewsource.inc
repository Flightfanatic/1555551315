<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/mail/viewsource.inc                                     |
 |                                                                       |
 | This file is part of the Crystal Webmail client                       |
 | Copyright (C) 2005-2010, Crystal Dev Team - United States             |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Display a mail message similar as a usual mail application does     |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: viewsource.inc 2613 2010-06-05 13:34:07Z alec $

*/

ob_end_clean();

// similar code as in program/steps/mail/get.inc
if ($uid = get_input_value('_uid', cmail_INPUT_GET))
{
  $headers = $IMAP->get_headers($uid);
  $charset = $headers->charset ? $headers->charset : $IMAP->default_charset;
  header("Content-Type: text/plain; charset={$charset}");

  if (!empty($_GET['_save'])) {
    $filename = ($headers->subject ? $IMAP->decode_header($headers->subject) : 'Crystal') . '.eml';
    $browser = new cmail_browser;
    
    if ($browser->ie && $browser->ver < 7)
      $filename = rawurlencode(abbreviate_string($filename, 55));
    else if ($browser->ie)
      $filename = rawurlencode($filename);
    else
      $filename = addcslashes($filename, '"');

    header("Content-Length: {$headers->size}");
    header("Content-Disposition: attachment; filename=\"$filename\"");
  }
  
  $IMAP->print_raw_body($uid);
}
else
{
  raise_error(array(
      'code' => 500,
      'type' => 'php',
      'message' => 'Message UID '.$uid.' not found'),
    true,
    true);
}

exit;
?>
