<?= $this->extend('admin\layout') ?>
<?= $this->section('title') ?><?= lang('Realizations.editing_realization_image_title') ?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?= link_tag($dirPlugins . '/dropify/css/dropify.min.css') ?>
<?= link_tag($dirPlugins . '/icheck-bootstrap/icheck-bootstrap.min.css') ?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?= script_tag($dirPlugins . '/dropify/js/dropify.min.js') ?>
<!-- page script -->
<script>
    $(function() {
        $('.dropify').dropify();

        $("#editImageForm #title").keyup(function() {
            let t = $("#editImageForm #title").val().toSeoUrl();
            $('#editImageForm #friendlyUrl strong').html(t);
        });

        let realizationId = '<?= $realization->id ?>';
        let imageTitle = '<?= $image->title ?>';
        let imageId = '<?= $image->id ?>';
        let imageSrc = '<?= $image->image->basic ?>';
        let imageFriendlyUrl = '<?= $image->friendly_url ?>';
        $(document).delegate("form#editImageForm", "submit", function(c) {
            c.preventDefault();
            let form = new FormData(this);
            $(`#editImageForm #formRaport`).remove();
            $(`#editImageForm input.is-warning`).removeClass('is-warning');
            form.append("friendly_url", $("#editImageForm #title").val().toSeoUrl());
            form.append("realizationId", realizationId);
            form.append("imageId", imageId);
            form.append("imageTitle", imageTitle);
            form.append("imageSrc", imageSrc);
            form.append("imageFriendlyUrl", imageFriendlyUrl);

            if (!form.get('visible'))
                form.append("visible", 0);

            $.ajax({
                type: 'POST',
                url: '<?= route_to('admin_realizations_editImage') ?>',
                data: form,
                processData: false,
                contentType: false,
                success: function(data) {
                    if ($(`input[name="csrfOvenCms"]`).length > 0)
                        $(`input[name="csrfOvenCms"]`).val(data.token);
                    $(`#editImageForm .card-body`).prepend(`<div id="formRaport" class="alert"></div>`);
                    if (!Array.isArray(data.errors) || !data.errors.lengt === 0) {
                        $(`#editImageForm #formRaport`).addClass(`alert-warning`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-exclamation-triangle"></i> <?= lang('OvenCms.problem_form_validation') ?></h5>`);
                        $.each(data.errors, function(k, val) {
                            if ($(`#editImageForm #${k}`).length > 0)
                                $(`#editImageForm #${k}`).addClass(`is-warning`);

                            if (!$(`#editImageForm #formRaport ul`).length)
                                $(`#editImageForm #formRaport`).append($("<ul>"));

                            $(`#editImageForm #formRaport ul`).append($("<li>").text(val));
                        });
                    } else {
                        $(`#editImageForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?= lang('OvenCms.success') ?></h5>`).append('<?= lang('OvenCms.changes_have_been_saved') ?>');
                        imageSrc = data.image;
                        imageTitle = form.get('title');
                        imageFriendlyUrl = form.get('friendly_url');
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
                <h1 class="m-0 text-dark"><?= lang('Realizations.editing_realization_image') ?></h1>
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
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-edit"></i> <?= lang('Realizations.form_editing_realization_image'); ?></h3>
            </div>
            <!-- /.card-header -->
            <?= form_open('', ['id' => 'editImageForm', 'enctype' => 'multipart/form-data']) ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= ocms_form_label(lang('Realizations.image_title_label'), 'title', lang('Realizations.image_title_info')) ?>
                            <?= form_input($title) ?>
                            <p class="text-sm font-italic mb-0" id="friendlyUrl"><?= lang('Realizations.index_friendly_url') ?>: <strong><?= $image->friendly_url ?></strong></p>
                        </div>
                        <div class="form-group">
                            <?= ocms_form_label(lang('Realizations.image_order_by_label'), 'order', lang('Realizations.image_order_by_info')) ?>
                            <?= form_input($order) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= ocms_form_label(lang('Realizations.image_label'), 'image', lang('Realizations.image_info', ['weight' => $imageWeight])) ?>
                            <?= form_input($imageForm) ?>
                        </div>
                        <div class="form-group">
                            <?= ocms_form_label(lang('Realizations.image_publication_label'), 'visibleLabel', lang('Realizations.image_publication_info')) ?>
                            <div class="icheck-primary d-inline">
                                <?= form_checkbox($visible) ?>
                                <label for="visible"><?= lang('Realizations.image_publication_info') ?></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <?= anchor(route_to('admin_realizations_item_gallery', $realization->id), lang('OvenCms.cancal_btn'), ['class' => 'btn btn-default']) ?>
                <?= form_submit('submit', lang('OvenCms.submit_btn'), ['id' => 'createImageSubmit', 'class' => 'btn btn-primary float-right']) ?>
            </div>
            <?= form_close() ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>