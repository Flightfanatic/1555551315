<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/mail/get.inc                                            |
 |                                                                       |
 | This file is part of the Crystal Webmail client                       |
 | Copyright (C) 2005-2010, Crystal Dev Team - United States             |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Delivering a specific part of a mail message                        |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: get.inc 2979 2010-09-22 07:50:32Z alec $

*/


// show loading page
if (!empty($_GET['_preload'])) {
  $url = str_replace('&_preload=1', '', $_SERVER['REQUEST_URI']);
  $message = cmail_label('loadingdata');

  header('Content-Type: text/html; charset=' . cmail_CHARSET);
  print "<html>\n<head>\n"
        . '<meta http-equiv="refresh" content="0; url='.Q($url).'">' . "\n"
        . '<meta http-equiv="content-type" content="text/html; charset='.cmail_CHARSET.'">' . "\n"
        . "</head>\n<body>\n$message\n</body>\n</html>";
  exit;
}

ob_end_clean();

// similar code as in program/steps/mail/show.inc
if (!empty($_GET['_uid'])) {
  $cmail->config->set('prefer_html', true);
  $MESSAGE = new cmail_message(get_input_value('_uid', cmail_INPUT_GET));
}


// show part page
if (!empty($_GET['_frame'])) {
  $OUTPUT->send('messagepart');
  exit;
}

else if ($pid = get_input_value('_part', cmail_INPUT_GET)) {
  // TNEF encoded attachment part
  if (preg_match('/^winmail\.([0-9.]+)\.([0-9]+)$/', $pid, $nt)) {
    $pid = $nt[1]; $i = $nt[2];
    if ($part = $MESSAGE->mime_parts[$pid]) {
      $tnef_arr = $IMAP->tnef_decode($part, $MESSAGE->uid);
      if (is_a($tnef_arr[$i], 'cmail_message_part'))
        $MESSAGE->mime_parts[$pid] = $tnef_arr[$i];
    }
  }
  
  if ($part = $MESSAGE->mime_parts[$pid]) {
    $ctype_primary = strtolower($part->ctype_primary);
    $ctype_secondary = strtolower($part->ctype_secondary);
    $mimetype = sprintf('%s/%s', $ctype_primary, $ctype_secondary);
    
    $browser = new cmail_browser;

    send_nocacheing_headers();
    
    // send download headers
    if ($_GET['_download']) {
      header("Content-Type: application/octet-stream");
      if ($browser->ie)
        header("Content-Type: application/force-download");
    }
    else if ($ctype_primary == 'text') {
      header("Content-Type: text/$ctype_secondary; charset=" . ($part->charset ? $part->charset : cmail_CHARSET));
    }
    else {
      header("Content-Type: $mimetype");
      header("Content-Transfer-Encoding: binary");
    }

    // deliver part content
    if ($ctype_primary == 'text' && $ctype_secondary == 'html') {
      // get part body if not available
      if (!$part->body)
        $part->body = $MESSAGE->get_part_content($part->mime_id);

      $OUTPUT = new cmail_html_page();
      $OUTPUT->write(cmail_print_body($part, array('safe' => $MESSAGE->is_safe, 'inline_html' => false)));
    }
    else {
      // don't kill the connection if download takes more than 30 sec.
      @set_time_limit(0);
      
      $filename = $part->filename ? $part->filename : ($MESSAGE->subject ? $MESSAGE->subject : 'Crystal') . '.'.$ctype_secondary;
      
      if ($browser->ie && $browser->ver < 7)
        $filename = rawurlencode(abbreviate_string($filename, 55));
      else if ($browser->ie)
        $filename = rawurlencode($filename);
      else
        $filename = addcslashes($filename, '"');
      
      $disposition = !empty($_GET['_download']) ? 'attachment' : 'inline';
      
      header("Content-Disposition: $disposition; filename=\"$filename\"");
      
      // turn off output buffering and print part content
      if ($part->body)
        echo $part->body;
      else if ($part->size)
        $IMAP->get_message_part($MESSAGE->uid, $part->mime_id, $part, true);
    }

    exit;
  }
}

// print message
else {
  // send correct headers for content type
  header("Content-Type: text/html");

  $cont = "<html>\n<head><title></title>\n</head>\n<body>";
  $cont .= cmail_message_body(array());
  $cont .= "\n</body>\n</html>";

  $OUTPUT = new cmail_html_page();
  $OUTPUT->write($cont);

  exit;
}


// if we arrive here, the requested part was not found
header('HTTP/1.1 404 Not Found');
exit;

?>
