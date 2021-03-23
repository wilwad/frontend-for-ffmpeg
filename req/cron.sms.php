<?php
	require_once('database.php');

    // fetch clickatell API config      
	 $sql = "SELECT name,value 
	 			FROM settings 
	 			WHERE name LIKE '%clickatell%';";
	 $ret = $database->query($sql) or die(alertbuilder($database->error, 'warning'));
	 if (!$ret || !$ret->num_rows){
	 	die(alertbuilder("Failed to fetch Clickatell SMS Provider settings.", "danger"));
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
	
	 require_once('sms.clickatell.php');   	
	 $date = date('Y-m-d H');
	
	 /*
	 	12345678910  B  C D  14 15
	 	2020-05-20 21:30:00
	 */
	 $sql = "SELECT * 
	 			FROM `sms_sent` 
	 			WHERE status='PENDING' AND 
	 			MID(exitdate,1,13) ='$date' LIMIT 100;";
	 $ret = $database->query($sql) or die($database->error);
	
	 $buffer = [];

	 // send out 100	  
	 while ($row = $ret->fetch_array()){
	 	   $id      = $row['id'];
	 	   $msisdn  = $row['msisdn'];
	 	   $message = $row['message'];
	 	   
			$info['to']       = $msisdn;
			$info['text']     = $message;
			
			// send using clickatell
			$buffer[$id] = sms_clickatell($info);	 	
	 }		
	 
	 // update the table
	 foreach($buffer as $id=>$response){
	 	$response = addslashes($response);
	 	$sql = "UPDATE `sms_sent` SET status='$response' WHERE id=$id;";
	 	$ret = $database->query($sql) or die($database->error);
	 }
?>