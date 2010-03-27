<?php
/*

 +-----------------------------------------------------------------------+
 | bin/html2text.php                                                     |
 |                                                                       |
 | This file is part of the Crystal Webmail client                       |
 | Copyright (C) 2005-2010, Crystal Dev Team - United States             |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Convert HTML message to plain text                                  |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: html2text.php 2237 2010-01-17 01:55:39Z till $

*/

define('INSTALL_PATH', realpath(dirname(__FILE__) . '/..') . '/');
require INSTALL_PATH . 'program/include/iniset.php';

$cmail = cmail::get_instance();

if (!empty($cmail->user->ID)) {
  $converter = new html2text($HTTP_RAW_POST_DATA);

  header('Content-Type: text/plain; charset=UTF-8');
  print trim($converter->get_text());
}
else {
  header("HTTP/1.0 403 Forbidden");
  echo "Requires a valid user session";
}

?>
