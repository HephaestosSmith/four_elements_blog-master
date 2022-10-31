<?php
CONTENTResult("WebAuthn 簡_633f82906dde3");
function CONTENTResult($KEYWORD){
  //SQL語法
  $sql = "SELECT *,
                 date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
            FROM article
           WHERE UUID = ?";
   //解析回應資料
   $ss = "s";
   $arry = [$KEYWORD];
   $returnData = SelectResult($sql,$ss,$arry);
   $Output = OutputResult("","",$returnData);
   echo $Output;
}

function SelectResult($sql,$ss,$param){
  include 'Conection.php';
  try {
    $data = array();
    if($ss != ""){
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($ss,...$param);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
          $data[] = $row;
        }  
        $stmt->close();
        $conn->close();
      } else{
        $query = $conn->query($sql);
        $data = $query->fetch_all(MYSQLI_ASSOC);
        $conn->close();
      }
      return json_encode($data);
  }
  catch(Exception $e) {
    r($e);
  }
}

function OutputResult($msg,$success,$data){
  $jsonData = Tojson($data);
  $result = array('msg' => $msg,
                  'success' => $success,
                  'result' =>$jsonData);
  $Output = Tojson($result); 
  $Output =  str_replace('"{','{',$Output);
  $Output =  str_replace('}"','}',$Output);
  $Output =  str_replace('"[','[',$Output);
  $Output =  str_replace(']"',']',$Output);
  return $Output; 
}

function r($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
  }
?>