<?= $this->extend('layout') ?>
<?= $this->section('head-meta') ?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?= script_tag($dirPage . '/js/particles/particles.min.js') ?>
<?= script_tag($dirPage . '/js/particles/particles-line.js') ?>
<?= $this->endSection('') ?>
<?= $this->section('content') ?>
<!-- Page Title
============================================= -->

<section id="page-title" class="page-title-parallax page-title-center page-title-dark include-header" style="background-image: linear-gradient(to top, rgba(254,150,3,0.5), #39384D), url('/uploads/bg-headers/realizations.webp'); background-size: cover; padding: 120px 0;" data-bottom-top="background-position:0px 300px;" data-top-bottom="background-position:0px -300px;">
    <div id="particles-line"></div>

    <div class="container clearfix mt-4">
        <div class="badge rounded-pill border border-light text-light"><?= lang('Realizations.realizations') ?></div>
        <h1><?= $realization->title ?></h1>
        <?= $breadcrumb ?>
    </div>

</section><!-- #page-title end -->

<!-- Content Realization
============================================= -->
<section id="content">
    <div class="content-wrap">
        <div class="container clearfix">
            <div class="row col-mb-50">
                <div class="col-md-4">

                    <!-- Realization - Description
					============================================= -->
                    <div class="fancy-title title-border">
                        <h2><?= lang('Realizations.information') ?></h2>
                    </div>

                    <?= (!empty($realization->content) ? $realization->content : '<p>' . lang('Realizations.empty_content')) . '</p>' ?>
                    <!-- Realization - Description End -->

                    <div class="clear"></div>
                    <!-- Realization  - Share
					============================================= -->
                    <?= view('App\Views\widgets\shere', ['border' => true]) ?>
                    <!-- Realization - Share End -->

                </div>


                <div class="col-md-8 portfolio-single-content">
                    <!-- Realization  - Slider
					============================================= -->
                    <div class="fslider" data-arrows="true" data-animation="fade" data-thumbs="true">
                        <div class="flexslider">
                            <div class="slider-wrap">
                                <?php foreach ($realization->images as $k => $image) : ?>
                                    <div class="slide" data-thumb="<?= $image->image->basic ?>">
                                        <picture>
                                            <source srcset="<?= $image->image->webp ?>" type="image/webp">
                                            <source srcset="<?= $image->image->basic ?>" type="image/<?= $image->image->type ?>">
                                            <img class="lazy" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E" width="800" height="600" data-src="<?= $image->image->basic ?>" alt="<?= $image->title . ' - ' . $websiteName ?>">
                                        </picture>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <!-- Realization - Slider End -->
                </div>
            </div>

            <div class="divider divider-center"><i class="icon-circle"></i></div>
        </div>
        <?php
        $panel = (object) ['parameters' => (object)['class' => 'mb-5']];
        ?>
        <h4 class="center"><?= lang('Realizations.similar_realizations') ?></h4>
        <?= view('App\Modules\Realizations\Views\widgets\carusela-similar', ['similarRelizations' => $realization->similar, 'panel' => $panel]) ?>
    </div>
</section><!-- #content end -->


<?= $this->endSection('') ?>