<?= $this->extend('admin/layout') ?>
<?= $this->section('title') ?><?= lang('News.news_creating_post') ?><?= $this->endSection('') ?>
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
            tags: true,
            tokenSeparators: [',', ';', ' ']
        });

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

        $("#createPostForm #title").keyup(function() {
            let t = $("#createPostForm #title").val().toSeoUrl();
            $('#createPostForm #friendlyUrl strong').html(t);
        });

        $(document).delegate("form#createPostForm", "submit", function(c) {
            c.preventDefault();
            let form = new FormData(this);
            $(`#createPostForm #formRaport`).remove();
            $(`#createPostForm input.is-warning`).removeClass('is-warning');

            form.append("friendly_url", $("#createPostForm #title").val().toSeoUrl());
            form.append('content', tinyMCE.activeEditor.getContent().escape_html());

            if (!form.get('visible'))
                form.append("visible", 0);

            $.ajax({
                type: 'POST',
                url: '<?= route_to('admin_news_createPost') ?>',
                data: form,
                processData: false,
                contentType: false,
                success: function(data) {
                    $(`#createPostForm .card-body`).prepend(`<div id="formRaport" class="alert"></div>`);
                    if (!Array.isArray(data.errors) || !data.errors.lengt === 0) {
                        $(`#createPostForm #formRaport`).addClass(`alert-warning`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-exclamation-triangle"></i> <?= lang('OvenCms.problem_form_validation') ?></h5>`);
                        $.each(data.errors, function(k, val) {
                            if ($(`#createPostForm #${k}`).length > 0)
                                $(`#createPostForm #${k}`).addClass(`is-warning`);

                            if (!$(`#createPostForm #formRaport ul`).length)
                                $(`#createPostForm #formRaport`).append($("<ul>"));

                            $(`#createPostForm #formRaport ul`).append($("<li>").text(val));
                        });
                    } else {
                        $(`#createPostForm #formRaport`).addClass(`alert-success`).append(`<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fas fa-check"></i> <?= lang('OvenCms.success') ?></h5>`).append('<?= lang('OvenCms.changes_have_been_saved') ?></h5>');
                        setTimeout(function() {
                            window.location.href = data.success.url;
                        }, 3100);
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
                <h1 class="m-0 text-dark"><?= lang('News.news_creating_post') ?></h1>
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
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-edit"></i> <?= lang('News.news_form_creating_category'); ?></h3>
            </div>
            <!-- /.card-header -->
            <?= form_open('', ['id' => 'createPostForm', 'enctype' => 'multipart/form-data']) ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= ocms_form_label(lang('News.post_title_label'), 'title', lang('News.post_title_info')) ?>
                            <?= form_input($title) ?>
                            <p class="text-sm font-italic mb-0" id="friendlyUrl"><?= lang('News.index_friendly_url') ?>: <strong></strong></p>
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
                                    <?= form_dropdown('categories[]', $categoriesList, NULL, $categoriesOptions) ?>
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
                    <?= form_textarea($content) ?>
                </div>
            </div>
            <div class="card-footer">
                <?= anchor(route_to('admin_news_posts'), lang('OvenCms.cancal_btn'), ['class' => 'btn btn-default']) ?>
                <?= form_submit('submit', lang('OvenCms.submit_btn'), ['id' => 'createPostSubmit', 'class' => 'btn btn-success float-right']) ?>
            </div>
            <?= form_close() ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<?= $this->endSection('') ?>