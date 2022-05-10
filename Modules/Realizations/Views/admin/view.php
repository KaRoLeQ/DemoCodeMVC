<?= $this->extend('admin/layout') ?>
<?= $this->section('title') ?><?= lang('Realizations.item_quick_preview') . ' #' . $item->id ?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?= $this->endSection('') ?>
<?= $this->section('content') ?>
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= lang('Realizations.item_quick_preview') ?></h1>
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
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-eye"></i> <?= $item->title ?></h3>
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
            <div class="card-footer">
                <?= anchor(route_to('admin_realizations'), lang('OvenCms.undo_btn'), ['class' => 'btn btn-default']) ?>
                <?= anchor(route_to('admin_realizations_item_edit', $item->id), lang('Realizations.go_to_editing_item'), ['class' => 'btn btn-primary float-right']) ?>
            </div>
        </div>
        <!-- /.card -->
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
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>