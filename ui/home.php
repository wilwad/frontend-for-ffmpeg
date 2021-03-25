<style>
.cta {
    background-color:#354054;
    background-size: cover!important;
    padding: 10px;
}   

</style>
<section class="cta">
	<div class="container">
    <div>
    <h1 style='margin-bottom:0px; padding-bottom:0px;'>PHP frontend for FFMPEG <small class='pull-right'><a href='https://ffmpeg.org/ffmpeg-filters.html' target='_blank'>ffmpeg filters website</a></small></h1>
    </div>
	</div>
</section>

<?php
require_once('req/functions.php');
require_once('ffmpegactions/actions.php');
  /*
   * Edit these as needed
   */

   $debug                   = false;
   $button_submit           = "Run Action";                
   $field_first             = "";

   // icons
   $fa_exclaim = "<span class='fa fa-fw fa-exclamation-triangle'></span>";
   
  $fields = null;                                                              

	$errors = "";
	$extra  = (int) @ $_POST['extra'];
  $action = (int) @ $_GET['action'];

  if ($extra && $action){
    /* // POST parameters debug
    $errors = "<p class='alert alert-default'>";
    foreach($_POST as $key=>$val){
      $errors .= "<span class='badge'>$key: $val</span>&nbsp;";
    }
    $errors .= '</p>';
    */
    
    $action_ = @ $actions[$action]; // do not overwrite $actions array!
    if (!$action_) die("<p class='alert alert-danger'>Invalid action: $action</p>");

    $dir0 = "ffmpeguploads";
    $dir1 = "ffmpegresults";

    if (!is_dir($dir0)){
      @ mkdir($dir0);
    } else {
      // clean up directory
      $files = glob("$dir0/*"); // get all file names
      foreach($files as $file){ // iterate files
        if(is_file($file)) {
          @ unlink($file); // delete file
        }
      }      
    }    

    if (!is_dir($dir1)){
      @ mkdir($dir1);
    } else {
      // clean up directory
      $files = glob("$dir1/*"); // get all file names
      foreach($files as $file){ // iterate files
        if(is_file($file)) {
          @ unlink($file); // delete file
        }
      }      
    }

    if (!is_writable($dir0)) die("<p class='alert alert-danger'>Directory not writable: $dir0</p>");
    if (!is_writable($dir1)) die("<p class='alert alert-danger'>Directory not writable: $dir1</p>");
    
    $format = 'error';
    $files = [];
    $random = '';

    // handling file upload
    foreach ($_FILES as $key=>$val){
          $name = $_FILES[$key]["name"];
          $tmp  = $_FILES[$key]["tmp_name"];

					// generate random filename & set extension							
					$file = basename($name);
					$fileextension = get_file_extension($file);
					$file   = randomPassword();
          $random = $file;
					$file   = "$file.$fileextension";
		
          // dynamic insert
          // sometimes: filename is required name for primary file input
          // sometimes: posterimage, filename
          
          // set our default format when source selected or when source is only %format%
          if ($key == 'filename') $format = $fileextension;
          $targetfile =  "ffmpeguploads/$file";
          $files[$key] =$targetfile;
					$uploadOk = 1;
		
					if (@ move_uploaded_file($tmp, $targetfile)) {
             // handle zip file
             switch (mime_content_type($targetfile)){
               case 'application/zip':
                $unzip = new ZipArchive;
                $out = $unzip->open($targetfile);
                if ($out === TRUE) {
                  $unzip->extractTo("ffmpeguploads/");
                  $unzip->close();
                  //echo 'File unzipped';
                } else {
                  die("Failed to unzip archive: $targetfile");
                }

                break;
             }

					}	else {
            $error = $_FILES[$key]['error'];
            $err = '';
            switch($error) {
              case UPLOAD_ERR_INI_SIZE:
                $err = 'Exceeds max size in php.ini';
                break;
              case UPLOAD_ERR_PARTIAL:
                $err = 'Exceeds max size in html form';
                break;
              case UPLOAD_ERR_NO_FILE:
                $err = 'No file was uploaded';
                break;
              case UPLOAD_ERR_NO_TMP_DIR:
                $err = 'No /tmp dir to write to write to';
                break;
              case UPLOAD_ERR_CANT_WRITE:
                $err = 'Error writing to disk';
                break;
              default:
                $err = 'No error was faced! Phew!';
                break;
              }     

            $inisize = ini_get('post_max_size');
						die("<p class='alert alert-danger'>Error uploading: $key: $err ($inisize)</p>");
					}
    }

    $cmd = $action_['cmd']; 
    $controls = $action_['controls'];

    // dir
    $cmd = str_replace("%dir%",$dir1,$cmd);
    $cmd = str_replace("%random%",$random,$cmd);

    foreach ($_POST as $key=>$val){
      // some values have 1^2
      if (strpos($val,'^') >-1){
        $val = explode('^',$val)[0];
      }
      switch ($key){
        case 'format':          
            // only when $format is not set          
            if ($val == 'source'){
              // use parsed extension for output
              $cmd = str_replace("%format%",$format,$cmd);
            } else {
              // use specified extenstion
              $cmd = str_replace("%$key%",$val,$cmd);
            }

          break;

        default:
          $cmd = str_replace("%$key%",$val,$cmd);
      }
    }
      
  if (strlen($format) && strpos($cmd,'%format%') >-1) {  
    // %format% is still not set so set it
    $cmd = str_replace("%format%",$format,$cmd);
  }

    // str_replace the filenames
    foreach($files as $key=>$val){
      //echo "<p>$key: $val</p>";
      $cmd = str_replace("%$key%",$val,$cmd);
    }

    $errors .= "<p class='alert alert-info'><b>Command</b><BR> $cmd</p>";
    if ($cmd){
      $output = null;
      $code   = null;
      exec("$cmd -y", $output, $code);
      $output = implode(" ", $output);
      $output = trim($output);
      if (!strlen($output)) $output = "Run the generated command in terminal to view the ffmpeg error";
      $errors .= ($code == 0) ? "<p class='alert alert-success'>ffmpeg success: <a href='?view=ffmpeg-results&dir=$dir1&action=$action' target='_blank'><u>Open results</u></a></p>" : "<p class='alert alert-danger'>ffmpeg failed: $output</p>";
    }
  } // if ($extra)
?>
<section class='bg-teal'>
  <div class='container'>
  
      <div class="section-title align-left">
          <h3><?php 
              $total = sizeof($actions);
              echo "$total Available Actions"; ?>								         
          </h3>
          <div class='row row-no-shadow bg-teal'>
           <div class='col-md-8'></div>
           <div class='col-md-4'>
								 <div class='form-group'>
								 	<input type='text' class='form-control filter' placeholder='Type something to filter the results below' name='term' id='term'>
								 	<i class='glyphicon glyphicon-search form-control-feedback'></i>
								 </div>
								
								<script>
									window.addEventListener('load', ()=>{								
										var term = document.querySelector('input[name=\"term\"]');
										term.addEventListener('keyup', ()=>{
								
								document.querySelectorAll('div#actions a.badge').forEach((tr,idx)=>{
								if (term.value.trim().length == 0){
								    $(tr).show();
								} else {
								   let val = tr.innerText.toLowerCase();
								   if (val.indexOf(term.value.toLowerCase()) > -1){
								       $(tr).show()
								   } else {
								       $(tr).hide();
								   }
								}
								
								});
								}, false);
								
								},false);
								</script>           
           </div>
          </div>
          <div class='well' id='actions'>
          <?php
            foreach ($actions as $key=>$details){
              $title = $details['title'];
              $color = $action == $key ? 'yellow' : 'default';
              echo "<a href='?view=home&action=$key' class='badge' style='color:$color'>$title</a>&nbsp;";
            }
          ?>
          </div>
      </div>

	<?php echo $errors; ?>
	<form method="POST" enctype="multipart/form-data">
	<input type='hidden' name='extra' value="1">
	
		<?php
			$idx = 0;

      if ($action){
        $act    = @ $actions[$action];
        $fields = @ $act['controls'];
        $title  = @ $act['title'];
        $cmd    = @ $act['cmd'];
        $notes  = @ $act['notes'] == '' ? "&nbsp;" : $act['notes'] . "<p>&nbsp;</p>";
        echo "<h3>$title</h3>
              <pre>$cmd</pre>
              <p>$notes</p>";        
      }      

    if ($fields){
			foreach($fields as $field){
				$idx++;

				$fld       = $field['name'];			
        $accept    = @ $field['accept'];
        if ($accept) $accept = "accept='$accept'";
				$name      = $fld;		

				if ($idx == 1) $field_first = $name;

				$val       = @ $_POST[$fld];
				$caption   = $field['caption'];
				
				if ($fld == 'captcha'){
					$caption = "Add $op1 to $op2";
				}
				
        $default   = @ $field['default'];
        $default   = trim($default);
        if (strlen($default) && ! $val) $val = $default;

				$source    = @ $field['source'];
				$small     = "<BR>" . @ $field['small'];
				$type      = $field['type'];
				$maxlength = $field['maxlength'] == 0 ? '' : $field['maxlength'];
				
				$required  = $field['required'] == true ? 'required' : '';
        $requiredstar = $required ? "<span style='color:red'>*</span>" : "";
				$input   = "";
				
        // exclude some values from the loaded select file
        $source_exclude = @ $field['select_exclude'];

				switch($type) {
					case 'email':
					case 'text':
					case 'number':
		            case 'file':
		            case 'color':
						$input = "<input type='$type' $accept $required class='form-control' id='$name' name='$name' maxlength='$maxlength' value='$val'>";
						break;
						
					case 'textarea':
							$input = "<textarea name='$name' $required class='form-control' id='$name' maxlength='$maxlength'>$val</textarea>"; 
						break;
							
					case 'select':
						$filename = $source;
						$options= "";
						
						if (file_exists($filename)){
								$handle = fopen($filename, "r");
								if ($handle) {
									while (($line = fgets($handle)) !== false) {
										$line = trim($line);
										$selected = strpos($line,$val) > -1 ? 'selected' : '';							    	  
										if (strlen($line)){
                        if (is_array($source_exclude) && in_array($line, $source_exclude)){
                            continue;
                        }
												$options .= "<option value='$line' $selected>$line</option>";
                    }
									}
								
									fclose($handle);
								} else {
									echo "<p class='alert alert-warning'>Error opening the file $filename</p>";
								} 

							$input = "<select class='form-control' id='$name' name='$name'>
									$options
									</select>";
						}
						else {
							$input = "<p class='alert alert-warning'>Unable to locate file $filename</p>";
						}
						break;
				}
				
				echo "<div class='form-group row row-no-shadow'>
							<label for='$fld' class='col-sm-4 form-control-label'>$caption $requiredstar <small class='tiny'>$small</small></label>
							<div class='col-sm-6'>
							$input
							</div>
						</div>";
			}
    } else {
      echo "<p class='alert alert-info'>Select an action from the actions list above.</p>";
    }
		?>
		
		<div class="form-group row row-no-shadow">
			<div class="col-sm-offset-4 col-sm-6">
			<button type="submit" class="cta-btn2"><span class='fa fa-fw fa-paper-plane'></span>
			<?php echo $button_submit; ?></button>
			</div>
		</div>
	</form>
	</div>

	</div>
 </div> <!-- container -->
</section><!--section -->
