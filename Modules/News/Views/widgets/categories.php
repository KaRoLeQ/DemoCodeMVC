<div class="widget p-0 clearfix">
    <div id="categoriesNews" class="widget-ocms widget-categories" data-animate="bounceInRight">
        <h4 class="widget-title"><?= lang('News.categories') ?></h4>
        <ul class="list-group">
            <?php foreach ($categories as $category) : ?>
                <li class="list-group-item">
                    <?= anchor(route_to('news_category', $category->friendly_url), $category->name, ['title' => lang('News.go_to_category', ['category' => $category->name])]) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>