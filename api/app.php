<?php
    $req = $_GET['view'];
    switch ($req){
        case 'adverts':
            echo "<!DOCTYPE html>
                    <html lang='en'>
                    <head>
                    <meta charset='utf-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no' />
                    
                    <link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>
                    <link href='../lib/bootstrap/bootstrap.3.3.4.min.css' rel='stylesheet' type='text/css'>
                    <script src='../lib/jquery/jquery.1.11.1.min.js'></script>
                    <script src='../lib/bootstrap/bootstrap.3.3.4.min.js'></script>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='row'>
                                <div class='col-md-12'>";

            echo "<h4 class='text-center'>Adverts</h4><HR>";
            
            require_once('../admin/settings.php');
            require_once('../admin/timezone.php');
            require_once('../admin/database.php');
            
            $sql = "SELECT * FROM adverts WHERE active=1 ORDER BY entrydate DESC;";
            $ret = $database->query($sql) or die($database->error);
            if (!$ret || !$ret->num_rows){
                echo "No data returned";
            } else {
                while ($row = $ret->fetch_array()){
                    $img = $row['advert'];
                    $rand1 = mt_rand(1,20);
                    $rand2 = mt_rand(1,20);
                    $rand3 = mt_rand(1,20);
                    
                    echo "<p>
                           <img src='../admin/$img' class='img-responsive thumbnail'>
                           <span class='fa fa-fw fa-thumbs-down' style='color:black'></span> $rand1
                           <span class='fa fa-fw fa-thumbs-up' style='color:blue'></span> $rand2
                           <span class='fa fa-fw fa-heart' style='color:red'></span> $rand3
                          </p>
                          <BR>";
                }
            }
         
            echo    "           </div>
                            </div>
                        </div>
                    </body>
                    </html> ";
            break;
            
        case 'about-kazoza':
            echo "Kazoza is a mobile commerce platform built on an economic principle that: as brands that promote products in an economy, we are equally responsible for the growth of the economy.";
            break;
            
        default:
            echo "Not handled";
            break;
    }
?>