<?php
	if (!function_exists('loggedin')) {
		function loggedin(){
			global $session_key;
			//die($session_key);
			
			return @ $_SESSION["$session_key::user_id"] >0 ? true : false;
		}
	}
	
	/*
	 * helper function returns the total from a table
	 */
	function db_get_count($database, $table, $id) {
		$sql = "SELECT COUNT($id) FROM $table;";
		$ret = $database->query($sql);
		if (!$ret || !$ret->num_rows)
			return 0;
		
		$row = $ret->fetch_array();
		return $row[0];
	}
	
	/** decorate policy status */
	function decorate_policy_status($status){
		switch(strtolower($status)){
			case 'active': $status = "<span class='label label-success'>$status</span>"; break;
			case 'elapsed': $status = "<span class='label label-default'>$status</span>"; break;
			case 'inactive': $status = "<span class='label label-warning'>$status</span>"; break;
			default: $status = "<span class='label label-danger'>$status</span>"; break;
		}
		
		return $status;
	}
	
	function days_between($dt1, $dt2) {
    		return date_diff(date_create($dt2), date_create($dt1))->format('%a');
	}	

/*
resizeimage('ghost2.jpg',250,250);
*/
function resizeimage($filename,$targetWidth=250,$targetHeight=250, $overwrite=false) {
	  $imageResourceId = null;	

          $ext    = pathinfo($filename, PATHINFO_EXTENSION);
          $props  = getimagesize($filename);
          $width  = $props[0];
          $height = $props[1];
	  $mime   = $props['mime'];
          $type   = $props[2];

 	  $ratio_orig = $width / $height;

	  if ($targetWidth/$targetHeight > $ratio_orig) {
              $targetWidth = $targetHeight*$ratio_orig;
          } else {
              $targetHeight = $targetWidth/$ratio_orig;
          }

	 $filename_ = explode(".$ext",basename($filename))[0];

	  //die("Filename: $filename, ext: $ext, type: $type, $ width: $width, height: $height, mime: $mime");
          $filenamenew = ($overwrite) ? $filename : $filename_ . "_{$targetWidth}x{$targetHeight}.{$ext}";

          switch ($type) {
            case IMAGETYPE_PNG:
                $imageResourceId = imagecreatefrompng($filename); 
    		$targetLayer=imagecreatetruecolor($targetWidth,$targetHeight);
    		imagecopyresampled($targetLayer,$imageResourceId,0,0,0,0,$targetWidth,$targetHeight, $width,$height);
                imagepng($targetLayer,$filenamenew);
                break;

            case IMAGETYPE_GIF:
                $imageResourceId = imagecreatefromgif($filename); 
    		$targetLayer=imagecreatetruecolor($targetWidth,$targetHeight);
    		imagecopyresampled($targetLayer,$imageResourceId,0,0,0,0,$targetWidth,$targetHeight, $width,$height);
                imagegif($targetLayer,$filenamenew);
                break;

            case IMAGETYPE_JPEG:
                $imageResourceId = imagecreatefromjpeg($filename); 
    		$targetLayer=imagecreatetruecolor($targetWidth,$targetHeight);
    		imagecopyresampled($targetLayer,$imageResourceId,0,0,0,0,$targetWidth,$targetHeight, $width,$height);
                imagejpeg($targetLayer,$filenamenew);
                break;

            default:
                echo "Unsupported Image type.";
                exit;
                break;
        }
}

/*
* source_url: filename (supports: gif, jpg, jpeg, png)
* new_width: 250 default
* quality: 10 default (between 1 and 100)
*/
function cropimage($source_url, $new_width=250, $quality=75){
        return false;
        
        if (!file_exists($source_url)){
            echo alertbuilder("File does not exist: $source_url",'warning');
            return false;
        }
        
        //separate the file name and the extention
        $source_url_parts = pathinfo($source_url);
        $filename         = $source_url_parts['filename'];
        $extension        = strtolower($source_url_parts['extension']);
        
        //detect the width and the height of original image
        $info = getimagesize($source_url);
    
        if($info === false){
           echo alertbuilder('This file is not a valid image', 'warning');
           return false;
        }
    
        $type    = $info[2];
        $width   = $info[0];
        $height  = $info[1];
    
        // resize only when the original image is larger than new width.
        // this helps you to avoid from unwanted resizing.
        if ($width > $new_width) {
            // cool to resize
        } else {
            echo alertbuilder('The image is already smaller than width', 'warning');
            return false;
        }
        
       //get the reduced width
        $reduced_width = ($width - $new_width);
        
        //now convert the reduced width to a percentage, round to 2 decimals
        $reduced_radio = round(($reduced_width / $width) * 100, 2);
    
        // reduce the same percentage from the height, round to 2 decimals
        $reduced_height = round(($height / 100) * $reduced_radio, 2);
        
        //reduce the calculated height from the original height
        $new_height = $height - $reduced_height;
            
        $img = null;
        $imgResized = null;
        
        switch($type) {
            case IMAGETYPE_JPEG:
                $img = imagecreatefromjpeg($source_url);
                $imgResized = imagescale($img, $new_width, $new_height, $quality);
                imagejpeg($imgResized, $source_url);                
                break;
                
            case IMAGETYPE_GIF:
                $img        = imagecreatefromgif($source_url);
                $imgResized = imagescale($img, $new_width, $new_height, $quality);
                imagegif($imgResized, $source_url);                
                break;
                
            case IMAGETYPE_PNG:
                $img = imagecreatefrompng($source_url);
                $imgResized = imagescale($img, $new_width, $new_height, $quality);
                imagepng($imgResized, $source_url);                
                break;
                
            default:
                echo alertbuilder('This file is not in JPG, GIF, or PNG format!');
                return false;
        }

        //Finally frees any memory associated with image
        imagedestroy($img);
        imagedestroy($imgResized);
        
        return true;
}

/* returns live body from settings matching query
 * e.g. SMS_
 *      USERS_
 */	
function build_settings_table($match, $default) {
	global $database;
	$body = $default;
	
	$sql = "SELECT * FROM system_settings WHERE name LIKE '$match';";
	$ret = $database->query($sql);
	if (!$ret || !$ret->num_rows)
	{}
	else
	{
		$i = 1;
		$body = "";
		while ($row = $ret->fetch_array()) {
			$name = $row['name'];
			$value = $row['value'];
			$truefalse = $row['truefalse'];
			
			if ($truefalse) {
				$checked = $value == 1 ? 'checked' : '';
				$value = "<input type='checkbox' $checked />";
			}
			else
				$value = "<input type='text' class='form-control' value=\"$value\" />";
			
			$body .= "<tr><td>$i</td><td>$name</td><td>$value</td></tr>";
			$i++;
		}
	}
	
	return $body;
}
	
// gets the current date and time
function datetime_now() {
	return date('Y-m-d H:i:s');
}
 
// update the system log 
function update_system_log($action, $description) {
		global $database;
		
		// write to system log
		$action = strtoupper($action);
		$ip = get_client_ip();
		$now = datetime_now();
		
		$action = str_sanitize($action);
		$description = str_sanitize($description);
		
		$ret = $database->query("INSERT INTO system_log(action,ipaddress,entrydate,description)
		                         VALUES('$action', '$ip','$now', '$description');");	
}

// question on stackoverflow
// http://stackoverflow.com/questions/15699101/get-the-client-ip-address-using-php
// Function to get the client IP address
// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1')
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}	

/*
 * returns total
 */
function get_inbox($userid, $tableonly = false){
	global $database;
	
	$messages = array();
	$read = "";
	if ($tableonly)
		$read = " AND wasread=0";
	
	$sql = "SELECT * 
	        FROM notifications 
			WHERE userid_to=$userid $read 
			ORDER BY entrydate DESC;";
	$ret = $database->query($sql);
	if (!$ret || !$ret->num_rows)
		return false;

	$arr = [];
	$i = 0;
	
	while ($row = $ret->fetch_array()) {
		$i++;
		
		$arr['id'] = $row['id'];
		$arr['from'] = $row['userid_from'];
		$arr['entrydate'] = $row['entrydate'];
		$arr['subject'] = $row['subject'];
		$arr['body'] = $row['body'];
		$arr['wasread'] = $row['wasread'];
		
		// array_push
		$messages[] = $arr;
	}

	//$messages['total'] = $i;
	return $messages;
}
/*
 * creates a drop down
 */
function bs_dropdown($caption, $id, $arroptions, $icon=""){
	$temp = "";
	$opts = "";
	
	foreach ($arroptions as $val)
		$opts .= "<li role='presentation'><a role='menuitem' tabindex='-1' href='#'>$val</a></li>";
		
    $temp = "<div class='dropdown'>
				<button class='btn btn-default dropdown-toggle' type='button' id='$id' data-toggle='dropdown'>$icon $caption
				<span class='caret'></span></button>
				<ul class='dropdown-menu' role='menu' aria-labelledby='$id'>
				   $opts
				  <li role='presentation' class='divider'></li>
				  <li role='presentation'><a role='menuitem' tabindex='-1' href='#'>Cancel</a></li>
				</ul>
			  </div>";
					  
	return $temp;
}

function userinfo($userid, $fieldname ,$default){
		 global $database;
		 
	     /* get the group for this user */
		 $sql = "SELECT *, r.name AS rolename FROM users u, roles r WHERE u.id=$userid AND r.id = u.roleid;";
		 $ret = $database->query($sql);
		 if (!$ret || !$ret->num_rows)
			 return $default;
		 
		 $row = $ret->fetch_array();
		 $table = $row['rolename'];
		 
		 $sql = "SELECT *, CONCAT(fname,' ',sname) AS fullname FROM $table WHERE id=$userid;";
		 $ret = $database->query($sql);
		 if (!$ret || !$ret->num_rows)
			 return $default;	

		 $row = $ret->fetch_array();
		 return $row[$fieldname];		 
}

 /*
  * General functions
  */
  
 /* returns the breadcrumb */
 function breadcrumb(){
	 global $view;
	 
 	$view_header = ucfirst($view);
	return "<ul class='breadcrumb'>
	       <li>System</li>
	       <li class='active'>$view_header</li>
		  </ul>";
 }
 
 // for API consumer add / regenerate
 // we use this to generate the authkey
 function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	$max = 15;
	$temp = "";
	
    for ($i = 0; $i < $max; $i++) {
        $random_int = mt_rand();
        $temp .= $alphabet[$random_int % strlen($alphabet)];
    }
    return $temp;
}

// creates xml from array
function array2xml($data, $root = null){
    $xml = new SimpleXMLElement($root ? '<' . $root . '/>' : '<root/>');
    array_walk_recursive($data, function($value, $key)use($xml){
        $xml->addChild($key, $value);
    });
    return $xml->asXML();
}

// Array to CSV Function
// Copyright (c) 2014, Ink Plant
// https://inkplant.com/code/array-to-csv
function array2csv($data,$args=false) {
    if (!is_array($args)) { $args = array(); }
    foreach (array('download','line_breaks','trim') as $key) {
        if (array_key_exists($key,$args)) { $$key = $args[$key]; } else { $$key = false; }
    }

    //for this to work, no output should be sent to the screen before this function is called
    if ($download) {
        if ((is_string($download)) && (substr($download,-4) == '.csv')) { $filename = $download; }
        else { $filename = 'download.csv'; }
        header('Content-Type:text/csv');
        header('Content-Disposition:attachment; filename='.$filename);
    }

    if ($line_breaks == 'windows') { $lb = "\r\n"; }
    else { $lb = "\n"; }

    //get rid of headers row, if it exists (headers should exist as keys)
    if (array_key_exists('headers',$data)) { unset($data['headers']); }

    $i = 0;
    foreach ($data as $row) {
        $i++;
        //display headers
        if ($i == 1) { 
            $c = '';
            foreach ($row as $key => $value) {
                $key = str_replace('"','""',$key);
                if ($trim) { $key = trim($key); }
                echo $c.'"'.$key.'"'; $c = ',';
            }
            echo $lb;
        }

        //display values
        $c = '';
        foreach ($row as $key => $value) {
            $value = str_replace('"','""',$value);
            if ($trim) { $value = trim($value); }
            echo $c.'"'.$value.'"'; $c = ',';
        }
        echo $lb;
    }

    if ($download) { die(); }
}

/*
 * Warning this function only works
 * for SELECT COUNT(*) FROM *
 */
function query($sql){
	global $database;
	
	$ret = $database->query($sql);
	return (!$ret || !$ret->num_rows) ? 0 : $ret->fetch_array()[0];
}

/* clean up a string */
function str_sanitize($str){
		 global $database;
		 $str = $database->real_escape_string($str);
	     return trim($str);	
}

/*
 * returns true if item is in multidimensional array
 */
function exists_in_array($val, $arr) {
	foreach ($arr as $key)
	{
		$key = strtolower($key);
		$val = strtolower($val);
		
		if ($key == $val)
			return true;
	}	
}	

/*
 * helper: returns full span with font awesome icons
 */
function font_awesome($icon){
	return "<span class='fa fa-fw $icon'></span>";
}

function verify_right($view){
	global $myrights;
	
	// verify that this user has the right to manage this right
	foreach($myrights as $key=>$menu){
		foreach ($menu as $id=>$val)
			if (is_array($val)){
				foreach ($val as $k=>$data){
					if (strtolower($data['url']) == strtolower($view)){						
						return true;
						break;
					}
				}
			}
	}
	
	return false;
}

/*
 * returns file extension
 * lowercased
 */
function get_file_extension($file){
	$tmp = explode('.',$file);
	return strtolower(end($tmp)) ;
}

function build_selectbox($sql, $ctlid, $preselect_id=""){
	global $database;
	$options = "";

	$ret = $database->query($sql);
	if (!$ret || !$ret->num_rows)
	{ 
		// nothing 
	}
	else
	{
		while ($row = $ret->fetch_array())
		{
			$id = $row['id'];
			$name = $row['name'];
			$selected = "";

			if ($preselect_id)
			{
				if ((int)$id == (int)$preselect_id)
					$selected = "selected";
			}

			$options .= "<option value='$id' $selected>$name</option>";
		}
	}

	return  "<select name='$ctlid' id='$ctlid' class='form-control'>
	           $options
			  </select>
			  <script>
			   $(document).ready(function(){
			    //	$('#$ctlid').select2();
			   });
			  </script>";


}

function build_selectbox_textonly($sql, $ctlid, $preselect_id=""){
	global $database;
	$options = "";

	$ret = $database->query($sql);
	if (!$ret || !$ret->num_rows)
	{ 
		// nothing 
	}
	else
	{
		while ($row = $ret->fetch_array())
		{
			$id = $row['id'];
			$name = $row['name'];
			$selected = "";

			if ($preselect_id)
			{
				if ($name == $preselect_id)
					$selected = "selected";
			}

			$options .= "<option value='$name' $selected>$name</option>";
		}
	}

	return  "<select name='$ctlid' id='$ctlid' class='form-control'>
	           $options
			  </select>
			  <script>
			   $(document).ready(function(){
			    //	$('#$ctlid').select2();
			   });
			  </script>";


}

/*
 * returns a bootstrap color for status
 */
function decorate_status($status) {
			switch (strtolower($status)){
							case 'completed':
								$status = "<span class='label label-success'>$status</span>";
								break;
							
							case 'on hold':
								$status = "<span class='label label-warning'>$status</span>";
								break;
								
							case 'expired':
							case 'overdue':
								$status = "<span class='label label-danger'>$status</span>";
								break;
								
							case 'pending':
								$status = "<span class='label label-default'>$status</span>";
								break;
			}
						
			return $status;
}

 /*
  * simple query
  */
 function subquery($sql)
 {
	 global $database;
	 $ret = $database->query($sql);
	 if (!$ret || !$ret->num_rows)
		 return "";

	 $row = $ret->fetch_array();
	 return $row[0];
 }
 
/*
 * returns user friendly error for PHP upload errors
 */
function php_upload_error($code)
{
	    $message = "";
	    
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return "<p class='alert alert-danger'>$message</p>";
} 

 // creates a bootstrap alert
 // by default success will autoclose
 function alertbuilder($alert,$type="success",$autoclose=false){
	 $id = "alert" . randomPassword(5);

	 $autoclose_extra = "";
	 if ($autoclose || $type=='success'){
		$autoclose_extra = "<script>window.setTimeout(function() { $('#$id').fadeTo(500, 0).slideUp(500, function(){ $('$id' ).remove();});}, 5000);</script>";
	 }
 	return "<p id='$id' class='alert alert-$type'>$alert</p>$autoclose_extra";
 }
 
 // for POST parameters
 // returns an alert and sets focus on the missing field
 function missing_parameter($field){
			return "<p class='alert alert-danger'>Some fields have not been filled in: <b>$field</b></p>
			        <script>$(document).ready(function(){
			        		$('[name=\"$field\"]').focus();
			        	});
			        </script>";
 }
 
 //********************************************************
 // returns a true if a document has been locked
 // and sets the username variable and the userid variable
 // with the details of the user that locked the document
 //
 // NOT SAFE on all versions of PHP
 //********************************************************
 function document_locked($table, $id, &$vusername, &$vuserid){
 			global $database;
 			
			$vuserid   = 0;
			$vusername = "";
			
			$sql = "SELECT 
								lockedby_user_id, user_name
					  FROM 
					  			`$table` d, users u
					  WHERE 
					  			d.id=$id AND 
							   d.lockedby_user_id = u.id AND 
							   u.id;";								  		  
			$ret = $database->query($sql);
			
			if (!$ret || !$ret->num_rows){
				return false;
			}
			
			$row = $ret->fetch_array();
			
			$vuserid   = $row['lockedby_user_id'];
			$vusername = $row['user_name'];
			 
			return true;
 }
 
  /*
  * returns table comment
  */
 function get_table_comment($table){
 			 global $database;
 			 $comment = "<i style='color:red'>Unable to get table comment</i>";
 			 $settings_db = settings::db_db;
  			 
  			 $sql = "SELECT table_comment 
			         FROM INFORMATION_SCHEMA.TABLES 
			         WHERE table_schema='$settings_db' 
			         AND table_name='$table';";
			 $ret0=$database->query($sql);
			 if (!$ret0 || !$ret0->num_rows){
			 } else {
				$row0    = $ret0->fetch_array();
				$comment = $row0[0];
			 }
			 
			 return $comment;
 }
 
 /* returns validation routine for forms */
 function validate_form($required_string){
     return "/*
              * make sure required fields are filled in before submit
              */	
            function validate_form(){
                    var required = [$required_string];
                    var required_max = required.length;
                    
                for (var idx=0; idx < required_max; idx++){
                    var \$req = $('#'+required[idx]);
                    
                    console.log('req',required[idx], 'val:',\$req.val());
    
                    if (required[idx] == 'full_story'){
                        if (!tinyMCE.get(required[idx]).getContent().length){
                            alertify.error('Field data is required: ' + required[idx]);
                            return false;				       
                        }
                        
                    } else {
                        if (\$req.val() == '' || \$req.val() == null){
                            \$req.focus();
                            alertify.error('Field data is required: ' + required[idx]);
                            return false;
                        }
                    }
                }
                
                return true;
            }";
 }
function build_form($table,$required, $ignored = array()) {
 	global $database;
 	
 	// control size
 	$size_cap   = '3';
 	$size_field = '9';
 	
 	// buffer
	$inputs = "";
	
	$sql = "SHOW FULL COLUMNS FROM `$table`;";		  
	$ret = $database->query($sql);
	if (!$ret || !$ret->num_rows)
	{
		return alertbuilder("Failed to retrieve column to build the UI.","danger");
	}

	while ($row = $ret->fetch_array())
	{
			$input    = "";
			$field    = $row[0]; //Field -- 0
			$col_type = $row['Type'];
         $comment= $row['Comment'];
         
         if (in_array($field, $ignored)) continue;
         
			$field_ = str_replace("_", " ", $field);
			$field_ = $comment ? $comment : ucfirst($field);
			$val    =  stripSlashes( @ $_POST[$field] );
            
            //$val = htmlspecialchars($val);
			$star_required = in_array(strtolower($field), $required) ? "<span style='color:red'>*</span>" : "";
			$is_required = in_array($field, $required) ? "required" : "";
			
			// we ignore some fields
			switch (strtolower($field)){
			    /* ignored on a form */
				case 'entrydate':
				case 'user_id':
				case 'id':
		 		case "table":
		 		case 'sessionid':
		 		case 'activationhash':
		 		case 'lastlogin':
		 		case 'lastlogout':
					break;

                case 'roleid':
                     $sql = "SELECT * FROM `user_roles`;";
                     $select = build_selectbox($sql,$field,$val);
                                              
                     $inputs .= "<div class='form-group'>
                                 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
                                 <div class='col-sm-$size_field'>
                                                $select
                                 </div>
                                  </div>
                                  <script>
                                   $(document).ready(function(){
                                            $('#$field').select2();
                                   });
                                  </script>";
                      break;
                      				
                case 'severity_id':
                     $sql = "SELECT * FROM `projects_severity`;";
                     $select = build_selectbox($sql,$field,$val);
                                              
                     $inputs .= "<div class='form-group'>
                                 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
                                 <div class='col-sm-$size_field'>
                                                $select
                                 </div>
                                  </div>
                                  <script>
                                   $(document).ready(function(){
                                            $('#$field').select2();
                                   });
                                  </script>";
                      break;

                 case 'client_id':
                      $sql = "SELECT * FROM `projects_clients`;";
							$select = build_selectbox($sql,$field,$val);
                    $inputs .= "<div class='form-group'>
                                 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
                                 <div class='col-sm-$size_field'>
                                                $select
                                 </div>
                                  </div>
                              <script>
                               $(document).ready(function(){
                                        $('#$field').select2();
                               });
                              </script>";
                    break;

                case 'status_id':
                        $sql = "SELECT * FROM `projects_status`;";
                        $select = build_selectbox($sql,$field,$val);

                        $inputs .= "<div class='form-group'>
                                     <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
                                     <div class='col-sm-$size_field'>
                                                    $select
                                     </div>
                                      </div>
                                      <script>
                                       $(document).ready(function(){
                                                $('#$field').select2();
                                       });
                                      </script>";
                        break;
						
				case 'entity_type_id':
					$sql = "SELECT * FROM `my_finances_entity_types`;";
					$select = build_selectbox($sql,$field,$val);

					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$select
		    						 </div>
		  						  </div>
									  <script>
									   $(document).ready(function(){
										    $('#$field').select2();
									   });
									  </script>";
					break;
					
				case 'naturetype_id':
					$sql = "SELECT * FROM `projects_naturetypes`;";
					$select = build_selectbox($sql,$field,$val);

					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$select
		    						 </div>
		  						  </div>
									  <script>
									   $(document).ready(function(){
										    $('#$field').select2();
									   });
									  </script>";
					break;					

				case 'entrytype_id':
					$sql = "SELECT * FROM `projects_entrytypes`;";
					$select = build_selectbox($sql,$field,$val);

					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$select
		    						 </div>
		  						  </div>
									  <script>
									   $(document).ready(function(){
										    $('#$field').select2();
									   });
									  </script>";
					break;
            					
				case 'project_id':
					$sql = "SELECT * FROM `projects_projects`;";
					$select = build_selectbox($sql,$field,$val);

					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$select
		    						 </div>
		  						  </div>
									  <script>
									   $(document).ready(function(){
										    $('#$field').select2();
									   });
									  </script>";
					break;
					
				case 'user_password':
					$view = @ $_GET['view'];
					$fa_edit = font_awesome('fa-edit');
					
					switch ($view){
							case 'profile':
							        $input = "<a href='?view=profile-update-password' class='cta-link'>$fa_edit Change Password</a>";
							        break;
							        
							case 'edit-user':
							        $id = $_GET['id'];
									$input = "<a href='?view=user-update-password&id=$id' class='cta-link'>$fa_edit Change User Password</a>";
									break;
									
							default:
							        // password is never shown
							        $val = '';
									$input = "<input type='password' class='form-control' name='$field' id='$field' value='$val'>";
									break;
					}
										                
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$input
		    						 </div>
		  						  </div>";
					break;					
										
				case 'file_type_id':
					$sql = "SELECT * FROM `file_types`;";
					$select = build_selectbox($sql,$field,$val);
					
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$select
		    						 </div>
		  						  </div>
									  <script>
									   $(document).ready(function(){
										    $('#$field').select2();
									   });
									  </script>";
					break;
				
				case 'date_stamp':
				case 'dob':	
				case 'date_started':
				case 'date_ended':
				case 'birth_date':
				//case 'exitdate':
				    /* bootstrap datepicker does not play nice with autocomplete enabled (default) */
					$input  = "<input type='text' class='form-control' value=\"$val\" 
											id='$field' name='$field' 
											autocomplete='off'
											placeholder='YYYY-mm-dd'>";
					$inputs .=  "<div class='form-group'>
										<label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
										<div class='col-sm-$size_field'>$input</div>
									  </div>
									  <script>
									   $(document).ready(function(){
									   	 // set field to calendar & resolve drop down bug
										    $('#$field').datepicker({'format':'yyyy-mm-dd'});
										    $('#$field').datepicker().on('changeDate',function(e) {
										    $('#$field').datepicker('hide');
										  });
									   });
									  </script>";	
					break;				  			  		

				case 'company_id':
					$sql = "SELECT * FROM `institutions`;";
					$select = build_selectbox($sql,$field,$val);
					
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$select
		    						 </div>
		  						  </div>
									  <script>
									   $(document).ready(function(){
										    $('#$field').select2();
									   });
									  </script>";
					break;

				case 'institution_id':
					$sql = "SELECT * FROM `institutions` ORDER BY name ASC;";
					$select = build_selectbox($sql,$field,$val);
					
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$select
		    						 </div>
		  						  </div>
									  <script>
									   $(document).ready(function(){
										    $('#$field').select2();
									   });
									  </script>";
					break;
										
				case 'country_id':
					if (!$val) $val = 123; // default to NAMIBIA
					
					$sql = "SELECT * FROM `list_countries`;";
					$select = build_selectbox($sql,$field,$val);
					
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$select
		    						 </div>
		  						  </div>
									  <script>
									   $(document).ready(function(){
										    $('#$field').select2();
									   });
									  </script>";
					break;
					
				case 'comm_type_id':
					$sql = "SELECT * FROM `comm_types`;";
					$select = build_selectbox($sql,$field,$val);
					
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$select
		    						 </div>
		  						  </div>
									  <script>
									   $(document).ready(function(){
										    $('#$field').select2();
									   });
									  </script>";
					break;
					
				case 'sex_id':
					$sql = "SELECT * FROM `list_sex`;";
					$select = build_selectbox($sql,$field,$val);
					
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$select
		    						 </div>
		  						  </div>
									  <script>
									   $(document).ready(function(){
										    $('#$field').select2();
									   });
									  </script>";
					break;

				case 'payment_method_id':
					$sql = "SELECT * FROM `list_payment_methods`;";
					$select = build_selectbox($sql,$field,$val);
					
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$select
		    						 </div>
		  						  </div>
									  <script>
									   $(document).ready(function(){
										    $('#$field').select2();
									   });
									  </script>";
					break;
					
				case 'sex_id':
					$sql = "SELECT * FROM `list_sex`;";
					$select = build_selectbox($sql,$field,$val);
					
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$select
		    						 </div>
		  						  </div>
									  <script>
									   $(document).ready(function(){
										    $('#$field').select2();
									   });
									  </script>";
					break;
					
				case 'enabled':
				case 'isactive':
				case 'active':
					$options = "";
					$data = [1=>'Yes', '0'=>'No'];
					foreach($data as $k=>$v){
						$selected = $k == $val ? 'selected="selected"' : '';
						$options .= "<option value='$k' $selected>$v</option>";
					}
					
					$input = "<select class='form-control2' name='$field' id='$field' value='$val'>
									$options
								 </select>";
					                
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$input
		    						 </div>
		  						  </div>";
		  						  					
					break;
					
				case 'quantity':
				case 'amount':
				case 'price':
				case 'repayment_months':
					$input = "<input type='number' class='form-control' name='$field' 
					                 id='$field' value='$val'>";
					                
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$input
		    						 </div>
		  						  </div>";
					break;
		
				case 'cellphone':
					$input = "<input type='text' placeholder='081???' class='form-control' name='$field' 
					                 id='$field' value='$val'>";
					                
                  $input = "<div class='input-group'>
        			            <span class='input-group-addon'><span class='fa fa-mobile'></span></span>
        			            <input type='text' placeholder='' class='form-control' $is_required
        			                                 name='$field' id='$field' value='$val'>
        			          </div>";
          					                
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$input
		    						 </div>
		  						  </div>";
					break;
																
				case 'email':
		          $input = "<div class='input-group'>
					            <span class='input-group-addon'><span class='fa fa-envelope-o'></span></span>
					            <input type='email' placeholder='name@mail.com' $is_required class='form-control' 
					                                 name='$field' id='$field' value=\"$val\">
					          </div>";
		          					                
							$inputs .= "<div class='form-group'>
				    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
				    						 <div class='col-sm-$size_field'>
												$input
				    						 </div>
				  						  </div>";
							break;
					
				case 'filename*':
				case 'project_proposal':				
					$input = "<input type='file' class='form-control' $is_required name='$field' 
					                 id='$field' value='$val'>";
					                
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-$size_field'>
										$input
		    						 </div>
		  						  </div>";
					break;

			case 'filename':
				$input = "<input type='file' class='form-control' name='$field' $is_required accept='application/pdf,image/*' id='$field' value='$val'>";
				                
				$preview = "";
				
				if (strlen($val)){
    				$ext = strtolower(get_file_extension($val));
    				$base = basename($val);
    				
    				switch ($ext){
    				    case 'gif':
    				    case 'jpg':
    				    case 'jpeg':
    				    case 'png':
    				        $preview = "<BR>
    				        <a href='$val' class='fancybox'><img class='img-thumbnail' src='$val'></a>";
    				        break;
    				        
    				    case 'pdf':
    				        $preview = "<BR><small>Attachment</small><BR>
    				        <a href='$val' class='fancybox'><span class='fa fa-fw fa-file-o'></span> $base</a>";
    				        break;			
    				        
    				    default:
    				        $preview = "<BR><small>Attachment</small><BR>
    				        <a href='$val' class='fancybox'><span class='fa fa-fw fa-file-o'></span> $base</a>";				        
    				        break;
    				}
				}
				
				$inputs .= "<div class='form-group'>
	    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
	    						 <div class='col-sm-$size_field'>
									$input
									$preview
	    						 </div>
	  						  </div>";
				break;
									
			case 'profilepic':
			case 'artwork':			
				$input = "<input type='file' class='form-control' $is_required name='$field' accept='image/*' id='$field' value='$val'>";
				                
				$preview = "";
				
				if (strlen($val)){
    				$ext = strtolower(get_file_extension($val));
    				$base = basename($val);
    				
    				switch ($ext){
    				    case 'gif':
    				    case 'jpg':
    				    case 'jpeg':
    				    case 'png':
    				        $preview = "<BR>
    				        <a href='$val' class='fancybox'><img class='img-thumbnail' style='width:150px' src='$val'></a>";
    				        break;
    				        
    				    case 'pdf':
    				        $preview = "<BR><small>Attachment</small><BR>
    				        <a href='$val' class='fancybox'><span class='fa fa-fw fa-file-o'></span> $base</a>";
    				        break;			
    				        
    				    default:
    				        $preview = "<BR><small>Attachment</small><BR>
    				        <a href='$val' class='fancybox'><span class='fa fa-fw fa-file-o'></span> $base</a>";				        
    				        break;
    				}
				}
				
				$inputs .= "<div class='form-group'>
	    						 <label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
	    						 <div class='col-sm-$size_field'>
									$input
									$preview
	    						 </div>
	  						  </div>";
				break;
								
			
			case 'value':
				$type = 'textarea';
				$input = "<textarea $is_required id='$field' name='$field' 
				            class='form-control' rows='3'
										$tagsinput>$val</textarea>";	
				$inputs .= "<div class='form-group'>
								<label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
								<div class='col-sm-$size_field'>$input</div>
							</div>";
				break;	
								
			default:							
					$tagsinput = "";
					$type = 'text';
					
					switch($col_type) {
						case 'tinyint(1)':
							$type = 'checkbox';
							$input = "<input type='$type' $is_required class='form-control' 
													$tagsinput value='$val' 
							                 id='$field' name='$field'>";											
							break;
							
						case 'longtext':
							$type = 'textarea';
							$input = "<textarea $is_required id='$field' name='$field' 
							            class='form-control' rows='3'
													$tagsinput>$val</textarea>";												
							break;	
							
						default:
							$input = "<input type='$type' $is_required class='form-control' 
													$tagsinput value=\"$val\" 
							                 id='$field' name='$field'>";	
							break;										
					}
					
					$inputs .= "<div class='form-group'>
									<label class='control-label col-sm-$size_cap' for='$field'>$field_ $star_required</label>
									<div class='col-sm-$size_field'>$input</div>
								</div>";
					break;
				}
		}

		return $inputs;	
 }
 
 /*
  * Measuring page load times
  */
 function timer(){
    static $start;

    if (is_null($start))
    {
        $start = microtime(true);
    }
    else
    {
        $diff = round((microtime(true) - $start), 4);
        $start = null;
        return $diff;
    }
}

/*
https://stackoverflow.com/questions/619610/whats-the-most-efficient-test-of-whether-a-php-string-ends-with-another-string
*/
function endswith($string, $test) {
    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) return false;
    return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}



/*
 * Written by William Sengdara - April 20, 2019
 *
 * Generates data dynamically from a SQL query for ChartJS :
 *  first column is used as the labels
 *  all other columns will be used as the datasets
 *
 * Parameters:
 *
 *			$chartid			-- existing canvas id for Chart.Js
 *			$type				-- line, bar, horizontalBar, doughnut, pie, scatter, radar 
 *			$title			-- 
 *			$description	-- 
 *			$sql				-- SQL query
 *
 *
 * Example usage:
 *

	<div class="col-md-6"><canvas id="chart1" width='300' height='200'></canvas></div>
	<div class="col-md-6"><canvas id="chart2" width='300' height='200'></canvas></div>
	<div class="col-md-6"><canvas id="chart3" width='300' height='200'></canvas></div>
	<div class="col-md-6"><canvas id="chart4" width='300' height='200'></canvas></div>
	
	<script>
			<?php
			   // Quarter 1
			   // granted vs printed
			  // month	| 
			  $sql1 = "SELECT 
									s.name `Category`,
									COUNT(distinct d.id) `Total`						
							FROM 
							  		app_data d, 
							  		app_log l,
							  		app_status s 
							WHERE 
							  		d.id = l.app_id AND 
							  		d.status_id = s.id AND
							  		l.description LIKE CONCAT('%to ',s.name,'%') AND
									DATE(l.entrydate) BETWEEN '2018-01-01' AND '2018-03-31'
							GROUP BY `Category`";
			  
			  $sql2 = "SELECT m.name `month`, 
			  				SUM(d.value) `total` 
			  			  FROM sample_data_1 d, 
			  			  		  sample_data_months m 
			  			  WHERE d.month_id = m.id 
			  			  GROUP BY m.id;";
			  
			  echo generateChartJs("chart1", 'line', "Line Chart", "description", $sql1);
			  echo generateChartJs("chart2", 'bar', "Bar Chart", "description", $sql2);
			  echo generateChartJs("chart3", 'doughnut', "Doughnut Chart", "description", $sql2);
			  echo generateChartJs("chart4", 'horizontalBar', "Horizontal Bar Chart", "description", $sql1);		  
			?>
	</script>

*/
function generateChartJs($chartid, $type, $title, $description, $sql){
		global $database;
		
		$res = $database->query($sql);
		if (!$res || !$res->num_rows){
			return "var ctx = document.getElementById('$chartid').getContext('2d'); ctx.fillStyle='red'; ctx.fillText(\"Error generating this chart. Check your SQL.\", 10,10);";
		} 

		$row = $res->fetch_assoc();
		$cols = array_keys($row);
		
		$labels = array();
		$datasets = array();
		
		if (!sizeof($cols) or sizeof($cols) < 2){
			return "var ctx = document.getElementById('$chartid').getContext('2d'); ctx.fillStyle='red'; ctx.fillText(\"sizeof cols is below expected number.\", 10,10);";
		}
		
		mysqli_data_seek($res,0);
			 		
		while ($row = $res->fetch_assoc()){
			foreach ($cols as $key=>$val){
				switch ($key){
					case 0: /* first column should be your labels */
						$label = $row[$val];
						$labels[] = "'$label'";
						break;
						
					default:
						$data = $row[$val];
						$data = $data ? $data : 0; 
						$datasets[$key-1][] = "'$data'";
						break;
				}
			}
		}
     
     $colors = array();
     $colors[] = "'rgba(54, 162, 235, 0.7)'";
     $colors[] = "'rgba(255, 159, 64, 0.7)'";
     $colors[] = "'rgba(255, 99, 132, 0.7)'";
     $colors[] = "'rgba(75, 192, 192, 0.7)'";
     $colors[] = "'rgba(153, 102, 255, 0.7)'";
     $colors[] = "'rgba(83, 90, 55, 0.7)'";
     $colors[] = "'rgba(113, 102, 25, 0.7)'";
     $colors[] = "'rgba(13, 62, 85, 0.7)'";
     $colors[] = "'rgba(73, 120, 155, 0.7)'";
     $colors[] = "'rgba(154, 62, 245, 0.7)'";                  
     $colors[] = "'rgba(204, 162, 235, 0.7)'";
     $colors[] = "'rgba(55, 159, 64, 0.7)'";
     $colors[] = "'rgba(85, 99, 132, 0.7)'";
     $colors[] = "'rgba(95, 192, 192, 0.7)'";
     $colors[] = "'rgba(53, 102, 255, 0.7)'";
     $colors[] = "'rgba(43, 90, 55, 0.7)'";
     $colors[] = "'rgba(103, 102, 25, 0.7)'";
     $colors[] = "'rgba(130, 62, 85, 0.7)'";
     $colors[] = "'rgba(33, 120, 155, 0.7)'";
     $colors[] = "'rgba(194, 62, 245, 0.7)'";
                       
	$max_data_cols = sizeof($datasets[0]);
	$labels = implode(",", $labels);

	$datasets_ = array();

	foreach($datasets as $key=>$datas){ 
		$data = implode(",",$datas);
		$label = $cols[$key+1]; // ignore the first column as it is our label

		$temp = array();
      for($idx=0; $idx<$max_data_cols; $idx++){ 
      	$temp[] = $colors[$key];
      }
		$colors_ = implode(',', $temp);

		$datasets_[] = "{
							label: '$label',					            
						    data: [$data],
						    backgroundColor: [$colors_],
				            borderColor: [],
				            borderWidth: 1
						}";
	}

	$datasets = implode(",", $datasets_);

	$chartdata = "/* Original SQL:
							$sql 
						*/
					  ctx = document.getElementById('$chartid').getContext('2d');
					  new Chart(ctx, {
	    						type: '$type',
	    						data: {
	    									labels: [ $labels ], 
	    									datasets: [ $datasets ]
	    								},
							    options: {
										        title: {
										            display: true,
										            text: '$title'
										        }
											   }
								});";
								
	return $chartdata;
}

/*
 * Written by William Sengdara - April 21, 2019
 *
 * Generates a table dynamically from a SQL query :
 *  columns are used for table TH
 *  values are used for table TD
 *
 * Parameters:
 *
 * 	$tableid 		-- unused id to be used for the new table (e.g. table1) 
 *                   	if set, dataTable() will be called on the table. 
 *		title				-- title above table
 * 	$description   -- description after the title
 * 	$sql				-- SQL query
 *
 * Example usage:
 *
  echo generateVerticalTableFromQuery("table1", "Table Generator", "Testing the table generator", $sql1);
*/
 function generateVerticalTableFromQuery($tableid, $title, $description, $sql){
		  global $database;

        $rec = $database->query($sql);
        
        if (!$rec || !$rec->num_rows){
			   return $database->error;
		  }
		   
		  $field_names = array();
		  $fields = "";
		  $body = "";
			
			while ($fieldinfo=mysqli_fetch_field($rec)){	
				$fieldname = $fieldinfo->name;
				$field_names[] = $fieldname;
				$fields .= "<th>$fieldname</th>";
			}

			$datatable = "$('#$tableid').dataTable();";
			if (!$tableid) $datatable = "";
			$data = "";
			
			while ($row = $rec->fetch_array()){
				$data = "";
				foreach ($field_names as $fld){
					$val = $row[$fld];
					if (!$val) $val = 0;
					$data .= "<td>$val</td>";
				}
				
			   $body .= "<tr>$data</tr>";		
			}

			return "<h5 style='font-weight:bold'>$title <small class='pull-right'>$description</small></h5>
			      <div class='table-responsive' style='overflow-x:scroll;'>
				  <table class='table table-hover table-bordered' id='$tableid'>
			       <thead>
				     <tr>$fields</tr>
				   </thead>
				   <tbody>
						$body
				   </tbody>
				  </table>
				  </div>
				  <script>
				  	$('document').ready(function(){
				  		$datatable
				  	});
				  </script>";
 }
function generateTableFromQuery($tableid, $title, $description, $sql){
	return generateVerticalTableFromQuery($tableid, $title, $description, $sql);
}

/*
 * Written by William Sengdara - April 21, 2019
 *
 * Generates a table dynamically from a SQL query :
 *  columns are used for table TH
 *  values are used for table TD
 *
 * Parameters:
 *
 * 	$tableid 		-- unused id to be used for the new table (e.g. table1) 
 *                   	if set, dataTable() will be called on the table. 
 *		title				-- title above table
 * 	$description   -- description after the title
 * 	$sql				-- SQL query
 *
 * Example usage:
 *
		  $sql1 = "SELECT m.name AS `month`, 
					  (SELECT SUM(d.value) FROM sample_data_2 d WHERE d.sex_id = 1 AND d.month_id =m.id ) AS `M`, 
					  (SELECT SUM(d.value) FROM sample_data_2 d WHERE d.sex_id = 2 AND d.month_id =m.id) AS `F`, 
					  (SELECT SUM(d.value) FROM sample_data_2 d WHERE d.sex_id = 3 AND d.month_id =m.id) AS `O` 
					  FROM 
					  		sample_data_2 d, 
					  		sample_data_months m 
					  WHERE 
					  		d.month_id = m.id 
					  GROUP BY m.id;";
		  
		  $sql2 = "SELECT m.name `month`, 
		  				SUM(d.value) `total` 
		  			  FROM sample_data_1 d, 
		  			  		  sample_data_months m 
		  			  WHERE d.month_id = m.id 
		  			  GROUP BY m.id;";
		  
		  
  echo generateHorizontalTableFromQuery("table1", "Table Generator", "Testing the table generator", $sql1);
*/
 function generateHorizontalTableFromQuery($tableid, $title, $description, $sql){
		  global $database;

        $rec = $database->query($sql);
        
        if (!$rec || !$rec->num_rows){
			   return $database->error;
		  }
		   
		  $field_names = array();
		  $fields = "";
		  $body = "";
			
			// first field
			$field_names[] = "<th>Category</th>";
			while ($row = $rec->fetch_array()){
				$col = $row[0];
				$field_names[] = "<th>$col</th>";
			}

			$fields = implode("", $field_names);
			
			mysqli_data_seek($rec, 0);

			$keys = array();			
			$row = $rec->fetch_array();
			$keys_ = array_keys($row);
			$idx = 0;

			foreach($keys_ as $key){
				if (! is_numeric($key) && $idx > 1){
					$keys[] = $key;
				}
					
				$idx++;
			}

			mysqli_data_seek($rec, 0);
			$data = "";
			$body = "";
			
			$total_fields = sizeof($field_names); // account for category
			$datasets = [];
			$idx = 1;
			
			while ($row = $rec->fetch_array()){		
				foreach($keys as $category) {
						 $data = $row[$category];
						 if (!$data) $data = 0;
						 $datasets[$category][] = "<td>$data</td>"; 
				}
			}			

			foreach ($datasets as $key=>$arr){
				$data = implode("", $arr);
				$body .= "<tr><td>$key</td>$data</tr>";
				$idx++;
			}
			
			return "<h5 style='font-weight:bold'>$title <small class='pull-right'>$description</small></h5>
					  <div class='table-responsive' style='overflow-x:scroll;'>
						  <table class='table table-hover table-bordered' id='$tableid'>
					       <thead>
						     <tr>$fields</tr>
						   </thead>
						   <tbody>
								$body
						  </tbody>
						 </table>
						</div>";
 } 
 
 function generateTableFromQuery2($tableid, $title, $description, $sql){
 		return generateHorizontalTableFromQuery($tableid, $title, $description, $sql);
 }

/**
 * Generate bar chart using C3
 * Written by William Sengdara - 10 November 2019
 *
 * Notes: requires a div container not a canvas
 * 		echo "<div id=\"chart3\" width='300'></div>";
 * 
 * 	
 	SELECT
		`cellphone`, 
		(SELECT COUNT(ref) FROM nampost_ref d 
		WHERE d.cellphone = r.cellphone AND DAYNAME(d.entrydate)='Sunday' ) AS `Sunday`, 
		(SELECT COUNT(ref) FROM nampost_ref d 
		WHERE d.cellphone = r.cellphone AND DAYNAME(d.entrydate)='Monday') AS `Monday`,
		(SELECT COUNT(ref) FROM nampost_ref d 
		WHERE d.cellphone = r.cellphone AND DAYNAME(d.entrydate)='Tuesday') AS `Tuesday`, 
		(SELECT COUNT(ref) FROM nampost_ref d 
		WHERE d.cellphone = r.cellphone AND DAYNAME(d.entrydate)='Wednesday') AS `Wednesday`,
		(SELECT COUNT(ref) FROM nampost_ref d 
		WHERE d.cellphone = r.cellphone AND DAYNAME(d.entrydate)='Thursday') AS `Thursday`,
		(SELECT COUNT(ref) FROM nampost_ref d 
		WHERE d.cellphone = r.cellphone AND DAYNAME(d.entrydate)='Friday') AS `Friday`,
		(SELECT COUNT(ref) FROM nampost_ref d 
		WHERE d.cellphone = r.cellphone AND DAYNAME(d.entrydate)='Saturday') AS `Saturday`
	FROM 
		`nampost_ref` r
	GROUP BY `cellphone`;
 */
function generateChartJs2($chartid, $sql){
		global $database;
		
		$res = $database->query($sql) or die("<div style='color:red'>{$database->error}</div>");
		if (!$res || !$res->num_rows){
			return "//<div style='color:red'>Error generating this chart. Check your SQL.</div>";
		} 

		$row = $res->fetch_assoc();
		$cols = array_keys($row);
		
		if (!sizeof($cols) or sizeof($cols) < 2){
			return "//<div style='color:red'>sizeof cols is below expected number.</div>";
		}
		
		// reset to first row
		mysqli_data_seek($res,0);
					 
		$labels = [];
		$datasets = [];

		while ($row = $res->fetch_assoc()){
			foreach ($cols as $key=>$val){
				switch ($key){
					case 0: /* first column should be your labels */
						$label = $row[$val];
						$labels[] = "'$label'";
						break;
						
					default:
						$data = $row[$val];
						$data = $data ? $data : 0; 
						$datasets[$key-1][] = "'$data'";
						break;
				}
			}
		}
     
		$max_data_cols = sizeof($datasets[0]);
		if (sizeof($labels))
			$labels = implode(",\n", $labels);
		else 
			$labels = "";

		$datasets_ = [];
		$types = [];
		
		foreach($datasets as $key=>$datas){ 
				$data = implode(",",$datas);
				$label = $cols[$key+1]; // ignore the first column as it is our label
		
				$datasets_[] = "['$label',	$data]";
				$types[] = "'$label':'bar'";
		}
	
		if (sizeof($datasets_)) 
			$datasets = implode(",\n", $datasets_);
		else
			$datasets = "";

		if (sizeof($types)) 
			$types = implode(",\n", $types);
		else
			$types = "";

		$chartdata = "\n$(window).load(function(){
								var chart = c3.generate({
									bindto: '#$chartid',
									data: {
									  columns: [
											$datasets
									  ],		  
									  axes: {
										  data2: 'y2'
									  },
									  types: {
											$types
									  }								  	  
									},
										axis: {
											 	x: {
												 	type: 'category',
												 	categories: [$labels]
											 	}
									 	}								
								});
							});";
									
		return $chartdata;
}


/*
  Use this for when data is basic in the format
  total location
  ===============
  1	  Windhoek
  22    Swakopmund
  3     Erongo
*/
function generatePieFlat($chartid, $type, $title, $description, $sql){
		global $database;
		
		$res = $database->query($sql);
		if (!$res || !$res->num_rows){
			return "var ctx = document.getElementById('$chartid').getContext('2d'); ctx.fillStyle='red'; ctx.fillText(\"Error generating this chart. Check your SQL.\", 10,10);";
		} 

		$row = $res->fetch_assoc();
		$cols = array_keys($row);
		
		if (!sizeof($cols) or sizeof($cols) < 2){
			return "var ctx = document.getElementById('$chartid').getContext('2d'); ctx.fillStyle='red'; ctx.fillText(\"sizeof cols is below expected number.\", 10,10);";
		}
		
		$labels = [];
		$datas = [];
				
		mysqli_data_seek($res,0);
			 		
				 		
		while ($row = $res->fetch_assoc()){
			foreach ($cols as $key=>$val){
						echo "/*$key:$val, {$cols[$key]}=={$row[$val]}*/ \n";
						$data = $row[$val];
						if (is_numeric($data)) $datas[] = $data ? $data : 0; 
						$fld = $row[$val];
						if (!is_numeric($fld)) $labels[] = "'$fld'";
			}
		}
     
     $colors = array();
     $colors[] = "'rgba(54, 162, 235, 0.7)'";
     $colors[] = "'rgba(255, 159, 64, 0.7)'";
     $colors[] = "'rgba(255, 99, 132, 0.7)'";
     $colors[] = "'rgba(75, 192, 192, 0.7)'";
     $colors[] = "'rgba(153, 102, 255, 0.7)'";
     $colors[] = "'rgba(83, 90, 55, 0.7)'";
     $colors[] = "'rgba(113, 102, 25, 0.7)'";
     $colors[] = "'rgba(13, 62, 85, 0.7)'";
     $colors[] = "'rgba(73, 120, 155, 0.7)'";
     $colors[] = "'rgba(154, 62, 245, 0.7)'";                  
     $colors[] = "'rgba(204, 162, 235, 0.7)'";
     $colors[] = "'rgba(55, 159, 64, 0.7)'";
     $colors[] = "'rgba(85, 99, 132, 0.7)'";
     $colors[] = "'rgba(95, 192, 192, 0.7)'";
     $colors[] = "'rgba(53, 102, 255, 0.7)'";
     $colors[] = "'rgba(43, 90, 55, 0.7)'";
     $colors[] = "'rgba(103, 102, 25, 0.7)'";
     $colors[] = "'rgba(130, 62, 85, 0.7)'";
     $colors[] = "'rgba(33, 120, 155, 0.7)'";
     $colors[] = "'rgba(194, 62, 245, 0.7)'";
                       
	$labels = implode(",", $labels);

	$colors_ = [];
	
	foreach($datas as $key=>$val){ 
      	$colors_[] = $colors[$key];
	}
	
	$data_ = implode(",", $datas);
	$colors_ = implode(",", $colors_); 

	$chartdata = "/* Original SQL:
							$sql 
						*/
					  ctx = document.getElementById('$chartid').getContext('2d');
					  new Chart(ctx, {
	    						type: '$type',
	    						data: {
	    									datasets: [{ 
	    														data: [$data_],
	    														backgroundColor: [$colors_]
	    													}],
	    									labels: [ $labels ]
	    								},
	    								
							    options: {
										        title: {
										            display: true,
										            text: '$title'
										        }
											   }
								});";
								
	return $chartdata;
}

/* from cmskubata */
   function generate_form($table, $ignored, $required){
        global $database;

        $fields = "";

        // get the fields & comment for each field
        $sql = "show full columns from $table;";
        $res = $database->query($sql) or die($database->error);
        if (!$res || !$res->num_rows){
            die("Unable to fetch the columns for table $table");
        }

        while ($row  = $res->fetch_array()){
            $field   = $row['Field'];
            $type    = $row['Type'];
            $comment = $row['Comment'];
            $comment = ucwords($comment);

            $field_ = $field; // total_people
            if ($comment != "") $field_ = ucwords($comment); // Table for

            // by default field is not required
            $fieldrequired = "";
            $requiredstar = "";

            if (in_array($field, $ignored)) continue;
            if (in_array($field, $required)){
                $fieldrequired = "required='required'";
                $requiredstar = "<span class='color-red'>*</a>";
            } 

            $value = @ $_POST[$field];
            $input = "";
            

            switch ($field){
                case 'entrydate':
                case 'daterequired':
                case 'datebirth':
                case 'datesalary':
                case 'exitdate':
                    $type  = 'date';
                    $input = "<input class='form-control'  type='$type' value=\"$value\" $fieldrequired  name='$field' id='$field'>";
                    break;

                case 'exittime':
                case 'timerequired':
                    $type  = 'time';
                    $input = "<input class='form-control'  type='$type' value=\"$value\" $fieldrequired  name='$field' id='$field'>";
                    break;

                case 'user_type_id':
                    $options = "";
                    $table1 = "user_types";
                    $sql = "SELECT id, name FROM `$table1` ORDER BY name ASC;";
                    $res1 = $database->query($sql) or die($database->error);
                    if (!$res1 || !$res1->num_rows){
                        // we dont do anything, options is empty already
                    } else {
                        while ($row1 = $res1->fetch_array()){
                            $id_ = $row1['id'];
                            $name_ = $row1['name'];
                            $selected = $id_ == @ $_POST[$field] ? 'selected' : '';
                            $options .= "<option value='$id_' $selected>$name_</option>";
                        }
                    }

                    $input = "<select class='form-control'  name='$field' id='$field' $fieldrequired >$options</select>";
                    break;

                case 'comm_type_id':
                    $options = "";
                    $table1 = "communication_types";
                    $sql = "SELECT id, name 
                            FROM `$table1` 
                            WHERE name <> 'SMS';";
                    $res1 = $database->query($sql) or die($database->error);
                    if (!$res1 || !$res1->num_rows){
                        // we dont do anything, options is empty already
                    } else {
                        while ($row1 = $res1->fetch_array()){
                            $id_ = $row1['id'];
                            $name_ = $row1['name'];
                            $selected = $id_ == @ $_POST[$field] ? 'selected' : '';
                            $options .= "<option value='$id_' $selected>$name_</option>";
                        }
                    }

                    $input = "<select class='form-control'  name='$field' id='$field' $fieldrequired >$options</select>";
                    break;
                    
                case 'file_type_id':
                    $options = "";
                    $table1 = "file_types";
                    $sql = "SELECT id, name FROM `$table1` ORDER BY name ASC;";
                    $res1 = $database->query($sql) or die($database->error);
                    if (!$res1 || !$res1->num_rows){
                        // we dont do anything, options is empty already
                    } else {
                        while ($row1 = $res1->fetch_array()){
                            $id_ = $row1['id'];
                            $name_ = $row1['name'];
                            $selected = $id_ == @ $_POST[$field] ? 'selected' : '';
                            $options .= "<option value='$id_' $selected>$name_</option>";
                        }
                    }

                    $input = "<select class='form-control'  name='$field' id='$field' $fieldrequired >$options</select>";
                    break;
                    
			case 'filename':
			    
			    $val = $value;
				$input = "<input type='file' class='form-control' name='$field' $fieldrequired accept='application/pdf,image/*' id='$field' value='$val'>";
				                
				$preview = "";
				
				if (strlen($val)){
    				$ext = strtolower(get_file_extension($val));
    				$base = basename($val);
    				
    				switch ($ext){
    				    case 'gif':
    				    case 'jpg':
    				    case 'jpeg':
    				    case 'png':
    				        $preview = "<BR>
    				        <a href='$val' class='fancybox'><img class='img-thumbnail' src='$val'></a>";
    				        break;
    				        
    				    case 'pdf':
    				        $preview = "<BR><small>Attachment</small><BR>
    				        <a href='$val' class='fancybox'><span class='fa fa-fw fa-file-o'></span> $base</a>";
    				        break;			
    				        
    				    default:
    				        $preview = "<BR><small>Attachment</small><BR>
    				        <a href='$val' class='fancybox'><span class='fa fa-fw fa-file-o'></span> $base</a>";				        
    				        break;
    				}
				}
				//echo alertbuilder("Here! $val $preview",'warning');
				$input = "$input
				            $preview";
				break;
				
                case 'person_id':
                    $options = "";
                    $table1 = "persons";
                    $sql = "SELECT id, fullname AS name FROM `$table1` ORDER BY name ASC;";
                    $res1 = $database->query($sql) or die($database->error);
                    if (!$res1 || !$res1->num_rows){
                        // we dont do anything, options is empty already
                    } else {
                        while ($row1 = $res1->fetch_array()){
                            $id_      = $row1['id'];
                            $name_    = $row1['name'];
                            $selected = $id_ == @ $_POST[$field] ? 'selected' : '';
                            $options .= "<option value='$id_' $selected>$name_</option>";
                        }
                    }

                    $input = "<select class='form-control'  name='$field' id='$field' $fieldrequired>
                    				 	$options
                    				 </select>";
                    break;
                    
                case 'sex_id':
                   // echo alertbuilder($field . "==". $_POST[$field],'warning');
                    $options = "";
                    $table1 = "list_sex";
                    $sql = "SELECT id, name FROM `$table1` ORDER BY name ASC;";
                    $res1 = $database->query($sql) or die($database->error);
                    if (!$res1 || !$res1->num_rows){
                        // we dont do anything, options is empty already
                    } else {
                        while ($row1 = $res1->fetch_array()){
                            $id_      = $row1['id'];
                            $name_    = $row1['name'];
                            $selected = $id_ == @ $_POST[$field] ? 'selected' : '';
                            $options .= "<option value='$id_' $selected>$name_</option>";
                        }
                    }

                    $input = "<select class='form-control'  name='$field' id='$field' $fieldrequired>
                    				 	$options
                    				 </select>";
                    break;

                case 'town_id':
                    $options = "";
                    $table1 = "list_towns";
                    $sql = "SELECT id, name FROM `$table1` ORDER BY name ASC;";
                    $res1 = $database->query($sql) or die($database->error);
                    if (!$res1 || !$res1->num_rows){
                        // we dont do anything, options is empty already
                    } else {
                        while ($row1 = $res1->fetch_array()){
                            $id_      = $row1['id'];
                            $name_    = $row1['name'];
                            $selected = $id_ == @ $_POST[$field] ? 'selected' : '';
                            $options .= "<option value='$id_' $selected>$name_</option>";
                        }
                    }

                    $input = "<select class='form-control'  name='$field' id='$field' $fieldrequired>
                    			$options
                    		  </select>
                    		  <script>
                    		   $(document).ready(()=>{
                    		    $('#$field').select2();   
                    		   });
                    		  </script>
                    		  ";
                    break;
                    
                case 'notes':
                case 'message':
                    $type = '';
                    $input = "<textarea class='form-control' rows='3' $fieldrequired  name='$field' id='$field'>$value</textarea>";
                    break;

                case 'total_people':
                case 'cost':
                case 'repayment_months':
                        $type = 'number';
                        $input = "<input class='form-control'  type='$type' value=\"$value\" $fieldrequired  name='$field' id='$field'>";
                        break;

                case 'available':
                    $opts = "";
                    $options = [1=>'Yes', 0=>'No'];

                    foreach($options as $key=>$val){
                        
                        $selected = $value == $key ? 'selected' : '';
                        $opts .= "<option value='$key' $selected>$val</option>";
                    }
                    $input = "<select  class='form-control' name='$field' id='$field'>
                                $opts
                              </select>";
                    break;

                case 'picture':
                        $type = 'file';
                        $picture = "";
                        $pic = @ $_POST[$field];
                        if (file_exists($pic)) $picture = "<p><img src='$pic'></p>";
                        $input = "<input class='form-control'  type='$type' $fieldrequired  name='$field' id='$field'> $picture";
                        break;

                case 'payment_method_id':
                    $options = "";
                    $table1 = "list_payment_methods";
                    $sql = "SELECT id, name 
                            FROM `$table1` 
                            ORDER BY name ASC;";
                    $res1 = $database->query($sql) or die($database->error);
                    if (!$res1 || !$res1->num_rows){
                        // we dont do anything, options is empty already
                    } else {
                        while ($row1 = $res1->fetch_array()){
                            $id_ = $row1['id'];
                            $name = $row1['name'];
                            $selected = $id_ == @ $_POST[$field] ? 'selected' : '';
                            $options .= "<option value='$id_' $selected>$name</option>";
                        }
                    }

                    $input = "<select  class='form-control' name='$field' id='$field' $fieldrequired >$options</select>";
                    break;
                    
                case 'interest_id':
                    $options = "";
                    $table1 = "list_interests";
                    $sql = "SELECT id, days, interest 
                            FROM `$table1` 
                            ORDER BY days ASC;";
                    $res1 = $database->query($sql) or die($database->error);
                    if (!$res1 || !$res1->num_rows){
                        // we dont do anything, options is empty already
                    } else {
                        while ($row1 = $res1->fetch_array()){
                            $id_ = $row1['id'];
                            $days = $row1['days'];
                            $interest = $row1['interest'];
                            $selected = $id_ == @ $_POST[$field] ? 'selected' : '';
                            $options .= "<option value='$id_' $selected>$days Days ($interest%)</option>";
                        }
                    }

                    $input = "<select  class='form-control' name='$field' id='$field' $fieldrequired >$options</select>";
                    break;
                    
                case 'loan_status_id':
                    $options = "";
                    $table1 = "list_loan_status";
                    $sql = "SELECT id, name FROM `$table1` ORDER BY name ASC;";
                    $res1 = $database->query($sql) or die($database->error);
                    if (!$res1 || !$res1->num_rows){
                        // we dont do anything, options is empty already
                    } else {
                        while ($row1 = $res1->fetch_array()){
                            $id_ = $row1['id'];
                            $name_ = $row1['name'];
                            $selected = $id_ == @ $_POST[$field] ? 'selected' : '';
                            $options .= "<option value='$id_' $selected>$name_</option>";
                        }
                    }

                    $input = "<select  class='form-control' name='$field' id='$field' $fieldrequired >$options</select>";
                    break;

                case 'collect_debt':
                        $list = [0=>'No', 1=>'Yes'];
                        $options = "";
                        foreach($list as $key=>$val){
                            $selected = $key == @ $_POST[$field] ? 'selected' : '';
                            $options .= "<option value='$key' $selected>$val</option>";
                        }
                        
                        $input = "<select  class='form-control' name='$field' id='$field' $fieldrequired >$options</select>";
                        break;
                        
                case 'password':
                    $type='password';
                    // we never set the password value
                    //value=\"$value\" 
                    $input = "<input class='form-control'  type='$type' $fieldrequired  name='$field' id='$field'>";
                    break;

                default:
                    $type = 'text'; // default type for this fieldis text
                    $input = "<input class='form-control' type='$type' value=\"$value\" $fieldrequired  name='$field' id='$field'>";
            }

            $fields .= "<div class='form-group'>
                            <label class='control-label col-sm-3' for='$field'>$field_ $requiredstar</label>
                            <div class='col-sm-8'>$input</div>
                        </div>";
        }

        return $fields;
    }
    
    function in_array_multidim($item , $array){
    	foreach($array as $key=>$things){
    		if ($key == $item) return true;
    	}
	 }

    /**
     * This function generates an HTML table with data from the table specified & options
     * Parameters:
     *  table: name of the table 
     */
    function build_table($table, $ignored=[], $options=[], $limit = "", $dynamic_sql = "", $styling = [], $addtoarrayfield = []){
        global $database;

        $currency = 'N$';//settings::currency;
        $columns = [];
        $cols = "";
        $data = "";
        $buffer = [];
                    
        // get the fields & comment for each field
        if (strlen($dynamic_sql))
            $sql = $dynamic_sql;
        else
            $sql = "show full columns from `$table`;";

        $res = $database->query($sql) or die("<h4>SQL</h5><pre>$sql</pre> <h5>Error</h5>{$database->error}");
        if (!$res || !$res->num_rows){
            //
        } else {
            if (strlen($dynamic_sql))
               $sql = $dynamic_sql;
            else
               $sql = "SELECT * FROM `$table` $limit ORDER BY id DESC;";

            //echo alertbuilder($sql, 'info');

            $res1 = $database->query($sql) or die("<h4>SQL</h5><pre>$sql</pre> <h5>Error</h5>{$database->error}");

            if (strlen($dynamic_sql)){
                $maxfields = mysqli_num_fields($res);

                $cols_ = mysqli_fetch_array($res, MYSQLI_ASSOC);
                foreach($cols_ as $col_=>$data){
                    if (in_array($col_, $ignored)) continue;
                    
                    $col__ = ucwords($col_);
                    $cols .= "<th>$col__</th>";
                    $columns[] = $col_;                    
                }
                $cols .= "<th>Options</th>"; 

            } else {

                while ($fields = $res->fetch_array()){
                    $col_ = $fields['Field'];
                    $comment = @ $fields['Comment'];
    
                    if (in_array($col_, $ignored)) continue;
    
                    $col = strlen($comment) ? ucwords($comment) : ucwords($col_);
                    $cols .= "<th>$col</th>";
                    $columns[] = $col_;
                }
                $cols .= "<th>Options</th>";                

            }
            
            $data   = "";
            
            while ($row = $res1->fetch_array()){
                $data .= "<tr>"; // start of table body row
                
	                $id = $row['id'];
	
	                    if (sizeof($addtoarrayfield)){
	                        
	                        foreach($addtoarrayfield as $colname) {
	                            if (!is_array(@$buffer[$id])){
	                               $buffer[$id] = [];
	                            }
	                            
	                            
	                            if (in_array($colname, $columns)){
	                                   
	                                $buffer[$id][$colname] = $row[$colname];
	                                //echo "[$id][$colname]: {$buffer[$id][$colname]} <BR>"; 
	                            }
	                        }
	                    }
	                    
	                // here is where we are showing the data
	                foreach($columns as $col){
	                    $value = $row[$col];
	                    

	                    
	                    if (in_array($col, $ignored)) continue;
	                          
							  // styling
							  $style = "";

							  if ( in_array_multidim($col, $styling)){
							  	
							  		foreach($styling[$col] as $op=>$opts){
										$val  = @ $opts['value'];
										$val = trim($val);
										
										$css  = @ $opts['css'];		
										$url  = @ $opts['url'];
										$fld  = @ $opts['field'];
										$icon = @ $opts['icon'];
										$pre  = @ $opts['pre'];
										$post = @ $opts['post'];
										$title= @ $opts['title'];				$title= trim($title);						    
										$fielddata = "";
								  		switch ($op){
								  			case 'lt':  if ($value < $val)   $style = $css; break;
											case 'lte': if ($value <= $val)  $style = $css; break;
											case 'gt':  if ($value > $val)   $style = $css; break;
											case 'gte': if ($value >= $val)  $style = $css; break;
											case 'eq':  if ($value == $val)  $style = $css; break;
											case 'neq':	if ($value !== $val) $style = $css; break;	
											case 'url':
												
												if (strlen($fld)) {
													$fv = $row[$fld];
													$fielddata = "&$fld=$fv";
												}	
												if(strlen($title)){
												   $title="title=\"$title\"";
												}
												
												if (strlen($val)){
													$value = "<a href='$val&id=$id$fielddata' $title>{$pre}{$icon}$value{$post}</a>";
												} else {
													$value = "{$pre}{$icon}$value{$post}";
												}
												 										
												break;		  			
								  		}	
								  				  			
							  		}
							  }
							  
	                    switch ($col){
	                        case 'entrydate':
	                        	 //$value = "<abbr class='timeago' title='$value'>$value</abbr>";
	                            $data .= "<td>$value</td>";
	                            break;

									/*	                            
	                        case 'user_id':
	                            $username = "Unknown";
	
	                            $sql0 = "SELECT username 
	                            			 FROM `users` 
	                            			 WHERE id=$value LIMIT 1;";
	                            $res0 = $database->query($sql0);
	                            if (!$res0 || !$res0->num_rows){
	                                // no user was found matching id=$value
	                            } else {
	                                $username = $res0->fetch_array()['username'];
	                            }
	
	                            $data .= "<td style='$style'>$username</td>";
	                            break;
	
	                        case 'sex_id':
	                             $name = "Unknown";
	 
	                             $sql0 = "SELECT name FROM `list_sex` WHERE id=$value LIMIT 1;";
	                             $res0 = $database->query($sql0);
	                             if (!$res0 || !$res0->num_rows){
	                                 // no user was found matching id=$value
	                             } else {
	                                 $name = $res0->fetch_array()['name'];
	                             }
	 
	                             $data .= "<td style='$style'>$name</td>";
	                             break;
										*/
							
							case 'filename':			
	                        case 'picture':
	                            
                				$preview = "";
                				$val = $value;
                				
                				if (strlen($val)){
                    				$ext = strtolower(get_file_extension($val));
                    				$base = basename($val);
                    				
                    				switch ($ext){
                    				    case 'gif':
                    				    case 'jpg':
                    				    case 'jpeg':
                    				    case 'png':
                    				        $preview = "<a href='$val' rel='attachments' class='fancybox'><img class='img-thumbnail' style='width:150px' src='$val'></a>";
                    				        break;
                    				        
                    				    case 'pdf':
                    				        $preview = "<small>Attachment</small><BR>
                    				        <a href='$val' class='fancybox' data-fancybox-type='iframe'><span class='fa fa-fw fa-file-o'></span> $base</a>";
                    				        break;			
                    				        
                    				    default:
                    				        $preview = "<small>Attachment</small><BR>
                    				        <a href='$val' class='fancybox'><span class='fa fa-fw fa-file-o'></span> $base</a>";				        
                    				        break;
                    				}
                				}
                				
	                            $data .= "<td>$preview</td>";
	                            break;

	                        case 'temperature':
	                            $data .= "<td style='$style'>$value &deg;C</td>";
	                            break;
	                            	
	                        case 'cost':
	                            $data .= "<td style='$style'>$currency $value</td>";
	                            break;
	
	                        case 'available':
	                            $value = $value == 1 ? 'Yes' : 'No';
	                            $data .= "<td style='$style'>$value</td>";
	                            break;
	
	                        default:
	                            $data .= "<td style='$style'>$value</td>";
	                    }
	                    
	                }
	
	                $opts = "";
	                foreach($options as $title=>$link){
	                    $opts .= "<a href=\"?view=$link&id=$id\" class='btn btn-sm btn-primary'>$title</a>&nbsp;";
	                }
	                $data .= "<td>$opts</td>";
	                
                $data .= "</tr>"; // end of table row body
            }
            
        }

        $arr = ['columns'=>$cols, 'data'=>$data, "buffer"=>$buffer];
                
        return $arr;
    }
    
    /*
     * this function returns the extension of a file
     * parameters:
     *    $filename (string)  - name of the file
     * returns:
     *    a string e.g. jpg when filename is test.jpg and 
     *                  png when filename is test.png
     *    the dot is not included in the return value  
     */
    function getfileextension($filename){
        return pathinfo($filename, PATHINFO_EXTENSION);
    }    
    
    /*
     * this function generates a string with random characters
     * parameters:
     *   $maxlength (number, optional) - the total length of characters in the return value
     * returns:
     *   a string of characters
     */
    function randomString($maxlength=10){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $lenchars   = strlen($chars);
        $temp = "";

        for($idx = 0; $idx < $maxlength; $idx++){
            $randomInt = mt_rand(); // 1,0,2
            $temp .= $chars[ $randomInt % $lenchars];
        }

        return $temp;
    }    
?>