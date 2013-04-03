<?php
  /**
   * 获取参数 
   */
  $size = $_GET["size"];
  $_size = explode("x", $size);
  $_width = $_size[0];
  $_height = $_size[1];



  /**
   * 获取文件扩展名
   */
  function get_extension($file) { 
    return pathinfo($file, PATHINFO_EXTENSION); 
  }

  /**
   * 随机获取文件夹下一张图片名 
   */
  $images = array();
  if ($handle = opendir('images/')) {
    while (false !== ($file = readdir($handle))) {
       if ($file != "." && $file != ".." && get_extension($file) == 'jpg') {
           array_push($images, $file);
       };
    }
    $rand_img = $images[array_rand($images,1)];
    closedir($handle);
  }

  /**
   * 渲染图像
   */
  header('Content-Type:image/jpeg');
  // 获取图片原来的宽度和高度
  list($width,$height) = getimagesize('images/'.$rand_img);

  // 缩放图片
  // $_width = $width * 0.2;
  // $_height = $height * 0.2;

  // 获取图像比例
  $pec_width = $_width/$width;
  $pec_height = $_height/$height;
  if($pec_width > $pec_height){
    $perc = round($pec_width, 3) + 0.001;
    $new_width = $width * $perc;
    $new_height = $height * $perc;
  }else{
    $perc = round($pec_height, 3) + 0.001;
    $new_width = $width * $perc;
    $new_height = $height * $perc;
  }

  // $width = $width * 0.2;
  // $height = $height * 0.2;

  $console = $pec_width.'x'.$pec_height;

  // 创建一张新图
  $im = imagecreatetruecolor($_width, $_height);

  // 白色画笔
  $white = imagecolorallocate($im,255,255,255);
  // 载入原图
  $_im = imagecreatefromjpeg('images/'.$rand_img);

  // 将原图拷贝到新图上输出
  // $_im2 = imagecopyresampled($im, $_im, 0, 0, 0, 0, $_width, $_height, $width, $height);
  $_im2 = imagecopyresampled($im, $_im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
  // imagecopyresized($im, $_im, 0, 0, 0, 0, $_width, $_height, $width, $height);
  imagecopy($im, $_im2, 0, 0, 0, 0, $_width, $_height);
  // 字体库0
  if(PHP_OS == 'Darwin'){
    $font = '/Library/Fonts/Hei.ttf';
  }else{
    $font = 'C:\WINDOWS\Fonts\SIMHEI.TTF';
  }
  // $font = '/Library/Fonts/Hei.ttf';
  // $font_size = ceil(40 * $pec_width);
  // if($font_size < 14){
  //   $font_size = 14;
  // }
  $font_size = 20;
  $font_top = $_height - 20;
  $font_left = $_width - 160;
  imagettftext($im, $font_size, 0, $font_left, $font_top, $white, $font, $size);
  // 写入水印
  // imagestring($im, 5, 50, 50, $font_size, $white);

  // 输出新图 第二个参数过渡 第三个参数是清晰度
  imagejpeg($im,null,100); 


  // 销毁新图
  imagedestroy($im);
  // 销毁原图
  imagedestroy($_im);
  imagedestroy($_im2);
?>