<?php
require_once '../functions.php';



if (!empty($_GET['id'])) {
   $edit_result=fetch_data("select * from users where id={$_GET['id']}");
 }

 function edit_users(){
  if (empty($_POST['email'])) {
    $GLOBALS['error'] = '邮箱不能为空';
    return;
  }
  if (empty($_POST['slug'])) {
    $GLOBALS['error'] = '别名不能为空';
    return;
  }
  if (empty($_POST['nickname'])) {
    $GLOBALS['error'] = '昵称不能为空';
    return;
  }
  if (empty($_POST['password'])) {
    $GLOBALS['error'] = '密码不能为空';
    return;
  }
  $sql="update users set email='{$_POST['email']}',slug='{$_POST['slug']}',nickname='{$_POST['nickname']}',password='{$_POST['password']}' where id={$_GET['id']}";
  $rows=execute($sql);
  if ($rows<=0) {
   $GLOBALS['error'] = '信息添加失败';
   return;
 }

 }
 

function add_users(){
  if (empty($_POST['email'])) {
    $GLOBALS['error'] = '邮箱不能为空';
    return;
  }
  $reg = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
  if (!preg_match($reg, $_POST['email'])) {
    $GLOBALS['error'] = '邮箱格式不正确';
    return;
  }
  if (empty($_POST['slug'])) {
    $GLOBALS['error'] = '别名不能为空';
    return;
  }
  if (empty($_POST['nickname'])) {
    $GLOBALS['error'] = '昵称不能为空';
    return;
  }
  if (empty($_POST['password'])) {
    $GLOBALS['error'] = '密码不能为空';
    return;
  }
$password=md5($_POST['password']);
  $sql="insert into users values (null,'{$_POST['slug']}','{$_POST['email']}','{$password}','{$_POST['nickname']}','/baixiu/static/uploads/demo.jpg',null,'unactivated')";
  $rows=execute($sql);
  if ($rows <= 0) {
    $GLOBALS['error'] = '信息添加失败';
    return;
  } else{
    $GLOBALS['error'] = '信息添加成功';
    return;
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (empty($_GET['id'])) {
    add_users();
  }else{
    edit_users();
  }
  
}
//查询所有用户数据
$users=fetch_data("select * from users");


function convert_status($status){
$list = array('unactivated' => '未激活', 'activated' => '已激活', 'forbidden' => '黑名单', 'trashed' => '已删除');
  return isset($list[$status]) ? $list[$status] : '未知状态';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/baixiu/static/assets/css/admin.css">
  <script src="/baixiu/static/assets/vendors/nprogress/nprogress.js"></script>
    <script src="/baixiu/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/baixiu/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <style>
    
   #btn_delete {
      display:none;
    }
  </style>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
     <?php include 'inc/navbar.php';?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($error)): ?>
        <div class="alert alert-info">
        <strong><?php echo $error; ?></strong>
      </div>
      <?php endif ?>    
      <div class="row">
        <div class="col-md-4">
         <?php if (isset($edit_result)): ?>
          <form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $_GET['id']; ?>">
              <h2>编辑用户：<?php echo isset($edit_result[0]['nickname'])?$edit_result[0]['nickname']:''; ?></h2>

            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="text" placeholder="邮箱" value="<?php echo isset($edit_result[0]['email'])?$edit_result[0]['email']:''; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo isset($edit_result[0]['slug'])?$edit_result[0]['slug']:''; ?>">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称" value="<?php echo isset($edit_result[0]['nickname'])?$edit_result[0]['nickname']:''; ?>">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">编辑</button>
            </div>
             </form>
            <?php else: ?>
              <form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
              <h2>添加新用户</h2>

            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="text" placeholder="邮箱" value="<?php echo isset($_POST['email'])?$_POST['email']:''; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo isset($_POST['slug'])?$_POST['slug']:''; ?>">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称" value="<?php echo isset($_POST['nickname'])?$_POST['nickname']:''; ?>">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="password" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
              </form>
            <?php endif ?>
            
        
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="/admin/users-delete.php?id=<?php echo $items['id']; ?>" id="btn_delete">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $items ): ?>   
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $items['id']; ?>"></td>
                <td class="text-center"><img class="avatar" src="<?php echo $items['avatar']; ?>"></td>
                <td><?php echo $items['email']; ?></td>
                <td><?php echo $items['slug']; ?></td>
                <td><?php echo $items['nickname']; ?></td>
                <td><?php echo convert_status($items['status']);?></td>
                <td class="text-center">
                  <a href="/admin/users.php?id=<?php echo $items['id']; ?>" class="btn btn-default btn-xs">编辑</a>
                  <a href="/admin/users-delete.php?id=<?php echo $items['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php $page_sign = 'users';?>
  <?php include 'inc/sidebar.php';?>
  <script>NProgress.done()</script>
  <script type="text/javascript">
         $(function ($) {
      // 在表格中的任意一个 checkbox 选中状态变化时
      var $tbodyCheckboxs = $('tbody input');
      var $btnDelete = $('#btn_delete');

      // 定义一个数组记录被选中的
      var allCheckeds = [];
      $tbodyCheckboxs.on('change', function () {
      var num=$('tbody :checkbox:checked').length;
      var sum=$('tbody :checkbox').length;
      if (num==sum) {
        $('thead :checkbox').prop('checked',true);
      }else{
        $('thead :checkbox').prop('checked',false);
      }

        var id = $(this).data('id');

        // 根据有没有选中当前这个 checkbox 决定是添加还是移除
        if ($(this).prop('checked')) {
          // allCheckeds.indexOf(id) === -1 || allCheckeds.push(id)
          allCheckeds.includes(id) || allCheckeds.push(id);
        } else {
          allCheckeds.splice(allCheckeds.indexOf(id), 1);
        }

        // 根据剩下多少选中的 checkbox 决定是否显示删除
        allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
        $btnDelete.prop('search', '?id=' + allCheckeds);
      });

      // 找一个合适的时机 做一件合适的事情
      // 全选和全不选
      $('thead input').on('change', function () {
        // 1. 获取当前选中状态
        var checked = $(this).prop('checked');
        // 2. 设置给标体中的每一个
        $tbodyCheckboxs.prop('checked', checked).trigger('change');
        // $tbodyCheckboxs.attr('checked', checked).trigger('change')
      });
    });
  </script>
</body>
</html>
