<?php

#### This script was NOT written by World Wide Creations.  It was written by the author located here:
#### 
#### http://www.mtsdev.com/opensource/
#### 
#### And titled "phpCrontab Tutorial Tool"
#### 
#### At the time of packaging, the author indicates this about their software: "Most can be used as standalone 
#### applications, or applied as parts to larger applications. Since they are open source, 
#### it really doesn't matter what you do with them, but be aware that they come with no warranty, 
#### and are to be used at your own risk."

?>
<HTML>
<head>
<TITLE>phpCrontab - Crontab Tutorial Tool</TITLE>
<meta name="title" content="phpCrontab - Crontab Tutorial Tool">
<meta name="description" content="Tutorial for learning the basics of cron using a simple web interface.">
<meta name="keywords" content="php, cron, crontab, tutorial, tool, phpcrontab">
<meta name="copyright" content="2005 Tri-County Web Design, LLC">
<meta name="Abstract" content="Written in PHP (See php.net).  Brought to you by Tri-County Web Design, LLC (tricountywebdesign.com).">
</head>

<BODY bgcolor="#FFFFFF" link="red" vlink="red" alink="red">
<?


function form($error=false) {
if ($error) print $error . "<br><br>";
print '
<form action="'.$_SERVER["PHP_SELF"].'" method="post">
  <table width="500" border="0" cellspacing="2" cellpadding="1" align="center">
    <tr> 
      <td colspan="7" valign="top" align="left"><font face="Arial, Helvetica, sans-serif"><b>phpCrontab 
        Tutorial Tool</b><br><font size="2">Fill in the appropriate information to generate a crontab entry.</font><br>
		<a style="font-size:12px" href="/opensource/phpcrontab-tutorial-052505.tar.gz">Get the source code for this file here</a>.</font>
      <br><br></td>
    </tr>
  </table>  <table width="500" border="0" cellspacing="2" cellpadding="1" align="center">
    <tr bgcolor="#CCCCCC"> 
      <td colspan="2"> 
        <div align="center"><font face="Arial, Helvetica, sans-serif">MINUTES</font></div>
      </td>
      <td colspan="2"> 
        <div align="center"><font face="Arial, Helvetica, sans-serif">HOURS</font></div>
      </td>
    </tr>
    <tr> 
      <td width="73" height="28"><font face="Arial, Helvetica, sans-serif"><b><u>Custom</u>:</b></font></td>
      <td width="164" height="28"><font face="Arial, Helvetica, sans-serif"><b><u>Templates</u>:</b></font></td>
      <td width="75"><font face="Arial, Helvetica, sans-serif"><b><u>Custom</u>:</b></font></td>
      <td width="142"><font face="Arial, Helvetica, sans-serif"><b><u>Templates</u>:</b></font></td>
    </tr>
    <tr> 
      <td width="73"> 
        <table width="99%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td valign="top" width="28%"> 
              <input type="radio" name="minutes" value="custom">
            </td>
            <td valign="top" width="72%"> <font face="Arial, Helvetica, sans-serif">
              <select name="custminutes[]" size="10" multiple>
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
                <option value="21">21</option>
                <option value="22">22</option>
                <option value="23">23</option>
                <option value="24">24</option>
                <option value="25">25</option>
                <option value="26">26</option>
                <option value="27">27</option>
                <option value="28">28</option>
                <option value="29">29</option>
                <option value="30">30</option>
                <option value="31">31</option>
                <option value="32">32</option>
                <option value="33">33</option>
                <option value="34">34</option>
                <option value="35">35</option>
                <option value="36">36</option>
                <option value="37">37</option>
                <option value="38">38</option>
                <option value="39">39</option>
                <option value="40">40</option>
                <option value="41">41</option>
                <option value="42">42</option>
                <option value="43">43</option>
                <option value="44">44</option>
                <option value="45">45</option>
                <option value="46">46</option>
                <option value="47">47</option>
                <option value="48">48</option>
                <option value="49">49</option>
                <option value="50">50</option>
                <option value="51">51</option>
                <option value="52">52</option>
                <option value="53">53</option>
                <option value="54">54</option>
                <option value="55">55</option>
                <option value="56">56</option>
                <option value="57">57</option>
                <option value="58">58</option>
                <option value="59">59</option>
              </select>
              </font></td>
          </tr>
        </table>
      </td>
      <td width="164" valign="top"> <font face="Arial, Helvetica, sans-serif"> 
        <input type="radio" name="minutes" value="*" checked>
        Every Minute<br>
        <input type="radio" name="minutes" value="0,2,4,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,42,44,46,48,50,52,54,56,58">
        Even Minutes<br>
        <input type="radio" name="minutes" value="1,3,5,7,9,11,13,15,17,19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49,51,53,55,57,59">
        Odd Minutes<br>
        <input type="radio" name="minutes" value="0,5,10,15,20,25,30,35,40,45,50,55">
        Every 5 Minutes<br>
        <input type="radio" name="minutes" value="0,15,30,45">
        Every 1/4 Hour<br>
        <input type="radio" name="minutes" value="0,30">
        Every 1/2 Hour<br>
        <input type="radio" name="minutes" value="0">
        Once Per Hour</font></td>
      <td width="75" valign="top"> 
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td valign="top" width="28%"> 
              <input type="radio" name="hours" value="custom">
            </td>
            <td valign="top" width="72%"> <font face="Arial, Helvetica, sans-serif">
              <select name="custhours[]" size="10" multiple>
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
                <option value="21">21</option>
                <option value="22">22</option>
                <option value="23">23</option>
              </select>
              </font></td>
          </tr>
        </table>
      </td>
      <td width="142" valign="top"> <font face="Arial, Helvetica, sans-serif">
        <input type="radio" name="hours" value="*" checked>
        Every Hour<br>
        <input type="radio" name="hours" value="0,2,4,6,8,10,12,14,16,18,20,22">
        Even Hours<br>
        <input type="radio" name="hours" value="1,3,5,7,9,11,13,15,17,19,21,23">
        Odd Hours<br>
        <input type="radio" name="hours" value="0,6,12,18">
        Every 1/4 Day<br>
        <input type="radio" name="hours" value="0,12">
        Every 1/2 Day<br>
        <input type="radio" name="hours" value="0">
        Once Per Day</font></td>
    </tr>
  </table>
  <br>
  <table width="500" border="0" cellspacing="2" cellpadding="1" align="center">
    <tr bgcolor="#CCCCCC"> 
    <td colspan="2"> 
        <div align="center"><font face="Arial, Helvetica, sans-serif">DAYS OF 
          MONTH</font></div>
    </td>
    <td colspan="2"> 
      <div align="center"><font face="Arial, Helvetica, sans-serif">MONTHS</font></div>
    </td>
  </tr>
  <tr> 
    <td width="73" height="28"><font face="Arial, Helvetica, sans-serif"><b><u>Custom</u>:</b></font></td>
    <td width="164" height="28"><font face="Arial, Helvetica, sans-serif"><b><u>Templates</u>:</b></font></td>
    <td width="75"><font face="Arial, Helvetica, sans-serif"><b><u>Custom</u>:</b></font></td>
    <td width="142"><font face="Arial, Helvetica, sans-serif"><b><u>Templates</u>:</b></font></td>
  </tr>
  <tr> 
    <td width="73"> 
      <table width="99%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
            <td valign="top" width="28%"> 
              <input type="radio" name="days" value="custom">
          </td>
            <td valign="top" width="72%"><font face="Arial, Helvetica, sans-serif"> 
              <select name="custdays[]" size="10" multiple>
                <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
              <option value="13">13</option>
              <option value="14">14</option>
              <option value="15">15</option>
              <option value="16">16</option>
              <option value="17">17</option>
              <option value="18">18</option>
              <option value="19">19</option>
              <option value="20">20</option>
              <option value="21">21</option>
              <option value="22">22</option>
              <option value="23">23</option>
              <option value="24">24</option>
              <option value="25">25</option>
              <option value="26">26</option>
              <option value="27">27</option>
              <option value="28">28</option>
              <option value="29">29</option>
              <option value="30">30</option>
              <option value="31">31</option>
            </select>
            </font></td>
        </tr>
      </table>
    </td>
    <td width="164" valign="top"> <font face="Arial, Helvetica, sans-serif"> 
        <input type="radio" name="days" value="*" checked>
      Every Day<br>
        <input type="radio" name="days" value="2,4,6,8,10,12,14,16,18,20,22,24,26,28,30">
      Even Days<br>
        <input type="radio" name="days" value="1,3,5,7,9,11,13,15,17,19,21,23,25,27,29,31">
      Odd Days<br>
        <input type="radio" name="days" value="1,15">
      Twice Per Month<br>
        <input type="radio" name="days" value="1">
      Once Per Month</font></td>
    <td width="75" valign="top"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td valign="top" width="28%"> 
              <input type="radio" name="months" value="custom">
          </td>
            <td valign="top" width="72%"><font face="Arial, Helvetica, sans-serif"> 
              <select name="custmonths[]" size="10" multiple>
                <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
            </select>
            </font></td>
        </tr>
      </table>
    </td>
    <td width="142" valign="top">
        <input type="radio" name="months" value="*" checked>
      <font face="Arial, Helvetica, sans-serif">Every Month<br>
        <input type="radio" name="months" value="2,4,6,8,10,12">
      Even Months<br>
        <input type="radio" name="months" value="1,3,5,7,9,11">
      Odd Months<br>
        <input type="radio" name="months" value="1,4,7,10,">
      Every 1/4 Year<br>
        <input type="radio" name="months" value="1,7">
      Every 1/2 Year<br>
        <input type="radio" name="months" value="1">
      Once Per Year</font></td>
  </tr>
</table>
<br>
  <table width="500" border="0" cellspacing="2" cellpadding="1" align="center">
    <tr bgcolor="#CCCCCC">
      <td colspan="2">
        <div align="center"><font face="Arial, Helvetica, sans-serif">WEEKDAYS</font></div>
      </td>
    </tr>
    <tr>
      <td width="120"><font face="Arial, Helvetica, sans-serif"><b><u>Custom</u>:</b></font></td>
      <td width="370"><font face="Arial, Helvetica, sans-serif"><b><u>Templates</u>:</b></font></td>
    </tr>
    <tr>
      <td width="120" valign="top">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="12%" valign="top">
              <input type="radio" name="weekdays" value="custom">
            </td>
            <td width="88%" valign="top"><font face="Arial, Helvetica, sans-serif">
              <select name="custweekdays[]" size="7" multiple>
                <option value="0">Sunday</option>
                <option value="1">Monday</option>
                <option value="2">Tuesday</option>
                <option value="3">Wednesday</option>
                <option value="4">Thursday</option>
                <option value="5">Friday</option>
                <option value="6">Saturday</option>
              </select>
              </font></td>
          </tr>
        </table>
        <font face="Arial, Helvetica, sans-serif"> </font></td>
      <td width="370" valign="top"><font face="Arial, Helvetica, sans-serif">
        <input type="radio" name="weekdays" value="*" checked>
        Every Day of the Week<br>
        <input type="radio" name="weekdays" value="1-5">
        Non-Weekend Days (Mon-Fri)<br>
        <input type="radio" name="weekdays" value="0,6">
        Weekend Days<br>
        <input type="radio" name="weekdays" value="0">
        Once Per Week (Sunday)</font></td>
    </tr>
  </table>
<br>
  <table width="500" border="0" cellspacing="2" cellpadding="1" align="center">
    <tr bgcolor="#CCCCCC"> 
    <td width="391"> 
      <div align="center"><font face="Arial, Helvetica, sans-serif">COMMAND TO 
        EXECUTE</font></div>
    </td>
    <td width="71"> 
      <div align="center">DONE?</div>
    </td>
  </tr>
  <tr> 
    <td width="391" align="center"> 
      <input type="text" name="command" size="50">
    </td>
    <td width="71"> 
      <div align="center">
        <input type="submit" name="submit" value="YES">
      </div>
    </td>
  </tr>
</table></form>
';
} #end form

function sort_range($custom) {
   unset($consec);
   $sizeof_custom = sizeof($custom);
   for ($i=0; $i<$sizeof_custom; $i++) {
       if ($custom[$i] +1 == $custom[$i + 1]) {
       $consec.= $custom[$i].'-';
         while ($custom[$i] +1 == $custom[$i + 1]) {
         $i++;
         }
       $consec.= $custom[$i].',';
       } else {
       $consec.= $custom[$i].',';
       }
   }
   $consec = preg_replace('/,\s*$/i', '', $consec);
return $consec;
}

if ($_POST["command"]) {
   if ($_POST["minutes"] == "custom") {
      $minutes = sort_range($_POST["custminutes"]);
   } else {
      $minutes = $_POST["minutes"];
   }
   if ($_POST["hours"] == "custom") {
      $hours = sort_range($_POST["custhours"]);
   } else {
      $hours = $_POST["hours"];
   }
   if ($_POST["days"] == "custom") {
      $days = sort_range($_POST["custdays"]);
   } else {
      $days = $_POST["days"];
   } 
   if ($_POST["months"] == "custom") {
      $months = sort_range($_POST["custmonths"]);
   } else {
      $months = $_POST["months"];
   }
   if ($_POST["weekdays"] == "custom") {
      $weekdays = sort_range($_POST["custweekdays"]);
   } else {
      $weekdays = $_POST["weekdays"];
   }

   $cronout = "$minutes $hours $days $months $weekdays ".$_POST["command"];

    print '
    <table width="700" border="1" cellspacing="2" cellpadding="1" align="center">
  <tr>
    <td bgcolor="#CCCCCC">
      <div align="center"><font face="Arial, Helvetica, sans-serif">The following
        line could be manually added to your Crontab:</font></div>
    </td>
  </tr>
  <tr valign="middle" align="center">
    <td>
      <form><input type="text" name="cron" size="75" value="' . $cronout . '"></form>
    </td>
  </tr>
  <tr valign="middle" align="center" bgcolor="#CCCCCC">
    <td><font face="Arial, Helvetica, sans-serif">It is important to know how the
     <CODE>cron</CODE> works.  If you are confused about
     the syntax of this line, please read the tutorial provided below, or
     consult the documentation provided by your Linux distribution.</font></td>
  </tr>
</table>
<br>
<table width="700" border="1" cellspacing="2" cellpadding="1" align="center">
  <tr>
    <td valign="top" align="left">
      <p><font face="Arial, Helvetica, sans-serif" size="-2">The following
        is an excerpt from:</font><br>
        <a href="http://kb.indiana.edu/data/afiz.html" target="external"><font face="Arial, Helvetica, sans-serif">
        Indiana University Knowledge Base</font></a></p>
    </td>
  </tr>
  <tr>
    <td valign="top" align="left">
      <p>&nbsp;</p><font face="Arial, Helvetica, sans-serif">
      <H2>  What are cron and crontab, and how do I use them?</H2>


   <P>
The <CODE>cron</CODE> <A HREF="http://kb.indiana.edu/data/aiau.html?cust=36" target="external">
daemon</A> is a long-running process that
executes commands at specific dates and times.  You can use this to
schedule activities, either as one-time events or as recurring tasks.
</P>



   <P>To schedule one-time only tasks with <CODE>cron</CODE>, use
<CODE>at</CODE> or <CODE>batch</CODE>.  For more information, see the
Knowledge Base document <A HREF="http://kb.indiana.edu/data/aewo.html?cust=36" target="external">
In Unix, what are at and batch, and how do I use them to submit non-interactive job requests?</A> </P>



   <P>For commands that need to be executed repeatedly (e.g., hourly, daily,
or weekly), use <CODE>crontab </CODE>, which has the following
options:
</P>
<TABLE BORDER=0 CELLPADDING=4><COL WIDTH=27%></COL><COL WIDTH=72%></COL>
<TR VALIGN=\"TOP\"><TD ALIGN=\"LEFT\"><CODE>crontab -a filename</CODE> </TD><TD ALIGN=\"LEFT\">Install
<CODE>filename</CODE> as your <CODE>crontab file</CODE>.  On many
systems, this command is executed simply as <CODE>crontab
filename</CODE> (i.e., without the <CODE>-a</CODE> option).
</TD></TR><TR VALIGN=\"TOP\"><TD ALIGN=\"LEFT\"><CODE>crontab -e</CODE>
</TD><TD ALIGN=\"LEFT\">Edit your <CODE>crontab file</CODE>, or
create one if it doesn\'t already exist.
</TD></TR><TR VALIGN=\"TOP\"><TD ALIGN=\"LEFT\"><CODE>crontab -l</CODE>
</TD><TD ALIGN=\"LEFT\">Display your <CODE>crontab file</CODE>.
</TD></TR><TR VALIGN=\"TOP\"><TD ALIGN=\"LEFT\"><CODE>crontab -r</CODE>
</TD><TD ALIGN=\"LEFT\">Remove your <CODE>crontab file</CODE>.
</TD></TR><TR VALIGN=\"TOP\"><TD ALIGN=\"LEFT\"><CODE>crontab -v</CODE>
</TD><TD ALIGN=\"LEFT\">Displays the last time you edited your
<CODE>crontab file</CODE>.  (This option is only available on a few systems.)
</TD></TR><TR VALIGN=\"TOP\"><TD ALIGN=\"LEFT\"><CODE>crontab -u user</CODE>
</TD><TD ALIGN=\"LEFT\">Used in conjunction with other options,
modify or view the <CODE>crontab file</CODE> of <CODE>user</CODE>.  When
available, this option can only be used by administrators.
</TD></TR></TABLE>






   <P>
The <CODE>crontab</CODE> command creates a <CODE>crontab file</CODE>
containing commands and instructions specifying when <CODE>cron</CODE>
should execute them. Each entry in a <CODE>crontab file</CODE>
consists of six fields, specifying in the following order:

</P>
<PRE>  minute(s) hour(s) day(s) month(s) weekday(s) command(s)</PRE>
   <P>

The fields are separated by spaces or tabs.  The first five are integer
patterns and the sixth is the command to be executed.  The following
table briefly describes each of the fields.
</P>
<TABLE BORDER=0 CELLPADDING=4><COL WIDTH=12%></COL><COL WIDTH=12%>
</COL><COL WIDTH=75%></COL><TR VALIGN=\"TOP\"><TH ALIGN=\"LEFT\">Field </TH>
<TH ALIGN=\"LEFT\">Value </TH><TH ALIGN=\"LEFT\">Description
</TH></TR><TR VALIGN=\"TOP\"><TD ALIGN=\"LEFT\">minute </TD>
<TD ALIGN=\"LEFT\">0-59 </TD><TD ALIGN=\"LEFT\">The exact minute that the command sequence executes
</TD></TR><TR VALIGN=\"TOP\"><TD ALIGN=\"LEFT\">hour </TD><TD ALIGN=\"LEFT\">0-23
</TD><TD ALIGN=\"LEFT\">The hour of the day that the command sequence executes
</TD></TR><TR VALIGN=\"TOP\"><TD ALIGN=\"LEFT\">day </TD><TD ALIGN=\"LEFT\">1-31
</TD><TD ALIGN="LEFT">The day of the month that the command sequence executes
</TD></TR><TR VALIGN=\"TOP\"><TD ALIGN=\"LEFT\">month </TD><TD ALIGN=\"LEFT\">1-12
</TD><TD ALIGN=\"LEFT\">The month of the year that the command sequence
executes
</TD></TR><TR VALIGN=\"TOP\"><TD ALIGN=\"LEFT\">weekday </TD><TD ALIGN=\"LEFT\">0-6
</TD><TD ALIGN=\"LEFT\">The day of the week that the command sequence
executes. Sunday=0, Monday = 1, Tuesday = 2, and so forth.
</TD></TR><TR VALIGN=\"TOP\"><TD ALIGN=\"LEFT\">command </TD><TD ALIGN=\"LEFT\">Special
</TD><TD ALIGN=\"LEFT\">The complete sequence of commands to be
executed.  The command string must conform to Bourne
<A HREF="http://kb.indiana.edu/data/agvf.html?cust=36" target="external">shell</A>
syntax. Commands, executables (such as scripts), or combinations are
acceptable.
</TD></TR></TABLE>



   <P>Each of the patterns from the first five fields may be either an
asterisk (meaning all legal values) or a list of elements separated by
commas.  An element is either a number or an inclusive range,
indicated by two numbers separated by a minus sign (10-12).
You can specify days with two fields: day of the month and day of the
week.  If you specify both of them as a list of elements,
<CODE>cron</CODE> will observe both of them.  For example:

</P>
<PRE>  0 0 1,15 * 1 /mydir/myprogram</PRE>
   <P>
</P>

   <P>The <CODE>cron</CODE> daemon would run the program
<CODE>myprogram</CODE> in the <CODE>mydir</CODE> directory on the
first and fifteenth of each month, as well as on every Monday.  To
specify days by only one field, the other field should be set to *.
For example:

</P>
<PRE>  0 0 * * 1 /mydir/myprogram</PRE>
   <P>
</P>

   <P>The program would then only run on Mondays.</P>



   <P>If a <CODE>cron</CODE> job specified in your <CODE>crontab</CODE>
entry produces any error messages when it runs, you will get a mail message reporting the errors.</P>



   <P>For more information, consult the following relevant <A HREF="http://kb.indiana.edu/data/afjm.html?cust=36" target="external">
   man</A> pages:

</P>
<PRE>  man crontab
  man cron
  man at
  man batch</PRE>
   <P>

<STRONG>Note:</STRONG> On some systems, you must get permission from
the system administrator before you can submit job requests to
<CODE>cron</CODE>.  On many shared systems, because there is only one
<CODE>crontab file</CODE>, only the administrator has access to the
<CODE>crontab</CODE> command.
</P>
    </td></font>
  </tr>
</table>
    ';

} else {
form();
}

?>

</BODY>
</HTML>
