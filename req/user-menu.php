<style>
/* Add a black background color to the top navigation */
.topnav {
  background-color: #d9edf7;
  overflow: hidden;
  
    /*position: fixed !important;
    width: 100%;*/
    z-index: 999;  
}

.topnav .active {
  background-color: #43949f;
  
}
  
/* Style the links inside the navigation bar */
.topnav a {
  float: left;
  display: block;
  color: #f2f2f2;
  text-align: center;
  /*padding: 14px 16px;*/
  text-decoration: none;
}


/* Hide the link that should open and close the topnav on small screens */
.topnav .icon {
  display: none;
}

/* Dropdown container - needed to position the dropdown content */
.dropdown {
  float: left;
  overflow: hidden;
}

/* Style the dropdown button to fit inside the topnav */
.dropdown .dropbtn {
  /*font-size: 17px;*/
  border: none;
  outline: none;
  /*color: white;*/
  padding: 14px 16px;
  background-color: inherit;
  font-family: inherit;
  margin: 0;
}

/* Style the dropdown content (hidden by default) */
.dropdown-content {
  display: none;
  position: fixed;
  background-color: #656565;
  color:#fff;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

/* Style the links inside the dropdown */
.dropdown-content a {
  float: none;
  color: #FFF;
  padding: 7px 16px;
  text-decoration: none;
  display: block;
  text-align: left;
}

/* Add a dark background on topnav links and the dropdown button on hover */
.topnav a:hover, .dropdown:hover .dropbtn {
  background-color: #43949f;
  color: white;
}

/* Add a grey background to dropdown links on hover */
.dropdown-content a:hover {
  background-color: #43949f;
  color: #FFF;
}

/* Show the dropdown menu when the user moves the mouse over the dropdown button 
.dropdown:hover .dropdown-content {
  display: block;
}
*/
/* When the screen is less than 600 pixels wide, hide all links, except for the first one ("Home"). Show the link that contains should open and close the topnav (.icon) */
@media screen and (max-width: 600px) {
  .topnav a:not(:first-child), .dropdown .dropbtn {
    display: none;
  }
  .topnav a.icon {
    float: right;
    display: block;
  }
}

/* The "responsive" class is added to the topnav with JavaScript when the user clicks on the icon. This class makes the topnav look good on small screens (display the links vertically instead of horizontally) */
@media screen and (max-width: 600px) {
  .topnav.responsive {position: relative;}
  .topnav.responsive a.icon {
    position: absolute;
    right: 0;
    top: 0;
  }
  .topnav.responsive a {
    float: none;
    display: block;
    text-align: left;
  }
  .topnav.responsive .dropdown {float: none;}
  .topnav.responsive .dropdown-content {position: relative;}
  .topnav.responsive .dropdown .dropbtn {
    display: block;
    width: 100%;
    text-align: left;
  }
  
	.dropdown-content {
	    position: absolute;
	}
	  
}

.dropbtn.active {
    background-color: #fafafa;
    color: initial;
}
</style>

<?php

$nav_menu = "";
$image = "";
$username = "";

$options = "";
	$myrights = $role_rights[ $_SESSION["$session_key::user_role"]];

	// build the options for this user
	foreach($myrights as $key=>$arr) 
	{
		/* get the title of this panel and icon */
		$icon = $arr['icon'];
		$key_= str_replace(" ", "_", $key);
		$key = ucfirst($key);
		$initial_key = $key;
        $options .= "<div class='dropdown'>
				            <button class='dropbtn $key_'><i class='$icon dropextra'></i> $key
							      <i class='fa fa-caret-down dropextra'></i>
							    </button>
							    <div class='dropdown-content' id='dc$key_'>
                        ";

			$text = '??';
			
			foreach($arr['menu'] as $key=>$menu) {
					$ico = $menu['icon'];
					$tooltip = $menu['title'];
					$text = ucfirst($key);
					$introtext = $menu['intro-text'];
					$url = $menu['url'];
					
					$ishidden = @ $menu['hidden'] == true;
					$hidden = $ishidden ? "style='display:none'" : "";
					
					$divider_top = @ $menu['divider-top'] == true ? "border-top:1px dotted #f3f3f3;" : "";
					$divider_bottom = @ $menu['divider-bottom'] == true ? "border-bottom:1px dotted #f3f3f3;" : "";
					
					$active = strtolower($view) == strtolower($url) ? "class='active'" : "";
					
					$disabled = strtolower($view) == strtolower($text) ? 'disabled' : '';
					
					if ($active){
						$key__ = ucfirst($key_);
						
						$options .= "<script>
								 console.log(\"Selector running: '.$key_'\");
								 document.querySelectorAll('.$key_') && document.querySelectorAll('.$key_')[0].classList.add('active');
								</script>";
					}
						
					$img   = "";
					$extra = "";

                     //if ($divider_top && !$ishidden)  
                     //    $options .= "<li class='divider'></li>";
				
					switch ($key) {

						default:
								$span_notifications = "<span class='pull-right' 
                                                     style='cursor:pointer' 
                                                     data-toggle='tooltip' 
                                                     title='Start help wizard'
                                                     onclick=\"introJs().goToStep($introJsIndex).start();\"
                                                     data-placement='right'>
                                               </span>";
                        
								switch ($url) {
									case 'profile':
										$pic = "";
										$sql = "SELECT profilepic FROM `users` WHERE id=$_userid_ LIMIT 1";
										$ret = $database->query($sql) or die($database->error);
										if (!$ret || !$ret->num_rows){
										} else {
											$pic = $ret->fetch_array()['profilepic'];
										}
										
									   if (file_exists($pic))
										   $options .= "<p class='align-center' style='padding-top:10px;'>
										   					<img style='border-radius:5px; border:none; height: 100px;' src='$pic'>
										   				 </p>";
									
									break;
								}
							
                       $options .= "<a href=\"?view=$url\" id=\"$key\" $hidden title=\"$tooltip\" $active
                                     data-tippy-placement='bottom' style='$divider_top $divider_bottom'><i class=\"$ico\"></i>&nbsp;$text</a>";

								$introJsIndex++;
								break;
					}

			}
			$options .= "</div><!-- drop down content -->  
                   </div> <!-- drop down -->";

        }

echo "<section class='no-padding'>
       <div class='container'>
        <div class='row'>
          <div class='col-md-12'>
          	<div class='topnav fixed' id='myTopnav'>
					$options
					<span class='dropdown-hint visible-xs'>User Menu</span>
		  			<a href='javascript:void(0);' style='background: transparent' class='navbar-toggle collapsed icon myfunc' onclick='myFunction()'>
		  				
		            <span class='sr-only'>Toggle navigation</span>
		            <span class='icon-bar' style='background:#FFF'></span>
		            <span class='icon-bar' style='background:#FFF'></span>
		            <span class='icon-bar' style='background:#FFF'></span>  			
		  			</a>			
				</div> <!-- topnav -->
			  </div> <!-- col-md-12 -->
			 </div> <!-- row -->
			</div> <!-- container -->
		 </section>
		 <div class='spacer'></div>
		 ";   
 ?>
<script>
/* Toggle between adding and removing the "responsive" class to topnav when the user clicks on the icon */
function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav fixed") {
    x.className += " responsive";
  } else {
    x.className = "topnav fixed";
  }  
}
	var dropdowns = null;
	
	window.addEventListener('DOMContentLoaded', (e)=>{
			console.log('DOMContentLoaded')
			dropdowns = document.getElementsByClassName("dropbtn");	
			
		window.addEventListener('click', (e)=>{
				if (e.target.matches('.dropbtn') || e.target.matches('.dropextra')){
					console.log('menu click');
				} else {
					
					for (var i = 0; i < dropdowns.length; i++) {
						  dropdowns[i].nextElementSibling.style.display = "none"

					}
				}
				
		}, false);
	
		/* Loop through all dropdown buttons to toggle between hiding and showing its dropdown content - This allows the user to have multiple dropdowns without any conflict */
		
		for (var i = 0; i < dropdowns.length; i++) {
			  dropdowns[i].addEventListener("click", function() {
			  //this.classList.toggle("active");
			  var dropdownContent = this.nextElementSibling;
			
			  if (dropdownContent.style.display === "block") {
			  		dropdownContent.style.display = "none";
			  } else {
			  		dropdownContent.style.display = "block";
			  		// hide all other dropdowns
			  		document.querySelectorAll('.dropdown-content').forEach(el=>{
			  			if (el != dropdownContent) el.style.display = "none"
			  		});
			  }
		  });
		}
					
	}, false);
</script>