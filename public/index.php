<?php 
include './api/controllers/ReturnResult.php';
$path = './api/controllers/Conection.php';
$title = '';

if (file_exists($path))
{	
    $title = getTitle();
}else{
	$title = '想個好名字吧';
}

function getTitle(){
  $title = $_SERVER['REQUEST_URI'];
  $urlarry = explode("/",$_SERVER['REQUEST_URI']);
  $name = '';
  $UUID = '';
  $sqlarry = [];
  $ss = "";
  
  $name = $urlarry[1];
  if(count($urlarry) >= 3){
  	$UUID = urldecode($urlarry[2]);
  }
  if ($name != "" &&  $name != "home"){
      $pagesql = "(SELECT PAGENAME
                     FROM title
                    WHERE NAME = ?)";
      $ss = $ss."s";
      array_push($sqlarry,$name);
  } else {
      $pagesql = "''";
  }    
  
  if ($UUID != ""){
    $topicsql = "(SELECT TOPIC
                    FROM article
                   WHERE UUID = ?)";
    $ss = $ss."s";
    array_push($sqlarry,$UUID);
  } else {
      $topicsql = "''";
  }

  $sql ="SELECT PAGENAME AS HOME,
                ".$pagesql." AS PAGNAME,
                ".$topicsql." AS TOPIC
            FROM title
           WHERE NAME = ?";
		   
  //解析回應資料
  $ss = $ss."s";
  array_push($sqlarry,'home');
  $returnData = json_decode(SelectResult($sql,$ss,$sqlarry),true)[0];
  
  $homename = $returnData['HOME'];
  if($returnData['PAGNAME'] != null){
     if(strlen($returnData['PAGNAME']) > 0){
         $homename = $homename."-".$returnData['PAGNAME'];
     }
  }
  if($returnData['TOPIC'] != null){
    if(strlen($returnData['TOPIC']) > 0){
       $homename = $homename."@".$returnData['TOPIC'];
    }
  }
  return $homename;
}

//查看陣列用
function r($var){
  echo '<pre>';
  print_r($var);
  echo '</pre>';
}
?>
<!DOCTYPE html>
<html lang="">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="icon" href="<%= BASE_URL %>favicon.ico">
    <title><?php echo $title ?></title>
    <meta name="google-site-verification" content="eJzSKHIa-n-JOE7VpCQZ2sH_gCTcIVDcOChcsdzbb3w" />
  </head>
  <body>
    <noscript>
      <strong>We're sorry but <%= htmlWebpackPlugin.options.title %> doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
    </noscript>
    <div id="app"></div>
    <!-- built files will be auto injected -->
  </body>
</html>
