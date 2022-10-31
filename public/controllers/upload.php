<?php
$path = '../img/';
if (!file_exists($path)) {
  mkdir($path, 0777, true);
}
$EXTENSIONname = strtolower(pathinfo( $_FILES['upload']['name'], PATHINFO_EXTENSION));
$tempfile = $_FILES['upload']['tmp_name'];
$filename = uniqid().".".$EXTENSIONname;
$dest = $path.$filename;
while (file_exists($dest)){
  $filename = uniqid().".".$EXTENSIONname;
  $dest = $path.$filename;
}

// 0 寬度 1 高度
/*if ($EXTENSIONname == "png"){
  $imginfo = getimagesize($_FILES['upload']['tmp_name']);  
  $imgwidth = $imginfo[0];
  $imgheight = $imginfo[1];
  
  //產生新圖檔
  $newimage = imagecreatetruecolor($imgwidth ,$imgheight);
  //將顏色塗滿大小
  $black = imagecolorallocate($newimage, 0, 0, 0);
  //將指定顏色變透明
  imagecolortransparent($newimage, $black);
  //將透明畫布畫上原本圖案
  imagecopyresampled($newimage,$tempfile,0,0,0,0,$fullSize_x,$fullSize_y,$width,$height);
  imagealphablending($newimage, false);
  imagesavealpha($newimage, true);
  //儲存圖片
  imagepng($newimage, $dest);
  //銷毀新圖片
  imagedestroy($newimage);
}else{
  copy($tempfile,$dest);// 複製檔案
}*/
copy($tempfile,$dest);// 複製檔案
unlink($tempfile);


echo '{ "url":"'.$_SERVER['HTTP_ORIGIN']."/api/img/".$filename.'"}';

function r($var){
  echo '<pre>';
  print_r($var);
  echo '</pre>';
}
?>