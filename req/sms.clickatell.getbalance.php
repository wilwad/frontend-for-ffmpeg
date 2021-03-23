<?php
	require_once('database.php');

    // fetch clickatell API config      
	 $sql = "SELECT name,value 
	 			FROM settings 
	 			WHERE name LIKE '%clickatell%';";
	 $ret = $database->query($sql) or die(alertbuilder($database->error, 'warning'));
	 if (!$ret || !$ret->num_rows){
	 	die("<p class='text-warning'>Failed to fetch Clickatell SMS Provider settings.</p>");
	 }
	
	 $info = [];
	 while ($row = $ret->fetch_array()){
			$name = strtolower($row['name']);
			
			if ($name == 'clickatell_url_sendsms')    $info['url_sendsms']    = $row['value'];
			if ($name == 'clickatell_url_getbalance') $info['url_getbalance'] = $row['value'];
			if ($name == 'clickatell_username')       $info['user']           = $row['value'];
			if ($name == 'clickatell_password')       $info['password']       = $row['value'];
			if ($name == 'clickatell_api_id')         $info['api_id']         = $row['value'];
	 }
		
	//https://api.clickatell.com/http/getbalance
	$urlsendsms =$info['url_sendsms'];
	$urlbalance = $info['url_getbalance'];
	$username   = urlencode($info['user']);
	$password   = urlencode($info['password']);
	$api_id     = urlencode($info['api_id']);
	 
	$submit = "$urlbalance?user={$username}&password={$password}&api_id={$api_id}";
	echo file_get_contents($submit);		
?>