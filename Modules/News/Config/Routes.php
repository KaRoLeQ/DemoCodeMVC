<?php

if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
/* Public */

$posts = [];
$posts[lang('News.public_url')]				= 'News::posts';
$posts[lang('News.public_url') . '/(:num)']	= 'News::posts/$1';

$routes->map($posts, ['namespace' => 'App\Modules\News\Controllers']);

$category = [];
$category[lang('News.public_url') . '/(:any)']	= 'News::category/$1';
$category[lang('News.public_url') . '/(:any)/(:num)']	= 'News::category/$1/$2';

$routes->map($category, ['namespace' => 'App\Modules\News\Controllers']);

/* Public tres*/
$routes->get(lang('News.public_url'), 'News::posts', ['namespace' => 'App\Modules\News\Controllers', 'as' => 'news']);
$routes->group(lang('News.public_url'), ['namespace' => 'App\Modules\News\Controllers'], function ($routes) {
	$routes->get('(:num)', 'News::posts/$1');
	$routes->get('(:any)', 'News::category/$1', ['as' => 'news_category']);
	$routes->get('(:any)/(:num)', 'News::category/$1/$2', ['as' => 'news_category']);
});
$routes->get(lang('News.public_url_post') . '/(:num)/(:any)', 'News::post/$1/$2', ['namespace' => 'App\Modules\News\Controllers', 'as' => 'news_post']);

/* Admin */
$routes->group('admin', ['namespace' => 'App\Modules\News\Controllers\Admin', 'filter' => 'isadmin'], function ($routes) {
	$routes->get('news', 'News::index', ['as' => 'admin_news']);
	$routes->group('news', ['namespace' => 'App\Modules\News\Controllers\Admin'], function ($routes) {
		$routes->get('posts', 'News::posts', ['as' => 'admin_news_posts']);
		$routes->get('post/create', 'News::create_post', ['as' => 'admin_news_post_create']);
		$routes->get('post/(:num)', 'News::view_post/$1', ['as' => 'admin_news_post']);
		$routes->get('post/(:num)/edit', 'News::edit_post/$1', ['as' => 'admin_news_edit']);
		$routes->get('post/(:num)/remove', 'News::remove_post/$1', ['as' => 'admin_news_post_remove']);

		$routes->post('createPost', 'News::createPost', ['as' => 'admin_news_createPost']);
		$routes->post('editPost', 'News::editPost', ['as' => 'admin_news_editPost']);
		$routes->post('removePost', 'News::removePost', ['as' => 'admin_news_removePost']);

		$routes->get('categories', 'News::categories', ['as' => 'admin_news_categories']);
		$routes->get('category/create', 'News::create_category', ['as' => 'admin_news_category_create']);
		$routes->get('category/(:num)', 'News::view_category/$1', ['as' => 'admin_news_category']);
		$routes->get('category/(:num)/edit', 'News::edit_category/$1', ['as' => 'admin_news_category_edit']);
		$routes->get('category/(:num)/remove', 'News::remove_category/$1', ['as' => 'admin_news_category_remove']);

		$routes->post('createCategory', 'News::createCategory', ['as' => 'admin_news_createCategory']);
		$routes->post('editCategory', 'News::editCategory', ['as' => 'admin_news_editCategory']);
		$routes->post('removeCategory', 'News::removeCategory', ['as' => 'admin_news_removeCategory']);
	});
});
