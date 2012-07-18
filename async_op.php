<?php
// 清除缓冲内容
ob_end_clean();
// 返回200成功
header('HTTP/1.1 200 Ok');
// 连接关闭
header("Connection: close");
 
ob_start();
echo 'running';
// 获取网页文件的长度
$size=ob_get_length();
echo $size;
header("Content-Length: $size");
// 输出缓冲
ob_end_flush();
flush();
 
// 测试 睡眠10秒, 之后生成test.txt文件, 里面写当前的时间截
sleep(10);
set_time_limit(0);
$f=fopen('test.txt','a ');
fwrite($f,time()." ");
?>