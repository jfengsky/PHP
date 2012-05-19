<?php
/*
* 
* return 返回非常精确的秒数
*
*/

function loadtime(){
	list($_start,$_end) = explode(' ',microtime());
	return $_start + $_end;
}

//example
$start_time = loadtime();
for($i = 0;$i < 1000000;$i++){
	//循环1000000次
}
$end_time = loadtime();
echo $end_time - $start_time;
?>