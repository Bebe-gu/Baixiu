<?php
require_once '../functions.php';
if (empty($_GET['id'])) {
	exit('缺少必要参数');
}
// if ($_GET['id']) {
// 	echo $_GET['id'];
// 	//exit('非法参数');
// }
var_dump("delete from categories where id in ({$_GET['id']})");

$rows = execute("delete from categories where id in ({$_GET['id']})");

header('location:/admin/categories.php');

?>