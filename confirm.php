<?php
 // confirmation
 require_once('admin/req/database.php');
  
 $hash = @ $_GET['h'];
 
 if (!strlen(trim($hash)))
    die("No parameters.");
    
 $sql = "SELECT id, activation_hash 
 	 FROM users  
 	 ORDER BY id DESC;";
 $ret = $database->query($sql) or die("Error: {$database->error}");
 
 if (!$ret || !$ret->num_rows){
  die("Cannot confirm: : {$database->error}");
 } else {
  $bfound = false;
  $id = -1;
  
  while ($row = $ret->fetch_array()){
  	$hash_ = $row['activation_hash'];
  	$id_   = $row['id'];
  	
  	if ($hash_ == $hash){
  	  $bfound = true;
  	  $id = $id_;
  	}
  }
  
  if (!$bfound){
   die("Cannot confirm. No such request.");
  }
  
  $sql = "UPDATE users 
          SET isactive=1,
              activation_hash='' 
          WHERE id=$id";
  $ret = $database->query($sql) or die("Cannot confirm: {$database->error}");;
  if (!$ret){
    die("Failed to activate your account.");
  }
  
  echo "You have successfully confirmed your account.";
  echo "<meta http-equiv='refresh' content='2;https://gemdiamonds.com.na/proto/agridb/?view=admin'>";
 }
?>