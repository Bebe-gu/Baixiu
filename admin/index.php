<?php
require_once '../config.php';
require_once '../functions.php';
//require_once dirname(__FILE__) . '../functions.php';
get_login_user();

$posts = fetch_data('select count(1) from posts');
$drafted = fetch_data("select count(1) from posts where status='drafted'");
$categories = fetch_data('select count(1) from categories');
$comments = fetch_data('select count(1) from comments');
$help = fetch_data("select count(1) from comments where status='help'");
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
  <script src="/baixiu/static/assets/vendors/chart/Chart.js"></script>
  <script src="/baixiu/static/assets/vendors/echarts/echarts.js"></script>
  <style>
    #pie {
      width: 500px;
      height: 230px;
    }
  </style>
</head>
<body>
  <script>NProgress.start()</script>
  <div class="main">
<?php include 'inc/navbar.php';?>
    <div class="container-fluid">
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $posts[0]['count(1)']; ?></strong>篇文章（<strong><?php echo $drafted[0]['count(1)']; ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo $categories[0]['count(1)']; ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $comments[0]['count(1)']; ?></strong>条评论（<strong><?php echo $help[0]['count(1)']; ?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4">
          <!-- <canvas id="chart"></canvas> -->
          <div id="pie"></div>
        </div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>

<?php $page_sign = 'index';?>
<?php include 'inc/sidebar.php';?>


  <script src="/baixiu/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/baixiu/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <!-- <script>
    var ctx = document.getElementById('chart').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'pie',
      data: {
        datasets: [
          {
            data: [<?php echo $posts[0]['count(1)']; ?>, <?php echo $categories[0]['count(1)']; ?>, <?php echo $comments[0]['count(1)']; ?>],
            backgroundColor: [
              'red',
              'green',
              'yellow',
            ]
          },
          {
            data: [<?php echo $posts[0]['count(1)']; ?>, <?php echo $categories[0]['count(1)']; ?>, <?php echo $comments[0]['count(1)']; ?>],
            backgroundColor: [
              'red',
              'green',
              'yellow',
            ]
          }
        ],

        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: [
          '文章',
          '分类',
          '评论'
        ]
      }
    });
  </script> -->
<script>
var myChart = echarts.init(document.getElementById('pie'));
var option = {
    title : {
        text: '站点内容统计',
        //subtext: '纯属虚构',
        x:'center'
    },
    tooltip : {
        trigger: 'item',
        formatter: "{a} <br/>{b} : {c} ({d}%)"
    },
    legend: {
        orient: 'vertical',
        left: 'left',
        data: ['文章','文章草稿','分类','文章评论','待审核评论']
    },
    series : [
        {
            name: '数据统计',
            type: 'pie',
            radius : '55%',
            center: ['50%', '60%'],
            data:[
                {value:<?php echo $posts[0]['count(1)']; ?>, name:'文章'},
                {value:<?php echo $drafted[0]['count(1)']; ?>, name:'文章草稿'},
                {value:<?php echo $categories[0]['count(1)']; ?>, name:'分类'},
                 {value:<?php echo $comments[0]['count(1)']; ?>, name:'文章评论'},
                {value:<?php echo $help[0]['count(1)']; ?>, name:'待审核评论'}
            ],
            itemStyle: {
                emphasis: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }
    ]
};
myChart.setOption(option);
</script>
</body>
</html>
