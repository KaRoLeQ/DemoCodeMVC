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

<section id="page-title" class="page-title-parallax page-title-center page-title-dark include-header" style="background-image: linear-gradient(to top, rgba(254,150,3,0.5), #39384D), url('/uploads/bg-headers/news.webp'); background-size: cover; padding: 120px 0;" data-bottom-top="background-position:0px 800px;" data-top-bottom="background-position:0px -800px;">
  <div id="particles-line"></div>

  <div class="container clearfix mt-4">
    <div class="badge rounded-pill border border-light text-light"><?= lang('News.news') ?></div>
    <h1><?= lang('News.read_the_news') ?></h1>
    <?= $breadcrumb ?>
  </div>

</section><!-- #page-title end -->

<!-- Content Offers
============================================= -->
<section id="content" class="page-news">
  <div class="content-wrap">
    <div class="container clearfix">

      <div class="row gutter-40 col-mb-80">
        <!-- Post Content
						============================================= -->
        <div class="postcontent col-lg-9">

          <!-- Posts
					============================================= -->
          <div id="posts" class="post-timeline">
            <?php foreach ($posts as $post) : ?>
              <article class="entry">
                <div class="entry-timeline" title="<?= lang('News.date_publication', ['date' => date('j/m/Y H:i', strtotime($post->publication))]) ?>">
                  <?= date('j', strtotime($post->publication)) ?><span><?= dateV('K', strtotime($post->publication)) ?></span>
                  <div class="timeline-divider"></div>
                </div>
                <div class="entry-image">
                  <a href="<?= $post->url ?>" title="<?= lang('News.go_togo_to_news_news_page', ['title' => $post->title]) ?>">
                    <picture>
                      <source srcset="<?= $post->image->webp ?>" type="image/webp">
                      <source srcset="<?= $post->image->basic ?>" type="image/<?= $post->image->type ?>">
                      <img class="lazy offer-icon" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E" width="100" height="100" data-src="<?= $post->image->basic ?>" alt="<?= $post->title . ' - ' . $websiteName ?>">
                    </picture>
                  </a>
                </div>
                <div class="entry-title">
                  <h2><a href="<?= $post->url ?>" title="<?= lang('News.go_to_news', ['title' => $post->title]) ?>"><?= $post->title ?></a></h2>
                </div>
                <div class="entry-meta">
                  <ul>
                    <li title="<?= lang('News.date_publication', ['date' => date('j/m/Y H:i', strtotime($post->publication))]) ?>"><i class="icon-calendar3"></i> <?= date('j/m/Y', strtotime($post->publication)) ?></li>
                    <li><?= anchor($post->category->url, '<i class="icon-tags"></i> ' . $post->category->name) ?></li>
                  </ul>
                </div>
                <div class="entry-content">
                  <p class="text-justify"><?= $post->description ?></p>
                  <?= anchor($post->url, lang('News.btn_read_more'), ['class' => 'more-link', 'title' => lang('News.go_to_news', ['title' => $post->title])]) ?>
                </div>
              </article>
            <?php endforeach; ?>
            <?= $pager ?>
          </div><!-- #posts end -->

        </div><!-- .postcontent end -->

        <!-- Sidebar
						============================================= -->
        <div class="sidebar col-lg-3">
          <div class="sidebar-widgets-wrap">
            <!-- Widget - New & Popular News
					  ============================================= -->
            <?= view('App\Modules\News\Views\widgets\categories', ['categories' => $categories]) ?>
            <!-- Widget - New & Popular News End -->
          </div>

        </div><!-- .sidebar end -->
      </div>

    </div>
  </div>
</section><!-- #content offers end -->
<?= $this->endSection('') ?>