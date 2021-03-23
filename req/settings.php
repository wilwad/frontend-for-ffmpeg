<?php
 /*
  */	

$fa_info = "";
class settings {
				/* system info */
	     const version = '<b>Compton</b><BR><span class=\"fa fa-fw fa-info-circle\"></span> Internal build',
			   title   = 'frontend for ffmpeg',
			   about   = "frontend for ffmpeg.",
			   description = 'frontend for ffmpeg',
			   keywords ='frontend for ffmpeg',
			   logo    = 'images/logo.jpeg',
			   author  = 'William Sengdara',
			   copyright= 'FOSS by William Sengdara. Made with heart in Namibia',
			   footer_phone = '',
			   footer_email = '',
			   
    			   /* web crawlers */
			   crawlers = array(
                               'og:description'=>"frontend for ffmpeg",
                               'og:url'=>"https://localhost",
                               'og:title'=>"Mani Loans",
                               'og:image'=>"https://localhost/og/fb-post.png",
                               
                               'description'=>"frontend for ffmpeg",
                               'keywords'=>"ffmpeg, Namibia",
                               'author'=>"Sengdara IT, William Sengdara",
                               'robots'=>"index, nofollow"		       
			        ),
    
			   /* theme */
			   theme = "homeaffairs",
			   
			   /* warnings */
			   warning_logout = "",/*"<p></p><div class='well'><li class='fa fa-fw fa-exclamation-triangle'></li><small>&nbsp;After you logon, always log out by clicking the button and follow any instructions that may appear on a log out confirmation screen. By taking these steps you ensure the security and privacy of data important to you and the Ministry of Home Affairs and Immigration.</small></div>",*/
			   
			   /* database authentication */
			   
			   /* database authentication */			   
			   
			   db_host = "localhost",
			   db_port = 3306,
			   db_db   = "",
			   db_user = "",
			   db_pwd  = "",
			   
			   /* session constants
			    * remember to match in logout.php 
 			    */
			   session_key = "localhost::",
			   session_userid = 'localhost::userid',
			   session_logintime = 'localhost::logintime',
			   session_loginexpire = 'localhost::loginexpire';												 						 
}

?>
