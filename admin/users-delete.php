<?php 
require_once '../functions.php';

if (empty($_GET['id'])) {
	exit('缺少必要参数');
}

	$sql="delete from users where id in ({$_GET['id']})";
	var_dump($sql);
	execute($sql);
header('location:'.$_SERVER['HTTP_REFERER']);



 ?>