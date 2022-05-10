<?= $this->extend('admin/layout') ?>
<?= $this->section('title') ?><?= lang('News.news_editing_post') . ' #' . $post->id ?><?= $this->endSection('') ?>
<?= $this->section('head-meta') ?>
<?= link_tag($dirPlugins . '/dropify/css/dropify.min.css') ?>
<?= link_tag($dirPlugins . '/select2/css/select2.min.css') ?>
<?= link_tag($dirPlugins . '/daterangepicker/daterangepicker.css') ?>
<?= link_tag($dirPlugins . '/icheck-bootstrap/icheck-bootstrap.min.css') ?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?= script_tag($dirPlugins . '/dropify/js/dropify.min.js') ?>
<?= script_tag($dirPlugins . '/select2/js/select2.full.min.js') ?>
<?= script_tag($dirPlugins . '/select2/js/i18n/pl.js') ?>
<?= script_tag($dirPlugins . '/moment/moment.min.js') ?>
<?= script_tag($dirPlugins . '/daterangepicker/daterangepicker.js') ?>
<?= script_tag($dirPlugins . '/tinymce/tinymce.min.js') ?>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js"></script>

<!-- page script -->
<script>
    $(function() {
        $('.dropify').dropify();
        moment.locale('pl');
        $('.select2').select2({
            language: "pl"
        });
        $('.tags-select2').select2({
            language: "pl",
            tags: [<?= $post->tags ? ("'" . implode("','", explode(',', $post->tags)) . "'") : '' ?>],
            tokenSeparators: [',', ';', ' ']
        });
        $('.tags-select2').val([<?= $post->tags ? ("'" . implode("','", explode(',', $post->tags)) . "'") : '' ?>]).trigger('change');
        tinymce.init({
            selector: '#description',
            min_height: 300,
            language: 'pl',
            toolbar: 'undo redo | bold italic underline strikethrough | fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | image media responsivefilemanager | link unlink anchor | ltr rtl',
            plugins: 'paste importcss searchreplace image media responsivefilemanager code visualblocks visualchars link nonbreaking anchor advlist lists wordcount textpattern help',
            image_advtab: true,
            relative_urls: false,

            external_filemanager_path: "<?= base_url() ?>/assets/plugins/filemanager/",
            filemanager_title: "<?= lang('OvenCms.Responsive_Filemanager') ?>",
            external_plugins: {
                "filemanager": "<?= base_url($dirPlugins . '/tinymce/plugins/responsivefilemanager/plugin.min.js') ?>"
            }
        });

        $('#publication').daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            locale: {
                format: 'YYYY-MM-DD HH:mm',
                "applyLabel": "Zastosuj",
                "cancelLabel": "Anuluj",
                "fromLabel": "Formularz",
                "daysOfWeek": [
                    "N",
                    "Pn",
                    "Wt",
                    "Śr",
                    "Cz",
                    "Pt",
                    "So"
                ],
                "monthNames": [
                    "Styczeń",
                    "Luty",
                    "Marzec",
                    "Kwiecień",
                    "Maj",
                    "Czerwiec",
                    "Lipiec",
                    "Sierpień",
                    "Wrzesień",
                    "Październik",
                    "Listopad",
                    "Grudzień"
                ],
                "firstDay": 1
            }
        });

        $("#editPostForm #title").keyup(function() {
            let t = $("#editPostForm #title").val().toSeoUrl();
            $('#editPostForm #friendlyUrl strong').html(t);
        });

        let postTitle = '<?= $post->title ?>';
        let postImage = '<?= $post->image->basic ?>';
        let postFriendlyUrl = '<?= $post->friendly_url ?>';
        $(document).delegate("form#editPostForm", "submit", function(c) {
            c.preventDefault();
            let form = new FormData(this);
            $(`#editPostForm #formRaport`).remove();
            $(`#editPostForm input.is-warning`).removeClass('is-warning');

            form.append("postId", <?= $post->id ?>);
            form.append("friendly_url", $("#editPostForm #title").val().toSeoUrl());
            form.append("postTitle", postTitle);
            form.append("postImage", postImage);
            form.append("postFriendlyUrl", postFriendlyUrl);
            form.append('content', tinyMCE.activeEditor.getContent().escape_html());

            if (!form.get('visible'))
                form.append("visible", 0);

            $.ajax({
                type: 'POST',
                url: '<?= route_to('admin_news_editPost') ?>',
                data: form,
                processData: false,
                contentType: false,
                success: function(data) {
                    $(`#editPostForm .card-body`).prepend(`<div id="formRaport" class="alert"></div>`);
                    if (!Array.isArray(data.errors) || !data.errors.lengt === 0) {
                        $(`#editPostForm #formRaport`).addClass(`alert-warning`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-exclamation-triangle"></i> <?= lang('OvenCms.problem_form_validation') ?></h5>`);
                        $.each(data.errors, function(k, val) {
                            if ($(`#editPostForm #${k}`).length > 0)
                                $(`#editPostForm #${k}`).addClass(`is-warning`);

                            if (!$(`#editPostForm #formRaport ul`).length)
                                $(`#editPostForm #formRaport`).append($("<ul>"));

                            $(`#editPostForm #formRaport ul`).append($("<li>").text(val));
                        });
                    } else {
                        $(`#editPostForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?= lang('OvenCms.success') ?></h5>`).append('<?= lang('OvenCms.changes_have_been_saved') ?></h5>');
                        postTitle = form.get('title');
                        postImage = data.image;
                        postFriendlyUrl = form.get('friendly_url');
                        $('#postTitle').html(form.get('title'));
                    }
                }
            });

            $('html, body').animate({
                scrollTop: $(".wrapper").offset().top
            }, 200);
        });

        $(document).delegate("form#editSeoForm", "submit", function(c) {
            c.preventDefault();
            let form = new FormData(this);
            $(`#editSeoForm #formRaport`).remove();
            $(`#editSeoForm input.is-warning`).removeClass('is-warning');

            form.append("itemId", <?= $post->id ?>);
            form.append("dbName", 'news_posts');
            form.append('json', $('#seoJson').val().escape_html());
            form.append('other', $('#seoOther').val().escape_html());

            if (!form.get('robot'))
                form.append("robot", 0);

            $.ajax({
                type: 'POST',
                url: '<?= route_to('seoUpdate') ?>',
                data: form,
                processData: false,
                contentType: false,
                success: function(data) {
                    $(`#editSeoForm .card-body`).prepend(`<div id="formRaport" class="alert"></div>`);
                    if (!Array.isArray(data.errors) || !data.errors.lengt === 0) {
                        $(`#editSeoForm #formRaport`).addClass(`alert-warning`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-exclamation-triangle"></i> <?= lang('OvenCms.problem_form_validation') ?></h5>`);
                        $.each(data.errors, function(k, val) {
                            if ($(`#editSeoForm #${k}`).length > 0)
                                $(`#editSeoForm #${k}`).addClass(`is-warning`);

                            if (!$(`#editSeoForm #formRaport ul`).length)
                                $(`#editSeoForm #formRaport`).append($("<ul>"));

                            $(`#editSeoForm #formRaport ul`).append($("<li>").text(val));
                        });
                    } else {
                        $(`#editSeoForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?= lang('OvenCms.success') ?></h5>`).append('<?= lang('OvenCms.changes_have_been_saved') ?></h5>');
                    }
                }
            });

            $('html, body').animate({
                scrollTop: $(".wrapper").offset().top
            }, 200);
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
                <h1 class="m-0 text-dark"><?= lang('News.news_editing_post') ?></h1>
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
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="formTabNav" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="formTabNavGeneral" data-toggle="pill" href="#formTabContentGeneral" role="tab" aria-controls="form-tab-nav-general" aria-selected="true"><i class="fas fa-edit"></i> <?= lang('News.news_form_editing_post') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="formTabNavSeo" data-toggle="pill" href="#formTabContentSeo" role="tab" aria-controls="form-tab-nav-seo" aria-selected="false"><i class="fas fa-hashtag"></i> <?= lang('OvenCms.form_editing_seo'); ?></a>
                    </li>
                </ul>
            </div>
            <div class="tab-content" id="formTabContent">
                <div class="tab-pane fade show active" id="formTabContentGeneral" data-form="General" role="tabpanel" aria-labelledby="form-tab-general">
                    <?= form_open('', ['id' => 'editPostForm', 'enctype' => 'multipart/form-data']) ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= ocms_form_label(lang('News.post_title_label'), 'title', lang('News.post_title_info')) ?>
                                    <?= form_input($title) ?>
                                    <p class="text-sm font-italic mb-0" id="friendlyUrl"><?= lang('News.index_friendly_url') ?>: <strong><?= $post->friendly_url ?></strong></p>
                                </div>
                                <div class="form-group">
                                    <?= ocms_form_label(lang('News.author_label'), 'author', lang('News.author_info')) ?>
                                    <?= form_dropdown('author', $authorUsersList, $post->author->id, $authorOptions) ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?= ocms_form_label(lang('News.post_publication_label'), 'publication', lang('News.post_publication_info')) ?>
                                            <div class="input-group date" id="publicationdate" data-target-input="nearest">
                                                <?= form_input($publication) ?>
                                                <div class="input-group-append" data-target="#publicationdate" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?= ocms_form_label(lang('News.categories_label'), 'categories', lang('News.categories_info')) ?>
                                            <?= form_dropdown('categories[]', $categoriesList, $post->category->id, $categoriesOptions) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= ocms_form_label(lang('News.tags_label'), 'tags', lang('News.tags_info')) ?>
                                    <select name="tags[]" name="tags" id="tags" multiple="multiple" class="form-control tags-select2" style="width: 100%" data-placeholder="Wybierz kategorie"></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= ocms_form_label(lang('News.image_label'), 'image', lang('News.image_info', ['weight' => $imageWeight])) ?>
                                    <?= form_input($imageForm) ?>
                                </div>
                                <div class="form-group">
                                    <?= ocms_form_label(lang('News.publication_label'), 'visibleLabel', lang('News.publication_info')) ?>
                                    <div class="icheck-primary d-inline">
                                        <?= form_checkbox($visible) ?>
                                        <label for="visible"><?= lang('News.publication_info') ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= ocms_form_label(lang('News.post_content_label'), 'content', lang('News.post_content_info')) ?>
                            <?= form_textarea($content, $post->content) ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?= anchor($post->url->admin->view, lang('OvenCms.cancal_btn'), ['class' => 'btn btn-default']) ?>
                        <?= form_submit('submit', lang('OvenCms.submit_btn'), ['id' => 'editPostSubmit', 'class' => 'btn btn-primary float-right']) ?>
                    </div>
                    <?= form_close() ?>
                </div>
                <div class="tab-pane fade" id="formTabContentSeo" data-form="Seo" role="tabpanel" aria-labelledby="form-tab-seo">
                    <?= form_open('', ['id' => 'editSeoForm']) ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= ocms_form_label(lang('PageSeo.seo_title_label'), 'title', lang('PageSeo.seo_title_info')) ?>
                                    <?= form_input($seoTitle) ?>
                                </div>
                                <div class="form-group">
                                    <?= ocms_form_label(lang('PageSeo.seo_description_label'), 'description', lang('PageSeo.seo_description_info')) ?>
                                    <?= form_textarea($seoDescription, $itemSeo->description) ?>
                                </div>
                                <div class="form-group">
                                    <?= ocms_form_label(lang('PageSeo.seo_image_label'), 'image', lang('PageSeo.seo_image_info')) ?>
                                    <div class="input-group">
                                        <?= form_input($seoImage) ?>
                                        <span class="input-group-btn">
                                            <button type="button" id="btnSeoImage" data-toggle="modal" data-target="#modalSeoImage" class="btn btn-info btn-flat" title="<?= lang('OvenCms.open_filemanager_info') ?>"><?= lang('OvenCms.open_filemanager_btn') ?></button>
                                        </span>
                                    </div>
                                    <div class="modal fade" id="modalSeoImage">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"><?= lang('OvenCms.Responsive_Filemanager') ?></h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body p-0">
                                                    <object data="<?= base_url('assets/plugins/filemanager/dialog.php?type=1&relative_url=1&multiple=0&field_id=seoImage') ?>" style="width:100%;height:500px"></object>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                    <!-- /.modal -->
                                </div>
                                <div class="form-group">
                                    <?= ocms_form_label(lang('PageSeo.seo_robot_label'), 'seoRobotLabel', lang('PageSeo.seo_robot_info')) ?>
                                    <div class="icheck-primary d-inline">
                                        <?= form_checkbox($seoRobot) ?>
                                        <label for="seoRobot"><?= lang('PageSeo.seo_robot_mininfo') ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= ocms_form_label(lang('PageSeo.seo_json_label'), 'json', lang('PageSeo.seo_json_info')) ?>
                                    <?= form_textarea($seoJson, $itemSeo->json) ?>
                                </div>
                                <div class="form-group">
                                    <?= ocms_form_label(lang('PageSeo.seo_other_label'), 'other', lang('PageSeo.seo_other_info')) ?>
                                    <?= form_textarea($seoOther, $itemSeo->other) ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <?= anchor($post->url->admin->view, lang('OvenCms.cancal_btn'), ['class' => 'btn btn-default']) ?>
                        <?= form_submit('submit', lang('OvenCms.submit_btn'), ['id' => 'editSeoSubmit', 'class' => 'btn btn-primary float-right']) ?>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>