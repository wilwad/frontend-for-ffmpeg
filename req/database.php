<?php
	 /*
	  * William Sengdara
	  * Copyright (c) 2015
	  */
	  
	/*
     * This is the handler for databases 
	 * Please lower-case for all table names so we don't have issues on Linux machines
	 * that are case sensitive
	 */
	date_default_timezone_set('Africa/Windhoek');
	
	require_once('settings.php');
	require_once('user_rights.php');
	require_once('functions.php');

	$database = @ new mysqli(settings::db_host,settings::db_user,settings::db_pwd, settings::db_db);
	if ($database->connect_errno)
		throw new Exception("Fatal error: Failed to create a connection to the database.");

