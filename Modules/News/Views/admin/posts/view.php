<?= $this->extend('admin/layout') ?>
<?= $this->section('title') ?><?=lang('News.post_quick_preview').' - '.$post->title?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?=link_tag($dirPlugins.'/ekko-lightbox/ekko-lightbox.css')?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?=script_tag($dirPlugins.'/ekko-lightbox/ekko-lightbox.min.js')?>
<!-- page script -->
<script>
$(document).ready(function(){
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox({
        alwaysShowClose: true
      });
    });
});
</script>
<?= $this->endSection('') ?>
<?= $this->section('content') ?>
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark"><?=lang('News.post_quick_preview')?></h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><?=anchor('/admin',lang('OvenCms.ovencms_dashboard'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/news',lang('News.news_title'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/news/posts',lang('News.news_posts'))?></li>
            <li class="breadcrumb-item active"><?=anchor('/admin/news/post/'.$post->id,$post->title)?></li>
        </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-eye"></i> <?=lang('News.post_quick_preview');?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <dl>
                    <dt><?=lang('News.index_title')?></dt>
                    <dd><?=$post->title?></dd>
                    <dt><?=lang('News.index_friendly_url')?></dt>
                    <dd><?=$post->friendly_url?></dd>
                    <dt><?=lang('News.index_link')?></dt>
                    <dd><?=anchor(route_to('news_post',$post->id,$post->friendly_url),route_to('news_post',$post->id,$post->friendly_url))?></dd>
                    <dt><?=lang('News.index_content')?></dt>
                    <dd><?=$post->content?></dd>
                    <dt><?=lang('News.index_status')?></dt>
                    <dd><?=(!$post->visible) ? '<span class="badge badge-pill badge-dark">'.lang('News.unpublished').'</span>':'<span class="badge badge-pill badge-success">'.lang('News.published').'</span>'?></dd>
                </dl>
            </div>
            <div class="card-footer">
                <?=anchor('admin/news/posts',lang('OvenCms.undo_btn'),['class'=>'btn btn-default'])?>
                <?=anchor('admin/news/post/'.$post->id.'/edit',lang('News.go_to_editing_post'),['class'=>'btn btn-primary float-right'])?>
            </div>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>
