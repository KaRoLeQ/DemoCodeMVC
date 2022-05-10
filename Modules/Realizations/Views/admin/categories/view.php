<?= $this->extend('admin\layout') ?>
<?= $this->section('title') ?><?=lang('Hotel.rooms_category_title').' #'.$category->id?><?= $this->endSection('') ?>
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
$(document).ready(function(){
    $("#tableRooms").DataTable({
        "order": [[ 0, "asc" ]],
        "columnDefs": [
            {
                "width": "40px",
                "targets": 0,
                "className": "text-right"
            },
            {
                "width": "80px",
                "targets": 3,
                "className": "text-center"
            },
            {
                "targets": 4,
                "className": "text-center"
            },
            {
                "targets": 5,
                "width": "100px",
                "orderable": false,
                "className": "text-center"
            },
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
            <h1 class="m-0 text-dark"><?=lang('Hotel.rooms_category')?></h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><?=anchor('/admin',lang('OvenCms.ovencms_dashboard'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/hotel',lang('Hotel.hotel_title'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/hotel/rooms',lang('Hotel.rooms'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/hotel/rooms/categories',lang('Hotel.index_categories'))?></li>
            <li class="breadcrumb-item active"><?=anchor('/admin/hotel/rooms/category/'.$category->id,$category->name)?></li>
        </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
    <div class="card card-info card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="categoryTabNav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="categoryTabNavGeneral" data-toggle="pill" href="#categoryTabContentGeneral" role="tab" aria-controls="category-tab-nav-general" aria-selected="true"><?=lang('Auth.general')?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="categoryTabNavRooms" data-toggle="pill" href="#categoryTabContentRooms" role="tab" aria-controls="category-tab-nav-rooms" aria-selected="false"><?=lang('Hotel.rooms')?></a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content" id="categoryTabContent">
                        <div class="tab-pane fade show active" id="categoryTabContentGeneral" data-form="General" role="tabpanel" aria-labelledby="category-tab-general">
                            <div class="card-body">
                                <dl>
                                    <dt><?=lang('Hotel.index_name')?></dt>
                                    <dd><?=$category->name?></dd>
                                    <dt><?=lang('Hotel.index_friendly_url')?></dt>
                                    <dd><?=$category->friendly_url?></dd>
                                    <dt><?=lang('Hotel.index_link')?></dt>
                                    <dd><?=anchor($urlPublic.'/'.$category->friendly_url,$urlPublic.'/'.$category->friendly_url)?></dd>
                                    <dt><?=lang('Hotel.index_content')?></dt>
                                    <dd><?=$category->content?></dd>
                                    <dt><?=lang('Hotel.index_status')?></dt>
                                    <dd><?=(!$category->visible) ? '<span class="badge badge-pill badge-dark">'.lang('Hotel.unpublished').'</span>':'<span class="badge badge-pill badge-success">'.lang('Hotel.published').'</span>'?></dd>
                                </dl>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="categoryTabContentRooms" data-form="Rooms" role="tabpanel" aria-labelledby="category-tab-nav-rooms">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tableRooms" class="table table-bordered table-hover" width="100%">
                                        <thead>
                                            <tr>
                                                <th><?=lang('Hotel.index_id')?></th>
                                                <th><?=lang('Hotel.index_name')?></th>
                                                <th><?=lang('Hotel.index_number')?></th>
                                                <th><?=lang('Hotel.index_floor')?></th>
                                                <th><?=lang('Hotel.index_order_by')?></th>
                                                <th><?=lang('OvenCms.actions')?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($category->rooms as $room):?>
                                        <tr>
                                            <td class="align-middle text-right"><?=$room->id?></td>
                                            <td class="align-middle">
                                                <?=(!$room->visible) ? '<span class="float-right badge badge-pill badge-dark">'.lang('Hotel.unpublished').'</span>':''?>
                                                <?=htmlspecialchars($room->name,ENT_QUOTES,'UTF-8')?>
                                                <p class="text-sm font-italic mb-0">
                                                    <?=lang('Hotel.index_URL').': '.anchor($urlPublic.'/pokoj/'.$room->number.'-'.$room->friendly_url,$urlPublic.'/pokoj/'.$room->number.'-'.$room->friendly_url)?>
                                                </p>
                                            </td>
                                            <td class="align-middle"><?=$room->number?></td>
                                            <td class="align-middle"><?=$room->floor?></td>
                                            <td class="align-middle"><?=$room->order_by?></td>
                                            <td class="align-middle">
                                                <div class="btn-group dropleft">
                                                    <?=anchor('admin/hotel/room/'.$room->id.'/edit',lang('OvenCms.ovencms_edit'),['class'=>'btn btn-primary'])?></a>
                                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-hover dropdown-icon" data-toggle="dropdown">
                                                        <span class="sr-only"><?=lang('Auth.ovencms_toggle_dropdown')?></span>
                                                    </button>
                                                    <div class="dropdown-menu" role="menu">
                                                        <?=anchor('admin/hotel/room/'.$room->id,lang('OvenCms.ovencms_view'),['class'=>'dropdown-item'])?></a>
                                                        <?=anchor('admin/hotel/room/'.$room->id.'/remove',lang('OvenCms.ovencms_remove'),['class'=>'dropdown-item'])?></a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach;?>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>