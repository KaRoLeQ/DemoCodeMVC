<?= $this->extend('admin\layout') ?>
<?= $this->section('title') ?><?=lang('News.news_remove_category_title').' - '.$category->name?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?=link_tag($dirPlugins.'/icheck-bootstrap/icheck-bootstrap.min.css')?>
<?=link_tag($dirPlugins.'/datatables-bs4/css/dataTables.bootstrap4.min.css')?>
<?=link_tag($dirPlugins.'/datatables-responsive/css/responsive.bootstrap4.min.css')?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?=script_tag($dirPlugins.'/tinymce/tinymce.min.js')?>
<?=script_tag($dirPlugins.'/datatables/jquery.dataTables.min.js')?>
<?=script_tag($dirPlugins.'/datatables-bs4/js/dataTables.bootstrap4.min.js')?>
<?=script_tag($dirPlugins.'/datatables-responsive/js/dataTables.responsive.min.js')?>
<?=script_tag($dirPlugins.'/datatables-responsive/js/responsive.bootstrap4.min.js')?>
<!-- page script -->
<script>
$(document).ready(function(){
    $("#tablePosts").DataTable({
        "order": [[ 3, "asc" ]],
        "columnDefs": [
            {
                "width": "80px",
                "targets": 3,
                "className": "text-center"
            },
            {
                "width": "100px",
                "targets": 4,
                "orderable": false,
                "className": "text-center"
            }

        ]
    });

    $(document).delegate("form#removeCategoryForm", "submit", function(c) {
        c.preventDefault();
        let form = new FormData(this);
        $(`#removeCategoryForm #formRaport`).remove();
        $(`#removeCategoryForm .card-body`).prepend(`<div id="formRaport" class="alert"></div>`);

        if(form.get('remove')=='true')
        {
            form.append("categoryId", <?=$category->id?>);
                $.ajax({
                type: 'POST',
                url: '/admin/news/removeCategory',
                data: form,
                processData: false,
                contentType: false,
                success: function (data) {
                    if(!Array.isArray(data.errors) || !data.errors.lengt===0)
                    {
                        $.each(data.errors, function (k,val) {
                            $(`#removeCategoryForm #formRaport`).addClass(`alert-warning`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-exclamation-triangle"></i> ${k}</h5>`).append(`${val}`);
                        });
                    }else
                    {
                        $(`#removeCategoryForm #removeCategorySubmit`).remove();
                        $(`#removeCategoryForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?=lang('OvenCms.success')?></h5>`).append('<?=lang('News.remove_category_return_true_alert')?></h5>');
                        setTimeout(function(){
                            window.location.href = '/admin/news';
                        }, 3100);

                    }
                }
            });
        }else
        {
            $(`#removeCategoryForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?=lang('OvenCms.success')?></h5>`).append('<?=lang('News.remove_category_return_false_alert')?></h5>');
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
            <h1 class="m-0 text-dark"><?=lang('News.news_removing_category')?></h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><?=anchor('/admin',lang('OvenCms.ovencms_dashboard'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/news',lang('News.news_title'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/news/categories',lang('News.news_categories'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/news/category/'.$category->id,$category->name)?></li>
            <li class="breadcrumb-item active"><?=anchor('/admin/news/category/'.$category->id.'/remove',lang('OvenCms.remove'))?></li>
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
                <div class="callout callout-warning">
                    <h5><?=lang('News.remove_category_information_heading')?></h5>

                    <p><?=lang('News.remove_category_information_1')?></p>
                    <p><?=lang('News.remove_category_information_2')?></p>
                </div>
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-edit"></i> <?=lang('News.news_form_removing_category');?></h3>
                    </div>
                    <!-- /.card-header -->
                    <?=form_open('',['id'=>'removeCategoryForm'])?>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="removeTrue" name="remove" value="true">
                                    <label for="removeTrue" class="form-check-label"><?=lang('News.remove_category_yes_in_form')?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="removeFalse" name="remove" value="false" checked>
                                    <label for="removeFalse" class="form-check-label"><?=lang('News.remove_category_no_in_form')?></label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <?=anchor('admin/news/category/'.$category->id,lang('OvenCms.cancal_btn'),['class'=>'btn btn-default'])?>
                            <?=form_submit('submit', lang('OvenCms.submit_btn'),['id'=>'removeCategorySubmit','class'=>'btn btn-warning float-right'])?>
                        </div>
                    <?=form_close()?>
                </div>
                <!-- /.card -->
            </div>
            <div class="col-md-7">
                <div class="card card-info card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="categoryTabNav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="categoryTabNavGeneral" data-toggle="pill" href="#categoryTabContentGeneral" role="tab" aria-controls="category-tab-nav-general" aria-selected="true"><?=lang('Auth.general')?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="categoryTabNavMembers" data-toggle="pill" href="#categoryTabContentMembers" role="tab" aria-controls="category-tab-nav-posts" aria-selected="false"><?=lang('News.news_posts')?></a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content" id="categoryTabContent">
                        <div class="tab-pane fade show active" id="categoryTabContentGeneral" data-form="General" role="tabpanel" aria-labelledby="category-tab-general">
                            <div class="card-body">
                                <dl>
                                    <dt><?=lang('News.index_name')?></dt>
                                    <dd><?=$category->name?></dd>
                                    <dt><?=lang('News.index_friendly_url')?></dt>
                                    <dd><?=$category->friendly_url?></dd>
                                    <dt><?=lang('News.index_link')?></dt>
                                    <dd><?=anchor('blog/kategoria/'.$category->friendly_url,'blog/kategoria/'.$category->friendly_url)?></dd>
                                    <dt><?=lang('News.index_description')?></dt>
                                    <dd><?=$category->description?></dd>
                                    <dt><?=lang('News.index_status')?></dt>
                                    <dd><?=(!$category->visible) ? '<span class="badge badge-pill badge-dark">'.lang('News.unpublished').'</span>':'<span class="badge badge-pill badge-success">'.lang('News.published').'</span>'?></dd>
                                </dl>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="categoryTabContentMembers" data-form="Members" role="tabpanel" aria-labelledby="category-tab-nav-posts">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tablePosts" class="table table-bordered table-hover" width="100%">
                                        <thead>
                                        <tr>
                                            <th><?=lang('News.index_title')?></th>
                                            <th><?=lang('News.index_author')?></th>
                                            <th><?=lang('News.index_categories')?></th>
                                            <th><?=lang('News.index_publication')?></th>
                                            <th><?=lang('OvenCms.actions')?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($category->posts as $post):?>
                                        <tr>
                                            <td class="align-middle">
                                                <?=(!$post->visible) ? '<span class="float-right badge badge-pill badge-dark">'.lang('News.unpublished').'</span>':''?>
                                                <?=htmlspecialchars($post->title,ENT_QUOTES,'UTF-8')?>
                                                <p class="text-sm font-italic mb-0">
                                                    <?=lang('News.index_URL').': '.anchor('blog/'.$post->friendly_url,'blog/'.$post->friendly_url)?>
                                                </p>
                                            </td>
                                            <td class="align-middle"><?=anchor('admin/user/'.$post->author->id,$post->author->first_name.' '.$post->author->last_name)?></td>
                                            <td class="align-middle">
                                                <?php foreach ($post->categories as $category):?>
                                                    <?=anchor('admin/news/category/'.$category->id,$category->name,['class'=>'badge badge-'.($category->visible?'primary':'secondary')])?>
                                                <?php endforeach;?>
                                            </td>
                                            <td class="align-middle"><span data-toggle="tooltip" data-html="true" title="<strong><?=lang('News.index_created_at')?>:</strong> <?=$post->created_at?><br /><strong><?=lang('News.index_edited_at')?>:</strong> <?=$post->edited_at?><br />"><?=$post->publication?></span></td>
                                            <td class="align-middle">
                                                <div class="btn-group dropleft">
                                                    <?=anchor('admin/news/post/'.$post->id.'/edit',lang('OvenCms.ovencms_edit'),['class'=>'btn btn-primary'])?></a>
                                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-hover dropdown-icon" data-toggle="dropdown">
                                                        <span class="sr-only"><?=lang('Auth.ovencms_toggle_dropdown')?></span>
                                                    </button>
                                                    <div class="dropdown-menu" role="menu">
                                                        <?=anchor('blog/'.$post->friendly_url,lang('OvenCms.ovencms_view'),['class'=>'dropdown-item'])?></a>
                                                        <?=anchor('admin/news/post/'.$post->id.'/remove',lang('OvenCms.ovencms_remove'),['class'=>'dropdown-item'])?></a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach;?>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>