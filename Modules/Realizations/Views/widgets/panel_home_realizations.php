<?= $this->extend('layout') ?>
<?php
$RealizationsModel = new \App\Modules\Realizations\Models\RealizationsModel();
$realizations = $RealizationsModel->getRealizations();
?>
<!-- Realizations
				============================================= -->
<div class="section m-0 mb-0 lazy" data-bg="<?= base_url('/uploads/bg-panel/panel-home-realizations.webp') ?>" style="background-position: center center; background-repeat: no-repeat; background-size: cover;" style="padding: 80px 0;">
    <div class="container">
        <div class="heading-block border-bottom-0 center">
            <div class="badge rounded-pill badge-default" data-animate="fadeInUp"><?= lang('Realizations.realizations') ?></div>
            <h3 class="nott ls0" data-animate="fadeInUp" data-delay="100"><?= lang('Realizations.see_our_realizations') ?></h3>
        </div>

        <div id="portfolio" class="portfolio row grid-container gutter-20">
            <?php foreach ($realizations as $k => $realization) :
                if ($k >= 6) continue;
            ?>
                <article class="portfolio-item col-12 col-sm-6 col-md-4 pf-media pf-icons" data-animate="fadeInUp">
                    <div class="grid-inner">
                        <div class="portfolio-image">
                            <a href="<?= $realization->url ?>" class="box-img-bg lazy" data-bg="<?= $realization->images[0]->image->webp ?>" title="<?= $realization->images[0]->title ?>"></a>

                            <div class="bg-overlay">
                                <div class="bg-overlay-content dark" data-hover-animate="fadeIn" data-hover-speed="500">
                                    <a href="<?= $realization->url ?>" class="overlay-trigger-icon bg-light text-dark" data-hover-animate="fadeIn" data-hover-speed="500"><i class="icon-plus"></i></a>
                                </div>
                                <div class="bg-overlay-bg dark" data-hover-animate="fadeIn" data-hover-speed="500"></div>
                            </div>
                        </div>
                        <div class="portfolio-desc">
                            <h3><?= anchor($realization->url, character_limiter($realization->title, 30), ['title' => $realization->title]) ?></h3>
                            <span><?= anchor($realization->category->url, character_limiter($realization->category->title, 50), ['title' => $realization->category->title]) ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="center">
            <?= anchor(route_to('realizations'), lang('Realizations.see_more_realizations'), ['class' => 'button button-large button-rounded ms-0 mt-5 ls0']) ?>
        </div>

    </div>
</div>