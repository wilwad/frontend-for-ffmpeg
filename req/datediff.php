<?php
  $performance_check_start = date('Y-m-d H:i:s');	
  $i=0;
  while ($i<1000000)
  {
	  $i++;
  }
  $performance_check_end = date('Y-m-d H:i:s');	
  
  echo 'timelapse: ' . dateDiff( 
                                $performance_check_end,$performance_check_start);
  
  // Time format is UNIX timestamp or
  // PHP strtotime compatible strings
  function dateDiff($t1, $t2) {
		$t1 = strtotime($t1);
		$t2 = strtotime($t1);
		$delta_T = ($t2 - $t1);
		$day = round(($delta_T % 604800) / 86400); 
		$hours = round((($delta_T % 604800) % 86400) / 3600); 
		$minutes = round(((($delta_T % 604800) % 86400) % 3600) / 60); 
		$sec = round((((($delta_T % 604800) % 86400) % 3600) % 60));

		return "$day days $hours hours $minutes minutes $sec secs";	  
  }

?>