<?php
/*
* 
* return ���طǳ���ȷ������
*
*/

function loadtime(){
	list($_start,$_end) = explode(' ',microtime());
	return $_start + $_end;
}

//example
$start_time = loadtime();
for($i = 0;$i < 1000000;$i++){
	//ѭ��1000000��
}
$end_time = loadtime();
echo $end_time - $start_time;
?>