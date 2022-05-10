<?= $this->extend('admin/layout') ?>
<?= $this->section('title') ?><?= lang('Realizations.creating_item_title') ?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?= link_tag($dirPlugins . '/icheck-bootstrap/icheck-bootstrap.min.css') ?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?= script_tag($dirPlugins . '/tinymce/tinymce.min.js') ?>

<!-- page script -->
<script>
    $(function() {
        tinymce.init({
            selector: '#content',
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

        $("#createRealizationForm #title").keyup(function() {
            let t = $("#createRealizationForm #title").val().toSeoUrl();
            $('#createRealizationForm #friendlyUrl strong').html(t);
        });

        $(document).delegate("form#createRealizationForm", "submit", function(c) {
            c.preventDefault();
            let form = new FormData(this);
            $(`#createRealizationForm #formRaport`).remove();
            $(`#createRealizationForm input.is-warning`).removeClass('is-warning');

            form.append("friendly_url", $("#createRealizationForm #title").val().toSeoUrl());
            form.append('content', tinyMCE.activeEditor.getContent().escape_html());

            $.ajax({
                type: 'POST',
                url: '<?= route_to('admin_realizations_createItem') ?>',
                data: form,
                processData: false,
                contentType: false,
                success: function(data) {
                    $(`#createRealizationForm .card-body`).prepend(`<div id="formRaport" class="alert"></div>`);
                    if (!Array.isArray(data.errors) || !data.errors.lengt === 0) {
                        $(`#createRealizationForm #formRaport`).addClass(`alert-warning`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-exclamation-triangle"></i> <?= lang('OvenCms.problem_form_validation') ?></h5>`);
                        $.each(data.errors, function(k, val) {
                            if ($(`#createRealizationForm #${k}`).length > 0)
                                $(`#createRealizationForm #${k}`).addClass(`is-warning`);

                            if (!$(`#createRealizationForm #formRaport ul`).length)
                                $(`#createRealizationForm #formRaport`).append($("<ul>"));

                            $(`#createRealizationForm #formRaport ul`).append($("<li>").text(val));
                        });
                    } else {
                        $(`#createRealizationForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?= lang('OvenCms.success') ?></h5>`).append('<?= lang('OvenCms.changes_have_been_saved') ?></h5>');
                        // setTimeout(function() {
                        //     window.location.href = `/admin/realizations/${data.success.id}/edit`;
                        // }, 3100);
                    }
                }
            });

            $('html, body').animate({
                scrollTop: $(".wrapper").offset().top
            }, 200);
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
                <h1 class="m-0 text-dark"><?= lang('Realizations.creating_item') ?></h1>
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
                <h3 class="card-title"><i class="fas fa-edit"></i> <?= lang('Realizations.form_creating_item'); ?></h3>
            </div>
            <!-- /.card-header -->
            <?= form_open('', ['id' => 'createRealizationForm']) ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= ocms_form_label(lang('Realizations.item_title_label'), 'title', lang('Realizations.item_name_info')) ?>
                            <?= form_input($title) ?>
                            <p class="text-sm font-italic mb-0" id="friendlyUrl"><?= lang('Rules.index_friendly_url') ?>: <strong></strong></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= ocms_form_label(lang('Realizations.item_category_label'), 'category', lang('Realizations.item_category_info')) ?>
                            <?= ocms_form_dropdown($categoriesOptions, $categoriesList) ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <?= ocms_form_label(lang('Realizations.item_description_label'), 'description', lang('Realizations.item_description_info')) ?>
                    <?= form_textarea($description) ?>
                </div>

                <div class="form-group">
                    <?= ocms_form_label(lang('Realizations.item_content_label'), 'content', lang('Realizations.item_content_info')) ?>
                    <?= form_textarea($content) ?>
                </div>
            </div>
            <div class="card-footer">
                <?= anchor(route_to('admin_realizations'), lang('OvenCms.cancal_btn'), ['class' => 'btn btn-default']) ?>
                <?= form_submit('submit', lang('OvenCms.submit_btn'), ['id' => 'createOfferSubmit', 'class' => 'btn btn-success float-right']) ?>
            </div>
            <?= form_close() ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>