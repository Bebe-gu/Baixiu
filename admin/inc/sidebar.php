<?php
//require_once 'D:/Apache24/web/baixiu/functions.php';
// 因为这个 sidebar.php 是被 index.php 载入执行，所以 这里的相对路径 是相对于 index.php
// 如果希望根治这个问题，可以采用物理路径解决
require_once '../functions.php';
$page_sign = isset($page_sign) ? $page_sign : '';
// session_start();
$user = get_login_user();
?>

<div class="aside">
    <div class="profile">
      <img class="avatar" src="<?php echo $user['avatar']; ?>">
      <h3 class="name"><?php echo $user['nickname']; ?></h3>
    </div>
    <ul class="nav">
      <li <?php echo $page_sign == 'index' ? 'class="active"' : ''; ?>>
        <a href="index.php"><i class="fa fa-dashboard"></i>仪表盘</a>
      </li>
      <?php $menu_posts = array('posts', 'post-add', 'categories');?>
      <li <?php echo in_array($page_sign, $menu_posts) ? 'class="active"' : ''; ?>>
        <a href="#menu-posts" <?php echo in_array($page_sign, $menu_posts) ? '' : 'class="collapsed"'; ?> data-toggle="collapse">
          <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-posts" class="collapse <?php echo in_array($page_sign, $menu_posts) ? ' in' : ''; ?>">
          <li <?php echo $page_sign == 'posts' ? 'class="active"' : ''; ?>><a href="posts.php">所有文章</a></li>
          <li <?php echo $page_sign == 'post-add' ? 'class="active"' : ''; ?>><a href="post-add.php">写文章</a></li>
          <li <?php echo $page_sign == 'categories' ? 'class="active"' : ''; ?>><a href="categories.php">分类目录</a></li>
        </ul>
      </li>
      <li <?php echo $page_sign == 'comments' ? 'class="active"' : ''; ?>>
        <a href="comments.php"><i class="fa fa-comments"></i>评论</a>
      </li>
      <li <?php echo $page_sign == 'users' ? 'class="active"' : ''; ?>>
        <a href="users.php"><i class="fa fa-users"></i>用户</a>
      </li>
      <?php $menu_settings = array('nav-menus', 'slides', 'settings');?>
      <li <?php echo in_array($page_sign, $menu_settings) ? 'class="active"' : ''; ?>>
        <a href="#menu-settings" <?php echo in_array($page_sign, $menu_settings) ? '' : 'class="collapsed"'; ?> data-toggle="collapse">
          <i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-settings" class="collapse<?php echo in_array($page_sign, $menu_settings) ? ' in' : ''; ?>">
          <li <?php echo $page_sign == 'nav-menus' ? 'class="active"' : ''; ?>><a href="nav-menus.php">导航菜单</a></li>
          <li <?php echo $page_sign == 'slides' ? 'class="active"' : ''; ?>><a href="slides.php">图片轮播</a></li>
          <li <?php echo $page_sign == 'settings' ? 'class="active"' : ''; ?>><a href="settings.php">网站设置</a></li>
        </ul>
      </li>
    </ul>
  </div>
