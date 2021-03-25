<?php
@ session_start();

/*
 *Prevent caching on BlueHost

 
header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache'); */
?>
<!DOCTYPE html>
<?php 
 /*
  * RAD Framework (PHP MySQL Bootstrap)
  * William Sengdara -- william.sengdara@gmail.com
  * Copyright (c) 2017
  *
  * Created:
  * Updated: 22 March 2021
  *
  * This is the host (entry point) for this system
  */

  /* error reporting */
  ini_set('display_startup_errors',1);
  ini_set('display_errors',1);
  error_reporting(-1);
  
  require_once('req/settings.php');
  $session_key = settings::db_db;
  //require_once('req/database.php');

  require_once('req/menu-website.php');
  require_once('req/user_rights.php');
  
  function loggedin(){
  	return false;
  }
  
  $introJsIndex = 1;
?>

<html lang='en'>
<head>
<meta charset='utf-8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<?php
    
    $crawlers = settings::crawlers;
    foreach($crawlers as $key=>$val){
        echo "<meta name='$key' content='$val'>\n";
    }
?>
<title>
	<?php 
	    // get the title 
	    $title = settings::title;
		$navheader = $title;
		echo $title; 
		
		// current theme
		$theme = "homeaffairs";
	?>
</title>
<link rel="icon" href="favicon.png" />

<!-- Canonical -->
<link rel='canonical' href='https://localhost'>

<!-- Fav Icon and Apple Touch Icons -->
<!-- link rel='icon' href='favicon.ico' type='image/x-icon' -->

<!-- CSS -->
<link href="https://fonts.googleapis.com/css?family=Red+Hat+Text" rel="stylesheet">
<link href='bootstrap/themes/font-awesome/font-awesome.min.css' rel='stylesheet' type='text/css'>
<link href='lib/bootstrap/bootstrap.3.3.4.min.css' rel='stylesheet' type='text/css'>
<link href='bootstrap/themes/<?php echo $theme; ?>/bootstrap.min.css' rel='stylesheet' type='text/css'>
<!-- link href='bootstrap/themes/yeti2/sticky-footer-navbar.css' rel='stylesheet' -->
<link rel="stylesheet" href="lib/introjs/introjs.min.css">
<!-- link rel="stylesheet" href="lib/introjs/introjs-wall.css" -->
<link href='lib/bootstrap3dialog/css/bootstrap-dialog.min.css' rel='stylesheet'>
<link href='lib/datatables/css/jquery.dataTables.min.css' rel='stylesheet'>
<link href='lib/alertifyjs/css/alertify.min.css' rel='stylesheet'>
<link href='lib/alertifyjs/css/themes/bootstrap.min.css' rel='stylesheet'>
<link href='lib/introjs/introjs.min.css' rel='stylesheet'>
<link href='lib/select2/select2.min.css' rel='stylesheet'>
<link href='lib/select2/select2-bootstrap.css' rel='stylesheet'>
<link href='lib/date-picker/css/bootstrap-datepicker3.min.css' rel='stylesheet'>
<link href='lib/fancybox/source/jquery.fancybox.css' rel='stylesheet'>
<!-- link href='lib/lightcarousel/light-carousel.css' rel='stylesheet' type='text/css'-->
<link href='css/style.css?t=888' rel='stylesheet'>

<!-- syntax highlight -->
<link rel='stylesheet' href='lib/highlight.js/styles/xcode.css'>

<!--[if lt IE 9]>
<script src='https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js'></script>
<script src='https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js'></script>
<![endif]-->

<!-- Javascript -->
<script src='lib/jquery/jquery.1.11.1.min.js'></script>
<script src='lib/bootstrap/bootstrap.3.3.4.min.js'></script>
<script src="lib/introjs/intro.min.js"></script>
<script src='lib/bootstrap3dialog/js/bootstrap-dialog.min.js'></script>		
<script src='lib/datatables/js/jquery.dataTables.min.js'></script>		
<script src='lib/alertifyjs/js/alertify.min.js'></script>	
<script src='lib/select2/select2.min.js'></script>
<script src='lib/date-picker/js/bootstrap-datepicker.min.js'></script>
<script src='lib/timeago/jquery.timeago.js'></script>
<script src='lib/fancybox/source/jquery.fancybox.js'></script>
<!-- script src='lib/lightcarousel/jquery.light-carousel.js' charset='utf-8'></script -->

<script src='js/script.js'></script>

<!-- script src="lib/responsivevoice/responsivevoice.js"></script -->
</head>
<body>
<?php
  $view = @ $_GET['view'];
  $action = @ $_GET['action'];
    
    /*
     * get the default view for the current user
     */
    function get_default_view($role, $view){
    	$view = trim($view);
    	
    	if ($view == ""){
    		switch ($role) {
    			 case 'administrators':
    				  $view = 'system';
    				  break;
    						
    			 case 'receptionists':
    				  $view = "home";
    			 	  break;
    			 	  										
    			 case 'backup_operators':
    				  $view = 'database';
    				  break;
    				  
    			 case 'secretariats':
    			 case 'top_levels':
    				  $view = 'dashboard';
    				  break;
    				  
    			default:
    				  $view = 'home';
    				  break;
    		}
    	}
    	
    	return $view;
    
    }
    

                
  $side_menu_links = "";
  $nav_items = "";
  
    if (!$view) $view = 'home';
    
    $found = false;
    
    foreach($menu as $menuitem){
			 
			 $url     = trim($menuitem['url']);
			 
			 if ($view == $url){
			     $found = true;
			 }
			 
			 $enabled = trim($menuitem['enabled']); 
			 
			 if ($enabled){
    			 $caption = trim($menuitem['caption']);
        	     $active  = $view == $url ? "class='active'" : "";
    	        $target  = strtolower($caption) == 'login' ? "target='_blank'" : "";
    	            	        
            $nav_items .=  "<li $active><a href='?view=$url' $target id='$url' class='text-shadow' title='' data-toggle='tooltip' data-placement='bottom'>$caption</span></a></li>\n";
			 }
    }
    
    $_userid_ = -1;
    
    if (loggedin()){    	
    		$_role_ = $_SESSION["$session_key::user_role"];
    		$_userid_ = $_SESSION["$session_key::user_id"];
    		
		   foreach($role_rights[$_role_] as $arr0=>$arr1){
		   	foreach ($arr1['menu'] as $a0=>$a1){
		   		if ($a1['url'] == $view) {
		   			$found = true;
		   			break;
		   		}
		   	}
		   	if ($found) break;
		   }    	
    }

  echo "<nav class='navbar navbar-inverseX navbar-fixed-top'>
    	  <div class='container'>
        
          <div class='navbar-header'>
            <button type='button' class='navbar-toggle collapsed' 
                    data-toggle='collapse' data-target='#navbar' 
                    aria-expanded='false' aria-controls='navbar'>
            <span class='sr-only'>Toggle navigation</span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
            </button>
            <a class='navbar-brand' href='?view=home'>
            $navheader
            </a>
      
          </div>
      
            <!-- login menu start -->
            <div class='collapse navbar-collapse' id='navbar'>
              <ul class='nav navbar-nav navbar-right'>
                  $nav_items
              </ul>
            </div>
          
    	  </div>
    	</nav>";
?>


	 <?php 
	  if (!$found){
	    echo "<p class='alert alert-warning'>Not an authorized link.: $view</p>
				 <script>
 				   window.setTimeout(()=>{
  				   		window.location.href='?view=home';
  					}, 1000);
  				</script>";   
	    
	  } else {
	  			 if (loggedin()) require_once('req/user-menu.php');
	    		 require_once('body.php');
	  }
	 ?>	
   
   <footer id="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row">

          <div class="col-lg-3 col-md-6 footer-info">
            <h3><?php echo settings::title; ?></h3>
            <p>
              Street line 1<br>
              Street line 2<br><br>
              <strong>Phone:</strong> <?php echo settings::footer_phone; ?> <br>
              <strong>Email:</strong> <?php echo settings::footer_email; ?><br>
            </p>
            <div class="social-links mt-3">
              <a href="#" class="twitter"><i class="bx fa fa-fw fa-twitter"></i></a>
              <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
              <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
              <a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
              <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Material</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Web Design</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Web Development</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Product Management</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Marketing</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Graphic Design</a></li>
            </ul>
          </div>
          
          <div class="col-lg-2 col-md-6 footer-links">
            <h4>Useful Links</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Home</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">About us</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Services</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Terms of service</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Privacy policy</a></li>
            </ul>
          </div>

          <!--
          <div class="col-lg-4 col-md-6 footer-newsletter">
            <h4>Our Newsletter</h4>
            <p>Tamen quem nulla quae legam multos aute sint culpa legam noster magna</p>
            <form action="" method="post">
              <input type="email" name="email"><input type="submit" value="Subscribe">
            </form>

          </div>
          -->
        </div>
      </div>
    </div>

    <div class="container">
      <div class="copyright">
       <strong><span><?php echo settings::copyright . ' ' . date('Y');?></span></strong>
      </div>
    </div>
  </footer>

    <!--div id='social' class="hidden-xs">
        <a href='https://www.facebook.com/kaijata' target='_blank'>
         <div class='social-icon bg-facebook'>
          <span class='fa fa-facebook'></span>
         </div>
	    </a>
    </div -->

 </body>
</html>
