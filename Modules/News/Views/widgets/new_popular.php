<div class="widget p-0 widget-tabs clearfix">
    <div id="newPopularNews" class="widget-ocms widget-news widget-news-tabs tabs" data-animate="fadeInRight" data-delay="100">
        <h4 class=" widget-title clearfix">Aktualności</h4>
        <ul class="tab-nav clearfix">
            <li><a href="#pupular"><?= lang('News.tab_popular') ?></a></li>
            <li><a href="#new"><?= lang('News.tab_new') ?></a></li>
        </ul>
        <div class="tab-container">
            <div class="tab-content clearfix" id="pupular">
                <div id="popular-post-list-sidebar">
                    <?php foreach ($newPopularPosts->popular as $key => $post) : ?>
                        <div class="spost clearfix">
                            <div class="entry-image">
                                <a href="<?= $post->url ?>" class="nobg" title="<?= lang('News.go_to_news', ['title' => $post->title]) ?>">
                                    <picture>
                                        <source srcset="<?= $post->image->webp ?>" type="image/webp">
                                        <source srcset="<?= $post->image->basic ?>" type="image/<?= $post->image->type ?>">
                                        <img class="lazy offer-icon" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E" width="100" height="100" data-src="<?= $post->image->basic ?>" alt="<?= $post->title . ' - ' . $websiteName ?>">
                                    </picture>
                                </a>
                            </div>
                            <div class="entry-c">
                                <div class="entry-title">
                                    <h4><a href="<?= $post->url ?>" title="<?= lang('News.go_to_news', ['title' => $post->title]) ?>"><?= character_limiter($post->title, 28) ?></a></h4>
                                </div>
                                <ul class="entry-meta">
                                    <li title="<?= lang('News.date_publication', ['date' => date('j/m/Y H:i', strtotime($post->publication))]) ?>"><i class="icon-calendar3"></i> <?= date('j/m/Y', strtotime($post->publication)) ?></li>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="tab-content clearfix" id="new">
                <div id="recent-post-list-sidebar">
                    <?php foreach ($newPopularPosts->new as $key => $post) : ?>
                        <div class="spost clearfix">
                            <div class="entry-image">
                                <a href="<?= $post->url ?>" class="nobg" title="<?= lang('news.go_to_news', ['title' => $post->title]) ?>">
                                    <picture>
                                        <source srcset="<?= $post->image->webp ?>" type="image/webp">
                                        <source srcset="<?= $post->image->basic ?>" type="image/<?= $post->image->type ?>">
                                        <img class="lazy offer-icon" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E" width="100" height="100" data-src="<?= $post->image->basic ?>" alt="<?= $post->title . ' - ' . $websiteName ?>">
                                    </picture>
                                </a>
                            </div>
                            <div class="entry-c">
                                <div class="entry-title">
                                    <h4><a href="<?= $post->url ?>" title="<?= lang('news.go_to_news', ['title' => $post->title]) ?>"><?= character_limiter($post->title, 28) ?></a></h4>
                                </div>
                                <ul class="entry-meta">
                                    <li title="<?= lang('News.date_publication', ['date' => date('j/m/Y H:i', strtotime($post->publication))]) ?>"><i class="icon-calendar3"></i> <?= date('j/m/Y', strtotime($post->publication)) ?></li>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>