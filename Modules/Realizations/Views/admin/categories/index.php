<?= $this->extend('admin\layout') ?>
<?= $this->section('title') ?><?= lang('Realizations.categories_title') ?><?= $this->endSection('') ?>
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
        $("#tableCategories").DataTable({
            "order": [
                [1, "asc"]
            ],
            "columnDefs": [{
                    "width": "100px",
                    "targets": 1,
                    "className": "text-center"
                }, {
                    "width": "100px",
                    "targets": 2,
                    "className": "text-center"
                },
                {
                    "width": "100px",
                    "targets": 3,
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
                <h1 class="m-0 text-dark"><?= lang('Realizations.categories') ?></h1>
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
                <h3 class="card-title"><i class="fas fa-tags"></i> <?= lang('Realizations.categories_realizations'); ?></h3>
                <div class="card-tools">
                    <?= anchor(route_to('admin_realizations_category_create'), '<i class="fa fa-plus"></i> ' . lang('Realizations.create_item'), array('class' => 'btn btn-tool btn-xs btn-outline-primary')) ?>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableCategories" class="table table-bordered table-hover" width="100%">
                        <thead>
                            <tr>
                                <th><?= lang('Realizations.index_name') ?></th>
                                <th><?= lang('Realizations.index_order_by') ?></th>
                                <th><?= lang('Realizations.realizations') ?></th>
                                <th><?= lang('OvenCms.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category) : ?>
                                <tr>
                                    <td class="align-middle">
                                        <?= htmlspecialchars($category->title, ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="align-middle"><?= $category->order_by ?></td>
                                    <td class="align-middle"><?= count((array) $category->items) ?></td>
                                    <td class="align-middle">
                                        <div class="btn-group dropleft">
                                            <?= anchor($category->url->admin->edit, lang('OvenCms.ovencms_edit'), ['class' => 'btn btn-primary']) ?></a>
                                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-hover dropdown-icon" data-toggle="dropdown">
                                                <span class="sr-only"><?= lang('Auth.ovencms_toggle_dropdown') ?></span>
                                            </button>
                                            <div class="dropdown-menu" role="menu">
                                                <?= anchor($category->url->admin->view, lang('OvenCms.ovencms_view'), ['class' => 'dropdown-item']) ?></a>
                                                <?= anchor($category->url->admin->remove, lang('OvenCms.ovencms_remove'), ['class' => 'dropdown-item']) ?></a>
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
        <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>