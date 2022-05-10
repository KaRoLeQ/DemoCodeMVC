<?= $this->extend('admin\layout') ?>
<?= $this->section('title') ?><?=lang('News.news_categories_title')?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?=link_tag($dirPlugins.'/datatables-bs4/css/dataTables.bootstrap4.min.css')?>
<?=link_tag($dirPlugins.'/datatables-responsive/css/responsive.bootstrap4.min.css')?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?=script_tag($dirPlugins.'/datatables/jquery.dataTables.min.js')?>
<?=script_tag($dirPlugins.'/datatables-bs4/js/dataTables.bootstrap4.min.js')?>
<?=script_tag($dirPlugins.'/datatables-responsive/js/dataTables.responsive.min.js')?>
<?=script_tag($dirPlugins.'/datatables-responsive/js/responsive.bootstrap4.min.js')?>
<!-- page script -->
<script>
  $(function () {
    $("#tableCategories").DataTable({
        "order": [[ 0, "asc" ]],
        "columnDefs": [
            {
                "width": "100px",
                "targets": 1,
                "className": "text-center"
            },
            {
                "width": "100px",
                "targets": 2,
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
            <h1 class="m-0 text-dark"><?=lang('News.news_categories_title')?></h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><?=anchor('/admin',lang('OvenCms.ovencms_dashboard'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/news',lang('News.news_title'))?></li>
            <li class="breadcrumb-item active"><?=anchor('/admin/news/categories',lang('News.news_categories'))?></li>
        </ol>
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
                <h3 class="card-title"><i class="fas fa-tags"></i> <?=lang('News.categories_subheading');?></h3>
                <div class="card-tools">
                    <a href="<?=base_url('admin/news/category/create')?>" class="btn btn-xs btn-block btn-outline-primary float-right"><i class="fas fa-plus"></i> <?=lang('News.create_category_btn')?></a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableCategories" class="table table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th><?=lang('News.index_name')?></th>
                            <th><?=lang('News.index_publications')?></th>
                            <th><?=lang('OvenCms.actions')?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($categories as $category):?>
                        <tr>
                            <td class="align-middle">
                                <?=(!$category->visible) ? '<span class="float-right badge badge-pill badge-dark">'.lang('News.unpublished').'</span>':''?>
                                <?=htmlspecialchars($category->name,ENT_QUOTES,'UTF-8')?>
                                <p class="text-sm font-italic mb-0">
                                    <?=lang('News.index_URL').': '.anchor('aktualnosci/'.$category->friendly_url,'aktualnosci/'.$category->friendly_url)?>
                                </p>
                            </td>
                            <td class="align-middle"><?=count((array) $category->posts)?></td>
                            <td class="align-middle">
                                <div class="btn-group dropleft">
                                    <?=anchor('admin/news/category/'.$category->id.'/edit',lang('OvenCms.ovencms_edit'),['class'=>'btn btn-primary'])?></a>
                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-hover dropdown-icon" data-toggle="dropdown">
                                        <span class="sr-only"><?=lang('Auth.ovencms_toggle_dropdown')?></span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <?=anchor('admin/news/category/'.$category->id,lang('OvenCms.ovencms_view'),['class'=>'dropdown-item'])?></a>
                                        <?=anchor('admin/news/category/'.$category->id.'/remove',lang('OvenCms.ovencms_remove'),['class'=>'dropdown-item'])?></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach;?>
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