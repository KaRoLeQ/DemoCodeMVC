<?= $this->extend('layout') ?>

<!-- Similar Realizations Items
============================================= -->
<div id="related-portfolio" class="owl-carousel <?= $panel->parameters->class ?> portfolio-carousel carousel-widget" data-margin="30" data-nav="false" data-autoplay="5000" data-items-xs="1" data-items-sm="2" data-items-md="3" data-items-xl="4">
    <?php foreach ($similarRelizations as $k => $similarRelizationsItem) : ?>
        <div class="oc-item">
            <div class="portfolio-item">
                <div class="portfolio-image">
                    <a href="<?= $similarRelizationsItem->url ?>" class="box-img-bg lazy" data-bg="<?= $similarRelizationsItem->images[0]->image->webp ?>" title="<?= $similarRelizationsItem->images[0]->title ?>"></a>
                    <div class="bg-overlay" data-lightbox="gallery">
                        <div class="bg-overlay-content dark" data-hover-animate="fadeIn">
                            <?php foreach ($similarRelizationsItem->images as $k => $image) :
                                if ($k == 0) {
                                    echo anchor($image->image->basic, '<i class="icon-line-stack-2"></i>', ['class' => 'overlay-trigger-icon bg-light text-dark', 'data-lightbox' => 'gallery-item', 'data-hover-animate' => 'fadeInDownSmall', 'data-hover-animate-out' => 'fadeOutUpSmall', 'data-hover-speed' => '350', 'title' => lang('Realizations.item_quick_preview'), 'data-title' => $image->title, 'data-alt' => $image->title]);
                                } elseif ($k > 4) {
                                    break;
                                } else {
                                    echo anchor($image->image->basic, ' ', ['class' => 'd-none', 'data-lightbox' => 'gallery-item', 'title' => $image->title]);
                                }
                            endforeach; ?>
                            <a href="<?= $similarRelizationsItem->url ?>" class="overlay-trigger-icon bg-light text-dark" data-hover-animate="fadeInDownSmall" data-hover-animate-out="fadeOutUpSmall" data-hover-speed="350" title="<?= lang('Realizations.go_to_realization') ?>"><i class="icon-line-ellipsis"></i></a>
                        </div>
                        <div class="bg-overlay-bg dark" data-hover-animate="fadeIn"></div>
                    </div>
                </div>
                <div class="portfolio-desc">
                    <h3><?= anchor($similarRelizationsItem->url, character_limiter($similarRelizationsItem->title, 30), ['title' => $similarRelizationsItem->title]) ?></h3>
                    <span><?= anchor($similarRelizationsItem->category->url, character_limiter($similarRelizationsItem->category->title, 50), ['title' => $similarRelizationsItem->category->title]) ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div><!-- .similar realizations items -->