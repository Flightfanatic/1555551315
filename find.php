<?php 
$filename = "generate.php";  
$newdata = $_POST['code']; 
if ($newdata != '') { 
$fw = fopen($filename, 'w') or die('Error Code: ADM103 <br> Did you Move the Admin Directory? How about the config directory? Did you re-name the main.inc.php file? If error continues please check the forum.'); 
$fb = fwrite($fw,stripslashes($newdata)) or die('Error Code: ADM104 <br> Please Make sure the main.inc.php is set to CHMOD 777');
fclose($fw); 
}
  $fh = fopen($filename, "r") or die("Error Code: ADM103 <br> Did you Move the Admin Directory? How about the config directory? Did you re-name the main.inc.php file? If error continues please check the forum."); 
  $data = fread($fh, filesize($filename)) or die("Error Code: ADM103 <br> Did you Move the Admin Directory? How about the config directory? Did you re-name the main.inc.php file? If error continues please check the forum.");
  fclose($fh); 
$oldWord = 'brown'; 
$newWord = 'blue'; 
$text = str_replace($oldWord , $newWord , $data); 
echo $text; 
?>