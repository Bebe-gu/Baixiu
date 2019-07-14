<?php 
include_once '../../functions.php';

if (empty($_GET['id'])||empty($_GET['status'])) {
	exit('缺少必要参数');
}
header('Content-Type: application/json');
$sql=sprintf("update comments set status = '%s' where id in (%s)", $_GET['status'], $_GET['id']);
$rows=execute($sql);
echo json_encode($rows);
 ?>