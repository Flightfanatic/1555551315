<?php

#### Virtual Cron 1.0 By WorldWideCreations.com
#### You are free to use this software any way you wish except that you may not
#### redistribute it in ANY WAY!
####
#### You may use the CronParser.php class file in any way under the terms of GPL
####
#### By installing this script, you agree that you understand that this software
#### is to be used at YOUR OWN RISK!


### This is the email address you will use as the "Mail From" part of any mailing sent from the script.
$mail_from = 'you@yoursite.com';

### If you are having issues, you can set this to true and when you call the script with no arguments, it 
### will display information on the process.
$debug = false;

### This is the password to access the script and MUST BE CHANGED!
$pass = "ginger";

### Extra layer of security.  You can only allow certain IP addresses or ranges to access admin section.
$restrict_ip_access = false;

### IP addresses or IP range array.  These IP addresses/ranges allowed to access admin.
$ip_ranges = array('192.168.','127.0.0.1');

######################## No more editing required. ########################

if ($pass == "ChangeMe") { die("You must change the default password first!"); }

require("./CronParser.php");

if ($_POST[action] == 'add_job') { Proc_Add_Job(); }
elseif ($_POST[action] == 'edit_job') { Edit_Job(); }
elseif ($_POST[action] == 'login') { Proc_Login(); }
elseif (isset($_GET[log_out])) { Log_Out(); }
elseif (isset($_GET[add_job])) { Add_Job(); }
elseif (isset($_GET[list_jobs])) { List_Jobs(); }
elseif (isset($_GET[delete])) { Delete_Job(); }
elseif (isset($_GET[Admin]) or isset($_GET[admin])) { Admin(); }

else { Display(); }

### Display Login Form
function Login() {
?>
<BODY STYLE="font-family: verdana, arial, helvetica, sans-serif">

<DIV ALIGN="center">
  <TABLE BORDER="0" CELLSPACING="1" WIDTH="81%" HEIGHT="330" CELLPADDING="3">
    <TR>
      <TD COLSPAN="2" HEIGHT="72" VALIGN="top" BGCOLOR="#9999CC">
      <H1 ALIGN="center">Virtual Crontab</H1>
      <P ALIGN="right"><FONT SIZE="2">Released Free By </FONT>
      <A HREF="http://www.worldwidecreations.com"><FONT SIZE="2">
      WorldWideCreations.com</FONT></A></P></TD>
    </TR>
    <TR>
      <TD WIDTH="14%" VALIGN="top" BGCOLOR="#F0F0F0" STYLE="border-left-width: 1px; border-right-style: solid; border-right-width: 1px; border-top-width: 1px; border-bottom-width: 1px; margin-left: 2">&nbsp;<A HREF="?Admin">Home</A><P>&nbsp;<A HREF="?add_job">Add 
      Job</A></P>
      <P>&nbsp;<A HREF="?list_jobs">List Jobs</A></P>
      <P>&nbsp;<A HREF="?log_out">Log Out</A></P>
      <P>&nbsp;<A HREF="http://www.worldwidecreations.com/forums/">Get Support</A></P>
      <P>&nbsp;<A HREF="http://www.google.com/search?en&q=what+is+crontab">What 
      Is</A> <BR>
&nbsp;<A HREF="http://www.google.com/search?en&q=what+is+crontab">Crontab?</A></TD>
      <TD WIDTH="85%" VALIGN="top">
<form method=post>
<input type=hidden name=action value=login>
Password: <input type=password name=pass> <input type=submit value=Login></form>
</TD>
    </TR>
  </TABLE>
</DIV>

</BODY>
<?php
}

### Process Login
function Proc_Login() {
global $pass;
if ($_POST[pass] == $pass) { 
setcookie("VC", md5($pass), 2147483647);
$_COOKIE[VC] = md5($pass);
Admin();
}
else { die("Invalid Password"); }
}

### Process Logout
function Log_Out() {
setcookie ("VC", "", time() - 3600);
$_COOKIE[VC] = "";
Admin();
}

### Admin Menu
function Admin() {
Validate();
?>
<BODY STYLE="font-family: verdana, arial, helvetica, sans-serif">

<DIV ALIGN="center">
  <TABLE BORDER="0" CELLSPACING="1" WIDTH="81%" HEIGHT="330" CELLPADDING="3">
    <TR>
      <TD COLSPAN="2" HEIGHT="72" VALIGN="top" BGCOLOR="#9999CC">
      <H1 ALIGN="center">Virtual Crontab</H1>
      <P ALIGN="right"><FONT SIZE="2">Released Free By </FONT>
      <A HREF="http://www.worldwidecreations.com"><FONT SIZE="2">
      WorldWideCreations.com</FONT></A></P></TD>
    </TR>
    <TR>
      <TD WIDTH="14%" VALIGN="top" BGCOLOR="#F0F0F0" STYLE="border-left-width: 1px; border-right-style: solid; border-right-width: 1px; border-top-width: 1px; border-bottom-width: 1px; margin-left: 2">&nbsp;<A HREF="?Admin">Home</A><P>&nbsp;<A HREF="?add_job">Add 
      Job</A></P>
      <P>&nbsp;<A HREF="?list_jobs">List Jobs</A></P>
      <P>&nbsp;<A HREF="?log_out">Log Out</A></P>
      <P>&nbsp;<A HREF="http://www.worldwidecreations.com/forums/">Get Support</A></P>
      <P>&nbsp;<A HREF="http://www.google.com/search?en&q=what+is+crontab">What 
      Is</A> <BR>
&nbsp;<A HREF="http://www.google.com/search?en&q=what+is+crontab">Crontab?</A></TD>
      <TD WIDTH="85%" VALIGN="top">
<?php
if (!is_writable("./jobs.php")) {
echo "<CENTER><B>File jobs.php is <U>NOT</U> writeable.  This is NECESSARY!  Please CHMOD 666 or in some cases, 777</B></CENTER><P><P><P>";
}
elseif (!is_writable("./lock.php")) {
echo "<CENTER><B>File lock.php is <U>NOT</U> writeable.  This is NECESSARY!  Please CHMOD 666 or in some cases, 777</B></CENTER><P><P><P>";
}

?>


Welcome to Virtual Crontab.&nbsp; With VC, 
      you can schedule tasks on your server without having to use Crontab on 
      your server.&nbsp; In most cases Crontab is the best alternative, but if 
      it is not available to you, or too hard to understand, then this is your 
      answer.<P>You should note that if you plan on executing programs on your 
      server (i.e. netstat), PHP will require safe mode off, OR the program must 
      reside in the
      <A HREF="http://us2.php.net/manual/en/features.safe-mode.php#ini.safe-mode-exec-dir">
      safe_mode_exec_dir</A> path defined in your php.ini file.&nbsp; 
      Alternately you can create a PHP or Perl (CGI) script to do the task, then 
      call that script with Virtual Crontab.</P>
      <P>You must embed an invisible graphic on a popular webpage on your 
      server, or any page that receives a lot of hits.&nbsp; Since VC cannot run 
      constantly, it needs to be activated.&nbsp; By creating an image tag like 
      this:</P>
      <P ALIGN="center">&lt;img src=&quot;http://www.yoursite.com/virtualcron/virtualcron.php&gt;
      </P>
      <P ALIGN="left">the script will then be activated every time the &quot;image&quot; 
      is called.&nbsp; VC will output a 1X1 pixel clear gif on the page.</P>
      <P ALIGN="left">To begin adding scheduled tasks, start with the
      <A HREF="?add_job">Add Job</A> link.</P>
      <P ALIGN="left">If you are unfamiliar with Crontab commands, you can use
      <A HREF="phpcrontab.php">this tool</A> to 
      create a custom time command.&nbsp; Please note, you must only include the 
      timing, not the actual task that will be executed.&nbsp; For example, if 
      you use the tool to start netstat every 5 minutes, the tool will output:</P>
      <P ALIGN="center">0,5,10,15,20,25,30,35,40,45,50,55 * * * * /bin/netstat 
      -a</P>
      <P ALIGN="left">You should only use this part in the &quot;Run Custom Timing&quot; 
      form field:</P>
      <P ALIGN="center">0,5,10,15,20,25,30,35,40,45,50,55 * * * *</P>
      <P ALIGN="left">For further support, please visit our
      <A HREF="http://www.worldwidecreations.com/forums/">support forum</A> at
      <A HREF="http://www.worldwidecreations.com">worldwidecreations.com</A>.</P>
      <P>&nbsp;</TD>
    </TR>
  </TABLE>
</DIV>

</BODY>

<?php
}

### List out jobs
function List_Jobs() {
Validate();
?>
<BODY STYLE="font-family: verdana, arial, helvetica, sans-serif">

<DIV ALIGN="center">
  <TABLE BORDER="0" CELLSPACING="1" WIDTH="81%" HEIGHT="330" CELLPADDING="3">
    <TR>
      <TD COLSPAN="2" HEIGHT="72" VALIGN="top" BGCOLOR="#9999CC">
      <H1 ALIGN="center">Virtual Crontab</H1>
      <P ALIGN="right"><FONT SIZE="2">Released Free By </FONT>
      <A HREF="http://www.worldwidecreations.com"><FONT SIZE="2">
      WorldWideCreations.com</FONT></A></P></TD>
    </TR>
    <TR>
      <TD WIDTH="14%" VALIGN="top" BGCOLOR="#F0F0F0" STYLE="border-left-width: 1px; border-right-style: solid; border-right-width: 1px; border-top-width: 1px; border-bottom-width: 1px; margin-left: 2">&nbsp;<A HREF="?Admin">Home</A><P>&nbsp;<A HREF="?add_job">Add 
      Job</A></P>
      <P>&nbsp;<A HREF="?list_jobs">List Jobs</A></P>
      <P>&nbsp;<A HREF="?log_out">Log Out</A></P>
      <P>&nbsp;<A HREF="http://www.worldwidecreations.com/forums/">Get Support</A></P>
      <P>&nbsp;<A HREF="http://www.google.com/search?en&q=what+is+crontab">What 
      Is</A> <BR>
&nbsp;<A HREF="http://www.google.com/search?en&q=what+is+crontab">Crontab?</A></TD>
      <TD WIDTH="85%" VALIGN="top">
<?php

$jobs = file("./jobs.php");
$cron = new CronParser();
echo "<form method=post><input type=hidden name=action value=edit_job>";
foreach ($jobs as $job) {
$job = unserialize($job);
echo "Edit: <input type=radio name=job value=$job[3]> Job #$job[0]: <small>$job[4] " . $job[1][command] . "</small> <a href=\"?delete=$job[0]\" onclick=\"return confirm('Are you sure you want to delete this job?')\">Delete?</a><P>";
}
echo "<input type=submit name=submit value=Edit></form>";

?>
</TD>
    </TR>
  </TABLE>
</DIV>

</BODY>

<?php
 
}

### Delete Job
function Delete_Job($did=0) {
Validate();
if (isset($_GET[delete])) { $did = $_GET[delete]; } 
$lock = fopen('./lock.php', 'r');
flock($lock, LOCK_EX) or die("<P>Could not lock file<P>");
$jobs = file("./jobs.php");
$fh = fopen('./jobs.php','w') or die("Cannot open file");
if (flock($fh, LOCK_EX)) {
foreach ($jobs as $job) {
$job = unserialize($job);
if ($job[0] != $did) {
$job = serialize($job);
fwrite($fh,$job . "\n");
}
}
}
flock($fh, LOCK_UN);
fclose($fh);
flock($lock, LOCK_UN);
fclose($lock);
if (isset($_GET[delete])) {
echo "<CENTER><B>Job #$did Deleted</B></CENTER><P>";
List_Jobs();
} else { return; }
}

### Add Job
function Add_Job() {
Validate();
?>
<BODY STYLE="font-family: verdana, arial, helvetica, sans-serif">

<DIV ALIGN="center">
  <TABLE BORDER="0" CELLSPACING="1" WIDTH="81%" HEIGHT="330" CELLPADDING="3">
    <TR>
      <TD COLSPAN="2" HEIGHT="72" VALIGN="top" BGCOLOR="#9999CC">
      <H1 ALIGN="center">Virtual Crontab</H1>
      <P ALIGN="right"><FONT SIZE="2">Released Free By </FONT>
      <A HREF="http://www.worldwidecreations.com"><FONT SIZE="2">
      WorldWideCreations.com</FONT></A></P></TD>
    </TR>
    <TR>
      <TD WIDTH="14%" VALIGN="top" BGCOLOR="#F0F0F0" STYLE="border-left-width: 1px; border-right-style: solid; border-right-width: 1px; border-top-width: 1px; border-bottom-width: 1px; margin-left: 2">&nbsp;<A HREF="?Admin">Home</A><P>&nbsp;<A HREF="?add_job">Add 
      Job</A></P>
      <P>&nbsp;<A HREF="?list_jobs">List Jobs</A></P>
      <P>&nbsp;<A HREF="?log_out">Log Out</A></P>
      <P>&nbsp;<A HREF="http://www.worldwidecreations.com/forums/">Get Support</A></P>
      <P>&nbsp;<A HREF="http://www.google.com/search?en&q=what+is+crontab">What 
      Is</A> <BR>
&nbsp;<A HREF="http://www.google.com/search?en&q=what+is+crontab">Crontab?</A></TD>
      <TD WIDTH="85%" VALIGN="top">
* = wildcard<P>
<form method=post>
<input type=hidden name=action value="add_job">
Minute: <INPUT TYPE="text" NAME="minute" SIZE="2" MAXLENGTH="2" value="*"> (0 - 59)<P>
Hour: <INPUT TYPE="text" NAME="hour" SIZE="2" MAXLENGTH="2" value="*"> (0 - 23)<P> 
Day Of Month: <INPUT TYPE="text" NAME="day" SIZE="2" MAXLENGTH="2" value="*"> (1 - 31)<P>
Month: <INPUT TYPE="text" NAME="month" SIZE="2" MAXLENGTH="2" value="*"> (1 - 12)<P>
Day Of Week: <INPUT TYPE="text" NAME="dayw" SIZE="2" MAXLENGTH="2" value="*"> (0 - 6) (Sunday=0)<P>

<B>OR</B> Run Custom Timing <input type=checkbox name=custom value=1>: <input type=text size=80 name=custom_time><P>

Server Command:<input type=radio name=callweb value=command CHECKED> Activate URL: <input type=radio name=callweb value=url><P>
Command or URL to Execute: <input type=text size=80 name=command><P>
Email Results To: <input type=text name=email> (optional)<P>
<input type=submit name=submit value="Add Job">
</form>

</TD>
    </TR>
  </TABLE>
</DIV>

</BODY>
<?php
}

### Edit Job
function Edit_Job() {
Validate();
?>



<BODY STYLE="font-family: verdana, arial, helvetica, sans-serif">

<DIV ALIGN="center">
  <TABLE BORDER="0" CELLSPACING="1" WIDTH="81%" HEIGHT="330" CELLPADDING="3">
    <TR>
      <TD COLSPAN="2" HEIGHT="72" VALIGN="top" BGCOLOR="#9999CC">
      <H1 ALIGN="center">Virtual Crontab</H1>
      <P ALIGN="right"><FONT SIZE="2">Released Free By </FONT>
      <A HREF="http://www.worldwidecreations.com"><FONT SIZE="2">
      WorldWideCreations.com</FONT></A></P></TD>
    </TR>
    <TR>
      <TD WIDTH="14%" VALIGN="top" BGCOLOR="#F0F0F0" STYLE="border-left-width: 1px; border-right-style: solid; border-right-width: 1px; border-top-width: 1px; border-bottom-width: 1px; margin-left: 2">&nbsp;<A HREF="?Admin">Home</A><P>&nbsp;<A HREF="?add_job">Add 
      Job</A></P>
      <P>&nbsp;<A HREF="?list_jobs">List Jobs</A></P>
      <P>&nbsp;<A HREF="?log_out">Log Out</A></P>
      <P>&nbsp;<A HREF="http://www.worldwidecreations.com/forums/">Get Support</A></P>
      <P>&nbsp;<A HREF="http://www.google.com/search?en&q=what+is+crontab">What 
      Is</A> <BR>
&nbsp;<A HREF="http://www.google.com/search?en&q=what+is+crontab">Crontab?</A></TD>
      <TD WIDTH="85%" VALIGN="top">

* = wildcard<P>
<form method=post>
<input type=hidden name=edit value=1>
<input type=hidden name=action value="add_job">
<input type=hidden name=id value="<?php echo $_POST[job] ?>">
<?php
$jobs = file("./jobs.php");
foreach ($jobs as $job) {
$job = unserialize($job);
if ($job[0] == $_POST[job]) { $POSTER = $job[1]; }
}
?>
<input type=hidden name=last_run value="<?php echo $job[3] ?>">

Minute: <INPUT TYPE="text" NAME="minute" SIZE="2" MAXLENGTH="2" value="<?php echo $POSTER[minute] ?>"> (0 - 59)<P>
Hour: <INPUT TYPE="text" NAME="hour" SIZE="2" MAXLENGTH="2" value="<?php echo $POSTER[hour] ?>"> (0 - 23)<P> 
Day Of Month: <INPUT TYPE="text" NAME="day" SIZE="2" MAXLENGTH="2" value="<?php echo $POSTER[day] ?>"> (1 - 31)<P>
Month: <INPUT TYPE="text" NAME="month" SIZE="2" MAXLENGTH="2" value="<?php echo $POSTER[month] ?>"> (1 - 12)<P>
Day Of Week: <INPUT TYPE="text" NAME="dayw" SIZE="2" MAXLENGTH="2" value="<?php echo $POSTER[minute] ?>"> (0 - 6) (Sunday=0)<P>

<B>OR</B> Run Custom Timing <input type=checkbox name=custom value=1<?php if ($POSTER[custom] == 1) { echo " CHECKED"; } ?> >: <input type=text size=80 name=custom_time value="<?php echo $POSTER[custom_time] ?>"><P>

Server Command:<input type=radio name=callweb value=command<?php if ($POSTER[callweb] == "command") { echo " CHECKED"; } ?>> Activate URL: <input type=radio name=callweb value=url<?php if ($POSTER[callweb] == "url") { echo " CHECKED"; } ?>><P>
Command or URL to Execute: <input type=text size=80 name=command value="<?php echo $POSTER[command] ?>"><P>
Email Results To: <input type=text name=email value="<?php echo $POSTER[email] ?>"> (optional)<P>
<input type=submit name=submit value="Edit Job">
</form>

</TD>
    </TR>
  </TABLE>
</DIV>

</BODY>

<?php
}


### Add Job To Array
function Proc_Add_Job() {
Validate();
unset($_POST[submit],$_POST[action]);
$cron = new CronParser();
if ($_POST[edit] == 1) { $job[0] = $_POST[id]; }
else { $job[0] = time(); }
$job[1] = $_POST;
if ($_POST[edit] == 1) { $job[3] = $_POST[last_run]; $edit = true;}
else { $job[3] = time(); }
if ($edit) { Delete_Job($_POST[id]); }
unset($_POST[last_run],$_POST[edit],$_POST[id]);

if (isset($_POST[custom])) {
if (!$cron->calcLastRan($_POST[custom_time])) { die("Invalid Cron Entry, Please Click Back"); }
$job[4] = $_POST[custom_time];
}
else {
$the_job = $job[1]['minute'] . " " . $job[1]['hour'] . " " . $job[1]['day'] . " " . $job[1]['month'] . " " . $job[1]['dayw']; 

if (!$cron->calcLastRan($the_job)) { die("Invalid Cron Entry, Please Click Back"); }
$job[4] = $the_job;
}
$job = serialize($job);

$fh = fopen('./jobs.php','a') or die("Cannot open file");
if (flock($fh, LOCK_EX)) {
fwrite($fh,$job . "\n");
flock($fh, LOCK_UN);
}
else { echo "<P>Could not lock file to add job?<P>"; }

if ($edit) {
echo "<CENTER><B>Job #$did Edited</B></CENTER><P>";
List_Jobs();
} 
else { 
echo "<CENTER><B>Job Added</B></CENTER><P>"; 
Admin();
}
}

### Display graphic or debug info
function Display() {
global $debug;
$lock = fopen('./lock.php', 'r');
flock($lock, LOCK_EX) or die("<P>Could not lock file<P>");
if ($debug) { echo "<P>Assuming Lock<P>"; }

$jobs = file("./jobs.php");
$cron = new CronParser();
$x = 0;
foreach ($jobs as $job) {
$job = unserialize($job);
$cron->calcLastRan($job[4]);
if ($debug) { echo "<P>Examining $job[0]. <BR>Current Time: " . date('r', time()) . " <BR>Should have been executed on: " . date('r', $cron->getLastRanUnix()) . ". <BR>Was executed on " . date('r', $job[3]); }
if ($job[3] < $cron->getLastRanUnix()) {
if ($job[1][callweb] == 'url') {
$results = implode(file($job[1][command]));
}
else {
$handle = popen($job[1][command],'r') or die($php_errormsg);
while (!feof($handle))
   {
       $results .= fgets($handle, 4096);

   }

}
if ($job[1][email] != '') {
$to      = $job[1][email];
$subject = 'Cron Results For Job: ' . $job[3];
$message = 'Here are the results of job ' . $job[3] . '\n' . $results;
$headers = 'From: ' . $mail_from . "\r\n" .
   'Reply-To: ' . $mail_from . "\r\n" .
   'X-Mailer: PHP/' . phpversion();
mail($to, $subject, $message, $headers);
}
$job[3] = time();
if ($debug) { echo "<P>Job #$job[0] EXECUTED<P>"; }
}
$job = serialize($job);
$fjobs[$x] = $job;
$x++;
}
$fh = fopen('./jobs.php','w') or die("Cannot open file");
if (flock($fh, LOCK_EX)) {
foreach ($fjobs as $jobs) {
fwrite($fh,$jobs . "\n");
}
flock($fh, LOCK_UN);
fclose($fh);

}
else { echo "<P>Could not lock file?<P>"; }
flock($lock, LOCK_UN);
fclose($lock);
if ($debug) { echo "<P>Finished Cronjob(s)"; }
if (!$debug) {

header("Content-type: image/gif");
echo base64_decode("R0lGODlhAQABAIAAAAAAAAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
}
}

### Validate Admin
function Validate() {
global $pass,$restrict_ip_access;
if ($restrict_ip_access) { Validate_IP(); }
if ($_COOKIE[VC] == md5($pass)) { return true; } else { Login(); exit; }
}

### Validate IP
function Validate_IP() {
global $ip_ranges;
foreach ($ip_ranges as $ip) {
if (preg_match("/$ip/",$_SERVER[REMOTE_ADDR])) { $ip_ok = true; };
}
if (!$ip_ok) { die("IP: $_SERVER[REMOTE_ADDR] not in \$ip_range variable.  Access Denied!"); }
}

?>