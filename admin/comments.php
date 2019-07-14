<?php
require_once '../functions.php';

get_login_user();

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/baixiu/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/baixiu/static/assets/css/admin.css">
  <script src="/baixiu/static/assets/vendors/nprogress/nprogress.js"></script>

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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" id="btn_delete">
          <button class="btn btn-info btn-sm" >批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
          <ul class="pagination pagination-sm pull-right" id="pagination"></ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox" data-id=""></th>
            <th>作者</th>
            <th width="300">评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="160">操作</th>
          </tr>
        </thead>
        <tbody>
          <!-- jsrender 模板引擎 -->
          <script type="text/x-jsrender" id="tmpl">
    {{for data}}
    <tr class="{{: status === 'held' ? 'warning' : status === 'rejected' ? 'danger' : '' }}" data-id="{{: id }}">
      <td class="text-center"><input type="checkbox"></td>
      <td>{{: author }}</td>
      <td>{{: content }}</td>
      <td>《{{: post_title }}》</td>
      <td>{{: created}}</td>
      <td>{{: status === 'held' ? '待审' : status === 'rejected' ? '拒绝' : '准许' }}</td>
      <td class="text-center">
        {{if status === 'held'}}
        <a class="btn btn-info btn-xs btn-edit" href="javascript:;" data-status="approved">批准</a>
        <a class="btn btn-warning btn-xs btn-edit" href="javascript:;" data-status="rejected">拒绝</a>
        {{/if}}
        <a class="btn btn-danger btn-xs btn-delete" href="javascript:;" id="btn-delete">删除</a>
      </td>
    </tr>
    {{/for}}

 </script>
        </tbody>
      </table>
    </div>
  </div>

 <?php $page_sign = 'comments';?>
  <?php include 'inc/sidebar.php';?>
  <script src="/baixiu/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/baixiu/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/baixiu/static/assets/vendors/jsrender/jsrender.js"></script>
   <script src="/baixiu/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>

   <script>
   $(function(){

      var $tbody = $('tbody');
      var $tmpl = $('#tmpl');
      var $pagination = $('#pagination');
      var size=10; //一页显示多少行
      var currentPage=1; //当前页
      var checkedItems = [];

    //分页点击函数
    function loadPageData (page){
          $tbody.fadeOut();
          $.get('/baixiu/admin/api/comments.php', {p:page}, function(res) {
            //删除最后一页的时候，如果没有数据判断当前页是否大于最大页，实现实时刷新页码数
            $('th > input[type=checkbox]').prop('checked', false);
            if (page>res.total_pages) {
              loadPageData(res.total_pages);
              return;
            }
            //删除最后一条数据后，直接返回不执行下面代码
            if (page<=0) {
              return;
            }

          //'destroy' 销毁初始分页组件便于动态更新页码数
          $pagination.twbsPagination('destroy');
          $pagination.twbsPagination({
          totalPages: res.total_pages,
          first: "首页",
          last: "未页",
          prev: '上一页',
          next: '下一页',
          startPage: page,
          visiblePages:5,
          initiateStartPageClick: false,
          onPageClick: function (event, page){
          loadPageData(page);
            }
         });

        var html=$tmpl.render({data:res.data});
        $tbody.html(html).fadeIn();
         currentPage=page; //记录当前页
        });
        }
        loadPageData(currentPage);

    //删除功能
    //--------------------------------------
    $tbody.on('click', '#btn-delete', function(event) {
      var $tr=$(this).parent().parent();
     var id=$tr.data('id');
     $.get('/admin/api/comments-delete.php', {id:id},function(res) {
          if (!res) return
          //$tr.remove();
          loadPageData(currentPage);
     });
    });

     //批准,拒绝修改功能
    //--------------------------------------
    $tbody.on('click', '.btn-edit', function(event) {
      var $tr=$(this).parent().parent();
     var id=$tr.data('id');
     var status=$(this).data('status');
     $.get('/admin/api/comments-status.php?id='+id, {status:status},function(res) {
          if (!res) return
          //$tr.remove();
          loadPageData(currentPage);
     });
    });

    //全选和全不选
      var $btnBatch=$('#btn_delete');

      // 全选 / 全不选
      $('th > input[type=checkbox]').on('change', function () {
        var checked = $(this).prop('checked')
        $('td > input[type=checkbox]').prop('checked', checked).trigger('change')
      });
      // 批量操作按钮
      $tbody.on('change', 'td > input[type=checkbox]', function () {
        var id = parseInt($(this).parent().parent().data('id'));
        if ($(this).prop('checked')) {
          checkedItems.includes(id)||checkedItems.push(id);
        } else {
          checkedItems.splice(checkedItems.indexOf(id), 1);
        }
        checkedItems.length ? $btnBatch.fadeIn() : $btnBatch.fadeOut()
      });

      //------------批量操作-------------------------
      //批量删除
      $('.btn-batch').on('click','.btn-danger', function() {
      var $tr=$(this).parent().parent();
      var id=$tr.data('id');
      $.get('/admin/api/comments-delete.php?id='+checkedItems,function(res) {
          if (!res) return
          //$tr.remove();
          loadPageData(currentPage);
     });
      })
      //批量批准
      .on('click','.btn-info', function() {
      var $tr=$(this).parent().parent();
      var id=$tr.data('id');
      $.get('/admin/api/comments-status.php?id='+checkedItems,{ status: 'approved' },function(res) {
          if (!res) return
          //$tr.remove();
          loadPageData(currentPage);
     });
      })
      //批量拒绝
      .on('click','.btn-warning', function() {
      var $tr=$(this).parent().parent();
      var id=$tr.data('id');
      $.get('/admin/api/comments-status.php?id='+checkedItems,{ status: 'rejected' },function(res) {
          if (!res) return
          //$tr.remove();
          loadPageData(currentPage);
     });
      });

}); //end functtion

</script>
  <script>NProgress.done()</script>
  </body>
</html>
