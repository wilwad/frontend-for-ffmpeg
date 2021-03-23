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
<h1>FFMPEG Results</h1>
</div>
</section>
<section class='bg-teal'>
  <div class='container'>
	<div class="row row-no-shadow">
<?php

	$dir    = @ $_GET['dir'];
    if (!is_dir($dir)){
        die("<p class='alert alert-danger'>No such directory: $dir</p>");
    }

    $action = (int) @ $_GET['action'];
    if ($action){
        require_once('ffmpegactions/actions.php');
        $act = @ $actions[$action];
        if ($act){
            $title = @ $act['title'];
            $cmd   = @ $act['cmd'];
            echo "<div class='col-md-12'>
                    <h3>$title</h3>
                    <pre>$cmd</pre>
                  </div>";
        }
    }

    $nav     = "";
    $data    = "";
    $pagination = "";
    
        $files = get_files($dir, array('*'));
        $total = sizeof($files);
        
        if (!$total){
              $data = "<div>
                         <p class='alert alert-warning'>Nothing in this directory.</p>
                       </div>";
        } else {

			$idx=1;
			foreach($files as $file){
				if (is_file($file)){
					$filename = basename($file);
					$filename_ = explode(".", $filename)[0];
					$file0 = "$dir/$filename";
					
					//list($width, $height, $type, $attr) = getimagesize($file);
					
					/*
					$data .= "<div class='col-md-3'>
							   <h5><small><span class='badge'>$idx.</span> $filename_</small></h5>
							   <a href='$file' class='fancybox' 
							data-fancybox-group='gallery'><img class='lazy img-thumbnail img-responsive' width='$width' height='$height' data-src='$file'></a><BR>
							   <a href='?action=delete&dir=$dir&file=$filename' class='btn btn-sm btn-danger'>$fa_trash Delete</a> 
							   <!-- <a href='#' download='$file' class='btn btn-sm btn-success'>Download</a>-->
							  </div>";
					*/

                    $object = "";
                    $name = basename($file0);
                    switch ($extension = strtolower(pathinfo($file0, PATHINFO_EXTENSION))){
                        case 'jpg':
                        case 'jpeg':
                        case 'png':
                        case 'gif':
                                    $object = "<a href='$file0' class='fancybox' data-title=\"$name\" rel='gallery'><img src='$file0' class='img-responsive'><BR><span class='badge'>$name</span></a>";
                                    break;
                                    
                        case 'wav':
                        case 'ogg':
                        case 'mp3':
                            $object = "<audio controls>
                                        <source src='$file0'></source>
                                       </audio><BR>$name";
                            break;                                    
                            
                        // video
                        case 'mp4':
                        case 'mkv':
                        case 'mov':
                        case 'ogv':
                            $type = mime_content_type($file0);
                            $object = "<div align='center' class='embed-responsive embed-responsive-16by9'>
                                            <video controls class='embed-responsive-item'>
                                                <source src='$file0' type='$type'>
                                            </video>
                                        </div>";
                    }
                    
					$data .= "<div class='col-md-6 col-xs-12'>$object</div>";
					
					$idx++;
				}
			}
			
			
       }

        echo $data;
            
        function font_awesome($icon, $zoom = ''){
            return "<span class='fa fa-fw fa-$icon $zoom'></span>";    
        }
        
        function alert($message, $type = 'success'){
            return "<p class='alert alert-$type'>$message</p>";
        }
        
        function get_file_extension($file){
                $ext = explode('.', $file);
                if (count($ext)) return $ext[1];
                
                return $file;
        }
        
        function count_images($dir){
                 $images =glob("$dir/*.mkv", GLOB_BRACE);
                 return sizeof($images);
        }
        
        function get_files($images_dir,$exts = array('png')) {
                $files = array();
                $times = array();
                
                if($handle = opendir($images_dir)) {
                    
                    while(false !== ($file = readdir($handle))) {
                        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if($extension) {//} && in_array($extension,$exts)) {
                            $files[] = "$images_dir/$file";
                            $times[] = filemtime($images_dir . '/' . $file);
                        }
                    }
                    
                    closedir($handle);
                } else {
                    echo "<p class='alert alert-danger'>opendir($images_dir) failed</p>";
                }
        
                //array_multisort($files, SORT_DESC, $times);
                return $files;
        }
    ?>	
	</div>
 </div> <!-- container -->
</section><!--section -->