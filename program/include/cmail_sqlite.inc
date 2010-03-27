<?php

/*
 +-----------------------------------------------------------------------+
 | program/include/cmail_sqlite.inc                                      |
 |                                                                       |
 | This file is part of the Crystal Webmail client                       |
 | Copyright (C) 2005-2007, Crystal Dev. - United States                 |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Provide callback functions for sqlite that will emulate             |
 |   sone MySQL functions                                                |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: cmail_sqlite.inc 850 2007-10-03 00:13:32Z ihug $

*/

/**
 * Callback functions for sqlite database interface
 *
 * @package Database
 */


function cmail_sqlite_from_unixtime($timestamp)
  {
  $timestamp = trim($timestamp);
  if (!preg_match("/^[0-9]+$/is", $timestamp))
    $ret = strtotime($timestamp);
  else
    $ret = $timestamp;
    
  $ret = date("Y-m-d H:i:s", $ret);
  cmail_sqlite_debug("FROM_UNIXTIME ($timestamp) = $ret");
  return $ret;
  }


function cmail_sqlite_unix_timestamp($timestamp="")
  {
  $timestamp = trim($timestamp);
  if (!$timestamp)
    $ret = time();
  else if (!preg_match("/^[0-9]+$/is", $timestamp))
    $ret = strtotime($timestamp);
  else
    $ret = $timestamp;

  cmail_sqlite_debug("UNIX_TIMESTAMP ($timestamp) = $ret");
  return $ret;
  }


function cmail_sqlite_now()
  {
  cmail_sqlite_debug("NOW() = ".date("Y-m-d H:i:s"));
  return date("Y-m-d H:i:s");
  }


function cmail_sqlite_md5($str)
  {
  return md5($str);
  }


function cmail_sqlite_debug($str)
  {
  //console($str);
  }
  
?>
