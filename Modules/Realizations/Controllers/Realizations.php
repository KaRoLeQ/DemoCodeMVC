<?php

namespace App\Modules\Realizations\Controllers;

use App\Modules\Realizations\Models\RealizationsModel;
use App\Controllers\PublicController;
use App\Models\SeoModel;

class Realizations extends PublicController
{
    private $RealizationsModel;
    private $cfgRealizations;
    private $limitPosts = 12;

    public function __construct()
    {
        parent::__construct();

        $this->RealizationsModel = new RealizationsModel();

        $this->cfgRealizations = config('App\Modules\Realizations\Config\Realizations');
    }

    // Views public
    public function index()
    {
        $this->data['realizations']         = [];
        // $this->data['realizations']         = $this->RealizationsModel->getRealizations();
        $this->data['realizationsCount']    = count((array) $this->data['realizations']);

        $this->data['categories']       = $this->RealizationsModel->getCategories();
        $this->data['categoriesCount']  = count((array) $this->data['categories']);

        $this->breadcrumb->add(lang('OvenCms.page_home'), route_to('home'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('realizations'));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        $seoData = $this->SeoModel->getPublicPage('realizations');

        $this->data['seo'] = (object) [
            'title'        => !empty($seoData->title) ? $seoData->title : lang('Realizations.seo_page_realizations') . ' - ' . $this->data['websiteName'],
            'desc'        => !empty($seoData->title) ? $seoData->description : lang('Realizations.seo_page_realizations_desc'),
            'imgurl'    => !empty($seoData->title) ? $seoData->image : '',
            'sitetype'    => 'website',
            'json'        => !empty($seoData->json) ? $seoData->json : '',
            'other'        => !empty($seoData->other) ? $seoData->other : ''
        ];

        return view('App\Modules\Realizations\Views\index', $this->data);
    }

    public function category(string $categoryFriendlyUrl, int $page = 1)
    {
        $category   = $this->RealizationsModel->getCategory('friendly_url', $categoryFriendlyUrl);

        if (!$category) {
            return redirect()->to(route_to('realizations'));
        }

        $pager = \Config\Services::pager();
        $start = ($page - 1) * $this->limitPosts;
        $posts = $category->items;

        if ($page != 1 && empty((array) $posts))
            return redirect()->to(route_to('realizations'));

        $postsCount = isset($posts) ? count((array) $posts) : 0;

        $this->data['pager'] = $pager->makeLinks($page, $this->limitPosts, $postsCount, 'ocms', 3);
        $this->data['page']  = $page;
        $this->data['pages'] = ceil($postsCount / $this->limitPosts);

        $category->realizations      = array_slice((array) $posts, $start, $this->limitPosts);
        $category->realizationsCount = count((array) $category->realizations);
        if ($page != 1 && empty((array) $posts))
            return redirect()->to(route_to('realizations'));

        $this->data['category']         = $category;
        $this->data['categories']       = $this->RealizationsModel->getCategories();
        $this->data['categoriesCount']  = count((array) $this->data['categories']);

        $this->breadcrumb->add(lang('OvenCms.page_home'), route_to('home'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('realizations'));
        $this->breadcrumb->add($category->title, route_to('realizations_category', $category->friendly_url, $category->friendly_url));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        $seoData = $this->SeoModel->getPublicModuleItem('realizations_categories', $category->id);

        $this->data['seo'] = (object) [
            'title'     => !empty($seoData->title) ? $seoData->title : lang('Realizations.seo_page_realizations') . ' - ' . $category->title . ' - ' . $this->data['websiteName'],
            'desc'      => !empty($seoData->title) ? $seoData->description : lang('Realizations.seo_page_realizations_category_desc', ['category' => $category->title]),
            'imgurl'    => !empty($seoData->title) ? $seoData->image : '',
            'sitetype'  => 'website',
            'json'      => !empty($seoData->json) ? $seoData->json : '',
            'other'     => !empty($seoData->other) ? $seoData->other : ''
        ];

        return view('App\Modules\Realizations\Views\category', $this->data);
    }

    public function view_item(string $categoryFriendlyUrl, string $realizationFriendlyUrl)
    {
        $realization = $this->RealizationsModel->getRealization('friendly_url', $realizationFriendlyUrl);
        if (!$realization) {
            return redirect()->to(route_to('realizations_category', $categoryFriendlyUrl));
        }

        $this->breadcrumb->add(lang('OvenCms.page_home'), route_to('home'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('realizations'));
        $this->breadcrumb->add($realization->category->title, route_to('realizations_category', $realization->category->friendly_url));
        $this->breadcrumb->add($realization->title, route_to('realizations_item', $realization->category->friendly_url, $realization->friendly_url));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        $seoData = $this->SeoModel->getPublicModuleItem('realizations', $realization->id);

        $this->data['seo'] = (object) [
            'title'        => !empty($seoData->title) ? $seoData->title : lang('Realizations.seo_page_realization', ['title' => $realization->title]) . ' - ' . $this->data['websiteName'],
            'desc'        => !empty($seoData->title) ? $seoData->description : lang('Realizations.seo_page_realization_desc', ['realization' => $realization->title, 'category' => $realization->category->title]),
            'imgurl'    => !empty($seoData->title) ? $seoData->image : '',
            'sitetype'    => 'website',
            'json'        => !empty($seoData->json) ? $seoData->json : '',
            'other'        => !empty($seoData->other) ? $seoData->other : ''
        ];

        $realization->similar = $this->RealizationsModel->getSimilarRealizations($realization->id, $realization->category->id);
        $this->data['realization'] = $realization;

        return view('App\Modules\Realizations\Views\realization', $this->data);
    }



    //API
    public function getRealizations()
    {
        $realizations   = $this->RealizationsModel->getRealizations();
        $categories     = $this->RealizationsModel->getCategories();

        $relCat = [];
        $relCatImg = [];

        foreach ($realizations as $realization) {
            $relCat[$realization->category->friendly_url][] = $realization;
            foreach ($realization->images as $image) {
                $relCatImg[$realization->category->friendly_url][] = $image;
            }
        }

        foreach ($categories as $category) {
            $category->items = (object) $relCat[$category->friendly_url];
            $category->itemsCount = count((array) $relCat[$category->friendly_url]);
            $category->images = (object) $relCatImg[$category->friendly_url];
            $category->imagesCount = count((array) $relCatImg[$category->friendly_url]);
        }

        $r = (object) [
            'realizations'      => $realizations,
            'realizationsCount' => count((array) $realizations),
            'realizationsCategories'      => $categories,
            'realizationsCategoriesCount' => count((array) $categories)
        ];

        return $this->response->setJSON((object)$r);
    }

    public function getRealizationsCategory()
    {
        $r = (object) [
            'id'    => $this->request->getVar("categoryId") ?: '',
            'fu'    => $this->request->getVar("categoryFriendlyUrl") ?: '',
        ];

        if ($this->request->getVar('categoryId') || $this->request->getVar('categoryFriendlyUrl')) {
            if ($this->request->getVar('categoryId')) {
                $col = 'id';
                $parm = $this->request->getVar('categoryId');
            } else {
                $col = 'friendly_url';
                $parm = $this->request->getVar('categoryFriendlyUrl');
            }

            $realizations = $this->RealizationsModel->getRealizations($col, $parm);
            if (empty($realizations)) {
                $r = [
                    'error' => [
                        'code' => 212,
                        'message' => lang('Realizations.error_211', ['key' => $col, 'value' => $parm]),
                    ]
                ];
            } else {
                $categories     = $this->RealizationsModel->getCategories();
                $r = (object) [
                    'realizations'                => $realizations,
                    'realizationsCount'           => count((array) $realizations),
                    'realizationsCategories'      => $categories,
                    'realizationsCategoriesCount' => count((array) $categories)
                ];
            }
        } else {
            $r = [
                'error' => [
                    'code' => 101,
                    'message' => lang('Realizations.error_101'),
                ]
            ];
        }

        return $this->response->setJSON((object)$r);
    }

    public function getRealization()
    {
        $r = (object) [
            'id'    => $this->request->getVar("realizationId") ?: '',
            'fu'    => $this->request->getVar("realizationFriendlyUrl") ?: '',
        ];

        if ($this->request->getVar('realizationId') || $this->request->getVar('realizationFriendlyUrl')) {
            if ($this->request->getVar('realizationId')) {
                $col = 'id';
                $parm = $this->request->getVar('realizationId');
            } else {
                $col = 'friendly_url';
                $parm = $this->request->getVar('realizationFriendlyUrl');
            }

            $realization = $this->RealizationsModel->getRealization($col, $parm);
            if (empty($realization)) {
                $r = [
                    'error' => [
                        'code' => 211,
                        'message' => lang('Realizations.error_211', ['key' => $col, 'value' => $parm]),
                    ]
                ];
            } else {
                $r = $realization;
            }
        } else {
            $r = [
                'error' => [
                    'code' => 100,
                    'message' => lang('Realizations.error_100'),
                ]
            ];
        }

        return $this->response->setJSON((object)$r);
    }
}
