<?= $this->extend('admin/layout') ?>
<?= $this->section('title') ?><?=lang('News.news_removing_post').' - '.$post->title?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?=link_tag($dirPlugins.'/icheck-bootstrap/icheck-bootstrap.min.css')?>
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

    $(document).delegate("form#removePostForm", "submit", function(c) {
        c.preventDefault();
        let form = new FormData(this);
        $(`#removePostForm #formRaport`).remove();
        $(`#removePostForm .card-body`).prepend(`<div id="formRaport" class="alert"></div>`);

        if(form.get('remove')=='true')
        {
            form.append("postId", <?=$post->id?>);
            form.append("postImage", '<?=$post->image?>');
            $.ajax({
                type: 'POST',
                url: '/admin/news/removePost',
                data: form,
                processData: false,
                contentType: false,
                success: function (data) {
                    if(!Array.isArray(data.errors) || !data.errors.lengt===0)
                    {
                        $.each(data.errors, function (k,val) {
                            $(`#removePostForm #formRaport`).addClass(`alert-warning`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-exclamation-triangle"></i> ${k}</h5>`).append(`${val}`);
                        });
                    }else
                    {
                        $(`#removePostForm #removePostSubmit`).remove();
                        $(`#removePostForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?=lang('OvenCms.success')?></h5>`).append('<?=lang('News.remove_post_return_true_alert')?></h5>');
                        setTimeout(function(){
                            window.location.href = '/admin/news/posts';
                        }, 3100);

                    }
                }
            });
        }else
        {
            $(`#removePostForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?=lang('OvenCms.success')?></h5>`).append('<?=lang('News.remove_post_return_false_alert')?></h5>');
        }


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
            <h1 class="m-0 text-dark"><?=lang('News.news_removing_post')?></h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><?=anchor('/admin',lang('OvenCms.ovencms_dashboard'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/news',lang('News.news_title'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/news/posts',lang('News.news_posts'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/news/post/'.$post->id,$post->title)?></li>
            <li class="breadcrumb-item active"><?=anchor('/admin/news/post/'.$post->id.'/remove',lang('OvenCms.remove'))?></li>
        </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-edit"></i> <?=lang('News.news_form_removing_post');?></h3>
                    </div>
                    <!-- /.card-header -->
                    <?=form_open('',['id'=>'removePostForm'])?>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="removeTrue" name="remove" value="true">
                                    <label for="removeTrue" class="form-check-label"><?=lang('News.remove_post_yes_in_form')?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="removeFalse" name="remove" value="false" checked>
                                    <label for="removeFalse" class="form-check-label"><?=lang('News.remove_post_no_in_form')?></label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <?=anchor('admin/news/posts',lang('OvenCms.cancal_btn'),['class'=>'btn btn-default'])?>
                            <?=form_submit('submit', lang('OvenCms.submit_btn'),['id'=>'removePostSubmit','class'=>'btn btn-warning float-right'])?>
                        </div>
                    <?=form_close()?>
                </div>
                <!-- /.card -->
            </div>
            <div class="col-md-7">
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
                            <dd><?=anchor('blog/'.$post->friendly_url,'blog/'.$post->friendly_url)?></dd>
                            <dt><?=lang('News.index_content')?></dt>
                            <dd><?=$post->content?></dd>
                            <dt><?=lang('News.index_status')?></dt>
                            <dd><?=(!$post->visible) ? '<span class="badge badge-pill badge-dark">'.lang('News.unpublished').'</span>':'<span class="badge badge-pill badge-success">'.lang('News.published').'</span>'?></dd>
                        </dl>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>
