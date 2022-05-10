<?= $this->extend('admin/layout') ?>
<?= $this->section('title') ?><?= lang('Realizations.removing_item_title') . ' #' . $item->id ?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?= link_tag($dirPlugins . '/icheck-bootstrap/icheck-bootstrap.min.css') ?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<!-- page script -->
<script>
    $(document).ready(function() {
        $(document).delegate("form#removeOffersForm", "submit", function(c) {
            c.preventDefault();
            let form = new FormData(this);
            $(`#removeOffersForm #formRaport`).remove();
            $(`#removeOffersForm .card-body`).prepend(`<div id="formRaport" class="alert"></div>`);

            if (form.get('remove') == 'true') {
                form.append("itemId", <?= $item->id ?>);
                form.append("itemImage", '<?= $item->image->basic ?>');
                $.ajax({
                    type: 'POST',
                    url: '<?= route_to('admin_realizations_removeItem') ?>',
                    data: form,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (!Array.isArray(data.errors) || !data.errors.lengt === 0) {
                            $.each(data.errors, function(k, val) {
                                $(`#removeOffersForm #formRaport`).addClass(`alert-warning`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-exclamation-triangle"></i> ${k}</h5>`).append(`${val}`);
                            });
                        } else {
                            $(`#removeOffersForm #removeOfferSubmit`).remove();
                            $(`#removeOffersForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?= lang('OvenCms.success') ?></h5>`).append('<?= lang('Realizations.remove_item_return_true_alert') ?></h5>');
                            setTimeout(function() {
                                window.location.href = '<?= route_to('admin_realizations') ?>';
                            }, 3100);

                        }
                    }
                });
            } else {
                $(`#removeOffersForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?= lang('OvenCms.success') ?></h5>`).append('<?= lang('Realizations.remove_item_return_false_alert') ?></h5>');
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
                <h1 class="m-0 text-dark"><?= lang('Realizations.removing_item') ?></h1>
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
        <div class="row">
            <div class="col-md-5">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-edit"></i> <?= lang('Realizations.form_removing_item'); ?></h3>
                    </div>
                    <!-- /.card-header -->
                    <?= form_open('', ['id' => 'removeOffersForm']) ?>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="removeTrue" name="remove" value="true">
                                <label for="removeTrue" class="form-check-label"><?= lang('Realizations.remove_item_yes_in_form') ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="removeFalse" name="remove" value="false" checked>
                                <label for="removeFalse" class="form-check-label"><?= lang('Realizations.remove_item_no_in_form') ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?= anchor(route_to('admin_realizations'), lang('OvenCms.cancal_btn'), ['class' => 'btn btn-default']) ?>
                        <?= form_submit('submit', lang('OvenCms.submit_btn'), ['id' => 'removeOfferSubmit', 'class' => 'btn btn-warning float-right']) ?>
                    </div>
                    <?= form_close() ?>
                </div>
                <!-- /.card -->
            </div>
            <div class="col-md-7">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-eye"></i> <?= lang('Realizations.item_quick_preview'); ?></h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <dl>
                            <dt><?= lang('Realizations.item_title_label') ?></dt>
                            <dd><?= $item->title ?></dd>
                            <dt><?= lang('Realizations.index_friendly_url') ?></dt>
                            <dd><?= $item->friendly_url ?></dd>
                            <dt><?= lang('Realizations.item_publication_label') ?></dt>
                            <dd><?= (!$item->visible) ? '<span class="badge badge-pill badge-dark">' . lang('Realizations.unpublished') . '</span>' : '<span class="badge badge-pill badge-success">' . lang('Realizations.published') . '</span>' ?></dd>
                            <dt><?= lang('Realizations.item_description_label') ?></dt>
                            <dd><?= $item->description ?></dd>
                        </dl>
                    </div>
                </div>
                <!-- /.card -->
            </div>

            <div class="col-md-12">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-eye"></i> <?= lang('Realizations.item_content_label'); ?></h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <?= $item->content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>