<?php
 /*
  * William Sengdara
  * Copyright (c) 2015
  *
  * This file builds the menu for the current logged in user.
  * user rights are tied to roles
  *
  * rights[role] -> options
  */
  
	if (!@$users)
		die("FATAL ERROR: this file may not be launched outside the system. It can only be included.");
	
	$view = @ $_GET['view'];
	
	// is anyone logged in?
	$key_userid = settings::session_userid;	

	// check that we are logged in!
	$user = $users->loggedin();
	
	if (!$user) {
		// we need to login.
	}
	else
	{
		$userid     = $user['userid'];
		$username   = $users->user($userid)->get('user_name');
		//$firstname  = $users->user($userid)->get('fname');
		$profilepic = $users->user($userid)->get('profilepic');
		//$lastname   = $users->user($userid)->get('sname');
		//$role       = $users->user($userid)->get('rolename');
		$role       = strtolower('youth');

		// update GET['view']
		$_GET['view'] = get_default_view($role, $view);

	   // options based on the role of logged in user
	   //$myrights = $role_rights[$role];

	echo "<script>
		   $(document).ready(function(){
			   /*$('body').css('background-color','#EEF0F3');*/
			   
			   introJs().onchange(function(){
				   console.log('introJS change');
			   });
			   
			   // remove footer cos its blocking system icon
			   $('.footer').remove();
		   });
		   
			var collapsed = false; // the default value

			$('.collapse').on('hide.bs.collapse', function (e) {
				collapsed = true; // on hide, collapsed is true			
				ajax_proxy(e.currentTarget.id,0);
			})

			$('.collapse').on('show.bs.collapse', function (e) {
				collapsed = false; // on show, collapsed is false
				ajax_proxy(e.currentTarget.id,1);
			})
						   
			function ajax_proxy(id,newstate){
				var url = 'session_collapse.php';
				var params = {action: 'set-collapse-state',
						       id:id,
							   state: newstate
							   };

				$.ajax({url: url,
				        method: 'POST',
				        data: params,
						success: function(data){console.log(data);},
						error: function(a,b,c){console.log('error',a,b,c);}
						});
			}
		  </script>";		
	}	
?>