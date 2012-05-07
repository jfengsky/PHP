<?php
$header = array(
    'js' => 'Content-Type: application/x-javascript',
    'css' => 'Content-Type: text/css'
);
$req = $_REQUEST;
//$host = "http://".$_SERVER["SERVER_NAME"]."/";
$path = $req["p"];
$type = $req['t'];
if(empty($type) || !$header[$type]) $type = "js";

//待合并的文件列表
$files = explode(",", $req["s"]);

//设置临时目标文件的文件名
$target = get_name();
$target_min = $target."-min.".$type;
$target = $target.".".$type;

//设置输出文件Mine-type
header($header[$type]);
$buffer = "/*** $target combined in ".date("Y-m-d H:i:s")." ***/".chr(10);

//读取源文件
foreach($files as $item){
  $item = $path.$item;
  $buffer .= @file_get_contents($item).chr(10);
}

//中文字符转换为unicode码
$buffer = iconv("gbk","utf-8",$buffer);
$buffer = utf8_unicode($buffer);

//创建临时合并文件
$fp = fopen($target,"w+");
fputs($fp, $buffer);
fclose($fp);


//创建临时压缩文件
if(isset($req["compress"])){
  if($type == "js"){
    $cmd = "java -jar compiler.jar --js $target --js_output_file $target_min";
  } else if($type == "css"){
    $cmd = "java -jar yuicompressor.jar -o $target_min $target";
  }
  if(!empty($cmd)){
    shell_exec($cmd);
    $buffer = @file_get_contents($host.$target_min);
  }
}

//删除临时文件并打印内容
shell_exec("del $target $target_min");
print_r($buffer);

function get_name(){
  $t = explode(" ", microtime());
  return md5($_COOKIE["PHPSESSID"].$t[1].substr($t[0],2));
}
function utf8_unicode($s){
  $s = iconv('UTF-8', 'UCS-2', $s);
  $len = strlen($s);
  $str = '';
  for ($i = 0; $i < $len - 1; $i = $i + 2){
    $c = $s[$i];
    $c2 = $s[$i + 1];
    if (ord($c) > 0){
      $str .= '\u'.base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
    } else {
      $str .= $c2;
    }
  }
  return $str;
}

?>