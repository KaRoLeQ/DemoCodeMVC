<?= $this->extend('admin\layout') ?>
<?= $this->section('title') ?><?= lang('News.news_category_title') . ' - ' . $category->name ?><?= $this->endSection('') ?>
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
        $("#tablePosts").DataTable({
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
                <h1 class="m-0 text-dark"><?= $category->name ?></h1>
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
                <h3 class="card-title"><i class="fas fa-aktualnosci"></i> <?= lang('News.category_posts_subheading', ['category' => $category->name]); ?></h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/news/post/create') ?>" class="btn btn-xs btn-outline-primary"><i class="fas fa-plus"></i> <?= lang('News.create_post_btn') ?></a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-wrench"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <?= anchor(route_to('admin_news_category_edit', $category->id), lang('News.news_edit_category'), ['class' => 'dropdown-item']) ?>
                            <?= anchor(route_to('admin_news_category_remove', $category->id), lang('News.news_remove_category'), ['class' => 'dropdown-item']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablePosts" class="table table-bordered table-hover" width="100%">
                        <thead>
                            <tr>
                                <th><?= lang('News.index_title') ?></th>
                                <th><?= lang('News.index_author') ?></th>
                                <th><?= lang('News.index_categories') ?></th>
                                <th><?= lang('News.index_publication') ?></th>
                                <th><?= lang('OvenCms.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($category->posts as $post) : ?>
                                <tr>
                                    <td class="align-middle">
                                        <?= (!$post->visible) ? '<span class="float-right badge badge-pill badge-dark">' . lang('News.unpublished') . '</span>' : '' ?>
                                        <?= htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8') ?>
                                        <p class="text-sm font-italic mb-0">
                                            <?= lang('News.index_URL') . ': ' . anchor('aktualnosci/' . $post->id . '/' . $post->friendly_url, 'aktualnosci/' . $post->id . '/' . $post->friendly_url) ?>
                                        </p>
                                    </td>
                                    <td class="align-middle"><?= anchor('admin/user/' . $post->author->id, $post->author->first_name . ' ' . $post->author->last_name) ?></td>
                                    <td class="align-middle">
                                        <?php foreach ($post->categories as $category) : ?>
                                            <?= anchor('admin/news/category/' . $category->id, $category->name, ['class' => 'badge badge-' . ($category->visible ? 'primary' : 'secondary')]) ?>
                                        <?php endforeach; ?>
                                    </td>
                                    <td class="align-middle"><span data-toggle="tooltip" data-html="true" title="<strong><?= lang('News.index_created_at') ?>:</strong> <?= $post->created_at ?><br /><strong><?= lang('News.index_edited_at') ?>:</strong> <?= $post->edited_at ?><br />"><?= $post->publication ?></span></td>
                                    <td class="align-middle">
                                        <div class="btn-group dropleft">
                                            <?= anchor('admin/news/post/' . $post->id . '/edit', lang('OvenCms.ovencms_edit'), ['class' => 'btn btn-primary']) ?></a>
                                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-hover dropdown-icon" data-toggle="dropdown">
                                                <span class="sr-only"><?= lang('Auth.ovencms_toggle_dropdown') ?></span>
                                            </button>
                                            <div class="dropdown-menu" role="menu">
                                                <?= anchor('aktualnosci/' . $post->id . '/' . $post->friendly_url, lang('OvenCms.ovencms_view'), ['class' => 'dropdown-item']) ?></a>
                                                <?= anchor('admin/news/post/' . $post->id . '/remove', lang('OvenCms.ovencms_remove'), ['class' => 'dropdown-item']) ?></a>
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