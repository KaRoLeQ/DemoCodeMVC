<?= $this->extend('admin\layout') ?>
<?= $this->section('title') ?><?= lang('Realizations.gallery_realization') . ' #' . $realization->id ?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?= link_tag($dirPlugins . '/icheck-bootstrap/icheck-bootstrap.min.css') ?>
<?= link_tag($dirPlugins . '/ekko-lightbox/ekko-lightbox.css') ?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?= script_tag($dirPlugins . '/ekko-lightbox/ekko-lightbox.min.js') ?>
<!-- page script -->
<script>
    $(document).ready(function() {
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
                <h1 class="m-0 text-dark"><?= lang('Realizations.gallery_realization') . ' ' . $realization->id ?></h1>
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
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-images"></i> <?= lang('Realizations.index_images'); ?>(<?= $realization->imagesCount ?>)</h3>
                <div class="card-tools">
                    <?= anchor(route_to('admin_realizations_image_create', $realization->id), '<i class="fas fa-plus"></i> ' . lang('Realizations.create_realization_image'), ['title' => lang('Realizations.creating_realization_image'), 'class' => 'btn btn-xs btn-block btn-outline-info float-right']) ?>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div id="gallery" class="ocms-gallery-grid">
                    <?php foreach ($realization->images as $k => $item) : ?>
                        <div class="ocms-gallery-item" style="background-image: url('<?= $item->image ?>');">
                            <div class="ocms-gallery-status <?= $item->visible ? 'active' : 'inactive' ?>" title="<?= $item->visible ? lang('Realizations.published') : lang('Realizations.unpublished') ?>"></div>
                            <div class="ocms-gallery-prieview" title="<?= lang('Realizations.realization_image_zoom') ?>" data-toggle="lightbox" data-title="<?= $item->title ?>" rel="lightbox" data-gallery="gallery-<?= $realization->id ?>" data-remote="<?= $item->image ?>"></div>
                            <div class="ocms-gallery-item__details">
                                <span class="ocms-gallery-order"><?= $item->order_by ?></span><?= $item->title ?>
                                <div class="btn-group dropup float-right">
                                    <button type="button" class="btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="<?= lang('OvenCms.ovencms_toggle_dropdown') ?>">
                                        <span aria-hidden="true"><i class="fas fa-ellipsis-v"></i></span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <?= anchor(route_to('admin_realizations_image_edit', $realization->id, $item->id), lang('Realizations.edit_realization_image'), ['class' => 'dropdown-item']) ?>
                                        <?= anchor(route_to('admin_realizations_image_remove', $realization->id, $item->id), lang('Realizations.remove_realization_image'), ['class' => 'dropdown-item']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>