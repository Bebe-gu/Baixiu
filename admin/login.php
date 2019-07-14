<?php
require '../config.php';
session_start();
function login() {
	if (empty($_POST['email'])) {
		$GLOBALS['error'] = '邮箱用户名不能为空';
		return;
	}
	//正则表达式验证函数 preg_match
	$reg = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
	if (!preg_match($reg, $_POST['email'])) {
		$GLOBALS['error'] = '邮箱格式不正确';
		return;
	}
	//过滤器函数  filter_var
	// if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	// 	$GLOBALS['error'] = '非法邮箱格式';
	// 	return;
	// }
	if (empty($_POST['password'])) {
		$GLOBALS['error'] = '密码不能为空';
		return;
	}
	$email = $_POST['email'];
	$password = $_POST['password'];
	$avatar = isset($_POST['avatar']) ? $_POST['avatar'] : '';

	$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if (!$connect) {
		exit('<h1>数据库连接失败</h1>');
	}

	$sql = "select * from users where email='{$email}' limit 1";
	$query = mysqli_query($connect, $sql);
	//var_dump($sql);
	if (!$query) {
		$GLOBALS['error'] = '登录失败，请稍后再试';
		return;
	}
	$user = mysqli_fetch_assoc($query);
	if (!$user) {
		$GLOBALS['error'] = '邮箱与密码不匹配';

		return;
	}
	//var_dump(md5($password));
	if ($user['password'] != md5($password)) {
		$GLOBALS['error'] = '密码不匹配';
		return;
	}

	//session 保存用户信息
	$_SESSION['login_user'] = $user;

	header('location:/admin/index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	login();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'logout') {
	if(isset($_COOKIE[session_name()])){  //判断客户端的cookie文件是否存在,存在的话将其设置为过期.
              setcookie(session_name(),'',time()-1,'/');
            }
			unset($_SESSION['login_user']);
			session_destroy();
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/baixiu/static/assets/css/admin.css">
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/animate/animate.css">
</head>
<script src="/baixiu/static/assets/vendors/jquery/jquery.js"></script>
 <script src="/baixiu/static/assets/vendors/nprogress/nprogress.js"></script>
<script type="text/javascript">

$(function(){
	var reg=/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/;
	//var flag=false;
	var flag;
	$('#email').blur(function() {
		var value=$(this).val();
		if (!value||!reg.test(value)||(flag==value)){return;}
		$.get('/admin/api/avatar.php',{email:value}, function(res) {
			if(!res){return;}
			var flag=$('#email').val();
			$(".avatar").fadeOut(function() {
				$(this).load(function() {
					$(this).fadeIn()
				}).attr('src',res);
			});
		});
			flag=$(this).val();
	});
});


</script>
<body>
	 <script>NProgress.start()</script>
  <div class="login">
    <form class="login-wrap <?php echo isset($GLOBALS['error']) ? 'shake animated' : ''; ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" novalidate autocomplete="off">
      <img class="avatar" src="/baixiu/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
     <?php if (isset($GLOBALS['error'])): ?>
        <div class="alert alert-danger">
        <strong><?php echo $GLOBALS['error'] = isset($GLOBALS['error']) ? $GLOBALS['error'] : ''; ?></strong>
      </div>
         <?php endif?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" type="email" class="form-control" placeholder="邮箱" autofocus name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" type="password" class="form-control" placeholder="密码" name="password">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
   <script>NProgress.done()</script>
</body>
</html>
