<?php
require_once '../functions.php';

get_login_user();

if (!empty($_GET['id'])) {
 $posts_result=fetch_data("select * from posts where id={$_GET['id']}");
 //datetime-local控件 value设置的格式为：YYYY-MM-DDTHH:mm:ss 需要加T,替换空格为T正常显示
 $datetime=str_replace(' ','T',$posts_result[0]['created']);

}


//写文章
function edit_posts() { 
  global $posts_result;
	if (empty($_POST['title'])) {
		$GLOBALS['error'] = '文章标题不能为空';
		return;
	}

	if (empty($_POST['content'])) {
		$GLOBALS['error'] = '文章内容不能为空';
		return;
	}
  
  if (!empty($_FILES['feature']['tmp_name'])) {
  if ($_FILES['feature']['error'] != UPLOAD_ERR_OK) {
  exit('[编辑]上传失败');
  }
  $ext = pathinfo($_FILES['feature']['name'], PATHINFO_EXTENSION);
  $target = '../baixiu/static/uploads/img-' . uniqid() . '.' . $ext;
  if (!move_uploaded_file($_FILES['feature']['tmp_name'], $target)) {
  exit('上传移动失败');
  }
   $target=substr($target, 2);
  }else{
    $target=$posts_result[0]['feature'];
  }
  
	if (empty($_POST['slug'])) {
		$GLOBALS['error'] = '文章别名不能为空';
		return;
	}
	
	if (empty($_POST['category'])) {
		$GLOBALS['error'] = '文章分类不能为空';
		return;
	}
	if (empty($_POST['created'])) {
		$GLOBALS['error'] = '发表时间不能为空';
		return;
	}
	if (empty($_POST['status'])) {
		$GLOBALS['error'] = '文章状态不能为空';
		return;
	}
$content_01 = $_POST['content'];//从数据库获取富文本content
$content_02 = htmlspecialchars_decode($content_01);//把一些预定义的 HTML 实体转换为字符
$content_03 = str_replace("&nbsp;","",$content_02);//将空格替换成空
$contents = strip_tags($content_03);//函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
  $sql="update posts set slug='{$_POST['slug']}',title='{$_POST['title']}', feature='{$target}', created='{$_POST['created']}', content='{$contents}', views=0, likes=0, status='{$_POST['status']}', category_id={$_POST['category']} where id={$_GET['id']}";
  $rows=execute($sql);

  if ($rows<=0) {
    $GLOBALS['error']='数据编辑失败或没有更新数据';
  }
  header('location:/admin/posts.php');
}

//编辑文章
function add_posts() {
  if (empty($_POST['title'])) {
    $GLOBALS['error'] = '文章标题不能为空';
    return;
  }
  if (empty($_POST['content'])) {
    $GLOBALS['error'] = '文章内容不能为空';
    return;
  }
  //处理富文本内容
$content_01 = $_POST['content'];//从数据库获取富文本content
$content_02 = htmlspecialchars_decode($content_01);//把一些预定义的 HTML 实体转换为字符
$content_03 = str_replace("&nbsp;","",$content_02);//将空格替换成空
$contents = strip_tags($content_03);//函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容

if (!empty($_FILES['feature']['tmp_name'])) {
  if ($_FILES['feature']['error'] != UPLOAD_ERR_OK) {
  exit('[添加]上传失败error');
  }
  $ext = pathinfo($_FILES['feature']['name'], PATHINFO_EXTENSION);
  $target = '../baixiu/static/uploads/img-' . uniqid() . '.' . $ext;
  if (!move_uploaded_file($_FILES['feature']['tmp_name'], $target)) {
  exit('上传移动失败');
  }
   $target=substr($target, 2);
  }

  if(empty($_FILES['feature']['tmp_name'])){
    $target='';
  }
  
  if (empty($_POST['slug'])) {
    $GLOBALS['error'] = '文章别名不能为空';
    return;
  }
  
  if (empty($_POST['category'])) {
    $GLOBALS['error'] = '文章分类不能为空';
    return;
  }
  if (empty($_POST['created'])) {
    $GLOBALS['error'] = '发表时间不能为空';
    return;
  }
  if (empty($_POST['status'])) {
    $GLOBALS['error'] = '文章状态不能为空';
    return;
  }

  $sql="insert into posts values (NULL, '{$_POST['slug']}', '{$_POST['title']}', '{$target}', '{$_POST['created']}', '{$contents}', 0, 0, '{$_POST['status']}', {$_SESSION['login_user']['id']}, {$_POST['category']})";
  $rows=execute($sql);
  if ($rows<=0) {
    $GLOBALS['error']='数据插入失败';
  }
header('location:/admin/posts.php');
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (empty($_GET['id'])) {
    add_posts();
}else{
edit_posts();
}  
}
$categories = fetch_data("select * from categories");
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/baixiu/static/assets/css/admin.css">
  <script src="/baixiu/static/assets/vendors/nprogress/nprogress.js"></script>
    <script src="/baixiu/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/baixiu/static/assets/vendors/bootstrap/js/bootstrap.js"></script>

  <style>
    #img {
      display:none;
    }
  </style>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php';?>
    <div class="container-fluid">
   <!-- / 有错误信息时展示 -->
   <?php if (isset($error)): ?>
     <div class="alert alert-info">
        <strong><?php echo isset($error) ? $error : ''; ?></strong>
      </div>
   <?php endif?>
      <?php if (!empty($_GET['id'])): ?>
      <div class="page-title">
        <h1>编辑文章</h1>
      </div>
        <form class="row" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $posts_result[0]['id']; ?>" method="post" enctype="multipart/form-data" name="form">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题" value="<?php echo $posts_result[0]['title']; ?>">
          </div>
          <div class="form-group">
              <script id="container" name="content" type="text/plain">
                  <?php echo $posts_result[0]['content']; ?>
              </script>
              <!-- 配置文件 -->
                <script src="/baixiu/static/assets/vendors/ueditor/ueditor.config.js"></script>
                <!-- 编辑器源码文件 -->
                <script  src="/baixiu/static/assets/vendors/ueditor/ueditor.all.js"></script>
              <!-- 实例化编辑器 -->
              <script type="text/javascript">
              var ue = UE.getEditor('container');
              </script>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $posts_result[0]['slug']; ?>">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" id='img'src="<?php echo empty($posts_result[0]['feature'])?'':$posts_result[0]['feature']; ?>">
            <input id="feature" class="form-control" name="feature" type="file">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach ($categories as $items): ?>
                <option value="<?php echo $items['id']; ?>" <?php echo $posts_result[0]['category_id'] === $items['id'] ? 'selected' : ''; ?>><?php echo $items['name']; ?></option>
              <?php endforeach ?>             
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local" value="<?php echo $datetime; ?>">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted" <?php echo $posts_result[0]['status'] === 'drafted' ? 'selected' : ''; ?>>草稿</option>
              <option value="published" <?php echo $posts_result[0]['status'] === 'published' ? 'selected' : ''; ?>>已发布</option>
              <option value="trashed" <?php echo $posts_result[0]['status'] === 'trashed' ? 'selected' : ''; ?>>回收站</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
      <?php else: ?>
      <div class="page-title">
        <h1>写文章</h1>
      </div>
          <form class="row" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="form">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题">
          </div>
          <div class="form-group">
              <script id="container" name="content" type="text/plain">这里写你的初始化内容</script>
              <!-- 配置文件 -->
                <script src="/baixiu/static/assets/vendors/ueditor/ueditor.config.js"></script>
                <!-- 编辑器源码文件 -->
                <script  src="/baixiu/static/assets/vendors/ueditor/ueditor.all.js"></script>
              <!-- 实例化编辑器 -->
              <script type="text/javascript">
              var ue = UE.getEditor('container');
              </script>
             
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" id='img'>
            <input id="feature" class="form-control" name="feature" type="file">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach ($categories as $items): ?>
                <option value="<?php echo $items['id']; ?>"><?php echo $items['name']; ?></option>
              
              <?php endforeach ?>
              
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
              <option value="trashed">回收站</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
      <?php endif ?>
      
    </div>
  </div>
<?php $page_sign = 'post-add';?>
  <?php include 'inc/sidebar.php';?>


  <script>NProgress.done()</script>
</body>
</html>
