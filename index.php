<?php
require_once 'config.php';

$str = '2017-07-08 13:14:05';
$time = date('Y年m月d日' . "\r\n" . ' H:i:s', strtotime($str));

var_dump($time);

?>


<h1>前台页面</h1>

