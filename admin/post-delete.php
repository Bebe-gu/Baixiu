<?php
require_once '../functions.php';

if (empty($_GET['id'])) {
	exit('缺少必要参数');
}

$rows = execute("delete from posts where id in ({$_GET['id']})");

header('location:'.$_SERVER['HTTP_REFERER']);

?>