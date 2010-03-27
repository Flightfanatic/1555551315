<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link type="text/css" href="css/redmond/jquery-ui-1.7.2.custom.css" rel="stylesheet" />	
		<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>

		<script type="text/javascript">
			$(function(){


	$('#tabs').tabs({
    select: function(event, ui) {
        var url = $.data(ui.tab, 'load.tabs');
        if( url ) {
            location.href = url;
            return false;
        }
        return true;
    }
});
	</script>

	

				
		</script>
		<style type="text/css">
			/*demo page css*/
			body{ font: 62.5% "Trebuchet MS", sans-serif; margin: 50px;}
			.demoHeaders { margin-top: 2em; }
			#dialog_link {padding: .4em 1em .4em 20px;text-decoration: none;position: relative;}
			#dialog_link span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}
			ul#icons {margin: 0; padding: 0;}
			ul#icons li {margin: 2px; position: relative; padding: 4px 0; cursor: pointer; float: left;  list-style: none;}
			ul#icons span.ui-icon {float: left; margin: 0 4px;}
		.style1 {
				font-size: large;
}
		</style>	
	</head>
	<body>
<a href="http://crystalwebmail.com"><img src="images/crystal_logo.png" border="0" alt="Crystal Webmail Installer" /></a>
<script type="text/javascript">
	$(function() {
		$("#tabs").tabs();
	});
	</script>


<div class="demo">

<div id="tabs">
	
	<div id="tabs-1">
<?php

ini_set('error_reporting', E_ALL&~E_NOTICE);
ini_set('display_errors', 1);

define('INSTALL_PATH', realpath(dirname(__FILE__) . '/../').'/');
define('cmail_CONFIG_DIR', INSTALL_PATH . 'config');

$include_path  = INSTALL_PATH . 'program/lib' . PATH_SEPARATOR;
$include_path .= INSTALL_PATH . 'program' . PATH_SEPARATOR;
$include_path .= INSTALL_PATH . 'program/include' . PATH_SEPARATOR;
$include_path .= ini_get('include_path');

set_include_path($include_path);

require_once 'utils.php';
require_once 'main.inc';


$RCI = cmail_install::get_instance();
$RCI->load_config();

if (isset($_GET['_getfile']) && in_array($_GET['_getfile'], array('main', 'db'))) {
  $filename = $_GET['_getfile'] . '.inc.php';
  if (!empty($_SESSION[$filename])) {
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    echo $_SESSION[$filename];
    exit;
  }
  else {
    header('HTTP/1.0 404 Not found');
    die("The requested configuration was not found. Please run the installer from the beginning.");
  }
}

if ($RCI->configured && ($RCI->getprop('enable_installer') || $_SESSION['allowinstaller']) &&
    isset($_GET['_mergeconfig']) && in_array($_GET['_mergeconfig'], array('main', 'db'))) {
  $filename = $_GET['_mergeconfig'] . '.inc.php';

  header('Content-type: text/plain');
  header('Content-Disposition: attachment; filename="'.$filename.'"');
  
  $RCI->merge_config();
  echo $RCI->create_config($_GET['_mergeconfig'], true);
  exit;
}

// go to 'check env' step if we have a local configuration
if ($RCI->configured && empty($_REQUEST['_step'])) {
  header("Location: ./?_step=1");
  exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Crystal Webmail Installer</title>
<meta name="Robots" content="noindex,nofollow" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="styles.css" />
<script type="text/javascript" src="client.js"></script>
</head>

<body>

<div id="banner">
  <div id="header">
  </div>
 </div>

<div id="content">



<?php
$include_steps = array('./welcome.html', './check.php', './config.php', './test.php');

if ($include_steps[$RCI->step]) {
  include $include_steps[$RCI->step];
}
else {
  header("HTTP/1.0 404 Not Found");
  echo '<h2 class="error">Invalid step</h2>';
}

?>
</div>

<div id="footer">
  Installer by the Hunter Dolan! Copyright &copy; 2010 - All Rights Reserved Baby;&nbsp;
  Icons by <a href="http://famfamfam.com">famfamfam</a>
</div>
</body>
</html>
	</div>

</div>

</div><!-- End demo -->

<div class="demo-description">

</div>
<!-- End demo-description -->
	</body>
</html>

