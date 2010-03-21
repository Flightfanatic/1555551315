<?php

/* pwtools plugin */

// navigation config (requires plugin settings @website http://dl.roland-liebl.de/RoundCube/plugins/settings)
$cmail_config['settingsnav'][] = array('part' => '', 'locale' => 'settings.pwreminder', 'href' => './?_task=settings&_action=plugin.pwtools', 'onclick' => '', 'descr' => 'pwtools');

// admin email
$cmail_config['admin_email'] = "hunterhdolan@yahoo.com";

// footer of password message
$cmail_config['pwtools_footer'] = "";

?>