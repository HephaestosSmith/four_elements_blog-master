<?php
include 'ReturnResult.php';
main();


function main(){
    $path = './Conection.php';
    $arr = array('null' => "");
    if (file_exists($path)) {
        echo OutputResult("已安裝完成","1",$arr);
        //getTitle();
    } else {
        if(!isset($_POST['HOME']) || $_POST['HOME'] == ""){
            echo OutputResult("請輸入網站名稱","0",$arr);
        }else if(!isset($_POST['hostname_gb']) || $_POST['hostname_gb'] == ""){
            echo OutputResult("請輸入資料庫網址","0",$arr);
        }else if(!isset($_POST['database_gb']) || $_POST['database_gb'] == ""){
            echo OutputResult("請輸入資料庫名稱","0",$arr);
        }else if(!isset($_POST['username_gb']) || $_POST['username_gb'] == ""){
            echo OutputResult("請輸入資料庫使用者","0",$arr);
        }else if(!isset($_POST['password_gb']) || $_POST['password_gb'] == ""){
            echo OutputResult("請輸入資料庫密碼","0",$arr);
        }else if(!isset($_POST['USERNAME']) || $_POST['USERNAME'] == ""){
            echo OutputResult("請輸入登入帳號","0",$arr);
        }else if(!isset($_POST['PASSWORD']) || $_POST['PASSWORD'] == ""){
            echo OutputResult("請輸入登入密碼","0",$arr);
        }else if(!isset($_POST['AUTHORNAME']) || $_POST['AUTHORNAME'] == ""){
            echo OutputResult("請輸入發文名稱","0",$arr);
        }else/*if(true)*/{
            try{
                $conn = new mysqli($_POST['hostname_gb'],$_POST['username_gb'],$_POST['password_gb']);
                if(CreateDATABASE($conn)){
                    $db = new mysqli($_POST['hostname_gb'],$_POST['username_gb'],$_POST['password_gb'],$_POST['database_gb']);
                    if($db->connect_error){
                        echo OutputResult("資料庫建立失敗:". $db->connect_error,"0",$arr);
                    } else if (CreateTable($db)){
                        CreateConection();
                        echo OutputResult("安裝完成","1",$arr);
                    }else{
                        echo OutputResult("安裝失敗","0",$arr);
                    }
                }
            }
            catch(Exception $e) {
                echo OutputResult("資料庫連線失敗:".$e->getMessage(),"0",$arr);
            }   
        }
    }
  }
function CreateDATABASE($con){
    //建立資料庫 
    $sql = "CREATE DATABASE ".$_POST['database_gb']." CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
    //解析回應資料    
    $ss = "";
    $arry = [];
    $returnData = InstallCommandResult($con,$sql,$ss,$arry);
    return $returnData;
}
function CreateTable($db){
    //article檢查資料表是否存在
    $query = $db->query("SHOW TABLES LIKE 'article'");
    if(mysqli_num_rows($query) == 0){
      $sql = "CREATE TABLE `article` ( `UUID` TEXT NOT NULL , `CONTENT` TEXT NOT NULL , `TOPIC` VARCHAR(30) NOT NULL , `AUTHOR` VARCHAR(50) NOT NULL , `CREATETIME` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `MTDT` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `POWER` VARCHAR(5) NOT NULL , `CATEGORY` VARCHAR(20) NOT NULL ) ENGINE = InnoDB;";
      $query = $db->query($sql);
    }

    //member檢查資料表是否存在
    $query = $db->query("SHOW TABLES LIKE 'member'");
    if(mysqli_num_rows($query) == 0){
      $sql = "CREATE TABLE `member` ( `USERID` INT(11) NOT NULL , `USERNAME` VARCHAR(20) NOT NULL , `PASSWORD` TEXT NOT NULL , `POWER` VARCHAR(5) NOT NULL , `TOKEN` TEXT NOT NULL , `AUTHORNAME` TEXT NOT NULL , PRIMARY KEY (`USERID`, `USERNAME`)) ENGINE = InnoDB;";
      $query = $db->query($sql);
    }

    //title檢查資料表是否存在
    $query = $db->query("SHOW TABLES LIKE 'title'");
    if(mysqli_num_rows($query) == 0){
      $sql = "CREATE TABLE `title` ( `NAME` VARCHAR(20) NOT NULL , `PAGENAME` TEXT NOT NULL ) ENGINE = InnoDB;";
      $query = $db->query($sql);
    }
    //新增title資料
    $sql = "INSERT INTO `title` (`NAME`, `PAGENAME`) VALUES ('home', ?), ('edited', '編輯'), ('article', '文章'), ('login', '登入'), ('install', '安裝')";
    $addname = $db->prepare($sql);
    $addname->bind_param('s',$_POST['HOME']);
    $addname->execute();
    $addname->close();

    //categorys檢查資料表是否存在
    $query = $db->query("SHOW TABLES LIKE 'categorys'");
    if(mysqli_num_rows($query) == 0){
      $sql = "CREATE TABLE `categorys` ( `CATEGORYINDEX` INT NOT NULL AUTO_INCREMENT , `CATEGORYNAME` TEXT NOT NULL , `MAINCATEGORYID` INT NOT NULL DEFAULT '0' , PRIMARY KEY (`CATEGORYINDEX`)) ENGINE = InnoDB;";
      $query = $db->query($sql);
    }

    //新增資料 
    $sql = "INSERT INTO `member` (`USERID`, `USERNAME`, `PASSWORD`, `POWER`, `TOKEN`, `AUTHORNAME`) VALUES ('1', ?, ?, '5', '', ?)";
    //解析回應資料    
    $ss = "sss";
    $arry = [$_POST['USERNAME'],$_POST['PASSWORD'],$_POST['AUTHORNAME']];
    $returnData = InstallCommandResult($db,$sql,$ss,$arry);
    return $returnData;
}
function CreateConection(){
  try {
    $myfile = fopen("Conection.php", "w") or die("Unable to open file!");  // 建立檔案
    $php = sprintf('<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
header("content-type:text/html; charset=utf-8");
$hostname_gb =  "%s";
$database_gb =  "%s";
$username_gb =  "%s";
$password_gb =  "%s";

// Create connection
$conn = new mysqli($hostname_gb, $username_gb, $password_gb, $database_gb);
$conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
die("Connection failed:" . $conn->connect_error);
}
?>',$_POST['hostname_gb'],$_POST['database_gb'],$_POST['username_gb'],$_POST['password_gb']);
    fwrite($myfile, $php);
    fclose($myfile);  // 關閉檔案
    return true;
}
catch(Exception $e) {
  $msg = 'Message:'.$e->getMessage();
  echo json_encode($msg);
  return false;
}
}


function getTitle(){
    $arry = [];
    $ss = "";
  
    if (isset($_POST['name']) &&  $_POST['name'] != "" &&  $_POST['name'] != "home"){
        $pagesql = "(SELECT PAGENAME
                       FROM title
                      WHERE NAME = ?)";
        $ss = $ss."s";
        array_push($arry,$_POST['name']);
    } else {
        $pagesql = "''";
    }    
    
    if (isset($_POST['param']) &&  $_POST['param'] != ""){
        
      $param = json_decode($_POST['param'],true);
      if(array_key_exists("UUID",$param)){
        $UUID = $param['UUID'];
        $topicsql = "(SELECT TOPIC
                        FROM article
                       WHERE UUID = ?)";
        $ss = $ss."s";
        array_push($arry,$UUID);
      }else{
        $topicsql = "''";
      }
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
    array_push($arry,'home');
    $returnData = json_decode(SelectResult($sql,$ss,$arry),true)[0];
    
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
    $title = array('title' => $homename);
    echo OutputResult("已安裝完成","1",$title);
  }
//查看陣列用
function r($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}
?>