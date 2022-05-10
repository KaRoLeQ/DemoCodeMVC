<?= $this->extend('admin\layout') ?>
<?= $this->section('title') ?><?= lang('Realizations.removing_realization_image_title') . ' #' . $image->id ?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?= link_tag($dirPlugins . '/icheck-bootstrap/icheck-bootstrap.min.css') ?>
<?= link_tag($dirPlugins . '/ekko-lightbox/ekko-lightbox.css') ?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?= script_tag($dirPlugins . '/ekko-lightbox/ekko-lightbox.min.js') ?>
<!-- page script -->

<?php
$img = str_replace(base_url(), '', $image->image->basic);
?>
<script>
    $(document).ready(function() {
        $(document).on('click', '[data-toggle="lightbox"]', function(event) {
            event.preventDefault();
            $(this).ekkoLightbox({
                alwaysShowClose: true
            });
        });
        $(document).delegate("form#removeImageForm", "submit", function(c) {
            c.preventDefault();
            let form = new FormData(this);
            $(`#removeImageForm #formRaport`).remove();
            $(`#removeImageForm .card-body`).prepend(`<div id="formRaport" class="alert"></div>`);

            if (form.get('remove') == 'true') {
                form.append("imageId", <?= $image->id ?>);
                form.append("imageSrc", "<?= (!empty($img) && file_exists($_SERVER['DOCUMENT_ROOT'] . $img)) ? $image->image->basic : '' ?>");
                $.ajax({
                    type: 'POST',
                    url: '<?= route_to('admin_realizations_removeImage') ?>',
                    data: form,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if ($(`input[name="csrfOvenCms"]`).length > 0)
                            $(`input[name="csrfOvenCms"]`).val(data.token);
                        if (!Array.isArray(data.errors) || !data.errors.lengt === 0) {
                            $.each(data.errors, function(k, val) {
                                $(`#removeImageForm #formRaport`).addClass(`alert-warning`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-exclamation-triangle"></i> ${k}</h5>`).append(`${val}`);
                            });
                        } else {
                            $(`#removeImageForm #removeCategorySubmit`).remove();
                            $(`#removeImageForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?= lang('OvenCms.success') ?></h5>`).append('<?= lang('Realizations.remove_realization_image_return_true_alert') ?></h5>');
                            setTimeout(function() {
                                window.location.href = '<?= route_to('admin_realizations_item_gallery', $realization->id) ?>';
                            }, 3100);

                        }
                    }
                });
            } else {
                $(`#removeImageForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?= lang('OvenCms.success') ?></h5>`).append('<?= lang('Realizations.remove_realization_image_return_false_alert') ?></h5>');
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
                <h1 class="m-0 text-dark"><?= lang('Realizations.removing_realization_image') ?></h1>
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
        <div class="row">
            <div class="col-md-5">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-edit"></i> <?= lang('Realizations.form_removing_realization_image'); ?></h3>
                    </div>
                    <!-- /.card-header -->
                    <?= form_open('', ['id' => 'removeImageForm']) ?>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="removeTrue" name="remove" value="true">
                                <label for="removeTrue" class="form-check-label"><?= lang('Realizations.remove_realization_image_yes_in_form') ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="removeFalse" name="remove" value="false" checked>
                                <label for="removeFalse" class="form-check-label"><?= lang('Realizations.remove_realization_image_no_in_form') ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?= anchor(route_to('admin_realizations_item_gallery', $realization->id), lang('OvenCms.cancal_btn'), ['class' => 'btn btn-default']) ?>
                        <?= form_submit('submit', lang('OvenCms.submit_btn'), ['id' => 'removeCategorySubmit', 'class' => 'btn btn-warning float-right']) ?>
                    </div>
                    <?= form_close() ?>
                </div>
                <!-- /.card -->
            </div>
            <div class="col-md-7">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-eye"></i> <?= lang('Realizations.image_quick_preview'); ?></h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <dl>
                            <dt><?= lang('Realizations.index_title') ?></dt>
                            <dd><?= $image->title ?></dd>
                            <dt><?= lang('Realizations.index_friendly_url') ?></dt>
                            <dd><?= $image->friendly_url ?></dd>
                            <dt><?= lang('Realizations.index_order_by') ?></dt>
                            <dd><?= $image->order_by ?></dd>
                            <dt><?= lang('Realizations.index_status') ?></dt>
                            <dd><?= (!$image->visible) ? '<span class="badge badge-pill badge-dark">' . lang('Realizations.unpublished') . '</span>' : '<span class="badge badge-pill badge-success">' . lang('Realizations.published') . '</span>' ?></dd>
                            <dt><?= lang('Realizations.index_image') ?></dt>
                            <dd><?= empty($img) ? '<span title="' . lang('OvenCms.file_does_not_exist_graphicinfo') . '">' . lang('OvenCms.no_related_artwork') . '</span>' : (!file_exists($_SERVER['DOCUMENT_ROOT'] . $img) ? '<span title="' . lang('OvenCms.file_does_not_exist_graphic_info') . '">' . lang('OvenCms.file_does_not_exist_graphic') . '</span>' : anchor($img, img(['src' => $img, 'class' => 'img-fluid']), ['data-toggle' => 'lightbox', 'rel' => 'lightbox', 'data-title' => $image->title])) ?></dd>
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