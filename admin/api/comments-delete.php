<?php
require_once '../../functions.php';

if (empty($_GET['id'])) {
	exit('缺少必要参数');
}

$rows = execute("delete from comments where id in ({$_GET['id']})");

header('Content-Type:application/json');
//header('location:/admin/comments.php');
echo $rows;
?>