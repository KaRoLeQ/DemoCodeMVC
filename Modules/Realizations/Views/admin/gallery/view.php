<?= $this->extend('admin\layout') ?>
<?= $this->section('title') ?><?=lang('Hotel.room_image_title').' #'.$image->id?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?=link_tag($dirPlugins.'/ekko-lightbox/ekko-lightbox.css')?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?=script_tag($dirPlugins.'/ekko-lightbox/ekko-lightbox.min.js')?>
<!-- page script -->
<script>
$(document).ready(function(){
    $(document).on('click', '[data-toggle="lightbox"]', function (event) {
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
            <h1 class="m-0 text-dark"><?=lang('Hotel.room_image')?></h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><?=anchor('/admin',lang('OvenCms.ovencms_dashboard'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/hotel',lang('Hotel.hotel_title'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/hotel/rooms',lang('Hotel.rooms'))?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/hotel/room/'.$room->id,lang('Hotel.room').' #'.$room->id)?></li>
            <li class="breadcrumb-item"><?=anchor('/admin/hotel/room/'.$room->id.'/images',lang('Hotel.index_images'))?></li>
            <li class="breadcrumb-item active"><?=anchor('/admin/hotel/room/'.$room->id.'/image/'.$image->id,lang('Hotel.index_image').' #'.$image->id)?></li>
        </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="attachment-block clearfix">
            <?=anchor($room->image,img(['src'=>$room->image,'class'=>'img-fluid attachment-img']),['data-toggle'=>'lightbox','rel'=>'lightbox','data-title'=>lang('Hotel.room_image_main_info')])?>

            <div class="attachment-pushed">
                <h4 class="attachment-heading"><?=anchor('admin/hotel/room/'.$room->id,$room->name,['title'=>lang('Hotel.room_name_info')])?></h4>

                <div class="attachment-text">
                    <span title="<?=lang('Hotel.index_friendly_url')?>"><strong><?=lang('Hotel.index_link')?></strong> <?=anchor($urlPublic.'/'.$room->friendly_url,$urlPublic.'/'.$room->friendly_url)?></span><br />
                    <span title="<?=lang('Hotel.room_number_info')?>"><strong><?=lang('Hotel.index_number')?></strong> <?=$room->number?></span>
                    <span title="<?=lang('Hotel.room_floor_info')?>"><strong><?=lang('Hotel.index_floor')?></strong> <?=$room->floor?></span>
                    <span title="<?=lang('Hotel.room_order_by_info')?>"><strong><?=lang('Hotel.index_order_by')?></strong> <?=$room->order_by?></span>
                    <strong
                        title="<?=lang('Hotel.room_categories_title')?>"><?=lang('Hotel.index_categories')?></strong>
                    <?php foreach ($room->categories as $k => $category):?>
                        <?=($k!=0?', ':'').anchor('/admin/hotel/rooms/category/'.$category->id,$category->name,['title'=>lang('Hotel.rooms_category_title')])?>
                    <?php endforeach;?>
                </div>
                <!-- /.attachment-text -->
            </div>
            <!-- /.attachment-pushed -->
        </div>
        <div class="row">
            <div class="col-md-5">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-eye"></i> <?=lang('Hotel.room_image_quick_preview');?></h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <dl>
                            <dt><?=lang('Hotel.index_title')?></dt>
                            <dd><?=$image->title?></dd>
                            <dt><?=lang('Hotel.index_friendly_url')?></dt>
                            <dd><?=$image->friendly_url?></dd>
                            <dt><?=lang('Hotel.index_order_by')?></dt>
                            <dd><?=$image->order_by?></dd>
                            <dt><?=lang('Hotel.index_status')?></dt>
                            <dd><?=(!$image->visible) ? '<span class="badge badge-pill badge-dark">'.lang('Hotel.unpublished').'</span>':'<span class="badge badge-pill badge-success">'.lang('Hotel.published').'</span>'?></dd>
                        </dl>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <div class="col-md-7">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-image"></i> <?=lang('Hotel.index_image');?></h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <?=anchor($image->image,img(['src'=>$image->image,'class'=>'img-fluid']),['data-toggle'=>'lightbox','rel'=>'lightbox','data-title'=>lang('Hotel.index_title')])?>
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