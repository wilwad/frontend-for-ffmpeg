<?php 
 /*
  * Handles the user object
  *
  * Copyright  William Sengdara (c) 2015
  *
  * Created:
  * Updated:
  */

/* database */
require_once('database.php');

date_default_timezone_set('Africa/Windhoek');

$users = new users();

/*
 * user object
 */
class user {
	public  $id = null;
	private $props = null;
	
	function __construct($id, $props) {
		global $database;
		
		$this->props = $props;
		$this->id = $props['id'];	
	}
	
	function get($prop) {
		global $database;
		
		switch ($prop) {
			case 'id':
				return $this->id;
				break;
				
			case 'roleid':
			case 'rolename':
			    $sql = "SELECT *, r.name AS rolename
						FROM users u
					    INNER JOIN user_roles r
						ON u.roleid = r.id AND u.id={$this->id};";
				$ret = $database->query($sql);
				if (!$ret || !$ret->num_rows){
					// write the system log
					$action = "FATAL_ERROR";
					$description = "Unexpected zero while querying $prop. SQL: $sql";
					update_system_log($action, $description);

					throw new Exception("Fatal Error: Unable to retrieve role name for role id");	
				}

				$row = $ret->fetch_array();
				return $row[$prop];
				break;
				
			case 'user_name':
			case 'profilepic':
			     $sql = "SELECT $prop 
			             FROM users 
			             WHERE id={$this->id};";
				 $ret = $database->query($sql);
				 if (!$ret || !$ret->num_rows)
					 return "";
				 
				 $row = $ret->fetch_array();
				 return $row[$prop];
				 break;
						
			case 'fname':
			case 'sname':
			case 'title':
			case 'initials':
			case 'dob':
			case 'address':
			case 'contactno':
			case 'email':
			case 'cellphone':			
			     $sql = "SELECT $prop 
			             FROM user_profiles 
			             WHERE id={$this->id};";
				 $ret = $database->query($sql);
				 if (!$ret || !$ret->num_rows)
					 return "";
				 
				 $row = $ret->fetch_array();
				 return $row[0];
				 break;
				 
			default:

				// write the system log
				$action = "FATAL_ERROR";
				$description = "Unhandled case when checking $prop.";
				update_system_log($action, $description);

				throw new Exception("unhandled get()::$prop");
				break;
		}
	}
	
	function set($prop,$val) {
		$database->update($this->group,[$prop=>$val]);
	}
}

/*
 * user authentication
 */
class users {
	function login($username, $password) {
		global $database;

		// sanitize
		$username = str_sanitize($username);
		$password = str_sanitize($password);
		
		$password = MD5($password);
		
		$sql = "SELECT *, u.id AS userid 
				FROM users u, user_roles ur
                WHERE u.user_name='$username' AND u.user_password='$password' AND 
                u.isactive=1 AND u.roleid = ur.id AND ur.isactive=1;";
		$result = $database->query($sql);
		if (!$result || !$result->num_rows) {
			// log this query?
			//update_system_log("LOGIN_TEXT",$sql);
			return false;
		}
		
		$row = $result->fetch_array();
		
		$userid    = (int) $row['userid']; 
		$logintime = date("Y-m-d H:i:s"); 

		$key_userid = settings::session_userid;
		$key_logintime = settings::session_logintime;
		
		$_SESSION[$key_userid]   = $userid;
		$_SESSION[$key_logintime]=$logintime;
		$sessionid = session_id();
		
		// save the session id
		$sql = "UPDATE users 
		        SET sessionid='$sessionid' 
				WHERE id=$userid;";
		$database->query($sql);
		
		$user = new user($userid,$row);
		return $user;
	}
	
	// returns true if user is logged in
	function loggedin() {
		global $database;
		
		$key_userid = settings::session_userid;
		$key_logintime = settings::session_logintime;
		
		$userid    = @ $_SESSION[$key_userid];
		$logintime = @ $_SESSION[$key_logintime];
		
		if (!$userid || !$logintime)		
			return false;
			
		// ensure this user exists!
		$sql = "SELECT * 
				FROM users 
				WHERE id=$userid;";
		$ret = $database->query($sql);
		if (!$ret || !$ret->num_rows)
			return false;
		
		// ensure this user is active!
		$row = $ret->fetch_array();
		if (!$row['isactive'])
			return false;
		
		// ensure the session_id is same !
		if ($row['sessionid'] != session_id())
			return false;
		
		return ['userid'=>$userid, 
		         0=>$userid,
		        'logintime'=>$logintime,
				1=>$logintime
				];
	}
	
	// returns a user object
	function user($id) {
		global $database;
		
		$sql = "SELECT * 
		        FROM users 
				WHERE id=$id;";
		$result = $database->query($sql);
		if (!$result || !$result->num_rows)
			return false;
		
		$row = $result->fetch_array();
		
		$user = new user($id, $row);
		return $user;
	}
}

 /*
  * Reusable calls
  */
 function view($view) {
	 switch ($view) {
		case 'dialog-login':
			  $login_title = settings::title;
			  $warning_logout = settings::warning_logout;
			  $version = settings::version;
			  $logo    = settings::logo;
			  $fa_home = font_awesome('fa-home');
			  
			  return " <!-- start -->
			  <style>
					/*
					 * Specific styles of signin component
					 */
					 
					/*
					 * General styles
					 */
					 body {
						/*margin-top: 30px !important;*/
						background-attachment: fixed;
					}
					
					#container {
						background: transparent !important;
					    -webkit-box-shadow: none;
					    -moz-box-shadow: none;
					    box-shadow: none;	
					}
						
					.card-container.card {
					    max-width: 350px;
					    /*padding: 40px 40px;*/
					}
					
					.btn {
					    font-weight: 700;
					    height: 35px;
					    -moz-user-select: none;
					    -webkit-user-select: none;
					    user-select: none;
					    cursor: default;
					}
					
					/*
					 * Card component
					 */
					.card {
					    background-color: #FFFFFF;
					    /* just in case there no content*/
					    padding: 20px 25px 30px;
					    margin: 0 auto 25px;
					    /*margin-top: 50px;*/
					    /* shadows and rounded borders */
					    -moz-border-radius: 2px;
					    -webkit-border-radius: 2px;
					    border-radius: 2px;
					    -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
					    -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
					    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
					}
					
					.profile-img-card {
					    /*
					    width: 96px;
					    height: 96px;
					    */
					    margin: 0 auto;
					    display: block;
					    /*
					    -moz-border-radius: 50%;
					    -webkit-border-radius: 50%;
					    border-radius: 50%;
					    */
					}
					
					/*
					 * Form styles
					 */
					.profile-name-card {
					    font-size: 16px;
					    font-weight: bold;
					    text-align: center;
					    margin: 10px 0 0;
					    min-height: 1em;
					}
					
					.reauth-email {
					    display: block;
					    color: #404040;
					    line-height: 2;
					    margin-bottom: 10px;
					    font-size: 14px;
					    text-align: center;
					    overflow: hidden;
					    text-overflow: ellipsis;
					    white-space: nowrap;
					    -moz-box-sizing: border-box;
					    -webkit-box-sizing: border-box;
					    box-sizing: border-box;
					}
					
					.form-signin #inputEmail,
					.form-signin #inputPassword {
					    direction: ltr;
					    height: 44px;
					    font-size: 16px;
					}
					
					.form-signin input[type=email],
					.form-signin input[type=password],
					.form-signin input[type=text],
					.form-signin button {
					    width: 100%;
					    display: block;
					    margin-bottom: 10px;
					    z-index: 1;
					    position: relative;
					    -moz-box-sizing: border-box;
					    -webkit-box-sizing: border-box;
					    box-sizing: border-box;
					}
					
					.form-signin .form-control:focus {
					    border-color: rgb(104, 145, 162);
					    outline: 0;
					    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgb(104, 145, 162);
					    box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgb(104, 145, 162);
					}
					
					.btn.btn-signin {
					    /*background-color: #4d90fe; */
					    background-color: rgb(104, 145, 162);
					    /* background-color: linear-gradient(rgb(104, 145, 162), rgb(12, 97, 33));*/
					    padding: 0px;
					    font-weight: 700;
					    font-size: 14px;
					    height: 35px;
					    -moz-border-radius: 3px;
					    -webkit-border-radius: 3px;
					    border-radius: 3px;
					    border: none;
					    -o-transition: all 0.218s;
					    -moz-transition: all 0.218s;
					    -webkit-transition: all 0.218s;
					    transition: all 0.218s;
						text-align: center;
					}
					
					.btn.btn-signin:hover,
					.btn.btn-signin:active,
					.btn.btn-signin:focus {
					    background-color: rgb(12, 97, 33);
					}
					
					.forgot-password {
					    color: rgb(104, 145, 162);
					    padding: 0px;
					    font-weight: 700;
					    font-size: 14px;
					    height: 35px;
					    -moz-border-radius: 3px;
					    -webkit-border-radius: 3px;
					    border-radius: 3px;
					    border: none;
					    -o-transition: all 0.218s;
					    -moz-transition: all 0.218s;
					    -webkit-transition: all 0.218s;
					    transition: all 0.218s;
						text-align: center;
					}
					
			  </style>
        <div class='card card-container' id='login-dialog'>
            <img src='$logo' class='img-responsive center-block'>
            <HR>

			<form  class='form-signin' onsubmit='return false;'>
                <span id='reauth-username' class='reauth-username'></span>
                <input type='text'     id='user_name' name='user_name' class='form-control' value='' title='Please enter you username' placeholder='Enter your username' required autofocus>
                <input type='password' id='user_password' name='user_password' class='form-control' value='' title='Please enter your password' placeholder='Enter your password' required autofocus>
                
                <div id='loginMsg'></div>

                <button  id='btn-login' class='btn btn-lg btn-primary btn-signin'>Login</button>
                <small class='pull-right'>
                    <a href='../?view=forgot-password'>Forgot Password</a> |
                    <a href='../'>$fa_home Website</a>
                </small>
            </form>
        </div><!-- /card-container -->
					  
			<script>
			  /*
			   * when the document is loaded and we have a login dialog,
			   * set focus on username textbox and listen for keypress
			   * ENTER cos this is not a form so we don't get default action
			   */							   
			  $(document).ready(function(){
				  // setfocus to username textbox
				  window.setTimeout(function(){
					if ($('#login-dialog').length) {
						console.log('ses: login-dialog exists, select username field');
						$('#user_name').focus();
					}
				  },1000);	

				  console.log('listening for keypress ENTER');
				  $(document).on('keypress',function(event){
					  if (event.which == 13) {
						  $('#btn-login').trigger('click');
					  }
				  });
			  });
			</script>";		
			break;
			
	 }
	 
 }

  // All method=POST are consumed here so remember to exclude your OP from this filter
  if (isset($_POST) && 
      !empty($_POST) && 
      @ $_POST['action'] != 'add' &&  
      @ $_POST['action'] != 'edit' &&
      @ $_POST['action'] != 'register' &&
      @ $_POST['action'] != 'unsubscribe' &&
      @ $_POST['action'] != 'product-add' &&
      @ $_POST['action'] != 'view')
  {
	  $view = @ $_POST['view'];
	  $view = strtolower($view);
	  
	  switch ($view) {
		  case 'generic-add':
		  		$fields = array();
		  		$data   = array();
		  		
		  		$table     = @ $_POST['table'];
				$returnurl = @ $_POST['returnurl'];
				
				foreach($_POST as $key=>$val){
					switch ($key){
						case 'view':
						case 'extra':
						case 'table':
						case 'returnurl':
							break;
							
						default:					
							$fields[] = $key;
							$data[] = $val;								
							break;
					}
				}
				
				$fields = implode(",", $fields);
				$data = implode("','", $data);
				
				$sql = "INSERT INTO `$table`($fields)VALUES('$data');";
				
				// run the query
				$ret = $database->query($sql) or die("<p class='alert alert-warning'>{$database->error}</p>");
				
				echo "<p class='alert alert-success'>Record successfully added to application.</p>
				      <script>
					    window.location.href='$returnurl';
					   </script>";
				break;
				  	
		  case 'generic-add-companies':
		  		$fields = array();
		  		$data   = array();
		  		
		  		$table     = @ $_POST['table'];
				$returnurl = @ $_POST['returnurl'];
				
				foreach($_POST as $key=>$val){
					switch ($key){
						case 'view':
						case 'extra':
						case 'table':
						case 'returnurl':
						case 'user_id':
							break;
							
						default:					
							$fields[] = $key;
							$data[] = $val;								
							break;
					}
				}
				
				$fields = implode(",", $fields);
				$data = implode("','", $data);
				
				$sql = "INSERT INTO `$table`($fields)VALUES('$data');";
				
				// run the query
				$ret = $database->query($sql) or die("<p class='alert alert-warning'>{$database->error}</p>");
				$newid = $database->insert_id;
				$returnurl = "?view=list-companies&action=summary&id=$newid";
				
				echo "<p class='alert alert-success'>Record successfully added to application.</p>
				      <script>
					    window.location.href='$returnurl';
					   </script>";
				break;
								  	
		  case 'generic-edit':
		  		$recordid    = (int) @ $_POST['recordid'];
		  		$fields_data = "";		  		
		  		$table     	 = @ $_POST['table'];
				$returnurl 	 = @ $_POST['returnurl'];
				
				foreach($_POST as $key=>$val){
					switch ($key){
						case 'view':
						case 'extra':
						case 'table':
						case 'returnurl':
						case 'user_id':
						case 'refugee_id':
						case 'recordid':
							break;
							
						default:					
							$val = strip_tags($val);
							$fields_data .= "$key='$val',";						
							break;
					}
				}
				
				$fields_data = substr($fields_data, 0, strlen($fields_data)-1);
				$sql = "UPDATE `$table` 
						  SET $fields_data
						  WHERE id=$recordid;";

				// run the query
				$ret = $database->query($sql) or die("<p class='alert alert-warning'>{$database->error}</p>");
				
				echo "<p class='alert alert-success'>Record successfully updated.</p>
				      <script>
					    window.location.href='$returnurl';
					   </script>";		  
		  		break;
		  		
		  case 'upload':
		       $table_     = @ $_POST['table'];
			    $refugeeid_    = (int) @ $_POST['refugeeid'];
			    $title       = @ $_POST['title'];
			    $userid_     = (int) @ $_POST['userid'];
			    $filetypeid_ = (int) @ $_POST['file_type_id'];
				 $returnurl   = @ $_POST['returnurl'];

					/*
					 * FILE uploads
					 */
					 $buploadedfile = false;
					 
					 $table_ = strip_tags($table_);
					 $title = strip_tags($title);
					 
					 if ($_FILES['filename']['size'] != 0)
					 {
						$target_dir = "uploads/";
						if (!is_dir($target_dir))
							mkdir($target_dir);

						// generate random filename & set extension							
						$file          = basename($_FILES["filename"]["name"]);
						$fileextension = get_file_extension($file);
						$file          = randomPassword();
						$file          = "$file.$fileextension";
						$target_file   = $target_dir . $file;
						$uploadOk      = 1;

						if (@ move_uploaded_file($_FILES["filename"]["tmp_name"], $target_file)) {
							//echo "The file ". basename( $_FILES["profilepic"]["name"]). " has been uploaded.";
						} 
						else 
						{
						    // Check $_FILES['upfile']['error'] value.
						    switch ($_FILES['filename']['error']) {
						        case UPLOAD_ERR_OK:
						            break;
						        case UPLOAD_ERR_NO_FILE:
						            throw new RuntimeException('No file sent.');
						        case UPLOAD_ERR_INI_SIZE:
						        case UPLOAD_ERR_FORM_SIZE:
						            throw new RuntimeException('Exceeded filesize limit.');
						        default:
						            throw new RuntimeException('Unknown errors.');
						    }
						}
					
						$buploadedfile = true;

						$sql = "INSERT INTO 
												`{$table_}`
												(entrydate,refugee_id,user_id,file_type_id, title, filename)
								  VALUES(
											NOW(),$refugeeid_,$userid_,$filetypeid_,'$title', '$target_file');";

						$ret = $database->query($sql);
						if (!$ret)
							die($database->error);
						else
						{							
							$type = "$filetypeid_";
							$sql  = "SELECT 
													name 
										FROM 
													file_types 
										WHERE 
													id=$filetypeid_
										LIMIT 1;";
													
							$ret  = $database->query($sql);
							if (!$ret || !$ret->num_rows)
							{}
							else
							{
								$row = $ret->fetch_array();
								$type = $row['name'];
							}

							// don't choke on apostrophe
							$type = addslashes($type);
							 
							// write to log
							/*
							$table_ = str_replace("documents", "log", $table_);
							$sql = "INSERT INTO `$table_`
									  (entrydate, refugee_id, user_id, action, description)
										VALUES(NOW(), $refugeeid, $userid_, 'Document upload', 'A document was uploaded ($type)');";
							$database->query($sql);
							*/
							
							echo "<span style='color:green'>Document was uploaded and saved successfully to application.</span>
							      <script>
							       //alert('$returnurl');
								    window.location.href='$returnurl';
								   </script>";
						}
					 }
				//}		
				/*
				else
				{
					echo "<h3>There is already a document of that type for this application uploaded.</h3>";
				}
				*/
			break;
				  	
		  case 'upload-refugee-profilepic':
		  	// normal upload
	 		// required fields

			$required = array('refugeeid','user_name', /*'profilepic',*/ 'returnurl');



				foreach($required as $field){

						$_POST[$field] = addslashes(trim(@ $_POST[$field]));


						if ($_POST[$field] == ""){
							//$errors = missing_parameter($field);

							die("Some fields have not been field in: $field");//$errors);
						}

				}	
				

			   $refugeeid    = (int) @ $_POST['refugeeid'];

			   $username   = @ $_POST['user_name'];
			   $password   = @ $_POST['user_password'];
			   $returnurl  = @ $_POST['returnurl'];
			   
			   // verify the refugee exists before we upload
			   $sql = "SELECT profilepic 
			   		  FROM refugee 
			   		  WHERE id=$refugeeid;";
			   $ret = $database->query($sql);
			   if (!$ret || !$ret->num_rows)
			   {
			   	die("<p class='alert alert-warning'>refugee does not exist.</p>");
			   }
			   
			   $row = $ret->fetch_array();
			   
			   $key = "profilepic";
			   
			   $row = $ret->fetch_array(); 
 		      $profilepic = $row[$key];
					
				// ensure we don't have an existing user name
				$sql = "SELECT user_name 
				        FROM refugee 
				        WHERE user_name = '' AND 
				        			id <> $refugeeid;";
				$ret = $database->query($sql);
				if (!$ret || !$ret->num_rows)
				{
					// update the user name and password
				   // update the login and password if applicable
					$extra = "";
					if ($password <> ''){
						$password = MD5($password);
						$extra = ", user_password='$password'";
					}
						
					$sql = "UPDATE refugee 
					        SET user_name='$username' 
					            $extra
							  WHERE id=$refugeeid;";
					$ret = $database->query($sql);

					if (!$ret)

						die('Error: ' .$database->error);
						
					echo "<p class='alert alert-success'><b>Update refugee:</b> $sql</p>";
				} else {
					echo "<p class='alert alert-warning'>That user name already exists: $username</p>";
				}
				
			   // if the profile pic has been set then adjust it
   			if ($_FILES[$key]['size'] != 0)

				{
				   if (file_exists($profilepic))
				   {
				   	unset($profilepic);				   	
				   }					
				   
				   // set profilepic to zero
				   $sql = "UPDATE refugee 
				           SET profilepic=''
				           WHERE id=$refugeeid;";
				   $ret = $database->query($sql);

					/*

					 * FILE uploads

					 */



					$target_dir = "refugee-profiles/";

					if (!is_dir($target_dir))

						 @ mkdir($target_dir);

	
					if (!is_writable($target_dir))
						echo "<p class='alert alert-success'><b>Not writable:</b> $target_dir</p>";
							

					// generate random filename & set extension							

					$file          = basename($_FILES[$key]["name"]);

					$fileextension = get_file_extension($file);

					$file          = randomPassword();

					$file          = "$file.$fileextension";

					$target_file   = $target_dir . $file;

	
					$tmp = $_FILES[$key]["tmp_name"];					
					

					if (@ move_uploaded_file($_FILES[$key]["tmp_name"], $target_file)) {



						$sql = "UPDATE refugee 
								  SET profilepic='$target_file'
						        WHERE id={$refugeeid};";

						$ret = $database->query($sql);

						if (!$ret)

							die('Error: ' .$database->error);						
						

					} else 

					{
					    // Check $_FILES['upfile']['error'] value.
					    switch ($_FILES[$key]['error']) {
					        case UPLOAD_ERR_OK:
					            break;
					        case UPLOAD_ERR_NO_FILE:
					            throw new RuntimeException('No file sent.');
					        case UPLOAD_ERR_INI_SIZE:
					        case UPLOAD_ERR_FORM_SIZE:
					            throw new RuntimeException('Exceeded filesize limit.');
					        default:
					            throw new RuntimeException('Unknown errors.');
					    }
					    

						die( "Could not upload the image. Check /var/log/apache2");

					}

				}			   


				echo "<small>refugee login profile updated successfully.</small>

				      <script>

					    window.location.href = '$returnurl';

					   </script>";		  
			break;
				  	
		    case 'all-refugee':
		    case 'refugee-sex':
		    case 'refugee-region':
		    case 'refugee-region-pie':
		    case 'refugee-sex-region':
		    case 'all-cls':
		    case 'cls-sex':
		    case 'cls-region':
		    case 'cls-region-pie':
		    case 'cls-sex-region':
		    		$total = 0;
				   $region_names = array("'Erongo'",
					                      "'Hardap'",
					                      "'Karas'",
					                      "'Kavango East'",
					                      "'Kavango West'",
					                      "'Khomas'",
					                      "'Kunene'",
					                      "'Ohangwena'",
					                      "'Omaheke'",
					                      "'Omusati'",
					                      "'Oshana'",
					                      "'Oshikoto'",
					                      "'Otjozondjupa'",
					                      "'Zambezi'");
					$regions = implode(',',$region_names);  
					 
					switch ($view){
						    case 'all-refugee':
									 // total refugee
									 $sql = "SELECT 
												    COUNT(*)
												FROM
												    (SELECT 
												        yp.refugee_id, TIMESTAMPDIFF(YEAR, yp.birth_date, CURDATE()) AS age
												    FROM
												        refugee_profile yp) temp
												WHERE
												    age >= 16 AND age <= 35;";
									 $regions = $database->query($sql);
									 if (!$regions || !$regions->num_rows)
									 {}
									 else {
									 	$row = $regions->fetch_array();
									 	$total = $row[0];
									 }
									 
									echo "<script>
									/* --c3js-- */								
									var chart = c3.generate({
													bindto: '#chart-default',
													data: {
													  columns: [
														         ['Total', $total]
													           ],		  
													  axes: {
														  data2: 'y2'
													  },
													  types: {
															Total: 'bar'
													  }		  
													},
												 axis: {
												     x: {
												         type: 'category',
												         categories: ['Total refugee']
												     }
												 }
												});
									</script>";
						    		break;
						    	
						    case 'refugee-sex':
									 // total refugee males
									 $total_m = 0;
									 $total_f = 0;
									 
									  $sql = "SELECT 
													    COUNT(*)
													FROM
													    (SELECT 
													        yp.refugee_id, TIMESTAMPDIFF(YEAR, yp.birth_date, CURDATE()) AS age
													    FROM
													        refugee_profile yp, list_sex ls
														WHERE yp.sex_id = ls.id AND ls.name = 'Male') temp
													WHERE
													    age >= 16 AND age <= 35;";
									 $regions = $database->query($sql);
									 if (!$regions || !$regions->num_rows)
									 {
									 }
									 else {
									 	$row = $regions->fetch_array();
									 	$total_m = $row[0];
									 }
									 
									  $sql = "SELECT 
													    COUNT(*)
													FROM
													    (SELECT 
													        yp.refugee_id, TIMESTAMPDIFF(YEAR, yp.birth_date, CURDATE()) AS age
													    FROM
													        refugee_profile yp, list_sex ls
														WHERE yp.sex_id = ls.id AND ls.name = 'Female') temp
													WHERE
													    age >= 16 AND age <= 35;";
									 $regions = $database->query($sql);
									 if (!$regions || !$regions->num_rows)
									 {}
									 else {
									 	$row = $regions->fetch_array();
									 	$total_f = $row[0];
									 }
									 
									echo "<script>
									/* --c3js-- */								
									var chart = c3.generate({
													bindto: '#chart-default',
													data: {
													  columns: [
														         ['Males',   $total_m],
														         ['Females', $total_f]
													           ],		  
													  axes: {
														  data2: 'y2'
													  },
													  types: {
															Males: 'bar',
															Females: 'bar'
													  }		  
													},
												 axis: {
												     x: {
												         type: 'category',
												         categories: ['Total refugee by sex']
												     }
												 }
												});
									</script>";
									
									echo "<script>
									/* --c3js-- */			
									var chart = c3.generate({
													bindto: '#chart-default',
													data: {
													  columns: [
														         ['Males', $total_m],
														         ['Females', $total_f]
													           ],		  
													  type: 'pie'	  
													}
												});
									</script>";
									break;
									
						    case 'refugee-region':
								 	$values = null;
								 	
								   foreach($region_names as $key)
								   {
								         $sql = "SELECT COUNT(*) FROM(
								                 SELECT yp.refugee_id, TIMESTAMPDIFF(YEAR, yp.birth_date, CURDATE()) AS age
								                 FROM refugee_profile yp,
								                      list_regions lr
								                 WHERE yp.region_id = lr.id AND
								                       lr.name=$key
								                 ) temp
								                 WHERE age >=16 AND age <=35;";
								         $ret = $database->query($sql);
								         if (!$ret || !$ret->num_rows)
								         {}
								         else
								         {
								              $row = $ret->fetch_array();
								              $values[] = $row[0];
								         }
								   }
						
								   $values = implode(',',$values);
									 
									echo "<script>
									/* --c3js-- */								
									var chart = c3.generate({
													bindto: '#chart-default',
													data: {
													  columns: [
														         ['Totals', $values]
													           ],		  
													  axes: {
														  data2: 'y2'
													  },
													  types: {
															Totals: 'bar'
													  }		  
													},
												 axis: {
												     x: {
												         type: 'category',
												         categories: [$regions]
												     }
												 }
												});
									</script>";
									
								   break;
								   
						    case 'refugee-region-pie':
								 	$data = null;
								 	
								   foreach($region_names as $region)
								   {
								         $sql = "SELECT COUNT(*) FROM(
								                 SELECT yp.refugee_id, TIMESTAMPDIFF(YEAR, yp.birth_date, CURDATE()) AS age
								                 FROM refugee_profile yp,
								                      list_regions lr
								                 WHERE yp.region_id = lr.id AND
								                       lr.name=$region
								                 ) temp
								                 WHERE age >=16 AND age <=35;";
								         $ret = $database->query($sql);
								         if (!$ret || !$ret->num_rows)
								         {}
								         else
								         {
								              $row = $ret->fetch_array();
								              // ['region', data]
								              $data[] = "[$region," . $row[0] ."]\n";
								         }
								   }
						
								   $data = implode(',',$data);
									 
									echo "<script>
									/* --c3js-- */			
									var chart = c3.generate({
													bindto: '#chart-default',
													data: {
													  columns: [
													  					$data
													           ],		  
													  type: 'pie'	  
													}
												});
									</script>";
								   break;
								   
						    case 'refugee-sex-region':
								   $values_male   = null;
								   $values_female = null;
								   
								   foreach($region_names as $region)
								   {
							         $sql = "SELECT COUNT(*) FROM(
							                 SELECT yp.refugee_id, TIMESTAMPDIFF(YEAR, yp.birth_date, CURDATE()) AS age
							                 FROM refugee_profile yp,
							                      list_regions lr,
							                      list_sex ls
							                 WHERE yp.region_id = lr.id AND
							                       ls.id = yp.sex_id AND
							                       ls.name = 'male' AND
							                       lr.name=$region
							                 ) temp
							                 WHERE age >=16 AND age <=35;";
							                       
							         $ret = $database->query($sql);
							         if (!$ret || !$ret->num_rows)
							         {
							         	$values_male[] = 0;
							         }
							         else
							         {
							              $row = $ret->fetch_array();
							              $values_male[] = $row[0];
							         }
							
							         $sql = "SELECT COUNT(*) FROM(
							                 SELECT yp.refugee_id, TIMESTAMPDIFF(YEAR, yp.birth_date, CURDATE()) AS age
							                 FROM refugee_profile yp,
							                      list_regions lr,
							                      list_sex ls
							                 WHERE yp.region_id = lr.id AND
							                       ls.id = yp.sex_id AND
							                       ls.name = 'female' AND
							                       lr.name=$region
							                 ) temp
							                 WHERE age >=16 AND age <=35;";
							                       
							         $ret = $database->query($sql);
							         if (!$ret || !$ret->num_rows)
							         {
							         	$values_female[] = 0;
							         }
							         else
							         {
							              $row = $ret->fetch_array();
							              $values_female[] = $row[0];
							         }
								   }
								   
								   $values_male   = implode(',',$values_male);
								   $values_female = implode(',',$values_female);

									echo "<script>
									/* --c3js-- */								
									var chart = c3.generate({
													bindto: '#chart-default',
													data: {
													  columns: [
														         ['Males',   $values_male],
														         ['Females', $values_female]
													           ],		  
													  axes: {
														  data2: 'y2'
													  },
													  types: {
															Males: 'bar',
															Females: 'bar'
													  }		  
													},
												 axis: {
												     x: {
												         type: 'category',
												         categories: [$regions]
												     }
												 }
												});
									</script>";									
								   break;

						    case 'all-cls':
									 $sql = "SELECT 
									 						COUNT(refugee_id) AS total
									 			FROM
									 					  service_cls;";
									 $regions = $database->query($sql);
									 if (!$regions || !$regions->num_rows)
									 {}
									 else {
									 	$row = $regions->fetch_array();
									 	$total = $row[0];
									 }
									 
									echo "<script>
									/* --c3js-- */								
									var chart = c3.generate({
													bindto: '#chart-default',
													data: {
													  columns: [
														         ['Total', $total]
													           ],		  
													  axes: {
														  data2: 'y2'
													  },
													  types: {
															Total: 'bar'
													  }		  
													},
												 axis: {
												     x: {
												         type: 'category',
												         categories: ['Total CLS']
												     }
												 }
												});
									</script>";
 									 break;
			    
						    case 'cls-sex':
								   $total_m   = 0;
									$total_f = 0;
 
						         $sql = "SELECT COUNT(sc.refugee_id) AS total
						                 FROM refugee_profile yp,
						                      list_regions lr,
						                      service_cls sc,
						                      list_sex ls
						                 WHERE yp.region_id = lr.id AND
						                       sc.refugee_id = yp.refugee_id AND
						                       ls.id = yp.sex_id AND
						                       ls.name = 'male';";	                       
						         $ret = $database->query($sql);
						         if (!$ret || !$ret->num_rows)
						         {
						         }
						         else
						         {
						              $row = $ret->fetch_array();
						              $total_m = $row['total'];
						         }
						
						         $sql = "SELECT COUNT(sc.refugee_id) AS total
						                 FROM refugee_profile yp,
						                      list_regions lr,
						                      service_cls sc,
						                      list_sex ls
						                 WHERE yp.region_id = lr.id AND
						                       sc.refugee_id = yp.refugee_id AND
						                       ls.id = yp.sex_id AND
						                       ls.name = 'female';";	                       
						         $ret = $database->query($sql);
						         if (!$ret || !$ret->num_rows)
						         {
						         }
						         else
						         {
						              $row = $ret->fetch_array();
						              $total_f = $row['total'];
						         }

									echo "<script>
									/* --c3js-- */								
									var chart = c3.generate({
													bindto: '#chart-default',
													data: {
													  columns: [
														         ['Males',   $total_m],
														         ['Females', $total_f]
													           ],		  
													  axes: {
														  data2: 'y2'
													  },
													  types: {
															Males: 'bar',
															Females: 'bar'
													  }		  
													},
												 axis: {
												     x: {
												         type: 'category',
												         categories: ['Total CLS by Sex']
												     }
												 }
												});
									</script>";
									
									echo "<script>
									/* --c3js-- */			
									var chart = c3.generate({
													bindto: '#chart-default',
													data: {
													  columns: [
														         ['Males', $total_m],
														         ['Females', $total_f]
													           ],		  
													  type: 'pie'	  
													}
												});
									</script>";
								   break;

						    case 'cls-region':
								   $values = null;
							
								   foreach($region_names as $key)
								   {
								         $sql = "SELECT COUNT(*) AS total
								                 FROM refugee_profile yp,
								                      list_regions lr,
								                      service_cls sc
								                 WHERE yp.region_id = lr.id AND
								                       sc.refugee_id = yp.refugee_id AND
								                       lr.name=$key;";
								                       
								         $ret = $database->query($sql);
								         if (!$ret || !$ret->num_rows)
								         {
								         	$values[] = 0;
								         }
								         else
								         {
								              $row = $ret->fetch_array();
								              $values[] = $row['total'];
								         }
								   }
								   
									$values = implode(',',$values);
									
									echo "<script>
									/* --c3js-- */				
									var chart = c3.generate({
													bindto: '#chart-default',
													data: {
													  columns: [
														         ['Totals', $values]
													           ],		  
													  axes: {
														  data2: 'y2'
													  },
													  types: {
															Totals: 'bar'
													  }		  
													},
												 axis: {
												     x: {
												         type: 'category',
												         categories: [$regions]
												     }
												 }
												});
									</script>";
								   break;
								   
						    case 'cls-region-pie':
								   $data = null;
							
								   foreach($region_names as $region)
								   {
								         $sql = "SELECT COUNT(*) AS total
								                 FROM refugee_profile yp,
								                      list_regions lr,
								                      service_cls sc
								                 WHERE yp.region_id = lr.id AND
								                       sc.refugee_id = yp.refugee_id AND
								                       lr.name=$region;";
								                       
								         $ret = $database->query($sql);
								         if (!$ret || !$ret->num_rows)
								         {
								         	$values[] = 0;
								         }
								         else
								         {
								              $row = $ret->fetch_array();
								              // ['region', data]
								              $data[] = "[$region," . $row['total'] ."]\n";
								         }
								   }
								   
									$data = implode(',',$data);
									
									echo "<script>
									/* --c3js-- */			
									var chart = c3.generate({
													bindto: '#chart-default',
													data: {
													  columns: [
													  					$data
													           ],		  
													  type: 'pie'	  
													}
												});
									</script>";
								   break;
								   								   
						    case 'cls-sex-region':
								   $values_male   = null;
									$values_female = null;
									
								   foreach($region_names as $region)
								   {	   
							         $sql = "SELECT COUNT(sc.refugee_id) AS total
							                 FROM refugee_profile yp,
							                      list_regions lr,
							                      service_cls sc,
							                      list_sex ls
							                 WHERE yp.region_id = lr.id AND
							                       sc.refugee_id = yp.refugee_id AND
							                       ls.id = yp.sex_id AND
							                       ls.name = 'male' AND
							                       lr.name=$region;";	                       
							         $ret = $database->query($sql);
							         if (!$ret || !$ret->num_rows)
							         {
							         	$values_male[] = 0;
							         }
							         else
							         {
							              $row = $ret->fetch_array();
							              $values_male[] = $row['total'];
							         }
							
							         $sql = "SELECT COUNT(sc.refugee_id) AS total
							                 FROM refugee_profile yp,
							                      list_regions lr,
							                      service_cls sc,
							                      list_sex ls
							                 WHERE yp.region_id = lr.id AND
							                       sc.refugee_id = yp.refugee_id AND
							                       ls.id = yp.sex_id AND
							                       ls.name = 'female' AND
							                       lr.name=$region;";	                       
							         $ret = $database->query($sql);
							         if (!$ret || !$ret->num_rows)
							         {
							         	$values_female[] = 0;
							         }
							         else
							         {
							              $row = $ret->fetch_array();
							              $values_female[] = $row['total'];
							         }
									}
									
								   $values_male   = implode(',',  $values_male);
								   $values_female = implode(',',$values_female);
								   
									echo "<script>
									/* --c3js-- */								
									var chart = c3.generate({
													bindto: '#chart-default',
													data: {
													  columns: [
														         ['Males',   $values_male],
														         ['Females', $values_female]
													           ],		  
													  axes: {
														  data2: 'y2'
													  },
													  types: {
															Males: 'bar',
															Females: 'bar'
													  }		  
													},
												 axis: {
												     x: {
												         type: 'category',
												         categories: [$regions]
												     }
												 }
												});
									</script>";
								   break;
					}   
		    		break;
    
			case 'toggle-lock':
				$userid = (int) @ $_POST['userid'];	
 	  			$table = @ $_POST['table'];
 	  			$id    = @ $_POST['id'];
 	  												
				// lock/unlock document		
				// check if this document is locked
				$sql = "SELECT 
							    r.name
							FROM
							    users u,
							    user_roles r
							WHERE
							    u.id = $userid AND 
							    u.roleid = r.id";
							    
				 $role  = subquery($sql);
			 	  			
				 switch($role) {
				 	  case 'top_levels':
			 	  			// only the one who locked the document can lock or unlock it

							$sql = "SELECT lockedby_user_id, user_name
									  FROM `$table` d, users u
									  WHERE d.id=$id AND 
									  		  d.lockedby_user_id = u.id;";
									  		  
							$ret = $database->query($sql);
							if (!$ret || !$ret->num_rows){
								// lock the document!
								$sql = "UPDATE 
												`$table`
										  Set 
										  		lockedby_user_id=$userid
										  WHERE 
										  		id=$id";
								$ret = $database->query($sql);
								$failed = !$ret;
								echo $failed ? $database->error : "$fa_lock You have locked the document. Other users will not be allowed access to it until you unlock it.";
							}
							else {
								$row = $ret->fetch_array();
								$lockerid = $row['lockedby_user_id'];
								$username = $row["user_name"];
								$locked = $lockerid > 0 ? true : false;
								
								if ($locked){
									if ($lockerid == $userid)
									{
										// unlock the document!
										$sql = "UPDATE 
														`$table`
												  Set 
												  		lockedby_user_id=0
												  WHERE 
												  		id=$id";
										$ret = $database->query($sql);
										$failed = !$ret;
										echo $failed ? $database->error : " You have unlocked the document.";
									}
									else {
										echo "You cannot unlock the document. Only $username can do that.";
									}
								}
							}
			 	  		break;
			 	  		
			 	  	default:
			 	  		echo "You do not have permission to lock or unlock documents. $role";
			 	  		break;
	
				 }
				break;					
					
		  case 'deleteitem':
			$table = @ $_POST['table'];
			$id    = @ $_POST['id'];
			
			switch (strtolower($table))
			{
				case 'documents':
				case 'website_events':
				case 'website_documents':
					// get filename
					$filename = "";
					
					$sql = "SELECT filename 
							  FROM `$table` 
							  WHERE id=$id;";
					$ret = $database->query($sql);
					if (!$ret || !$ret->num_rows)
					{}
					else
					{
						$row = $ret->fetch_array();
						$filename = $row['filename'];
						
						if (file_exists($filename))
							@unlink($filename);						
					}

					$sql = "DELETE FROM `$table` 
							  WHERE id=$id;";
					$ret = $database->query($sql);
					if ($ret)
						echo "true";
					else
						echo $database->error;
					
					break;
					
				default:
					$sql = "DELETE FROM 
							  `$table` 
							  WHERE id=$id;";
					$ret = $database->query($sql);
					
					if ($ret)
						echo "true";
					else
						echo $database->error;
						
					break;
			}
			break;
			
		  case 'new-refugee':
		  	// normal upload
	 		// required fields
			$required = array('name_surname',
			                  'name_first',
			                  'id_number',
			                  'birth_date',
			                  'region_id',
			                  'sex_id');
			
				foreach($required as $field){
						$_POST[$field] = addslashes(trim(@ $_POST[$field]));

						if ($_POST[$field] == ""){
							$errors = missing_parameter($field);
							die($errors);
						}
				}	

				$surname     = addslashes(trim(@ $_POST['name_surname']));
				$name_others = @ $_POST['name_others'];
				$name_first  = addslashes(trim(@ $_POST['name_first']));
				$cellphone   = addslashes(trim(@ $_POST['cellphone']));
				$email       = addslashes(trim(@ $_POST['email']));
				$nationality = (int) @ $_POST['nationality_id'];
                $citizenship = (int) @ $_POST['citizenship_id'];
                $id_number   = addslashes(trim(@ $_POST['id_number']));
                $passport    = addslashes(trim(@ $_POST['passport']));
                $birth_date  = addslashes(trim(@ $_POST['birth_date']));
                $birth_place = addslashes(trim(@ $_POST['birth_place']));
                $birth_country= (int) @ $_POST['birth_country_id'];		
                $birth_cert_no= addslashes(trim(@ $_POST['birth_certificate_number']));
                $region       = (int) @ $_POST['region_id'];		
                $sex          = (int) @ $_POST['sex_id'];		
                $address_residential = addslashes(trim(@ $_POST['address_residential']));
                $address_postal = addslashes(trim(@ $_POST['address_postal']));
                        
			   $userid_   = @ $_POST['userid'];

			   $default_pwd = 'refugee12345';
			   
			   // make sure we dont have same user
				$sql = "SELECT * 
						FROM `refugee`
						WHERE user_name='$id_number';";
				$ret = $database->query($sql);
				if (!$ret || !$ret->num_rows)
				{
				    // cool to proceed
				} else {
				    $fa_find = font_awesome('fa-search');
				    
				    die("A refugee already exists with that ID number: <a href='?view=search&extra=1&term=$id_number&region=0'>$fa_find $id_number</a>");
				}
				// make sure we don't have the same refugee id number?
				$sql = "SELECT * 
						FROM `refugee_profile`
						WHERE id_number='$id_number';";
							   
				$ret = $database->query($sql);
				if (!$ret || !$ret->num_rows)
				{
				    $entrydate = date('Y-m-d H:i:s');
				    
					// create the login details
					$sql = "INSERT INTO `refugee`(entrydate, user_id, user_name,user_password, lock_user_id)
					        VALUES('$entrydate','$userid_','$id_number', MD5('$default_pwd'),'0');";
					$ret = $database->query($sql);
					
					if (!$ret){
					   // remove the newly added rec or next rec won't succeed
					  $error = $database->error;
					  
					  $sql = "DELETE FROM `refugee` 
					          WHERE user_name='$id_number';";
					  $ret = $database->query($sql);
					  die("Error #1: $error");  
					} 
					
					// need this to create the refugee profile
					$refugeeid = $database->insert_id;
					
					echo "<p>refugee account was created successfully</p>";
					
					$cols = array();
					$vals = array();
					
					$cols[] = 'refugee_id';
					$cols[] = 'user_id';
					$vals[] = "'$refugeeid'";
					$vals[] = "'$userid_'";
					
					foreach ($_POST as $key=>$val){
					    switch ($key){
					        case 'view':
					        case 'extra':
					        case 'userid':
					            break;
					       
					        default:
					            $cols[] = $key;
					            $vals[] = "'". addslashes(trim($val)) . "'";
					            break;
					    }
					}
					
					$cols = implode(',',$cols);
					$vals = implode(',',$vals);
					
					// save refugee profile
					$sql = "INSERT INTO `refugee_profile`($cols) VALUES ($vals);";
					$ret = $database->query($sql) or	die('Error #2: ' .$database->error . "<BR><pre>$sql</pre>");
				    
				    // action=summary
				    $returnurl = "?view=manage-refugees&action=edit&id=$refugeeid";
				   	
					echo "<small>The application was successfully added to the system.</small>
					      <script>
						    window.location.href = '$returnurl';
						   </script>";
				}		
				else
				{
					echo alertbuilder("A refugee already exists on the system with the ID number: <b>$id_number</b>","danger");
				}
			break;
			
		  case 'authenticate':
			header('Content-Type: application/json');
			
		    // this is a post request!
		    // let us try to login this person!
			$result = $users->loggedin();
			if (!$result){
				$username = @ $_POST['user_name'];
				$password = @ $_POST['user_password'];
				
				// try to log me user in
				$user = $users->login($username, $password);
				$action = 'LOGIN_FAIL';
				
				if (!$user) {
					$error = 'Unable to login. Check your username or password or contact the administrator.';
					// log this failed attempt!
					$description = $error;
					update_system_log($action, $description);	

					echo json_encode(['result'=>false, 
									      'view'=>$view,
									      'status'=>$error,
									      'html'=>"<div class='alert alert-danger'><span><li class='fa fa-fw fa-exclamation-circle'></li>&nbsp;Unable to login. Check your username or password or contact the administrator.</span></div>"
									 ]);	
					exit;
				}	
				
				/* update lastloggedin time */
				$userid = $user->get('id');
					
				$logintime = @ $_SESSION[ settings::session_logintime];
				
				$action = "LOGIN_SUCCESS";
				$description = "User has successfully logged into the system. Details: userid: $userid, logintime: $logintime";
				update_system_log($action, $description);
				
				/* update lastloggedin time */
				$sql = "UPDATE users 
						SET lastlogin=NOW()
						WHERE id=$userid;";
				$database->query($sql) or update_system_log($action, "Failed to update user session, logout time. Error: {$database->error}"); ;						
							
				echo json_encode(['result'=>true, 
								  'view'=>$view,
								  'status'=>'You have been successfully logged in.',
								  'html'=>"<div class='alert alert-success'><span><li class='fa fa-fw fa-info-circle'></li>&nbsp;You have been successfully logged in.</span></div>"
								 ]);
				exit;
			}

			echo json_encode(['result'=>false, 
							  'view'=>'authenticate',
							  'status'=>'You are already logged in.',
							  'html'=>"<div class='alert alert-danger'><span><li class='fa fa-fw fa-exclamation-circle'></li>&nbsp;You are already logged in.</span></div>"
							 ]);			
			break;
			
		 default:
				header('Content-Type: application/json');
				echo json_encode(['result'=>false, 
								  'view'=>$view,
								  'status'=>'Unhandled view called'
								 ]);	
			break;
	  }						
						
	  exit;
  }
  
 /*
  * This will return an array of fields formatted as table headers
  * and tbody content
  */
function row_to_table($table, $ret, $default) {
	    $i = 0;
		$j = 0;
		$data = $default;
		$fields = "";
		$arr_fields = [];
		
		if ($ret) {
			while ($fld=mysqli_fetch_field($ret))
			{
				$fld_name = $fld->name;
				$fld_name = ucfirst($fld_name);
				
				$fields .= "<th>$fld_name</th>";

				// store fieldnames in array so we follow 
				// same field order when we spit out data
				$arr_fields[] = $fld->name;
			}

			// reset the recordset or you wont display any data
			mysqli_data_seek($ret,0);
			
			// spitting out data following field names
			$data = "<i>There is currently no data available.</i>";
			if ($ret->num_rows) $data = "";
			
			while ($res = $ret->fetch_array())
			{
				$data .= "<tr>";
				foreach ($arr_fields as $fld) {
					switch (strtolower($table)) {
							case 'system_log':
								switch (strtolower($fld)) {
									case 'ipaddress':
										$ip = $res[$fld];
										
										if (is_valid_ip($ip))
											$ip = "<a href='#' onclick=\"dlg_ip('$ip');\");\" data-toggle='tooltip' title='View location'><li class='fa fa-fw fa-search'></li>&nbsp;$ip</a>";
										
										$data .= "<td>$ip</td>";									
										break;
										
									case 'id':
										$id = $res[$fld];
										$id = "<a href='#' onclick=\"delete_row('$table','id',$id);\" data-toggle='tooltip' title='Delete this system log entry'><li class='fa fa-fw fa-trash'></li></a>";
										$data .= "<td>$id</td>";
										break;
										
									default:
										$data .= "<td>{$res[$fld]}</td>";
										break;
								}
								break;
								
							default:
								$data .= "<td>{$res[$fld]}</td>";
								break;
					}
				}

				$data .= "</tr>";
			}
		}
		return ['fields'=>$fields, 'data'=>$data];
}	
?>
