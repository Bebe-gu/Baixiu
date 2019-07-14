<?php
require_once '../functions.php';
get_login_user();

function update_info() {
	global $user;

	if (empty($_POST['slug'])) {
		$GLOBALS['error'] = '别名不能为空';
		return;
	}

	if (empty($_POST['nickname'])) {
		$GLOBALS['error'] = '昵称不能为空';
		return;
	}
//判断用户是否上传
	if (!empty($_POST['avatar-src'])) {
		//如果用户上传新图像，新数据覆原来数据，没有则默认原来图像
		$user[0]['avatar'] = $_POST['avatar-src'];
	}

	//新数据覆盖原来数据
	$user[0]['slug'] = $_POST['slug'];
	$_SESSION['login_user']['avatar'] = $user[0]['avatar'];
	$user[0]['nickname'] = $_POST['nickname'];
	$_SESSION['login_user']['nickname'] = $user[0]['nickname'];
	$user[0]['bio'] = $_POST['bio'];
	//var_dump($user);
	$sql = "update users set avatar='{$user[0]['avatar']}',slug='{$user[0]['slug']}',nickname='{$user[0]['nickname']}',bio='{$user[0]['bio']}' where id={$_SESSION['login_user']['id']}";
	$result = execute($sql);
	//var_dump($sql);

	if ($result <= 0) {
		$GLOBALS['error'] = '信息修改失败或没有修改信息';
	} else {
		$GLOBALS['error'] = '信息修改成功';
	}

}
$user = fetch_data("select * from users where id={$_SESSION['login_user']['id']}");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	update_info();
}
//var_dump($user);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/baixiu/static/assets/css/admin.css">
  <script src="/baixiu/static/assets/vendors/nprogress/nprogress.js"></script>
   <script src="/baixiu/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/baixiu/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
   <?php include 'inc/navbar.php';?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>我的个人资料</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($error)): ?>
         <div class="alert alert-info">
        <strong><?php echo $error; ?></strong>
      </div>
      <?php endif?>
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" method="post" name="form">
        <div class="form-group">
          <label class="col-sm-3 control-label">头像</label>
          <div class="col-sm-6">
            <label class="form-image">
              <input id="avatar" type="file" name="avatar">
              <img src="<?php echo $_SESSION['login_user']['avatar']; ?>" id='img'>
              <!-- 隐藏域，保存ajax返回的图片src,用于表单提交 -->
              <input type="hidden" id="avatar-src" name="avatar-src">
              <i class="mask fa fa-upload"></i>
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-3 control-label">邮箱</label>
          <div class="col-sm-6">
            <input id="email" class="form-control" name="email" type="type" value="<?php echo $user[0]['email']; ?>" placeholder="邮箱" readonly>
            <p class="help-block">登录邮箱不允许修改</p>
          </div>
        </div>
        <div class="form-group">
          <label for="slug" class="col-sm-3 control-label">别名</label>
          <div class="col-sm-6">
            <input id="slug" class="form-control" name="slug" type="type" value="<?php echo $user[0]['slug']; ?>" placeholder="slug">
            <p class="help-block">https://zce.me/author/<strong>zce</strong></p>
          </div>
        </div>
        <div class="form-group">
          <label for="nickname" class="col-sm-3 control-label">昵称</label>
          <div class="col-sm-6">
            <input id="nickname" class="form-control" name="nickname" type="type" value="<?php echo $user[0]['nickname']; ?>" placeholder="昵称">
            <p class="help-block">限制在 2-16 个字符</p>
          </div>
        </div>
        <div class="form-group">
          <label for="bio" class="col-sm-3 control-label">简介</label>
          <div class="col-sm-6">
            <textarea id="bio" class="form-control" name="bio" placeholder="这里输入您的简介" cols="30" rows="6"><?php echo $user[0]['bio']; ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6">
            <button type="submit" class="btn btn-primary">更新</button>
            <a class="btn btn-link" href="password-reset.php">修改密码</a>
          </div>
        </div>
      </form>
    </div>
  </div>
<?php $page_sign = 'profile';?>
  <?php include 'inc/sidebar.php';?>
<script type="text/javascript">

  $('input[name=avatar]').on('change', function() {
   var files=$(this).prop('files');
   if (!files.length) return;
   var file=files[0];
   var data=new FormData();
   data.append('avatar',file);
 $.ajax({
   url: '/admin/api/avatar-upload.php',
   type: 'POST',
   data: data,
     processData:false, //需要设置为false，避免jquery对formdata对象的默认处理
     contentType:false, //需要设置为false，默认会加上正确的content-type
     success:function(data){
      $('#img').prop('src',data);
      $('#avatar-src').val(data);
     }
 })
  });

// $('input[name=avatar]').change(function(){
//     // that = $(this)
//     // var form=new FormData();
//     // $file = document.getElementById('avatar')
//     // form.append('file',$file.files[0])
//     $.ajax({
//         type:'POST',
//         url:'/admin/api/avatar-upload.php',
//         data:{},
//         contentType : false, //需要设置为false，默认会加上正确的content-type
//         processData : false, //需要设置为false，避免jquery对formdata对象的默认处理
//         mimeType:"multipart/form-data",
//         dataType:'json',
//         success:function(data){//自己的逻辑处理
//             $('#img').prop('src', data);

//         }
//     });
// });


</script>

  <script>NProgress.done()</script>
</body>
</html>
