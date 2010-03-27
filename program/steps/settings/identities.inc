<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/settings/identities.inc                                 |
 |                                                                       |
 | This file is part of the Crystal Webmail client                       |
 | Copyright (C) 2005-2007, Crystal Dev. - United States                 |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Manage identities of a user account                                 |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <Crystal@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: identities.inc 2006 2008-10-24 07:57:21Z alec $

*/

define('IDENTITIES_LEVEL', intval($cmail->config->get('identities_level', 0)));

$OUTPUT->set_pagetitle(cmail_label('identities'));

$OUTPUT->include_script('list.js');


// similar function as /steps/addressbook/func.inc::cmail_contact_frame()
function cmail_identity_frame($attrib)
  {
  global $OUTPUT;

  if (!$attrib['id'])
    $attrib['id'] = 'rcmIdentityFrame';

  $attrib['name'] = $attrib['id'];

  $OUTPUT->set_env('contentframe', $attrib['name']);

  return html::iframe($attrib);
  }

$OUTPUT->add_handler('identityframe', 'cmail_identity_frame');
$OUTPUT->set_env('identities_level', IDENTITIES_LEVEL);

$OUTPUT->send('identities');
?>