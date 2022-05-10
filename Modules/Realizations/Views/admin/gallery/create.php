<?= $this->extend('admin\layout') ?>
<?= $this->section('title') ?><?= lang('Realizations.creating_realization_image_title') ?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?= link_tag($dirPlugins . '/bootstrap-fileinput/css/fileinput.min.css') ?>
<?= link_tag($dirPlugins . '/icheck-bootstrap/icheck-bootstrap.min.css') ?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?= script_tag($dirPlugins . '/bootstrap-fileinput/js/fileinput.min.js') ?>
<?= script_tag($dirPlugins . '/bootstrap-fileinput/js/locales/pl.js') ?>
<?= script_tag($dirPlugins . '/bootstrap-fileinput/themes/fas/theme.min.js') ?>
<!-- page script -->
<script>
    $(function() {
        $(".b-fileinput").fileinput({
            language: "pl",
            theme: "fas",
            allowedFileExtensions: ["jpg", "png"]
        });

        $("#createImageForm #title").keyup(function() {
            let t = $("#createImageForm #title").val().toSeoUrl();
            $('#createImageForm #friendlyUrl strong').html(t);
        });

        $(document).delegate("form#createImageForm", "submit", function(c) {
            c.preventDefault();
            let form = new FormData(this);
            $(`#createImageForm #formRaport`).remove();
            $(`#createImageForm input.is-warning`).removeClass('is-warning');
            form.append("friendly_url", $("#createImageForm #title").val().toSeoUrl());
            form.append("realizationId", '<?= $realization->id ?>');

            if (!form.get('visible'))
                form.append("visible", 0);

            $.ajax({
                type: 'POST',
                url: '<?= route_to('admin_realizations_createImage') ?>',
                data: form,
                processData: false,
                contentType: false,
                success: function(data) {
                    if ($(`input[name="csrfOvenCms"]`).length > 0)
                        $(`input[name="csrfOvenCms"]`).val(data.token);
                    $(`#createImageForm .card-body`).prepend(`<div id="formRaport" class="alert"></div>`);
                    if (!Array.isArray(data.errors) || !data.errors.lengt === 0) {
                        $(`#createImageForm #formRaport`).addClass(`alert-warning`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-exclamation-triangle"></i> <?= lang('OvenCms.problem_form_validation') ?></h5>`);
                        $.each(data.errors, function(k, val) {
                            if ($(`#createImageForm #${k}`).length > 0)
                                $(`#createImageForm #${k}`).addClass(`is-warning`);

                            if (!$(`#createImageForm #formRaport ul`).length)
                                $(`#createImageForm #formRaport`).append($("<ul>"));

                            $(`#createImageForm #formRaport ul`).append($("<li>").text(val));
                        });
                    } else {
                        $(`#createImageForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?= lang('OvenCms.success') ?></h5>`).append('<?= lang('OvenCms.changes_have_been_saved') ?></h5>');
                        setTimeout(function() {
                            window.location.href = `<?= route_to('admin_realizations_item_gallery', $realization->id) ?>`;
                        }, 3100);
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
                <h1 class="m-0 text-dark"><?= lang('Realizations.creating_realization_image') ?></h1>
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
        <div class="attachment-block clearfix">
            <h4 class="attachment-heading"><?= anchor(route_to('admin_realizations_item', $realization->id), $realization->title, ['title' => lang('Realizations.item_title_label')]) ?></h4>

            <div class="attachment-text">
                <span title="<?= lang('Realizations.index_friendly_url') ?>"><strong><?= lang('Realizations.index_public_link') ?></strong> <?= anchor(route_to('realizations_realization', $realization->id, $realization->friendly_url)) ?></span><br />
                <span title="<?= lang('Realizations.item_order_by_label') ?>"><strong><?= lang('Realizations.index_order_by') ?></strong> <?= $realization->order_by ?></span>
                <span title="<?= lang('Realizations.item_category_label') ?>"><strong><?= lang('Realizations.item_category_label') ?></strong> <?= anchor(route_to('admin_realizations_category', $realization->category->id), $realization->category->title) ?></span>
            </div>
            <!-- /.attachment-text -->
        </div>
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-edit"></i> <?= lang('Realizations.form_creating_realization_image'); ?></h3>
            </div>
            <!-- /.card-header -->
            <?= form_open('', ['id' => 'createImageForm', 'enctype' => 'multipart/form-data']) ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= form_label(lang('Realizations.image_title_label') . '<span class="ocms-form-input-help" data-title="' . lang('Realizations.image_title_label') . '" data-content="' . lang('Realizations.image_title_info') . '"></span>', 'title', ['class' => 'ocms-from-label']) ?>
                            <?= form_input($title, $realization->title) ?>
                            <p class="text-sm font-italic mb-0" id="friendlyUrl"><?= lang('Realizations.index_friendly_url') ?>: <strong></strong></p>
                        </div>
                        <div class="form-group">
                            <?= form_label(lang('Realizations.image_order_by_label') . '<span class="ocms-form-input-help" data-title="' . lang('Realizations.image_order_by_label') . '" data-content="' . lang('Realizations.image_order_by_info') . '"></span>', 'order', ['class' => 'ocms-from-label']) ?>
                            <?= form_input($order, 1) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= form_label(lang('Realizations.image_label') . '<span class="ocms-form-input-help" data-title="' . lang('Realizations.image_label') . '" data-content="' . lang('Realizations.image_info', ['weight' => $imageWeight]) . '"></span>', 'image', ['class' => 'ocms-from-label']) ?>
                            <?= form_input($imageForm) ?>
                        </div>
                        <div class="form-group">
                            <?= form_label(lang('Realizations.image_publication_label') . '<span class="ocms-form-input-help" data-title="' . lang('Realizations.image_publication_label') . '" data-content="' . lang('Realizations.image_publication_info') . '"></span>', 'visibleLabel', ['class' => 'ocms-from-label']) ?>
                            <div class="icheck-success d-inline">
                                <?= form_checkbox($visible) ?>
                                <label for="visible"><?= lang('Realizations.image_publication_info') ?></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <?= anchor(route_to('admin_realizations_item_gallery', $realization->id), lang('OvenCms.cancal_btn'), ['class' => 'btn btn-default']) ?>
                <?= form_submit('submit', lang('OvenCms.submit_btn'), ['id' => 'createImageSubmit', 'class' => 'btn btn-success float-right']) ?>
            </div>
            <?= form_close() ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>