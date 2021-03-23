<?php
   /*
	 * Body
	 *
	 * This file loads the php file specified by view with .php extension
	 *
	 * Author: William Sengdara
	 * Created:
	 * Modified:
	 */

	/*********** start rights verification ************/

	if (!@$users)
		die(ERRORS_FILES_MUSTINCLUDE);

	$user = $users->loggedin();
	if (!$user) {
		echo view( 'dialog-login' );
		exit;
	}
	$right_exists = verify_right($view);
	if (!$right_exists)
		die(alertbuilder("You have not been authorized to access that view.","danger"));
		
	/*********** end rights verification ************/
	
   define('DEBUG',0);

	$view   = @ $_GET['view'];
    
	// if we do not have a view, set a default view based on the user?
	if (strlen(trim($view)) == 0){
		switch ($role) {
			 case 'administrators':
				  $view = 'dashboard';
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
				  echo "<style>body {background: url('css/bg-dotted.png') repeat;}</style>";
				  $view = 'noticeboard';
				  break;
		}
	}	

	// handler for the current view 
	$filename = "ui/{$view}.php";
	
	//$action = "VIEW_LOAD";
	//$description = "Request to load view handler: $filename";
	//update_system_log($action, $description);
	
	if (file_exists($filename)){
		
		// modification details
		//$filename = __FILE__;
		$modified = date("F d Y H:i:s.", filemtime($filename));

		if (DEBUG)
		    echo "<li class='fa fa-fw fa-info-circle'></li>&nbsp;<b>Debug notes follow.</b> {\"filename\": \"$filename\", \"last-modified\": \"$modified\"}";    
			  
	   require_once($filename);    
	}
	else {
		
		$action = "VIEW_LOAD";
		$description = "Failed to load view handler: $filename. File not found.";
		update_system_log($action, $description);		
		
       echo "  <div class='alert alert-danger'>
			    <li class='fa fa-fw fa-exclamation-circle'></li>&nbsp;Unable to load view handler: `$filename`.
			   </div>";
	}
?>