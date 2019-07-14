<?php

include_once '../../config.php';

//$email = 'gc@qq.com';

if (empty($_GET['email'])) {
	exit('缺少邮箱参数');
}
$email = $_GET['email'];
$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$connect) {
	exit('<h1>数据库连接失败</h1>');
}
$sql = "select avatar from users where email='{$email}' limit 1";
$query = mysqli_query($connect, $sql);
//var_dump($sql);
if (!$query) {
	exit('查询失败');
}
$row = mysqli_fetch_assoc($query);
echo $row['avatar'];
?>