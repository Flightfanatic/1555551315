Virtual Cron 1.0 By WorldWideCreations.com

Installation:
-------------

Open virtualcron.php in a text editor and modify the $mail_from and $pass variables.  

You can, and should (see Security below) restrict admin access to this script via IP ranges or addresses.  If you do not know what your IP address/range is, see:

http://whatismyipaddress.com/

By changing the variable $restrict_ip_access to true, and adding your IP or range to the $ip_ranges variable, you add another layer of security to this script.

Upload all of the files and make sure you make jobs.php and lock.php writeable!  On Unix based servers (i.e. Linux), this means CHMOD 666 or in some cases, 777.  The script must be able to write to these files.  When you first log in to the script, it will issue you a bold warning if it finds the files are not writeable.

From here you can access the script and add scheduled tasks similiar to the way you would in Crontab, in fact, it follows the exact syntax as Crontab.

You MUST access the script like so to get to the admin section:

		http://www.yoursite.com/path/to/virtualcron.php?Admin

Obviously, change the path to the script to reflect your sites path to it.

We have included a help script called phpcrontab.php that will help you craft custom cron jobs if needed.  This script was NOT written by WorldWideCreations.com.  The author indicates it is a open source file under no licensing.  See script header for more information.

The phpcrontab.php file can be called at any time, by anyone but does not read or write any sensitive information.  If you are comfortable with your own crontab skills, you can delete this file, or access it directly here:

http://www.mtsdev.com/opensource/phpcrontab.php

Since this is a VIRTUAL Crontab type program, it does not run exactly like Crontab.  The script must be executed often in order to run tasks.  You can do this by including an image tag that calls the script anywhere on your site, preferrably on busy pages so it gets called often.  To do this, create an image tag like so:

		<img src="http://www.yoursite.com/path/to/virtualcron.php">

Obviously, change the path to the script to reflect your sites path to it.

You should test this script before relying on it to make sure it is running.  There are a couple ways of doing this.  You can create a cronjob that runs every minute, that activates a common URL, like http://www.google.com, and have it email you the results.  If you get the results every minute, it works, and you can delete the job.  If it doesn't work, turn the $debug variable to true and call the script in your browser like so:

		http://www.yoursite.com/path/to/virtualcron.php

And see if the debug information helps you.  You may post it in our forum at http://www.worldwidecreations.com for more help.

Security:
---------

VERY IMPORTANT!  If someone gains unauthorized access to this script, they could wreak havoc on your site! (pretty much true with any script)  If your site has PHP safe mode off, they could execute arbitrary commands on your server!  

I cannot suggest strongly enough that you use a very hard to guess password, and that you utilize the IP range feature in the script.  You may also want to rename this script to some other name to keep it from probing bots to avoid scripted brute force attacks.  

You CANNOT place this script in a password protected directory because then the images won't work and the cronjobs will never happen.

You have been warned, please do not take security lightly, because it could just wipe your site out.

If you follow our security instructions, it will be as safe as any other script on your server.  We will not accept any responsibility for any adverse actions this script may make on your server.













