<?php

function sms_clickatell($config){
		 /*
		   Send SMS via Clickatell SMS API
		   Using cURL
		   Recipients separated by comma, in international format, no leading + sign no leading zero for national number
		   
		   user=
		   password=
		   api_id= http_id / smtp_id / smpp_id
		   to=264813918334,264813026494
		   text=Message+when+using+method+GET
		    - & -
		   text=Message when using method POST
		
		   Method GET: 300 recipients per message p/second limit
		   Method POST: 600 recipients per message p/second limit
		
		   // find out about message parts for POST/GET. Think its max 3 parts
		   200000/600 = 333.3 seconds / 60 seconds (1 minute)  = 5.6 minutes
		 */
		 $data = [];
		 $post = [];
		
		 foreach($config as $k=>$v){
			$post[$k] = "$v"; // POST does not need text urlencoding
			if ($k == "text") $v = urlencode($v);
			$data[] = "$k=$v";
		 }
		
		//echo alertbuilder("Submitting SMS to:" . $config['url']);
		$ch = curl_init($config['url_sendsms']);
		if (!$ch) return "cURL failed to connect to URL";
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		// execute!
		$response = curl_exec($ch);
		
		// close the connection, release resources used
		curl_close($ch);
		
		// do anything you want with your response
		return $response;
	
}