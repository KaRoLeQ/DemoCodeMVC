<?= $this->extend('layout') ?>
<?= $this->section('head-meta') ?>
<?= $this->endSection('') ?>
<?= $this->section('end-body') ?>
<?= script_tag($dirPage . '/js/particles/particles.min.js') ?>
<?= script_tag($dirPage . '/js/particles/particles-line.js') ?>
<?= script_tag($dirPage . '/js/plugins.infinitescroll.js') ?>
<script type="text/javascript">
    var container = $('#portfolio');
    container.append(`
<article id="boxCachce" class="portfolio-item col-sm-6 col-12 pf-icons pf-illustrations">
    <div class="grid-inner">
        <div class="portfolio-image">
            <div class="fslider" data-arrows="false" data-speed="400" data-pause="4000">
                <div class="flexslider">

                <div class="flex-viewport" style="overflow: hidden; position: relative; height: 395.625px;">
                    <div class="slider-wrap" style="width: 800%; transition-duration: 0s; transform: translate3d(-1266px, 0px, 0px);"><div class="slide clone" aria-hidden="true" style="width: 633px; margin-right: 0px; float: left; display: block;"><a href="portfolio-single-gallery.html"><img src="https://ovens.local/uploads/realizations/16-strona-beauty-and-style_1650365133.webp" alt="Morning Dew" draggable="false"></a>
                </div>
                    <div class="slide" style="width: 633px; margin-right: 0px; float: left; display: block;" data-thumb-alt=""><a href="portfolio-single-gallery.html"><img src="images/portfolio/masonry/2/4.jpg" alt="Morning Dew" draggable="false"></a></div>
                    <div class="slide flex-active-slide" data-thumb-alt="" style="width: 633px; margin-right: 0px; float: left; display: block;"><a href="portfolio-single-gallery.html"><img src="https://ovens.local/uploads/realizations/16-strona-beauty-and-style_1650365133.webp" alt="Morning Dew" draggable="false"></a></div>
                    <div class="slide clone" style="width: 633px; margin-right: 0px; float: left; display: block;" aria-hidden="true"><a href="portfolio-single-gallery.html"><img src="images/portfolio/masonry/2/4.jpg" alt="Morning Dew" draggable="false"></a></div></div></div><ol class="flex-control-nav flex-control-paging"><li><a href="#" class="">1</a></li><li><a href="#" class="flex-active">2</a></li></ol>
            </div>
        </div>
    </div>
</article>
        `);
    $(`#boxCachce`).remove();
    drawRealization = (rk, r) => {

        container.append(`
                <article id="portfolioCategory${r.id}" class="portfolio-item col-md-6 col-sm-10 col-12 cat-${r.friendly_url}">
                    <div class="portfolio-image">
                        <div class="fslider" data-arrows="false" data-speed="300" data-pause="3000" data-animation="fade">
                            <div class="flexslider"><div class="slider-wrap"></div></div>
                        </div>
                        </div>
                        <div class="portfolio-desc">
                        <h3><a href="${r.url}">${r.title}</a></h3>
                        <span><?= lang('Realizations.published_realizations') ?>: <strong>${r.itemsCount}</strong></span>
                    </div>
                </article>
        `);

        $.each(r.images, function(ik, i) {
            $(`#portfolioCategory${r.id} .slider-wrap`).append(`
                <div class="slide">
                    <a href="${r.url}" class="box-img-bg" style="background-image: url('${i.image.webp}')" title="${r.title}"></a>
                </div>
            `);

            if (ik == 0) {
                $(`#portfolioRealization${r.id} .d-flex`).append(`
                       <a href="${i.image.basic}" class="overlay-trigger-icon bg-light text-dark" data-lightbox="gallery-item" data-hover-animate="fadeInUpSmall" data-hover-animate-out="fadeOutDownSmall" data-hover-speed="350" title="<?= lang('Realizations.item_quick_preview') ?>" data-title="${r.title}" data-alt="${r.title}"><i class="icon-line-stack-2"></i></a>
                `);
            } else if (ik >= 4) {
                return false;
            }
        });
    };

    $.ajax({
        type: 'POST',
        url: '<?= route_to('api_realizations') ?>',
        processData: false,
        contentType: false,
        success: function(data) {
            $.each(data.realizationsCategories, function(rk, r) {
                drawRealization(rk, r);
            });
        }
    });
</script>
<?= $this->endSection('') ?>
<?= $this->section('content') ?>
<!-- Page Title
============================================= -->

<section id="page-title" class="page-title-parallax page-title-center page-title-dark include-header" style="background-image: linear-gradient(to top, rgba(254,150,3,0.5), #39384D), url('/uploads/bg-headers/realizations.webp'); background-size: cover; padding: 120px 0;" data-bottom-top="background-position:0px 300px;" data-top-bottom="background-position:0px -300px;">
    <div id="particles-line"></div>

    <div class="container clearfix mt-4">
        <div class="badge rounded-pill border border-light text-light"><?= lang('Realizations.realizations') ?></div>
        <h1><?= lang('Realizations.see_our_realizations') ?></h1>
        <?= $breadcrumb ?>
    </div>

</section><!-- #page-title end -->

<!-- Content
============================================= -->
<section id="content">
    <div class="content-wrap">
        <div class="container clearfix">
            <!-- Portfolio Items
		    ============================================= -->
            <div id="portfolio" class="portfolio row portfolio-2 g-10"></div>

        </div>
    </div>
</section><!-- #content end -->
<?= $this->endSection('') ?>