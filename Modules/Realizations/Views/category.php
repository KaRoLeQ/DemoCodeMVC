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
        <h1><?= lang('Realizations.see_our_realizations_category', ['category' => $category->title]) ?></h1>
        <?= $breadcrumb ?>
    </div>

</section><!-- #page-title end -->

<!-- Content
		============================================= -->
<section id="content">
    <div class="content-wrap">
        <div class="container clearfix">

            <div class="row gutter-40 col-mb-80">
                <!-- Post Content
				============================================= -->
                <div class="postcontent col-lg-9 order-lg-last">
                    <!-- Portfolio Items
					============================================= -->
                    <div id="portfolio" class="portfolio row" data-layout="fitRows">
                        <?php foreach ($category->realizations as $rk => $realization) : ?>
                            <article id="portfolioCategory<?= $realization->id ?>" class="portfolio-item col-lg-1-3 col-md-3 col-sm-6 col-12 cat-<?= $realization->friendly_url ?>">
                                <div class="grid-inner">
                                    <div class="portfolio-image">
                                        <div class="fslider" data-arrows="false" data-speed="300" data-pause="3000" data-animation="fade">
                                            <div class="flexslider">
                                                <div class="slider-wrap">
                                                    <?php foreach ($realization->images as $image) : ?>
                                                        <div class="slide">
                                                            <a href="<?= $realization->url ?>" class="box-img-bg" style="background-image: url('<?= $image->image->webp ?>')" title="<?= $image->title ?>"></a>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-overlay">
                                            <div class="bg-overlay-content dark flex-column" data-hover-animate="fadeIn">
                                                <div class="portfolio-desc pt-0 center" data-hover-animate="fadeInDownSmall" data-hover-animate-out="fadeOutUpSmall" data-hover-speed="350">
                                                    <h3><?= anchor($realization->url, character_limiter($realization->title, 30), ['title' => $realization->title]) ?></h3>
                                                    <span><?= anchor($realization->category->url, character_limiter($realization->category->title, 50), ['title' => $realization->category->title]) ?></span>
                                                </div>
                                                <div class="d-flex">
                                                    <?php foreach ($realization->images as $k => $image) :
                                                        if ($k == 0) {
                                                            echo anchor($image->image->basic, '<i class="icon-line-stack-2"></i>', ['class' => 'overlay-trigger-icon bg-light text-dark', 'data-lightbox' => 'gallery-item', 'data-hover-animate' => 'fadeInDownSmall', 'data-hover-animate-out' => 'fadeOutUpSmall', 'data-hover-speed' => '350', 'title' => lang('Realizations.item_quick_preview'), 'data-title' => $image->title, 'data-alt' => $image->title]);
                                                        } elseif ($k > 4) {
                                                            break;
                                                        } else {
                                                            echo anchor($image->image->basic, ' ', ['class' => 'd-none', 'data-lightbox' => 'gallery-item', 'title' => $image->title]);
                                                        }
                                                    endforeach; ?>
                                                    <a href="<?= $realization->url ?>" class="overlay-trigger-icon bg-light text-dark" data-hover-animate="fadeInDownSmall" data-hover-animate-out="fadeOutUpSmall" data-hover-speed="350" title="<?= lang('Realizations.go_to_realization') ?>"><i class="icon-line-ellipsis"></i></a>
                                                </div>
                                            </div>
                                            <div class="bg-overlay-bg dark" data-hover-animate="fadeIn"></div>
                                        </div>
                                    </div>
                                    <div class="portfolio-desc">
                                        <h3><?= anchor($realization->url, character_limiter($realization->title, 30), ['title' => $realization->title]) ?></h3>
                                        <span><?= anchor($realization->category->url, character_limiter($realization->category->title, 50), ['title' => $realization->category->title]) ?></span>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                        <?= $pager ?>
                    </div>
                </div><!-- .postcontent end -->
                <!-- Portfolio Items
		        ============================================= -->

                <?php

                $animation['bounce'] = ['bounceIn', 'bounceInDown', 'bounceInUp', 'bounceInLeft', 'bounceInRight'];

                function buildCategoiresRealizationsMenu($menu)
                {
                    $o = 0;
                    $r = '<ul class="list-group">';
                    foreach ($menu as $elMenu) {
                        $r .= '<li class="list-group-item ' . ((strpos(uri_string(), $elMenu->friendly_url) !== false) ? 'active ' : '') . (uri_string() == $elMenu->friendly_url || (empty(uri_string()) && $elMenu->friendly_url == '/') ? 'current' : '') . '"><a href="' . $elMenu->url . '" itemprop="url"><div itemprop="name">' . $elMenu->title . '</div></a>';
                        $r .= '</li>';
                    }
                    $r .= '</ul>';
                    return $r;
                }
                ?>
                <!-- Sidebar
				============================================= -->
                <div class="sidebar col-lg-3">
                    <div class="sidebar-widgets-wrap">

                        <div class="widget clearfix">
                            <div id="categoriesRealizations" class="widget-ocms widget-categories" data-animate="bounceInLeft">
                                <h4 class="widget-title"><?= lang('Realizations.categories_realizations') ?></h4>
                                <?= buildCategoiresRealizationsMenu($categories) ?>
                            </div>
                        </div>

                    </div>
                </div><!-- .sidebar end -->
            </div>

        </div>
    </div>
</section><!-- #content end -->
<?= $this->endSection('') ?>