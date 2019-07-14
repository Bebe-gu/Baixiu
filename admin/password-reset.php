<?php
require_once '../functions.php';
get_login_user();
function reset_pwd() {
	if (md5($_POST['old']) != $_SESSION['login_user']['password']) {
		$GLOBALS['error'] = '旧密码输入不正确';
		return;
	}
	if (empty($_POST['password']) || ($_POST['password'] != $_POST['confirm'])) {
		$GLOBALS['error'] = '两次密码输入有误';
		return;
	}
	$newpwd = md5($_POST['password']);
	$row = execute("update users set password='{$newpwd}' where id={$_SESSION['login_user']['id']}");
	if ($row <= 0) {
		$GLOBALS['error'] = '密码修改失败';
	} else {
		$_SESSION['login_user']['password'] = $newpwd;
		$GLOBALS['error'] = '密码修改成功,3秒后自动跳转个人中心';
	}
	header('refresh:3;url=/admin/profile.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	reset_pwd();
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Password reset &laquo; Admin</title>
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/baixiu/static/assets/css/admin.css">
  <script src="/baixiu/static/assets/vendors/nprogress/nprogress.js"></script>
   <script src="/baixiu/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/baixiu/static/assets/vendors/bootstrap/js/bootstrap.js">
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
   <!-- <?php include 'inc/navbar.php';?> -->
    <div class="container-fluid">
      <div class="page-title">
        <h1>修改密码</h1>
      </div>
      <!-- 有错误信息时展示 -->
       <?php if (isset($error)): ?>
        <div class="alert alert-info">
        <strong><?php echo $error; ?></strong>
      </div>
  <?php endif?>
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="form-group">
          <label for="old" class="col-sm-3 control-label">旧密码</label>
          <div class="col-sm-7">
            <input id="old" class="form-control" type="password" placeholder="旧密码" name="old">
          </div>
        </div>
        <div class="form-group">
          <label for="password" class="col-sm-3 control-label">新密码</label>
          <div class="col-sm-7">
            <input id="password" class="form-control" type="password" placeholder="新密码" name="password">
          </div>
        </div>
        <div class="form-group">
          <label for="confirm" class="col-sm-3 control-label">确认新密码</label>
          <div class="col-sm-7">
            <input id="confirm" class="form-control" type="password" placeholder="确认新密码" name="confirm">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-7">
            <button type="submit" class="btn btn-primary">修改密码</button>
          </div>
        </div>
      </form>
    </div>
  </div>
<?php $page_sign = 'password-reset';?>
  <!-- <?php include 'inc/sidebar.php';?> -->

 </script>
  <script>NProgress.done()</script>
</body>
</html>
