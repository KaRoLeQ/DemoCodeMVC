<?= $this->extend('admin\layout') ?>
<?= $this->section('title') ?><?=lang('Hotel.creating_rooms_category_title')?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?=link_tag($dirPlugins.'/icheck-bootstrap/icheck-bootstrap.min.css')?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?=script_tag($dirPlugins.'/tinymce/tinymce.min.js')?>
<!-- page script -->
<script>
  $(function () {
    tinymce.init({
        selector: '#content',
        min_height: 300,
        language: 'pl',
        toolbar: 'undo redo | bold italic underline strikethrough | fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | link anchor | ltr rtl',
        plugins: 'paste importcss searchreplace  code visualblocks visualchars link nonbreaking anchor advlist lists wordcount textpattern help ',
    });

    $("#createCategoryForm #name").keyup(function(){
        let t = $("#createCategoryForm #name").val().toSeoUrl();
        $('#createCategoryForm #friendlyUrl strong').html(t);
    });

    $(document).delegate("form#createCategoryForm", "submit", function(c) {
        c.preventDefault();
        let form = new FormData(this);
        $(`#createCategoryForm #formRaport`).remove();
        $(`#createCategoryForm input.is-warning`).removeClass('is-warning');

        form.append("friendly_url", $("#createCategoryForm #name").val().toSeoUrl());
        form.append('content', tinyMCE.activeEditor.getContent().escape_html());

        if(!form.get('visible'))
            form.append("visible", 0);

        $.ajax({
            type: 'POST',
            url: '/admin/hotel/rooms/createCategory',
            data: form,
            processData: false,
            contentType: false,
            success: function (data) {
                if($(`input[name="csrfOvenCms"]`).length > 0)
                    $(`input[name="csrfOvenCms"]`).val(data.token);
                $(`#createCategoryForm .card-body`).prepend(`<div id="formRaport" class="alert"></div>`);
                if(!Array.isArray(data.errors) || !data.errors.lengt===0)
                {
                    $(`#createCategoryForm #formRaport`).addClass(`alert-warning`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-exclamation-triangle"></i> <?=lang('OvenCms.problem_form_validation')?></h5>`);
                    $.each(data.errors, function (k,val) {
                        if($(`#createCategoryForm #${k}`).length > 0)
                            $(`#createCategoryForm #${k}`).addClass(`is-warning`);

                        if(!$(`#createCategoryForm #formRaport ul`).length)
                            $(`#createCategoryForm #formRaport`).append($("<ul>"));

                        $(`#createCategoryForm #formRaport ul`).append($("<li>").text(val));
                    });
                }else
                {
                    $(`#createCategoryForm #createCategorySubmit`).remove();
                    $(`#createCategoryForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?=lang('OvenCms.success')?></h5>`).append('<?=lang('OvenCms.changes_have_been_saved')?></h5>');
                    setTimeout(function(){
                        window.location.href = `/admin/hotel/rooms/category/${data.success.id}/edit`;
                    }, 3100);
                }
            }
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
            <h1 class="m-0 text-dark"><?=lang('Hotel.creating_rooms_category')?></h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><?=anchor('/admin',lang('OvenCms.ovencms_dashboard'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/hotel',lang('Hotel.hotel_title'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/hotel/rooms',lang('Hotel.rooms'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/hotel/rooms/categories',lang('Hotel.index_categories'))?></li>
            <li class="breadcrumb-item active"><?=anchor('/admin/hotel/rooms/category/create',lang('OvenCms.create'))?></li>
        </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-edit"></i> <?=lang('Hotel.form_creating_rooms_category');?></h3>
            </div>
            <!-- /.card-header -->
            <?=form_open('',['id'=>'createCategoryForm'])?>
                <div class="card-body">
                    <div class="form-group">
                        <?=ocms_form_label(lang('Hotel.rooms_categories_name_label'), 'name', lang('Hotel.rooms_categories_name_info'))?>
                        <?=form_input($name)?>
                        <p class="text-sm font-italic mb-0" id="friendlyUrl"><?=lang('Hotel.index_friendly_url')?>: <strong></strong></p>
                    </div>
                    <div class="form-group">
                        <?=ocms_form_label(lang('Hotel.rooms_categories_order_by_label'), 'order', lang('Hotel.rooms_categories_order_by_info'))?>
                        <?=form_input($order)?>
                    </div>
                    <div class="form-group">
                        <?=ocms_form_label(lang('Hotel.rooms_categories_content_label'), 'content', lang('Hotel.rooms_categories_content_info'))?>
                        <?=form_textarea($content)?>
                    </div>
                    <div class="form-group">
                        <?=ocms_form_label(lang('Hotel.rooms_categories_publication_label'), 'visibleLabel', lang('Hotel.rooms_categories_publication_info'))?>
                        <div class="icheck-success d-inline">
                            <?=form_checkbox($visible)?>
                            <label for="visible"><?=lang('Hotel.rooms_categories_publication_info')?></label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <?=anchor('admin/hotel/rooms/categories/',lang('OvenCms.cancal_btn'),['class'=>'btn btn-default'])?>
                    <?=form_submit('submit', lang('OvenCms.submit_btn'),['id'=>'createCategorySubmit','class'=>'btn btn-success float-right'])?>
                </div>
            <?=form_close()?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>