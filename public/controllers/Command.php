<?php
  include 'ReturnResult.php';
  main();
  function main(){
    $commandType = $_POST['commandType'];
    
    switch($commandType){
       case "login":
        LoginResult($_POST['username'],$_POST['password']);
        break;
       case "check":
        CheckResult();
        break;
       case "checkPOWER":
        CheckPOWERResult();
        break;
       case "post":
        PostResult();
        break;
       case "getAticle":
         AticleResult($_POST['SEARCHTYPE'],$_POST['KEYWORD']);
         break;
       case  "getMAINCATEGORYS":
         MAINCATEGORYSResult();
         break;
       case  "getSUBCATEGORYS":
         SUBCATEGORYSResult();
         break;
       case  "delete":
         DeleteResult($_POST['UUID']);
         break;
       case  "update":
         UpdateResult($_POST['UUID'],$_POST['MTDT']);
         break;
       case  "gethomename":
         HomeNameResult();
         break;
       case "getTitle":
         getTitle();
         break;
       case "getALLCATEGORYS":
         ALLCATEGORYSResult();
         break;
          
    }
  }
  
  //回傳檢查結果 FOR VUE
  function AticleResult($SEARCHTYPE,$KEYWORD){
    switch($SEARCHTYPE){
      case "default":
        DefaultResult();
       break;
      case "KEYWORD":
        $KEYWORD = "%".$KEYWORD."%";
        KeywordResult($KEYWORD);
       break;
      case "CONTENT":
        CONTENTResult($KEYWORD);
       break;
       case "CATEGORY":
        CATEGORYResult($KEYWORD);
        break;
   }
  }
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
  {
  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
  }

  function str_to_utf8 ($str = '') {
  
  $current_encode = mb_detect_encoding($str, array("ASCII","GB2312","GBK",'BIG5','UTF-8'));
  
  $encoded_str = mb_convert_encoding($str, 'UTF-8', $current_encode);
  
  return $encoded_str;
  
  }

  //登入
  function LoginResult($username,$password){
     //SQL語法
     $sql = "SELECT *
               FROM member
              WHERE USERNAME = ?
                AND PASSWORD = ?";
     $ss = 'ss';
     $params = [$username,$password];
     //解析回應資料    
     $returnData = json_decode(SelectResult($sql,$ss,$params), true);
     if(count($returnData)> 0){
        //產生TOKEN
        $token = md5(uniqid());
        $data = $returnData[0];
        $authorname = $data["AUTHORNAME"];
        $sql = "UPDATE member
                   SET TOKEN = ?
                 WHERE member.USERNAME = ?
                   AND member.PASSWORD = ?";
        $ss = 'sss';
        $params = [$token,$username,$password];
        //解析回應資料    
        if(CommandResult($sql,$ss,$params)){
            $data = array('TOKEN' => $token,
                          'username' =>$username,
                          'authorname' =>$authorname);
            OutputResult("","1",$data);
         }
     }
     else{
      $arr = array('null' => "");
      OutputResult("查無使用者","0",$arr);
     }
  }
  //回傳檢查結果 FOR VUE
  function CheckResult(){
  if(isset($_COOKIE['username'])) {
  //SQL語法
  $sql = "SELECT *
            FROM member
           WHERE USERNAME = ?
             AND TOKEN = ?"; 
   $ss = 'ss';
   $params = [$_COOKIE['username'],$_COOKIE['TOKEN']];
   //解析回應資料    
   $returnData = json_decode(SelectResult($sql,$ss,$params), true);
   if(count($returnData)> 0){
   $arr = array('null' => "");
   OutputResult("","1",$arr);
   }
   else{
   $arr = array('null' => "");
   OutputResult("查無使用者","0",$arr);
   }
  }else{
    $arr = array('null' => "");
    OutputResult("查無使用者","0",$arr);
  }
  }
  //回傳檢查結果 FOR PHP
  function check(){
    //SQL語法
  $sql = "SELECT *
            FROM member
           WHERE USERNAME = ?
             AND TOKEN = ?"; 
     $ss = 'ss';
     $params = [$_COOKIE['username'],$_COOKIE['TOKEN']];
     //解析回應資料    
     $returnData = json_decode(SelectResult($sql,$ss,$params), true);
     if(count($returnData)> 0){
        return true;
     }
     else{
        return false;
     }
  }
  //發表
  function PostResult(){
     if(check()){
      InsertMAINCATEGORY();
      $uniqid = uniqid();
      $TOPIC = mb_substr( strip_tags($_POST['content']) , 0 , 10,'UTF-8');
      $UUID = mb_substr( strip_tags($_POST['content']) , 0 , 10,'UTF-8')."_".$uniqid;
      //SQL語法
      $sql = "INSERT INTO article 
                         (`UUID`,
                          `CONTENT`,
                          `TOPIC`,
                          `AUTHOR`,
                          `POWER`,
                          `CATEGORY`)
                   VALUES(?,
                          ?,
                          ?,
                          ?,
                          ?,
                          ?)";
      $ss="ssssss";
      $params = [$UUID, $_POST['content'], $TOPIC, $_COOKIE['authorname'], $_POST['POWER'],$_POST['SUBCATEGORY']];
      //解析回應資料     
      if(CommandResult($sql,$ss,$params)){
          $arr = array('null' => "");
          OutputResult("","1",$arr);
      }
      else{
        $arr = array('null' => "");
        OutputResult("沒有發表權限","0",$arr);
      }
     }
     else{
       $arr = array('null' => "");
       OutputResult("沒有發表權限","0",$arr);
     }
  }

  //回傳一般查詢結果 FOR VUE
  function DefaultResult(){
  if(isset($_COOKIE['username'])) {
    if(check()){
     if (isset($_POST['CREATETIME'])){
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
               WHERE POWER < (SELECT POWER
                                FROM member
                               WHERE USERNAME = ?
                                 AND TOKEN =?)
                 AND CREATETIME < ?
               ORDER BY CREATETIME DESC
               LIMIT 5";
       //解析回應資料
       $ss = "sss";
       $arry = [$_COOKIE['username'],$_COOKIE['TOKEN'],$_POST['CREATETIME']];
       $returnData = SelectResult($sql,$ss,$arry);
     }else{
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
               WHERE POWER < (SELECT POWER
                                FROM member
                               WHERE USERNAME = ?
                                 AND TOKEN =?)
               ORDER BY CREATETIME DESC
               LIMIT 5";

       //解析回應資料    
       $ss = "ss";
       $arry = [$_COOKIE['username'],$_COOKIE['TOKEN']];
       $returnData = SelectResult($sql,$ss,$arry);
     }
      
      if(strlen($returnData)> 0){    
        OutputResult("","1",$returnData);
      }
      else{
        $arr = array('null' => "");
        OutputResult("","1",$arr);
      }
  }else{
    defaultSearch();
  }
  }else{
    defaultSearch();
  }
  }
  function defaultSearch(){
    //loading使用
    if (isset($_POST['CREATETIME'])){
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
              WHERE POWER = 0
                AND CREATETIME < ?
               ORDER BY CREATETIME DESC
              LIMIT 5";
      //解析回應資料    
       $ss = "s";
       $arry = [$_POST['CREATETIME']];
       $returnData = SelectResult($sql,$ss,$arry);
    }
    else{
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
              WHERE POWER = 0
                AND 1 = ?
               ORDER BY CREATETIME DESC
              LIMIT 5";
      //解析回應資料    
       $ss = "s";
       $arry = [1];
       $returnData = SelectResult($sql,$ss,$arry);
    }
    if(strlen($returnData)> 0){
    OutputResult("","1",$returnData);
    }
    else{
    $arr = array('null' => "");
    OutputResult("","1",$arr);
    }
  }
  
  //回傳關鍵字查詢結果 FOR VUE
  function KeywordResult($KEYWORD){
    if(isset($_COOKIE['username'])) {
      if(check()){
       if (isset($_POST['CREATETIME'])){
        //SQL語法
        $sql = "SELECT *,
                       date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                  FROM article
                 WHERE POWER < (SELECT POWER
                                  FROM member
                                 WHERE USERNAME = ?
                                   AND TOKEN =?)
                   AND CREATETIME < ?
                   AND CONTENT LIKE ?
                 ORDER BY CREATETIME DESC
                 LIMIT 5";
         //解析回應資料
         $ss = "ssss";
         $arry = [$_COOKIE['username'],$_COOKIE['TOKEN'],$_POST['CREATETIME'],$KEYWORD];
         $returnData = SelectResult($sql,$ss,$arry);
       }else{
        //SQL語法
        $sql = "SELECT *,
                       date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                  FROM article
                 WHERE POWER < (SELECT POWER
                                  FROM member
                                 WHERE USERNAME = ?
                                   AND TOKEN =?)
                   AND CONTENT LIKE ?
                 ORDER BY CREATETIME DESC
                 LIMIT 5";
  
         //解析回應資料    
         $ss = "sss";
         $arry = [$_COOKIE['username'],$_COOKIE['TOKEN'],$KEYWORD];
         $returnData = SelectResult($sql,$ss,$arry);
       }
        
        if(strlen($returnData)> 0){    
          OutputResult("","1",$returnData);
        }
        else{
          $arr = array('null' => "");
          OutputResult("","1",$arr);
        }
    }else{
      KeywordDefaultSearch($KEYWORD);
    }
    }else{
      KeywordDefaultSearch($KEYWORD);
    }
    }
  function KeywordDefaultSearch($KEYWORD){
    //loading使用
    if (isset($_POST['CREATETIME'])){
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
              WHERE POWER = 0
                AND CREATETIME < ?
                AND CONTENT LIKE ?
               ORDER BY CREATETIME DESC
              LIMIT 5";
      //解析回應資料    
       $ss = "ss";
       $arry = [$_POST['CREATETIME'],$KEYWORD];
       $returnData = SelectResult($sql,$ss,$arry);
    }
    else{
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
              WHERE POWER = 0
                AND 1 = ?
                AND CONTENT LIKE ?
               ORDER BY CREATETIME DESC
              LIMIT 5";
      //解析回應資料    
       $ss = "ss";
       $arry = [1,$KEYWORD];
       $returnData = SelectResult($sql,$ss,$arry);
    }
    if(strlen($returnData)> 0){
    OutputResult("","1",$returnData);
    }
    else{
    $arr = array('null' => "");
    OutputResult("","1",$arr);
    }
  }
  
  //回傳關鍵字查詢結果 FOR VUE
  function CATEGORYResult($KEYWORD){
    if(isset($_COOKIE['username'])) {
      if(check()){
       if (isset($_POST['CREATETIME'])){
        //SQL語法
        $sql = "SELECT *,
                       date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                  FROM article
                 WHERE POWER < (SELECT POWER
                                  FROM member
                                 WHERE USERNAME = ?
                                   AND TOKEN =?)
                   AND CREATETIME < ?
                   AND CATEGORY LIKE ?
                 ORDER BY CREATETIME DESC
                 LIMIT 5";
         //解析回應資料
         $ss = "ssss";
         $arry = [$_COOKIE['username'],$_COOKIE['TOKEN'],$_POST['CREATETIME'],$KEYWORD];
         $returnData = SelectResult($sql,$ss,$arry);
       }else{
        //SQL語法
        $sql = "SELECT *,
                       date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                  FROM article
                 WHERE POWER < (SELECT POWER
                                  FROM member
                                 WHERE USERNAME = ?
                                   AND TOKEN =?)
                   AND CATEGORY LIKE ?
                 ORDER BY CREATETIME DESC
                 LIMIT 5";
  
         //解析回應資料    
         $ss = "sss";
         $arry = [$_COOKIE['username'],$_COOKIE['TOKEN'],$KEYWORD];
         $returnData = SelectResult($sql,$ss,$arry);
       }
        
        if(strlen($returnData)> 0){    
          OutputResult("","1",$returnData);
        }
        else{
          $arr = array('null' => "");
          OutputResult("","1",$arr);
        }
    }else{
      CATEGORYDefaultSearch($KEYWORD);
    }
    }else{
      CATEGORYDefaultSearch($KEYWORD);
    }
    }
  function CATEGORYDefaultSearch($KEYWORD){
    //loading使用
    if (isset($_POST['CREATETIME'])){
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
              WHERE POWER = 0
                AND CREATETIME < ?
                AND CATEGORY LIKE ?
               ORDER BY CREATETIME DESC
              LIMIT 5";
      //解析回應資料    
       $ss = "ss";
       $arry = [$_POST['CREATETIME'],$KEYWORD];
       $returnData = SelectResult($sql,$ss,$arry);
    }
    else{
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
              WHERE POWER = 0
                AND 1 = ?
                AND CATEGORY LIKE ?
               ORDER BY CREATETIME DESC
              LIMIT 5";
      //解析回應資料    
       $ss = "ss";
       $arry = [1,$KEYWORD];
       $returnData = SelectResult($sql,$ss,$arry);
    }
    if(strlen($returnData)> 0){
    OutputResult("","1",$returnData);
    }
    else{
    $arr = array('null' => "");
    OutputResult("","1",$arr);
    }
  }
  //回傳分類 FOR VUE
  function MAINCATEGORYSResult(){
    $ss = '';
    $params = [];
    if(isset($_POST['SUBCATEGORY'])){
        //SQL語法
        $sql = "SELECT A.*,
                       ISNULL(B.CATEGORYNAME) AS FLAG
                  FROM categorys A
                       LEFT JOIN categorys B ON B.MAINCATEGORYID = A.CATEGORYINDEX
                                            AND B.CATEGORYNAME = ?
                 WHERE A.MAINCATEGORYID = ?";
        $ss = 'ss';
        $params = [$_POST['SUBCATEGORY'],0];

    }else{
        //SQL語法
        $sql = "SELECT *
                  FROM categorys
                 WHERE MAINCATEGORYID = ?";
        $ss = 'i';
        $params = [0];
    }
     //解析回應資料    
    $returnData = SelectResult($sql,$ss,$params);
    if(strlen($returnData)> 0){    
      OutputResult("","1",$returnData);
    }
    else{
      $arr = array('null' => "");
      OutputResult("","1",$arr);
    }
  }
  //回傳分類 FOR VUE
  function SUBCATEGORYSResult(){
    //SQL語法
    $sql = "SELECT *
              FROM categorys
             WHERE MAINCATEGORYID = (SELECT CATEGORYINDEX
                                       FROM categorys
                                      WHERE MAINCATEGORYID = 0
                                        AND CATEGORYNAME = ?)";
     //解析回應資料    
    $returnData = SelectResult($sql,"s",[$_POST['MAINCATEGORY']]);
    if(strlen($returnData)> 0){    
      OutputResult("","1",$returnData);
    }
    else{
      $arr = array('null' => "");
      OutputResult("","1",$arr);
    }
  }
  
  //回傳刪除 FOR VUE
  function DeleteResult($UUID){
      if(check()){
         //SQL語法
         $sql = "DELETE 
                   FROM article 
                  WHERE UUID = ?";
         $ss="s";
         $params = [$UUID];
         //解析回應資料     
         if(CommandResult($sql,$ss,$params)){
             $arr = array('null' => "");
             OutputResult("","1",$arr);
         }
         else{
           $arr = array('null' => "");
           OutputResult("刪除失敗","0",$arr);
         }
      }
      else{
        $arr = array('null' => "");
        OutputResult("沒有刪除的權限","0",$arr);
      }
  }
  
  //回傳查詢文章 FOR VUE
  function CONTENTResult($KEYWORD){
    if(isset($_COOKIE['username'])) {
      if(check()){
        //SQL語法
        $sql = "SELECT *,
                       date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                  FROM article
                 WHERE POWER < (SELECT POWER
                                  FROM member
                                 WHERE USERNAME = ?
                                   AND TOKEN =?)
                   AND UUID = ?";
         //解析回應資料
         $ss = "sss";
         $arry = [$_COOKIE['username'],$_COOKIE['TOKEN'],$KEYWORD];
         $returnData = SelectResult($sql,$ss,$arry);
        
        if(strlen($returnData)> 0){    
          OutputResult("","1",$returnData);
          return;
        }
     }
    }else{
        //SQL語法
        $sql = "SELECT *,
                       date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                  FROM article
                 WHERE POWER = 0
                   AND UUID = ?";
         //解析回應資料
         $ss = "s";
         $arry = [$KEYWORD];
         $returnData = SelectResult($sql,$ss,$arry);
        if(strlen($returnData)> 0){    
          OutputResult("","1",$returnData);
          return;
        }
    }
    $arr = array('null' => "");
    OutputResult("","1",$arr);
  }
  //發表
  function UpdateResult($UUID,$MTDT){
    if(check()){
     InsertMAINCATEGORY();
     $today = date('Y/m/d H:i:s');
     
     //SQL語法
     $sql = "UPDATE article 
                SET CONTENT = ?,
                    POWER = ?,
                    CATEGORY = ?,
                    MTDT = ?
              WHERE article.UUID = ?
                AND MTDT = ?";
     $ss="ssssss";
     $params = [$_POST['CONTENT'], $_POST['POWER'], $_POST['SUBCATEGORY'], $today , $UUID,$MTDT];
     //解析回應資料     
     if(CommandResult($sql,$ss,$params)){
         $arr = array('null' => "");
         OutputResult("","1",$arr);
     }
     else{
       $arr = array('null' => "");
       OutputResult("沒有修改權限","0",$arr);
     }
    }
    else{
      $arr = array('null' => "");
      OutputResult("沒有修改權限","0",$arr);
    }
 }
  //回傳檢查結果 FOR VUE
  function CheckPOWERResult(){
    if(isset($_COOKIE['username'])) {
    //SQL語法
    $sql = "SELECT *
              FROM member
             WHERE USERNAME = ?
               AND TOKEN = ?"; 
     $ss = 'ss';
     $params = [$_COOKIE['username'],$_COOKIE['TOKEN']];
     //解析回應資料    
     $returnData = json_decode(SelectResult($sql,$ss,$params), true);
     if(count($returnData)> 0){
         $sql = "SELECT *
                   FROM article
                  WHERE POWER < (SELECT POWER
                                   FROM member
                                  WHERE USERNAME = ?
                                    AND TOKEN =?)
                    AND UUID = ?";
   
         $ss = 'sss';
         $params = [$_COOKIE['username'],$_COOKIE['TOKEN'],$_POST['UUID']];
         //解析回應資料    
         $returnData = json_decode(SelectResult($sql,$ss,$params), true);
         if(count($returnData)> 0){
           $arr = array('null' => "");
           OutputResult("","1",$arr);
         }else{
           $arr = array('null' => "");
           OutputResult("權限不足","0",$arr);
         }
     }
     else{
     $arr = array('null' => "");
     OutputResult("查無使用者","0",$arr);
     }
    }else{
      //SQL語法
      $sql = "SELECT *
                FROM article
               WHERE POWER = 0
                AND UUID = ?"; 
      $ss = 's';
      $params = [$_POST['UUID']];
      //解析回應資料    
      $returnData = json_decode(SelectResult($sql,$ss,$params), true);
      if(count($returnData)> 0){
        $arr = array('null' => "");
        OutputResult("","1",$arr);
      }else{
        $arr = array('null' => "");
        OutputResult("查無使用者","0",$arr);
      }
    }
    }
function HomeNameResult(){
  $sql = "SELECT PAGENAME
            FROM title
           WHERE NAME = ?";
  //解析回應資料
  $ss = "s";
  $arry = ['home'];
  $returnData = SelectResult($sql,$ss,$arry);
  OutputResult("","1",$returnData);
}

function getTitle(){
  $path = './Conection.php';
  if (!file_exists($path)) {
    OutputResult("未安裝","0",[]);
    return;
  }
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
  OutputResult("已安裝完成","1",$title);
}

function InsertMAINCATEGORY(){
  if(!isset($_POST['MAINCATEGORY'])){
    return;
  }
  if($_POST['MAINCATEGORY'] != ''){
    //SQL語法
    $sql = "SELECT CATEGORYINDEX
              FROM categorys
             WHERE MAINCATEGORYID = 0
               AND CATEGORYNAME = ?";
    $ss = 's';
    $params = [$_POST['MAINCATEGORY']];
    //解析回應資料    
    $returnData = json_decode(SelectResult($sql,$ss,$params), true);
    if(count($returnData) == 0){
       $sql ="INSERT INTO `categorys` (`CATEGORYINDEX`, `CATEGORYNAME`, `MAINCATEGORYID`) VALUES (NULL, ?, '0');";
       if(CommandResult($sql,$ss,$params)){
         //SQL語法
         $sql = "SELECT CATEGORYINDEX
                   FROM categorys
                  WHERE MAINCATEGORYID = 0
                    AND CATEGORYNAME = ?";
         $returnData = json_decode(SelectResult($sql,$ss,$params), true);
       }
    }
    $data = $returnData[0];
    $CATEGORYINDEX = $data['CATEGORYINDEX'];
    InsertSUBCATEGORY($CATEGORYINDEX);
  }
}
function InsertSUBCATEGORY($MAINCATEGORYID){
  //SQL語法
  $sql = "SELECT CATEGORYINDEX
            FROM categorys
           WHERE MAINCATEGORYID = ?
             AND CATEGORYNAME = ?";
  $ss = 'ss';
  $params = [$MAINCATEGORYID,$_POST['SUBCATEGORY']];
  //解析回應資料    
  $returnData = json_decode(SelectResult($sql,$ss,$params), true);
  if(count($returnData) == 0){
     $sql ="INSERT INTO `categorys` (`CATEGORYINDEX`, `CATEGORYNAME`, `MAINCATEGORYID`) VALUES (NULL, ?, ?);";
     $params = [$_POST['SUBCATEGORY'],$MAINCATEGORYID];
     CommandResult($sql,$ss,$params);
  }
}
function ALLCATEGORYSResult(){
    //SQL語法
    $sql = "SELECT *
              FROM categorys
             WHERE 1 = ?";
     //解析回應資料    
    $returnData = SelectResult($sql,"s",[1]);
    if(strlen($returnData)> 0){    
      OutputResult("","1",$returnData);
    }
    else{
      $arr = array('null' => "");
      OutputResult("","1",$arr);
    }
}

/*function getTitle(){
  $sql = "SELECT PAGENAME
            FROM title
           WHERE NAME = ?";
  //解析回應資料
  $ss = "s";
  $arry = ['home'];
  $returnData = json_decode(SelectResult($sql,$ss,$arry),true)[0];
  $homename = $returnData ['PAGENAME'];

  if(isset($_POST['name']) &&  $_POST['name'] != "" &&  $_POST['name'] != "home"){
     $sql = "SELECT PAGENAME
               FROM title
              WHERE NAME = ?";
     //解析回應資料
     $ss = "s";
     $arry = [$_POST['name']];
     $returnData = json_decode(SelectResult($sql,$ss,$arry),true)[0];
     $pagename = $returnData ['PAGENAME'];
     $homename = $homename."-".$pagename;
  }
  
  if(isset($_POST['param']) &&  $_POST['param'] != ""){
      $param = json_decode($_POST['param'],true);
      if(array_key_exists("UUID",$param)){
          $UUID = $param['UUID'];
          $sql = "SELECT TOPIC
                    FROM article
                   WHERE UUID = ?";
          //解析回應資料
          $ss = "s";
          $arry = [$UUID];
          $returnData = json_decode(SelectResult($sql,$ss,$arry),true)[0];
          $TOPIC = $returnData ['TOPIC'];
          $homename = $homename."@".$TOPIC ;
      }
  }
  
  $title = array('title' => $homename);
  OutputResult("已安裝完成","1",$title);
}*/

//查看陣列用
function r($var){
  echo '<pre>';
  print_r($var);
  echo '</pre>';
}
?>