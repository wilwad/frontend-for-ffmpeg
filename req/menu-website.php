<?php
	// menu
    $menu = [];
    $menu[] = array('caption'=>"Home",'url'=>'home','enabled'=>true);
 
         /*              
         array('caption'=>"<span class='fa fa-fw fa-search'></span> Search",
                        'url'=>'search','enabled'=>true),
         */

     /*$menu[] = array('caption'=>"Features",'url'=>'features','enabled'=>true);*/
     //$menu[] =	array('caption'=>"Contact Us",'url'=>'contact', 'enabled'=>true);

     if (  loggedin() ){
     	
         $email = $_SESSION["$session_key::user_email"];
         $role = $_SESSION["$session_key::user_role"];
         $fa_user = font_awesome('fa-user');
         
         $menu[] = array('caption'=>"$fa_user <b>$email</b> <span class='badge'>$role</span>", 'url'=>'profile','enabled'=>true);  
         $menu[] = array('caption'=>"<span class='fa fa-fw fa-lock'></span> Sign out", 'url'=>'logout','enabled'=>true);  
         
     } else {
     	
        // $menu[] =	array('caption'=>"Register",'url'=>'register','enabled'=>false);
         //$menu[] = array('caption'=>"Sign In", 'url'=>'login','enabled'=>true);
         
     }
      
     /* these are hidden */      

     $menu[] =	array('caption'=>'Forgot Password', 'url'=>'forgot-password','enabled'=>false);               
     $menu[] =	array('caption'=>"Privacy",'url'=>'privacy','enabled'=>false);
     $menu[] =	array('caption'=>"FFMPEG Results",'url'=>'ffmpeg-results','enabled'=>false);
?>