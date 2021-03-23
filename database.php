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

	// declare icons
	$fa_news     = font_awesome('fa-newspaper-o');
	$fa_edit     = font_awesome('fa-edit');
	$fa_download = font_awesome('fa-download');
	$fa_cog      = font_awesome('fa-cog');
	
	require_once('settings.php');
	require_once('user_rights.php');

	$database = new mysqli(settings::db_host,settings::db_user,settings::db_pwd);
	if ($database->connect_errno) throw new Exception("Fatal error: Failed to create a connection to the database.");

	// create db and tables
	$database->query(settings::sql_table_db) or die('sql_table_db: '   . $database->error);	
	$database->query(settings::sql_select_db) or die('sql_select_db: ' . $database->error);	
	$database->query(settings::sql_table_db) or die('sql_table_db: '   . $database->error);	
		
	$db = array();
	
	$db['host'] = settings::db_host;
	$db['db']   = settings::db_db;
	$db['user'] = settings::db_user;
	$db['pwd']  = settings::db_pwd;
	
	

	//$cms = new youthportal_cms($db);
	
	/*
	 * global introJs counter
	 */
	$introJsIndex = 1;
	 
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

	function loggedin(){
	    return @ $_SESSION['user_id'] >0 ? true : false;
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
 * returns an array of noticeboard items
 *
 * id, userid, entrydate, heading, author, body, image
 */
function get_notices($limit = 10){
	global $database;
	global $userid;
	global $role;
	
	$limit = (int) $limit;
	if (!$limit) $limit = 10;
	if ($limit)
		$limit = " LIMIT $limit";
	
	$disabled = $role == 'administrators' ? "" : "AND enabled=1";
	$sql = "SELECT 
	        u.id as user_id, 
			u.user_name, 
			n.id AS noticeboardid, 
			n.heading, n.image, n.body, n.entrydate, n.enabled
			FROM user_noticeboard n, users u 
			WHERE n.userid = u.id $disabled
			ORDER BY n.entrydate DESC $limit;";

	$ret = $database->query($sql);
	if (!$ret || !$ret->num_rows)
		return false;

	$notices = [];
	$arr = [];
	$i = 0;
	while ($row = $ret->fetch_array()) {
		$i++;
		
		$arr['id'] = $row['noticeboardid'];
		$arr['user_id'] = $row['user_id'];
		$arr['entrydate'] = $row['entrydate'];
		$arr['heading'] = $row['heading'];
		$arr['author'] = $row['user_name'];
		$arr['body'] = $row['body'];
		$arr['image'] = $row['image'];
		$arr['enabled'] = $row['enabled'];
		
		// array_push
		$notices[] = $arr;
	}

	return $notices;
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
	
	$right_exists = false;
	
	// verify that this user has the right to manage this right
	foreach($myrights as $key=>$menu){
		foreach ($menu as $id=>$val)
			if (is_array($val)){
				foreach ($val as $k=>$data){
					if (strtolower($data['url']) == strtolower($view)){
						$right_exists = true;
						break;
					}
				}
			}
	}
	
	return $right_exists;
}

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
 function subquery($sql){
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
 function alertbuilder($alert,$type="success"){
 	return "<p class='alert alert-$type'>$alert</p>";
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
 
 /*
  * Give me a table, I will return an input form preselected
  */
 function build_form($table,$required) {
 	global $database;
 	
	$inputs = "";
	$sql = "SHOW FULL COLUMNS
			  FROM `$table`;";		  
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
         
			$field_ = str_replace("_", " ", $field);
			$field_ = $comment ? $comment : ucfirst($field);
			$val    = @ $_POST[$field];
			
			$star_required = in_array(strtolower($field), $required) ? "<span style='color:red'>*</span>" : "";
			
			// we ignore some fields
			switch (strtolower($field)){
				case 'entrydate':
				case 'user_id':
				case 'id':
				case 'youth_id':
				case 'locked_user_id':
		 		case "youthid":
		 		case "table":
					break;
										
				case 'file_type_id':
					$sql = "SELECT * 
					        FROM `file_types`
					        ;";
					$select = build_selectbox($sql,$field,$val);
					
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-3' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-8'>
										$select
		    						 </div>
		  						  </div>
						  <script>
						   $(document).ready(function(){
							    $('#$field').select2();
						   });
						  </script>";
					break;
					
				case 'date_started':
				case 'date_ended':
				case 'birth_date':
					$input  = "<input type='text' class='form-control' value='$val' 
											id='$field' name='$field' 
											placeholder='YYYY-mm-dd'>";
					$inputs .=  "<div class='form-group'>
							<label class='control-label col-sm-3' for='$field'>$field_ $star_required</label>
							<div class='col-sm-8'>$input</div>
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
					
				case 'application_region_id':	
				case 'region_id':
					$sql = "SELECT * 
					        FROM `list_regions`
					        ;";
					$select = build_selectbox($sql,$field,$val);
					
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-3' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-8'>
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
					$sql = "SELECT * 
					        FROM `list_sex`
					        ;";
					$select = build_selectbox($sql,$field,$val);
					
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-3' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-8'>
										$select
		    						 </div>
		  						  </div>
									  <script>
									   $(document).ready(function(){
										    $('#$field').select2();
									   });
									  </script>";
					break;
					
				case 'children':
					$input = "<input type='number' class='form-control' name='$field' 
					                 id='$field' value='$val'>";
					                
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-3' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-8'>
										$input
		    						 </div>
		  						  </div>";
					break;
		
				case 'cellphone':
					$input = "<input type='number' placeholder='0812223333' class='form-control' name='$field' 
					                 id='$field' value='$val'>";
					                
				          $input = "<div class='input-group'>
							            <span class='input-group-addon'><span class='fa fa-mobile'></span></span>
							            <input type='number' placeholder='0812223333' class='form-control' 
							                                 name='$field' id='$field' value='$val'>
							          </div>";
          					                
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-3' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-8'>
										$input
		    						 </div>
		  						  </div>";
					break;
								
				case 'fax':
		          $input = "<div class='input-group'>
					            <span class='input-group-addon'><span class='fa fa-fax'></span></span>
					            <input type='text' placeholder='' class='form-control' 
					                                 name='$field' id='$field' value='$val'>
					          </div>";
		          					                
				$inputs .= "<div class='form-group'>
	    						 <label class='control-label col-sm-3' for='$field'>$field_ $star_required</label>
	    						 <div class='col-sm-8'>
									$input
	    						 </div>
	  						  </div>";
				break;
											
				case 'address':
		          $input = "<div class='input-group'>
					            <span class='input-group-addon'><span class='fa fa-map-marker'></span></span>
					            <input type='text' placeholder='' class='form-control' 
					                                 name='$field' id='$field' value='$val'>
					          </div>";
		          					                
				$inputs .= "<div class='form-group'>
	    						 <label class='control-label col-sm-3' for='$field'>$field_ $star_required</label>
	    						 <div class='col-sm-8'>
									$input
	    						 </div>
	  						  </div>";
				break;
																						
				case 'telephone':
		          $input = "<div class='input-group'>
					            <span class='input-group-addon'><span class='fa fa-phone'></span></span>
					            <input type='text' placeholder='' class='form-control' 
					                                 name='$field' id='$field' value='$val'>
					          </div>";
		          					                
				$inputs .= "<div class='form-group'>
	    						 <label class='control-label col-sm-3' for='$field'>$field_ $star_required</label>
	    						 <div class='col-sm-8'>
									$input
	    						 </div>
	  						  </div>";
				break;
																	
				case 'email':
		          $input = "<div class='input-group'>
					            <span class='input-group-addon'><span class='fa fa-envelope-o'></span></span>
					            <input type='email' placeholder='name@mail.com' class='form-control' 
					                                 name='$field' id='$field' value='$val'>
					          </div>";
		          					                
				$inputs .= "<div class='form-group'>
	    						 <label class='control-label col-sm-3' for='$field'>$field_ $star_required</label>
	    						 <div class='col-sm-8'>
									$input
	    						 </div>
	  						  </div>";
				break;
					
				case 'filename':
				case 'project_proposal':				
					$input = "<input type='file' class='form-control' name='$field' 
					                 id='$field' value='$val'>";
					                
					$inputs .= "<div class='form-group'>
		    						 <label class='control-label col-sm-3' for='$field'>$field_ $star_required</label>
		    						 <div class='col-sm-8'>
										$input
		    						 </div>
		  						  </div>";
					break;
					
				default:							
				$tagsinput = "";
				$type = 'text';
				
				switch($col_type) {
					case 'tinyint(1)':
						$type = 'checkbox';
						$input = "<input type='$type' class='form-control' 
												$tagsinput value='$val' 
						                 id='$field' name='$field'>";											
						break;
						
					case 'longtext':
						$type = 'textarea';
						$input = "<textarea id='$field' name='$field' class='form-control' 
												$tagsinput>$val</textarea>";												
						break;	
						
					default:
						$input = "<input type='$type' class='form-control' 
												$tagsinput value='$val' 
						                 id='$field' name='$field'>";	
						break;										
				}
				
				$inputs .= "<div class='form-group'>
								<label class='control-label col-sm-3' for='$field'>$field_ $star_required</label>
								<div class='col-sm-8'>$input</div>
							</div>";
				break;
				}
		}

		return $inputs;	
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
?>