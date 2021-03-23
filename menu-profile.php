<?php

	$mymenu = [];
	
	switch($userrole) {
		case 'providers':
				$mymenu[] = array('title'=>'Overview', 'action'=>'profile-overview', 'icon'=>'fa-home', 'desc'=>'');
				$mymenu[] = array('title'=>'Service Requests', 'action'=>'profile-service-requests', 'icon'=>'fa-question-circle','desc'=>'');
				$mymenu[] = array('title'=>'Sessions', 'action'=>'profile-sessions', 'icon'=>'fa-windows', 'desc'=>'');
				$mymenu[] = array('title'=>'Clients', 'action'=>'profile-clients', 'icon'=>'fa-users', 'desc'=>'');
				$mymenu[] = array('title'=>'Calendar', 'action'=>'profile-calendar', 'icon'=>'fa-calendar', 'desc'=>'');
				$mymenu[] = array('title'=>'Notes', 'action'=>'profile-notes', 'icon'=>'fa-file-o', 'desc'=>'');		
				$mymenu[] = array('title'=>'Tasks', 'action'=>'profile-tasks', 'icon'=>'fa-check', 'desc'=>'');			
				//$mymenu[] = array('title'=>'Messages', 'action'=>'profile-messages', 'icon'=>'fa-comment', 'desc'=>'');
				$mymenu[] = array('title'=>'Payments', 'action'=>'profile-payments', 'icon'=>'fa-usd', 'desc'=>'');
				
				$mymenu[] = array('title'=>'Notices', 'action'=>'profile-notices', 'icon'=>'fa-exclamation-triangle', 'desc'=>'');
				//$mymenu[] = array('title'=>'Help', 'action'=>'profile-help', 'icon'=>'fa-flag', 'desc'=>'');	
				$mymenu[] = array('title'=>'My Profile', 'action'=>'profile-settings', 'icon'=>'fa-user', 'desc'=>'');
				break;
				
		case 'clients':
				$mymenu[] = array('title'=>'Overview', 'action'=>'profile-overview', 'icon'=>'fa-home', 'desc'=>'');
				$mymenu[] = array('title'=>'My Service Requests', 'action'=>'profile-service-requests', 'icon'=>'fa-question-circle','desc'=>'');
				$mymenu[] = array('title'=>'My Sessions', 'action'=>'profile-sessions', 'icon'=>'fa-windows', 'desc'=>'');
				//$mymenu[] = array('title'=>'Clients', 'action'=>'profile-clients', 'icon'=>'fa-users', 'desc'=>'');
				//$mymenu[] = array('title'=>'Calendar', 'action'=>'profile-calendar', 'icon'=>'fa-calendar', 'desc'=>'');
				//$mymenu[] = array('title'=>'Notes', 'action'=>'profile-notes', 'icon'=>'fa-file-o', 'desc'=>'');		
				//$mymenu[] = array('title'=>'Tasks', 'action'=>'profile-tasks', 'icon'=>'fa-check', 'desc'=>'');			
				//$mymenu[] = array('title'=>'Messages', 'action'=>'profile-messages', 'icon'=>'fa-comment', 'desc'=>'');
				$mymenu[] = array('title'=>'My Payments', 'action'=>'profile-payments', 'icon'=>'fa-usd', 'desc'=>'');
				
				$mymenu[] = array('title'=>'Platform Notices', 'action'=>'profile-notices', 'icon'=>'fa-exclamation-triangle', 'desc'=>'');
				//$mymenu[] = array('title'=>'Help', 'action'=>'profile-help', 'icon'=>'fa-flag', 'desc'=>'');	
				$mymenu[] = array('title'=>'My Profile', 'action'=>'profile-settings', 'icon'=>'fa-user', 'desc'=>'');
				break;				
	}
?>