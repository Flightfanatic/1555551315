<?php

/*
 +-----------------------------------------------------------------------+
 | program/include/cmail_result_set.php                                  |
 |                                                                       |
 | This file is part of the Crystal Webmail client                       |
 | Copyright (C) 2006-2010, Crystal Dev. - United States                 |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Class representing an address directory result set                  |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: cmail_result_set.php 328 2006-08-30 17:41:21Z thomasb $

*/


/**
 * Crystal result set class.
 * Representing an address directory result set.
 *
 * @package Addressbook
 */
class cmail_result_set
{
  var $count = 0;
  var $first = 0;
  var $current = 0;
  var $records = array();
  
  function __construct($c=0, $f=0)
  {
    $this->count = (int)$c;
    $this->first = (int)$f;
  }
  
  function add($rec)
  {
    $this->records[] = $rec;
  }
  
  function iterate()
  {
    return $this->records[$this->current++];
  }
  
  function first()
  {
    $this->current = 0;
    return $this->records[$this->current++];
  }
  
  // alias
  function next()
  {
    return $this->iterate();
  }
  
  function seek($i)
  {
    $this->current = $i;
  }
  
}