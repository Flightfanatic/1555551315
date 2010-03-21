<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Starting Installer | Crystal Installer</title>
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
		<p>   <center><h1>Success!</h1>
		<p class="style1">Starting Configuration Process</p>
         <p>
<IMG SRC="activity.gif"><br>
<center>
<?php
$file = '../skins/default/templates/error.html.temp';
$newfile = '../skins/default/templates/error.html';

if (!copy($file, $newfile)) {
    echo "failed to copy $file...\n";
}
echo "<meta http-equiv=\"refresh\" content=\"0; url=index.php?_step=1\" />\n";
?>

  <p><p>
</center>
</p>
	</div>

</div>

</div><!-- End demo -->

<div class="demo-description">

</div>
<!-- End demo-description -->
	</body>
</html>







