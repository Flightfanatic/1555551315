<?php

/**
 * Send Message
 *
 * Plugin sends system messages
 *
 * @version 1.0 - 07.06.2010
 * @author Roland 'rosali' Liebl
 * @website http://myCrystal.googlecode.com
 * @licence GNU GPL
 *
 **/
 
/** USAGE
 *
 * #1- Register plugin ("./config/main.inc.php ::: $cmail_config['plugins']").
 * #2- Call sendmessage::compose($from,$email,$subject,$body[,$isHtml,$footer]);
 *
 **/
 
class sendmessage extends cmail_plugin
{

  function init()
  {
    $this->load_config('config/config.inc.php.dist');
    if(file_exists("./plugins/sendmessage/config/config.inc.php"))
      $this->load_config('config/config.inc.php');
  }

  function compose($from,$to,$subject,$body,$isHtml=1,$footer=false)
  {

    global $CONFIG, $cmail;
    
    if($CONFIG['smtp_pass'] == "%p"){
      $cmail->config->set('smtp_server', $CONFIG['default_smtp_server']);
      $cmail->config->set('smtp_user', $CONFIG['default_smtp_user']);
      $cmail->config->set('smtp_pass', $CONFIG['default_smtp_pass']);
    }
               
    $message_charset = "UTF-8";
    $message_id = sprintf('<%s@%s>', md5(uniqid('cmail'.rand(),true)), $cmail->config->mail_domain($_SESSION['imap_host']));
    
    // compose headers array
    $headers = array();

    // if configured, the Received headers goes to top, for good measure
    if ($CONFIG['http_received_header'])
    {

      $nldlm = $cmail->config->header_delimiter() . "\t";
      $http_header = 'from ';
      if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $http_header .= cmail_encrypt_header(gethostbyaddr($_SERVER['HTTP_X_FORWARDED_FOR'])) .
          ' [' . cmail_encrypt_header($_SERVER['HTTP_X_FORWARDED_FOR']) . ']';
        $http_header .= $nldlm . ' via ';
      }
      $http_header .= cmail_encrypt_header(gethostbyaddr($_SERVER['REMOTE_ADDR'])) .
          ' [' . cmail_encrypt_header($_SERVER['REMOTE_ADDR']) .']';
      $http_header .= $nldlm . 'with ' . $_SERVER['SERVER_PROTOCOL'] .
          ' ('.$_SERVER['REQUEST_METHOD'] . '); ' . date('r');
      $http_header = wordwrap($http_header, 69, $nldlm);
      $headers['Received'] = $http_header;
    }
    $headers['Date'] = date('r');
    $headers['From'] = cmail_charset_convert(trim($from), cmail_CHARSET, $message_charset);
    $headers['To'] = trim($to);

    // add subject
    $headers['Subject'] = trim($subject);

    // additional headers
    $headers['Message-ID'] = $message_id;
    $headers['X-Sender'] = $from;

    if (!empty($CONFIG['useragent']))
      $headers['User-Agent'] = $CONFIG['useragent'];
    
    // fetch message body
    $message_body = cmail_charset_convert(trim($body), cmail_CHARSET, $message_charset);

    // create extended PEAR::Mail_mime instance
    $MAIL_MIME = new cmail_mail_mime($cmail->config->header_delimiter());

    // For HTML-formatted messages, construct the MIME message with both
    // the HTML part and the plain-text part

    if ($isHtml) {
      $plugin = array('body' => $message_body, 'type' => 'html', 'message' => $MAIL_MIME);
      $MAIL_MIME->setHTMLBody($plugin['body'] . ($footer ? "\r\n<pre>".$footer.'</pre>' : ''));

      // add a plain text version of the e-mail as an alternative part.
      $h2t = new html2text($plugin['body'], false, true, 0);
      $plainTextPart = rc_wordwrap($h2t->get_text(), 75, "\r\n") . ($footer ? "\r\n".$footer : '');
      $plainTextPart = wordwrap($plainTextPart, 998, "\r\n", true);
      if (!strlen($plainTextPart)) {
        // empty message body breaks attachment handling in drafts 
        $plainTextPart = "\r\n"; 
      }
      $plugin = array('body' => $plainTextPart, 'type' => 'alternative', 'message' => $MAIL_MIME);
      $MAIL_MIME->setTXTBody($plugin['body']);

    }
    else
      {
      $message_body = rc_wordwrap($message_body, 75, "\r\n");
      if ($footer)
        $message_body .= "\r\n" . $footer;
        $message_body = wordwrap($message_body, 998, "\r\n", true);
      if (!strlen($message_body)) { 
        // empty message body breaks attachment handling in drafts 
        $message_body = "\r\n"; 
      }
      $plugin = array('body' => $message_body, 'type' => 'plain', 'message' => $MAIL_MIME);
      $MAIL_MIME->setTXTBody($plugin['body'], false, true);
    }

    // chose transfer encoding
    $charset_7bit = array('ASCII', 'ISO-2022-JP', 'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-15');
    $transfer_encoding = in_array(strtoupper($message_charset), $charset_7bit) ? '7bit' : '8bit';

    // encoding settings for mail composing
    $MAIL_MIME->setParam(array(
      'text_encoding' => $transfer_encoding,
      'html_encoding' => 'quoted-printable',
      'head_encoding' => 'quoted-printable',
      'head_charset'  => $message_charset,
      'html_charset'  => $message_charset,
      'text_charset'  => $message_charset,
    ));

    $data = array('headers' => $headers);
    $headers = $data['headers'];
    

    // encoding subject header with mb_encode provides better results with asian characters
    if (function_exists("mb_encode_mimeheader"))
    {
      mb_internal_encoding($message_charset);
      $headers['Subject'] = mb_encode_mimeheader($headers['Subject'], $message_charset, 'Q');
      mb_internal_encoding(cmail_CHARSET);
    }
    
    // pass headers to message object
    $MAIL_MIME->headers($headers);

    //SMTP Delivery 
    $message = $MAIL_MIME;
    $mailto = $to;


    if(!function_exists("smtp_mail")){
      include_once("./program/include/cmail_smtp.inc");
      //include_once("./program/include/cmail_imap.inc"); 
    } 

    $msg_body = $message->get();
    $headers = $message->headers();

    // send thru SMTP server using custom SMTP library
    if ($CONFIG['smtp_server']) {
      // generate list of recipients
      $a_recipients = array($mailto);
  
      if (strlen($headers['Cc']))
        $a_recipients[] = $headers['Cc'];
      if (strlen($headers['Bcc']))
        $a_recipients[] = $headers['Bcc'];
  
      // clean Bcc from header for recipients
      $send_headers = $headers;
      unset($send_headers['Bcc']);
      // here too, it because txtHeaders() below use $message->_headers not only $send_headers
      unset($message->_headers['Bcc']);

      // send message
      $smtp_response = array();
      $smtp_errors = array();
      $sent = smtp_mail($from, $a_recipients, ($foo = $message->txtHeaders($send_headers, true)), $msg_body, $smtp_response, $smtp_errors);

      // log error
      if (!$sent)
        raise_error(array('code' => 800, 'type' => 'smtp', 'line' => __LINE__, 'file' => __FILE__,
                          'message' => "SMTP error: ".join("\n", $smtp_response)), TRUE, FALSE);
    }
    // send mail using PHP's mail() function
    else {
      // unset some headers because they will be added by the mail() function
      $headers_enc = $message->headers($headers);
      $headers_php = $message->_headers;
      unset($headers_php['To'], $headers_php['Subject']);
    
      // reset stored headers and overwrite
      $message->_headers = array();
      $header_str = $message->txtHeaders($headers_php);
    
      // #1485779
      if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        if (preg_match_all('/<([^@]+@[^>]+)>/', $headers_enc['To'], $m)) {
          $headers_enc['To'] = implode(', ', $m[1]);
          }
        }
       
      if (ini_get('safe_mode'))
        $sent = mail($headers_enc['To'], $headers_enc['Subject'], $msg_body, $header_str);
      else
        $sent = mail($headers_enc['To'], $headers_enc['Subject'], $msg_body, $header_str, "-f$from");
    }
  
    if ($sent) {
      //$cmail->plugins->exec_hook('message_sent', array('headers' => $headers, 'body' => $msg_body));
        
      if ($CONFIG['smtp_log']) {
        write_log('sendmail', sprintf("User %s [%s]; Message for %s; %s",
          $cmail->user->get_username(),
          $_SERVER['REMOTE_ADDR'],
          $mailto,
          !empty($smtp_response) ? join('; ', $smtp_response) : ''));
      }
    }

    return $sent;

  }  

}

?>