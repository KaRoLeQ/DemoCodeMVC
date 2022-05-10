<?php

namespace App\Modules\News\Controllers;

use App\Modules\News\Models\NewsModel;
use App\Controllers\PublicController;

class News extends PublicController
{
    private $limitPosts = 5;
    private $cfgNews;

    public function __construct()
    {
        parent::__construct();

        $this->NewsModel = new NewsModel();

        $this->cfgNews = config('App\Modules\News\Config\News');
    }
    public function posts(int $page = 1)
    {
        $pager = \Config\Services::pager();

        $start = ($page - 1) * $this->limitPosts;

        $posts = $this->NewsModel->getPosts();
        if ($page != 1 && empty((array) $posts))
            return redirect()->to(route_to('news'));

        $postsCount = isset($posts) ? count((array) $posts) : 0;

        $this->data['pager'] = $pager->makeLinks($page, $this->limitPosts, $postsCount, 'ocms', 2);
        $this->data['page'] = $page;
        $this->data['pages'] = ceil($postsCount / $this->limitPosts);

        $this->breadcrumb->add(lang('OvenCms.page_home'), route_to('home'));
        $this->breadcrumb->add(lang('News.news_title'), route_to('news'));
        $this->data['breadcrumb'] = $this->breadcrumb->render();

        $this->data['posts']        = array_slice((array) $posts, $start, $this->limitPosts);
        if ($page != 1 && empty((array) $this->data['posts']))
            return redirect()->to(route_to('news'));

        $this->data['categories']   = $this->NewsModel->getCategories();
        $this->data['popularPosts'] = $this->NewsModel->getPopularPosts(5);

        if (isset($this->data['segments'][1])) {
            $seoData = $this->SeoModel->getPublicPage('news_pager', ['page' => $page]);
        } else {
            $seoData = $this->SeoModel->getPublicPage('news');
        }

        $this->data['seo'] = (object) [
            'title'        => !empty($seoData->title) ? $seoData->title : lang('PageSeo.seo_page_news') . ' - ' . $this->data['websiteName'],
            'desc'        => !empty($seoData->title) ? $seoData->description : '',
            'imgurl'    => !empty($seoData->title) ? $seoData->image : '',
            'sitetype'    => 'website',
            'json'        => !empty($seoData->json) ? $seoData->json : '',
            'other'        => !empty($seoData->other) ? $seoData->other : ''
        ];

        return view('App\Modules\News\Views\index', $this->data);
    }

    public function category(string $getCategory, int $page = 1)
    {
        $pager = \Config\Services::pager();

        $this->NewsModel = new NewsModel();

        $start = ($page - 1) * $this->limitPosts;

        $category = $this->NewsModel->getCategory($getCategory);
        if (!$category)
            return redirect()->to(route_to('news'));

        $postsCount = isset($category->posts) ? count((array) $category->posts) : 0;

        $this->data['pager'] = $pager->makeLinks($page, $this->limitPosts, $postsCount, 'ocms', 3);
        $this->data['page'] = $page;
        $this->data['pages'] = ceil($postsCount / $this->limitPosts);

        $this->breadcrumb->add(lang('OvenCms.page_home'), route_to('home'));
        $this->breadcrumb->add(lang('News.news_title'), route_to('news'));
        $this->breadcrumb->add($category->name, route_to('news_category', $getCategory));
        $this->data['breadcrumb'] = $this->breadcrumb->render();

        $category->posts = array_slice((array) $category->posts, $start, $this->limitPosts);
        if ($page != 1 && empty((array) $category->posts))
            return redirect()->to(route_to('news_category', $getCategory));

        $this->data['category']     = $category;
        $this->data['categories']   = $this->NewsModel->getCategories();
        $this->data['popularPosts'] = $this->NewsModel->getPopularPosts(3);

        if (isset($this->data['segments'][2])) {
            $seoData = $this->SeoModel->getPublicPage('news_category_pager', ['page' => $page, 'category' => $category->name]);
        } else {
            $seoData = $this->SeoModel->getPublicModuleItem($this->cfgNews->database['categories'], $category->id);
            if (!$seoData)
                $seoData = $this->SeoModel->getPublicPage('news_category', ['category' => $category->name]);
        }

        $this->data['seo'] = (object) [
            'title'        => !empty($seoData->title) ? $seoData->title : lang('PageSeo.seo_page_news_category', ['category' => $category->name]) . ' - ' . $this->data['websiteName'],
            'desc'        => !empty($seoData->title) ? $seoData->description : '',
            'imgurl'    => !empty($seoData->title) ? $seoData->image : '',
            'sitetype'    => 'website',
            'json'        => !empty($seoData->json) ? $seoData->json : '',
            'other'        => !empty($seoData->other) ? $seoData->other : ''
        ];

        return view('App\Modules\News\Views\category', $this->data);
    }

    public function post(int $postId, string $postFriendlyUrl)
    {
        $post = $this->NewsModel->getPost($postId, $postFriendlyUrl);

        if (!$post)
            return redirect()->to(route_to('news'));

        $this->data['post'] = $post;

        $this->NewsModel->addViewPost($postId, $post->views);

        $this->breadcrumb->add(lang('OvenCms.page_home'), route_to('home'));
        $this->breadcrumb->add(lang('News.news'), route_to('news'));
        $this->breadcrumb->add($post->category->name, route_to('news_category', $post->category->friendly_url));
        $this->breadcrumb->add($post->title, route_to('news_post', $post->id, $post->friendly_url));
        $this->data['breadcrumb'] = $this->breadcrumb->render();

        $this->data['categories']   = $this->NewsModel->getCategories();

        $this->data['newPopularPosts'] = (object) [
            'new' => $this->NewsModel->getPosts(5),
            'popular' => $this->NewsModel->getPopularPosts(5)
        ];

        $seoData = $this->SeoModel->getPublicModuleItem($this->cfgNews->database['news'], $post->id);

        $this->data['seo'] = (object) [
            'title'        => !empty($seoData->title) ? $seoData->title : lang('PageSeo.seo_page_news_post', ['title' => $post->title]) . ' - ' . $this->data['websiteName'],
            'desc'        => !empty($seoData->title) ? $seoData->description : '',
            'imgurl'    => !empty($seoData->title) ? $seoData->image : '',
            'sitetype'    => 'website',
            'json'        => !empty($seoData->json) ? $seoData->json : '',
            'other'        => !empty($seoData->other) ? $seoData->other : ''
        ];

        return view('App\Modules\News\Views\view', $this->data);
    }
}
