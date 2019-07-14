<?php
require_once '../functions.php';
//筛选-------------------------------------------------------------
$where = 'and 1=1';
$search = '';
if (isset($_GET['category']) && $_GET['category'] != 'all') {
	$where .= " and categories.id={$_GET['category']}";
	$search .= '&category=' . $_GET['category'];
}
if (isset($_GET['statu']) && $_GET['statu'] != 'all') {
	$where .= " and posts.`status`='{$_GET['statu']}'";
	$search .= '&statu=' . $_GET['statu'];
}
//---------------------------------------------------------------------
//分页功能
$curPage = empty($_GET['page']) ? 1 : (int) $_GET['page'];
//一页显示多少行
$posts_sum = fetch_data("select count(1) as num from posts,categories,users where posts.category_id=categories.id and posts.user_id=users.id {$where}");
$rowsPerPage = 10; //点击当前页获取的页面
$offset = ($curPage - 1) * $rowsPerPage; //(n-1)*一页页数
//总页数(最大页码)
$totalpage = (int) ceil((int) $posts_sum[0]['num'] / $rowsPerPage);

//页码超范围跳转

//---------------------------------------------------------------------------------------------------
//判断是否查询到数据，没有给出提示
if ($curPage == 0 || $totalpage == 0) {
	$GLOBALS['error'] = '没有查询到数据';
} else {
	$posts = fetch_data("select posts.id,posts.title,users.nickname,categories.`name`,posts.created,posts.`status` from posts,categories,users where posts.category_id=categories.id and posts.user_id=users.id {$where} ORDER BY posts.created DESC
  LIMIT {$offset},{$rowsPerPage}");

	if (empty($posts)) {
		$GLOBALS['error'] = '没有查询到数据';
	}
	if ($curPage > $totalpage) {
		header('location:/admin/posts.php?page=' . $totalpage . $search);
	}
	if ($curPage < 1) {
		header('location:/admin/posts.php?page=1' . $search);
	}
}
//var_dump($posts_sum[0]['num']);
$visiables = 5; //分页按钮个数
$region = ($visiables - 1) / 2; //当前页左右按钮个数
$begin = $curPage - $region; //开始按钮下标
$end = $curPage + $region; //结束按钮下标
//最小页码超范围
if ($begin < 1) {
	$begin = 1; //设置最小1
	$end = $totalpage > $visiables ? $visiables : $totalpage;
}
//最大页码超范围
if ($end > $totalpage) {
	$end = $totalpage; //最大数为最大页数
	$begin = $end - ($visiables - 1) <= 1 ? 1 : $end - ($visiables - 1);
}
//上一页
$prev = $curPage - 1 <= 1 ? 1 : $curPage - 1;
//下一页
$next = $curPage + 1 >= $totalpage ? $totalpage : $curPage + 1;

//所有文章分类
//var_dump($posts);
$categories = fetch_data('select * from categories');
//当前文章分类
// $categories = fetch_data('select count(*),name from categories group by name having count(*)>=1');
$status = fetch_data('select count(*),status from posts group by status having count(*)>=1');

function convert_status($status) {
	$list = array('published' => '已发布', 'published' => '已发布', 'drafted' => '草稿', 'trashed' => '回收站');
	return isset($list[$status]) ? $list[$status] : '未知状态';
}

//var_dump($url);

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>

        <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($error)): ?>
        <div class="alert alert-danger">
        <strong><?php echo $error; ?></strong>
      </div>
      <?php endif?>

      <div class="page-action">
        <!-- show when multiple checked -->
        <a id="btn_delete" class="btn btn-danger btn-sm" href="javascript:;"  >批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="search">
          <select  class="form-control input-sm" name="category">
            <option value="all">所有分类</option>
            <?php if (!empty($categories)): ?>
              <?php foreach ($categories as $items): ?>
              <option value="<?php echo $items['id']; ?>" <?php echo isset($_GET['category']) && $_GET['category'] == $items['id'] ? ' selected' : ''; ?>><?php echo $items['name']; ?></option>
            <?php endforeach?>
            <?php endif?>
          </select>
          <select name="statu" class="form-control input-sm">
            <option value="all">所有状态</option>
            <?php if (!empty($status)): ?>
              <?php foreach ($status as $items): ?>
               <option value="<?php echo $items['status']; ?>" <?php echo isset($_GET['statu']) && $_GET['statu'] == $items['status'] ? ' selected' : ''; ?>><?php echo convert_status($items['status']); ?></option>
            <?php endforeach?>
            <?php endif?>

          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href='?page=1<?php echo $search; ?>'>首页</a></li>
          <li><a href="?page=<?php echo $prev . $search; ?>">上一页</a></li>
       <?php for ($i = $begin; $i <= $end; $i++): ?>
             <li <?php echo $curPage == $i ? " class='active'" : ''; ?>><a href="?page=<?php echo $i . $search; ?>"><?php echo $i ?></a></li>
          <?php endfor?>
          <li><a href="?page=<?php echo $next . $search; ?>">下一页</a></li>
          <li><a href='?page=<?php echo $totalpage . $search; ?>'>尾页</a></li>
          <li><a>共<?php echo $totalpage; ?>页</a></li>
          <!-- <li><input type="button" value="确定"></li> -->
          
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($posts)): ?>
             <?php foreach ($posts as $items): ?>
            <tr>
            <td class="text-center"><input type="checkbox" data-id="<?php echo $items['id']; ?>"></td>
            <td><?php echo $items['title']; ?></td>
            <td><?php echo $items['nickname']; ?></td>
            <td><?php echo $items['name']; ?></td>
            <td class="text-center"><?php echo date('Y年m月d日<b\r> H:i:s', strtotime($items['created'])); ?></td>
            <td class="text-center"><?php echo convert_status($items['status']); ?></td>
            <td class="text-center">
              <a href="/admin/post-add.php?id=<?php echo $items['id'] . $search; ?>" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/post-delete.php?id=<?php echo $items['id'] . $search; ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach?>
          <?php endif?>

        </tbody>
      </table>
    </div>
  </div>

<?php $page_sign = 'posts';?>
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
