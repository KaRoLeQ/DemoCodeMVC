<?= $this->extend('admin/layout') ?>
<?= $this->section('title') ?><?= lang('Realizations.realizations_title') ?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?= link_tag($dirPlugins . '/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>
<?= link_tag($dirPlugins . '/datatables-responsive/css/responsive.bootstrap4.min.css') ?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?= script_tag($dirPlugins . '/datatables/jquery.dataTables.min.js') ?>
<?= script_tag($dirPlugins . '/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>
<?= script_tag($dirPlugins . '/datatables-responsive/js/dataTables.responsive.min.js') ?>
<?= script_tag($dirPlugins . '/datatables-responsive/js/responsive.bootstrap4.min.js') ?>
<!-- page script -->
<script>
    $(function() {
        $("#tableRalizations").DataTable({
            "order": [
                [3, "asc"]
            ],
            "columnDefs": [{
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
    });
</script>
<?= $this->endSection('') ?>
<?= $this->section('content') ?>
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= lang('Realizations.realizations_title') ?></h1>
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
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="far fa-folder-open"></i> <?= lang('Realizations.realizations'); ?></h3>
                <div class="card-tools">
                    <div class="">
                        <?= anchor(route_to('admin_realizations_item_create'), '<i class="fa fa-plus"></i> ' . lang('Realizations.create_item'), array('class' => 'btn btn-tool btn-xs btn-outline-primary')) ?>
                        <?= anchor(route_to('admin_realizations_categories'), '<i class="fa fa-tags"></i> ' . lang('Realizations.categories'), array('class' => 'btn btn-xs btn-warning')) ?>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableRalizations" class="table table-bordered table-hover" width="100%">
                        <thead>
                            <tr>
                                <th><?= lang('Realizations.index_name') ?></th>
                                <th><?= lang('Realizations.index_category') ?></th>
                                <th><?= lang('Realizations.index_images') ?></th>
                                <th><?= lang('Realizations.index_order_by') ?></th>
                                <th><?= lang('OvenCms.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item) : ?>
                                <tr>
                                    <td class="align-middle">
                                        <?= (!$item->visible) ? '<span class="float-right badge badge-pill badge-dark">' . lang('Realizations.unpublished') . '</span>' : '' ?>
                                        <?= htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="align-middle"><?= $item->category->title ?></td>
                                    <td class="align-middle"><?= $item->imagesCount ?></td>
                                    <td class="align-middle"><?= $item->order_by ?></td>
                                    <td class="align-middle">
                                        <div class="btn-group dropleft">
                                            <?= anchor($item->url->admin->edit, lang('OvenCms.ovencms_edit'), ['class' => 'btn btn-primary']) ?></a>
                                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-hover dropdown-icon" data-toggle="dropdown">
                                                <span class="sr-only"><?= lang('Auth.ovencms_toggle_dropdown') ?></span>
                                            </button>
                                            <div class="dropdown-menu" role="menu">
                                                <?= anchor($item->url->admin->view, lang('OvenCms.ovencms_view'), ['class' => 'dropdown-item']) ?></a>
                                                <?= anchor($item->url->admin->remove, lang('OvenCms.ovencms_remove'), ['class' => 'dropdown-item']) ?></a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tfoot>
                    </table>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>