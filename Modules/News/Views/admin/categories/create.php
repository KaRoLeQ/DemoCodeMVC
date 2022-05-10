<?= $this->extend('admin\layout') ?>
<?= $this->section('title') ?><?= lang('News.news_creating_category') ?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?= link_tag($dirPlugins . '/icheck-bootstrap/icheck-bootstrap.min.css') ?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?= script_tag($dirPlugins . '/tinymce/tinymce.min.js') ?>
<!-- page script -->
<script>
    $(function() {
        tinymce.init({
            selector: '#description',
            min_height: 300,
            language: 'pl',
            toolbar: 'undo redo | bold italic underline strikethrough | fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | image media responsivefilemanager | link unlink anchor | ltr rtl',
            plugins: 'paste importcss searchreplace image media responsivefilemanager code visualblocks visualchars link nonbreaking anchor advlist lists wordcount textpattern help',
            image_advtab: true,
            relative_urls: false,

            external_filemanager_path: "<?= base_url() ?>/assets/plugins/filemanager/",
            filemanager_title: "<?= lang('OvenCms.Responsive_Filemanager') ?>",
            external_plugins: {
                "filemanager": "<?= base_url($dirPlugins . '/tinymce/plugins/responsivefilemanager/plugin.min.js') ?>"
            }
        });

        $("#createCategoryForm #name").keyup(function() {
            let t = $("#createCategoryForm #name").val().toSeoUrl();
            $('#createCategoryForm #friendlyUrl strong').html(t);
        });

        $(document).delegate("form#createCategoryForm", "submit", function(c) {
            c.preventDefault();
            let form = new FormData(this);
            $(`#createCategoryForm #formRaport`).remove();
            $(`#createCategoryForm input.is-warning`).removeClass('is-warning');

            form.append("friendly_url", $("#createCategoryForm #name").val().toSeoUrl());
            form.append('description', tinyMCE.activeEditor.getContent().escape_html());

            if (!form.get('visible'))
                form.append("visible", 0);

            $.ajax({
                type: 'POST',
                url: '<?= route_to('admin_news_createCategory') ?>',
                data: form,
                processData: false,
                contentType: false,
                success: function(data) {
                    $(`#createCategoryForm .card-body`).prepend(`<div id="formRaport" class="alert"></div>`);
                    if (!Array.isArray(data.errors) || !data.errors.lengt === 0) {
                        $(`#createCategoryForm #formRaport`).addClass(`alert-warning`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-exclamation-triangle"></i> <?= lang('OvenCms.problem_form_validation') ?></h5>`);
                        $.each(data.errors, function(k, val) {
                            if ($(`#createCategoryForm #${k}`).length > 0)
                                $(`#createCategoryForm #${k}`).addClass(`is-warning`);

                            if (!$(`#createCategoryForm #formRaport ul`).length)
                                $(`#createCategoryForm #formRaport`).append($("<ul>"));

                            $(`#createCategoryForm #formRaport ul`).append($("<li>").text(val));
                        });
                    } else {
                        $(`#createCategoryForm #createCategorySubmit`).remove();
                        $(`#createCategoryForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?= lang('OvenCms.success') ?></h5>`).append('<?= lang('OvenCms.changes_have_been_saved') ?></h5>');
                        setTimeout(function() {
                            window.location.href = data.success.url;
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
                <h1 class="m-0 text-dark"><?= lang('News.news_creating_category') ?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <?= $breadcrumb ?>
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
                <h3 class="card-title"><i class="fas fa-edit"></i> <?= lang('News.news_form_creating_category'); ?></h3>
            </div>
            <!-- /.card-header -->
            <?= form_open('', ['id' => 'createCategoryForm']) ?>
            <div class="card-body">
                <div class="form-group">
                    <?= form_label(lang('News.category_name_label'), 'name') ?>
                    <?= form_input($name) ?>
                    <p class="text-sm font-italic mb-0" id="friendlyUrl"><?= lang('News.index_friendly_url') ?>: <strong></strong></p>
                </div>
                <div class="form-group">
                    <?= form_label(lang('News.description_label'), 'description') ?>
                    <?= form_textarea($description) ?>
                </div>
                <div class="form-group">
                    <?= form_label(lang('News.publication_label'), 'visibleLabel') ?><br />
                    <div class="icheck-success d-inline">
                        <?= form_checkbox($visible) ?>
                        <label for="visible"><?= lang('News.publication_info') ?></label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <?= anchor(route_to('admin_news_categories'), lang('OvenCms.cancal_btn'), ['class' => 'btn btn-default']) ?>
                <?= form_submit('submit', lang('OvenCms.submit_btn'), ['id' => 'createCategorySubmit', 'class' => 'btn btn-success float-right']) ?>
            </div>
            <?= form_close() ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>