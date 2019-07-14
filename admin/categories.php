<?php
require_once '../functions.php';

get_login_user();

//数据编辑
if (!empty($_GET['id'])) {

	$edit_result = fetch_data("select * from categories where id={$_GET['id']}");
}

function add_category() {

	if (empty($_POST['name']) || empty($_POST['slug']
	)) {
		$GLOBALS['error'] = '请填写完整表单';
		return;
	}
	$name = $_POST['name'];
	$slug = $_POST['slug'];
	$rows = execute("insert into categories values (null,'{$slug}','{$name}')");
	if ($rows <= 0) {
		$GLOBALS['error'] = '数据插入失败';
		return;
	}
}

function edit_category() {
	if (empty($_POST['name']) || empty($_POST['slug']
	)) {
		$GLOBALS['error'] = '请填写完整表单';
		return;
	}
	$name = empty($_POST['name']) ? $edit_result[0]['name'] : $_POST['name'];
	$edit_result[0]['name'] = $name;
	$slug = empty($_POST['slug']) ? $edit_result[0]['slug'] : $_POST['slug'];
	$edit_result[0]['slug'] = $slug;
	//header('location:/admin/categories.php');
	$rows = execute("update categories set slug='{$slug}',name='{$name}' where id={$_GET['id']}");

	if ($rows <= 0) {
		$GLOBALS['error'] = '数据更新失败';
		return;
	}
  header('location:/admin/users');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (empty($_GET['id'])) {
		add_category();
	} else {
		edit_category();
	}

}

$categories = fetch_data('select * from categories');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <div class="alert alert-danger" <?php echo isset($error) ? '' : 'style="display:none"'; ?>>
        <strong><?php echo isset($error) ? $error : ''; ?></strong>
      </div>
      <div class="row">
        <div class="col-md-4">
          <?php if (isset($edit_result)): ?>
            <form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $edit_result[0]['id']; ?>" method="post">
            <h2>编辑【 <?php echo $edit_result[0]['name']; ?>】目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo isset($edit_result[0]['name']) ? $edit_result[0]['name'] : ''; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo isset($edit_result[0]['slug']) ? $edit_result[0]['slug'] : ''; ?>" >
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">保存</button>
            </div>
          </form>
          <?php else: ?>
            <form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
          <?php endif?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" id="btn_delete" href="categories-delete.php" >批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
            <?php if (!empty($categories)): ?>
              <?php foreach ($categories as $items): ?>
             <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $items['id']; ?>"></td>
                <td><?php echo $items['name']; ?></td>
                <td><?php echo $items['slug']; ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $items['id']; ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/categories-delete.php?id=<?php echo $items['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
           <?php endforeach?>
            <?php endif?>




            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $page_sign = 'categories';?>
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
