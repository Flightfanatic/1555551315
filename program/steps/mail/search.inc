<?php
/*
 +-----------------------------------------------------------------------+
 | steps/mail/search.inc                                                 |
 |                                                                       |
 | Search functions for rc webmail                                       |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Benjamin Smith <defitro@gmail.com>                            |
 |         Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

*/

$REMOTE_REQUEST = TRUE;

// reset list_page and old search results
$IMAP->set_page(1);
$IMAP->set_search_set(NULL);
$_SESSION['page'] = 1;

// using encodeURI with javascript "should" give us
// a correctly encoded query string
$imap_charset = cmail_CHARSET;

// get search string
$str = get_input_value('_q', cmail_INPUT_GET);
$filter = get_input_value('_filter', cmail_INPUT_GET);
$mbox = get_input_value('_mbox', cmail_INPUT_GET);
$headers = get_input_value('_headers', cmail_INPUT_GET);
$search_request = md5($mbox.$filter.$str);

// add list filter string
$search_str = $filter && $filter != 'ALL' ? $filter : '';

$_SESSION['search_filter'] = $filter;

// Check the search string for type of search
if (preg_match("/^from:.*/i", $str))
{
  list(,$srch) = explode(":", $str);
  $subject['from'] = "HEADER FROM";
}
else if (preg_match("/^to:.*/i", $str))
{
  list(,$srch) = explode(":", $str);
  $subject['to'] = "HEADER TO";
}
else if (preg_match("/^cc:.*/i", $str))
{
  list(,$srch) = explode(":", $str);
  $subject['cc'] = "HEADER CC";
}
else if (preg_match("/^bcc:.*/i", $str))
{
  list(,$srch) = explode(":", $str);
  $subject['bcc'] = "HEADER BCC";
}
else if (preg_match("/^subject:.*/i", $str))
{
  list(,$srch) = explode(":", $str);
  $subject['subject'] = "HEADER SUBJECT";
}
else if (preg_match("/^body:.*/i", $str))
{
  list(,$srch) = explode(":", $str);
  $subject['text'] = "TEXT";
}
else if(trim($str))
{
  if ($headers) {
    $headers = explode(',', $headers);
    foreach($headers as $header)
      switch ($header) {
        case 'text': $subject['text'] = 'TEXT'; break;
        default: $subject[$header] = 'HEADER '.$header;
      }
  } else {
    // search in subject by default
    $subject['subject'] = 'HEADER SUBJECT';
  }
}

$search = $srch ? trim($srch) : trim($str);

if ($subject) {
  $search_str .= str_repeat(' OR', count($subject)-1);
  foreach ($subject as $sub)
    $search_str .= sprintf(" %s {%d}\r\n%s", $sub, strlen($search), $search);
  $_SESSION['search_mods'] = $subject;
}

$search_str = trim($search_str);

// execute IMAP search
if ($search_str)
  $result = $IMAP->search($mbox, $search_str, $imap_charset, $_SESSION['sort_col']);

// Get the headers
$result_h = $IMAP->list_headers($mbox, 1, $_SESSION['sort_col'], $_SESSION['sort_order']);
$count = $IMAP->messagecount();

// save search results in session
if (!is_array($_SESSION['search']))
  $_SESSION['search'] = array();

if ($search_str) {
  $_SESSION['search'][$search_request] = $IMAP->get_search_set();
  $_SESSION['last_text_search'] = $str;
}

// Make sure we got the headers
if (!empty($result_h))
{
  cmail_js_message_list($result_h);
  if ($search_str)
    $OUTPUT->show_message('searchsuccessful', 'confirmation', array('nr' => $count));
}
else
{
  $OUTPUT->show_message('searchnomatch', 'notice');
}

// update message count display
$OUTPUT->set_env('search_request', $search_str ? $search_request : -1);
$OUTPUT->set_env('messagecount', $count);
$OUTPUT->set_env('pagecount', ceil($count/$IMAP->page_size));
$OUTPUT->command('set_rowcount', cmail_get_messagecount_text($count, 1));
$OUTPUT->send();

?>
