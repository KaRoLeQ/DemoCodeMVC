<?php

namespace App\Modules\News\Controllers\Admin;

use App\Modules\News\Models\Admin\NewsModel;
use App\Controllers\Admin\AdminController;
use App\Models\SeoModel;
use CodeIgniter\HTTP\Files\UploadedFile;

class News extends AdminController
{
    private $imageWeight; //kb

    private $NewsModel;
    private $cfgNews;

    public function __construct()
    {
        parent::__construct();

        $this->NewsModel = new NewsModel();
        $this->cfgNews = config('App\Modules\News\Config\News');
        $this->imageWeight = $this->cfgNews->image['maxWeight'];
    }

    public function index()
    {
        $this->data['posts']      = $this->NewsModel->getPosts();
        $this->data['categories'] = $this->NewsModel->getCategories();

        return view('App\Modules\News\Views\admin\index', $this->data);
    }


    public function posts()
    {
        $this->data['posts'] = $this->NewsModel->getPosts();

        return view('App\Modules\News\Views\admin\posts\index', $this->data);
    }

    public function createPost()
    {
        $this->validation = \Config\Services::validation();

        $this->validation->setRule('title', lang('News.post_title_label'), 'trim|required');
        $this->validation->setRule('friendly_url', lang('News.index_friendly_url'), 'trim|required');
        if (!empty($_FILES['image']['name']))
            $this->validation->setRule('image', lang('News.post_image_label'), 'uploaded[image]|max_size[image,' . $this->imageWeight . ']|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]');
        $data = [
            'title'         => $this->request->getPost('title'),
            'content'       => $this->request->getPost('content'),
            'author'        => session('user_id'),
            'friendly_url'  => $this->request->getPost('friendly_url'),
            'visible'       => $this->request->getPost('visible'),
            'publication'   => $this->request->getPost('publication') . ':00',
            'categories'    => $this->request->getPost('categories') ? $this->request->getPost('categories') : [],
            'tags'          => $this->request->getPost('tags') ? implode(',', $this->request->getPost('tags')) : '',
        ];

        if (isset($data) && $this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $newsId = $this->NewsModel->createPost($data);
            if ($newsId) {
                $imageFileName = $newsId . '-' . $data['friendly_url'] . '_' . time();
                $imageSrc = $this->cfgNews->image['fileLocation'] . '/' . $imageFileName;
                if (!empty($_FILES['image']['name'])) {
                    $image = $this->request->getFile('image');
                    $imageFileName .= '.' . $image->guessExtension();
                    //Move new image
                    $image->move($this->cfgNews->image['fileLocation'], $imageFileName);
                    //Create image format WEBP
                    copy($this->cfgNews->image['fileLocation'] . '/' . $imageFileName, $imageSrc . '.webp');

                    //? Remove additional image
                    if ($this->cfgNews->image['saveOrginal']) {
                        $dataImage['image'] = '/' . $this->cfgNews->image['fileLocation'] . '/' . $imageFileName;
                    } else {
                        unlink($this->cfgNews->image['fileLocation'] . '/' . $imageFileName);
                        $dataImage['image'] = '/' . $imageSrc . '.webp';
                    }
                } else {
                    $imageDefault = base64_decode($this->cfgNews->image['default']['png']['base64']);
                    //? Remove additional image
                    if ($this->cfgNews->image['saveOrginal']) {
                        //Create default image format WEBP
                        file_put_contents($imageSrc . '.webp', $imageDefault);
                        $imageSrc .= '.png';
                        //Create default image format PNG
                        file_put_contents($imageSrc, $imageDefault);
                        $dataImage['image'] = '/' . $imageSrc;
                    } else {
                        $imageSrc .= '.webp';
                        $dataImage['image'] = '/' . $imageSrc;
                        //Create default image format WEBP
                        file_put_contents($imageSrc, $imageDefault);
                    }
                }

                $this->NewsModel->updatePost($newsId, $dataImage);

                // SEO
                $SeoModel = new SeoModel();
                $SeoModel->seoCreate('news_posts', ['item' => $newsId]);

                $response['errors'] = [];
                $response['success'] = ['id' => $newsId, 'url' => base_url(route_to('admin_news_category_edit', $newsId))];
                return $this->response->setJSON($response);
            } else {
                $response['errors'] = [lang('News.create_post_error_key') => lang('News.create_post_error_messages')];
                return $this->response->setJSON($response);
            }
        }

        $response['errors'] = $this->validation->getErrors();
        return $this->response->setJSON($response);
    }

    public function create_post()
    {
        helper(['form', 'ocms_form']);

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('News.news'), route_to('admin_news'));
        $this->breadcrumb->add(lang('OvenCms.create'), route_to('admin_news_post_create'));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        $this->data = array_merge($this->data, $this->news_form_elements());
        $this->data['imageWeight'] = $this->imageWeight;

        return view('App\Modules\News\Views\admin\posts\create', $this->data);
    }

    public function editPost()
    {
        $this->validation = \Config\Services::validation();

        $itemId = $this->request->getPost('postId');
        $postData = $this->request->getPost();

        $this->validation->setRule('title', lang('News.post_title_label'), 'trim|required');
        $this->validation->setRule('friendly_url', lang('News.index_friendly_url'), 'trim|required');
        if (!empty($_FILES['image']['name']))
            $this->validation->setRule('image', lang('News.post_image_label'), 'uploaded[image]|max_size[image,' . $this->imageWeight . ']|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]');
        $data = [
            'title'         => $this->request->getPost('title'),
            'content'       => $this->request->getPost('content'),
            'author'        => $this->request->getPost('author'),
            'friendly_url'  => $this->request->getPost('friendly_url'),
            'visible'       => $this->request->getPost('visible'),
            'publication'   => $this->request->getPost('publication') . ':00',
            'categories'    => $this->request->getPost('categories') ? $this->request->getPost('categories') : [],
            'tags'          => $this->request->getPost('tags') ? implode(',', $this->request->getPost('tags')) : '',
        ];

        if (!empty($_FILES['image']['name'])) {
            $imageFront = $this->request->getFile('image');
            $imageFrontFileName = $itemId . '-' . $data['friendly_url'] . '_' . time();
            $imageFrontFormat = $imageFront->guessExtension();
            $imageFrontSrc = $this->cfgNews->image['fileLocation'] . '/' . $imageFrontFileName;

            //? Additional image
            if ($this->cfgNews->image['saveOrginal']) {
                $data['image'] = '/' . $this->cfgNews->image['fileLocation'] . '/' . $imageFrontFileName . '.' . $imageFront->guessExtension();
            } else {
                $data['image'] = '/' . $imageFrontSrc . '.webp';
            }
        }

        if (isset($data) && $this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

            if ($this->NewsModel->updatePost($itemId, $data)) {
                $orgImageFront = $this->request->getPost('imageSrc');
                $response['orgImageFront'] = $orgImageFront;
                if (!empty($_FILES['image']['name'])) {
                    //Move new image
                    $imageFront->move($this->cfgNews->image['fileLocation'], $imageFrontFileName . '.' . $imageFrontFormat);
                    //Create image format WEBP
                    copy($imageFrontSrc . '.' . $imageFrontFormat, $imageFrontSrc . '.webp');

                    //? Remove additional image
                    if ($this->cfgNews->image['saveOrginal']) {
                        $response['image'] = base_url('/' . $imageFrontSrc . '.' . $imageFrontFormat);
                    } else {
                        unlink($imageFrontSrc . '.' . $imageFrontFormat);
                        $response['image'] = base_url('/' . $imageFrontSrc . '.webp');
                    }

                    $orgImageFront = str_replace(base_url(), '', $orgImageFront);
                    $orgImageFront = substr($orgImageFront, 1);

                    if (!empty($orgImageFront) && file_exists($orgImageFront)) {
                        unlink($orgImageFront);
                        $imgSeparator = explode('.', $orgImageFront);
                        $imgFormat = end($imgSeparator);
                        if ($imgFormat != 'webp' && file_exists($imgSeparator[0] . '.webp')) {
                            unlink($imgSeparator[0] . '.webp');
                        }
                    }
                } else {
                    $response['image'] = $orgImageFront;
                    if (($this->request->getPost('title') != $this->request->getPost('itemTitle')) && !empty($orgImageFront)) {
                        $newFileImage = '/' . $this->cfgNews->image['fileLocation'] . '/' . $itemId . '-' . $data['friendly_url'] . '_' . time() . '.' . pathinfo($orgImageFront, PATHINFO_EXTENSION);
                        rename('.' . $orgImageFront, '.' . $newFileImage);
                        $response['image'] = $newFileImage;
                        $this->OffersModel->updateItem($itemId, ['image' => $newFileImage]);
                    }
                }

                $response['errors'] = [];
                return $this->response->setJSON($response);
            } else {
                $response['errors'] = [lang('News.edit_post_error_key') => lang('News.edit_post_error_messages')];
                return $this->response->setJSON($response);
            }
        }
        $response['errors'] = $this->validation->getErrors();

        return $this->response->setJSON($response);
    }

    public function view_post(int $id)
    {
        helper(['form', 'ocms_form']);


        $post = $this->NewsModel->getPost($id);
        $this->data['post'] = $post;
        if (!$this->data['post'])
            return redirect()->to(route_to('admin_news'));

        return view('App\Modules\News\Views\admin\posts\view', $this->data);
    }

    public function edit_post(int $id)
    {
        helper(['form', 'ocms_form']);
        $post = $this->NewsModel->getPost($id);
        $this->data['post'] = $post;
        if (!$this->data['post'])
            return redirect()->to(route_to('admin_news'));

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('News.news'), route_to('admin_news'));
        $this->breadcrumb->add(lang('News.post') . ' #' . $id, route_to('admin_news_post', $id));
        $this->breadcrumb->add(lang('OvenCms.edit'), route_to('admin_news_edit', $id));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        $this->data = array_merge($this->data, $this->news_form_elements($post));
        $this->data['imageWeight'] = $this->imageWeight;

        // SEO form
        $SeoModel = new SeoModel();
        $itemSeo = $SeoModel->getModuleItem('news_posts', $id);
        $this->data['itemSeo'] = $itemSeo;
        $this->data['seoTitle'] = [
            'name'        => 'title',
            'id'          => 'seoTitle',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('PageSeo.seo_title_placeholder'),
            'value'       => set_value('title', $itemSeo->title ?: ''),
        ];
        $this->data['seoDescription'] = [
            'name'        => 'description',
            'id'          => 'seoDescription',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('PageSeo.seo_description_placeholder'),
            'value'       => set_value('description', $itemSeo->description ?: ''),
        ];
        $this->data['seoImage'] = [
            'name'        => 'image',
            'id'          => 'seoImage',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('PageSeo.seo_image_placeholder'),
            'value'       => set_value('title', $itemSeo->image ?: ''),
        ];
        $this->data['seoRobot'] = [
            'name'        => 'robot',
            'id'          => 'seoRobot',
            'type'        => 'checkbox',
            'value'       => 1,
            'checked'     => $itemSeo->robot ? TRUE : FALSE,
        ];
        $this->data['seoJson'] = [
            'name'        => 'json',
            'id'          => 'seoJson',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('PageSeo.seo_json_placeholder'),
        ];
        $this->data['seoOther'] = [
            'name'        => 'other',
            'id'          => 'seoOther',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('PageSeo.seo_other_placeholder'),
        ];

        return view('App\Modules\News\Views\admin\posts\edit', $this->data);
    }

    public function removePost()
    {
        $postId = $this->request->getPost('postId');
        $postImage = $this->request->getPost('postImage');


        if ($postId && $this->NewsModel->removePost($postId)) {
            $response['errors'] = [];

            if (!empty($postImage)) {
                unlink(substr($postImage, 1));
                $response['removeImage'] = true;
            } else {
                $response['removeImage'] = false;
            }
        } else {
            $response['errors'] = [lang('News.remove_post_error_key') => lang('News.remove_post_error_messages')];
        }

        return $this->response->setJSON($response);
    }

    public function remove_post($id)
    {
        helper(['form', 'html']);


        $this->data['post'] = $this->NewsModel->getPost($id);
        if (!$this->data['post'])
            return redirect()->to(route_to('admin_news'));

        return view('App\Modules\News\Views\admin\posts\remove', $this->data);
    }

    public function news_form_elements($data = NULL)
    {
        $el['title'] = [
            'name'        => 'title',
            'id'          => 'title',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('News.post_title_placeholder'),
            'required'    => 'required',
        ];
        $el['publication'] = [
            'name'          => 'publication',
            'id'            => 'publication',
            'type'          => 'text',
            'data-target'   => '#publication',
            'class'         => 'form-control datetimepicker-input',
            'placeholder'   => lang('News.post_publication_placeholder'),
            'value'         => set_value('publication', date('Y-m-d H:i')),
            'required'      => 'required',
        ];
        $el['categoriesList'] = $this->NewsModel->arrayCategoriesToFormSelect();
        $el['categoriesOptions'] = [
            'name'              => 'categories',
            'id'                => 'categories',
            'class'             => 'form-control select2',
            'style'             => 'width: 100%;',
            'data-placeholder'  => lang('News.categories_placeholder'),
            'required'          => 'required',
        ];
        $el['tagsOptions'] = [
            'name'              => 'tags',
            'id'                => 'tags',
            'multiple'          => 'multiple',
            'class'             => 'form-control tags-select2',
            'style'             => 'width: 100%;',
            'data-placeholder'  => lang('News.tags_placeholder'),
        ];
        $el['imageForm'] = [
            'name'                          => 'image',
            'class'                         => 'dropify',
            'id'                            => 'image',
            'type'                          => 'file',
            'data-allowed-file-extensions'  => 'jpg jpeg png',
            'accept'                        => 'image/jpeg,image/jpg,image/png',
            'required'                      => 'required',
        ];
        $el['visible'] = [
            'name'        => 'visible',
            'id'          => 'visible',
            'type'        => 'checkbox',
            'value'       => 1,
            'checked'     => FALSE,
        ];
        $el['content'] = [
            'name'        => 'content',
            'id'          => 'description',
            'type'        => 'textarea',
            'class'       => 'form-control',
            'placeholder' => lang('News.post_content_placeholder'),
        ];

        if (!empty($data)) {
            $el['title']['value']       = set_value('title', $data->title ?: '');
            $el['publication']['value']       = set_value('publication', $data->publication ?: '');
            $el['imageForm'] = [
                'name'                          => 'image',
                'class'                         => 'dropify',
                'id'                            => 'image',
                'type'                          => 'file',
                'data-default-file'             => $data->image->basic ?: '',
                'data-allowed-file-extensions'  => 'jpg jpeg png',
                'accept'                        => 'image/jpeg,image/jpg,image/png',
            ];
            $el['visible']['checked']           = $data->visible ? true : false;
            $el['authorUsersList'] = $this->arrayUsersToFormSelect();
            $el['authorOptions'] = [
                'name'        => 'author',
                'id'          => 'author',
                'class'       => 'form-control select2',
                'style'       => 'width: 100%;',
                'data-placeholder' => lang('News.author_placeholder'),
                'required'    => 'required',
            ];
        } else {
            $el['visible']['checked']           = false;
        }
        return $el;
    }

    // Categories

    public function categories()
    {


        $this->data['categories'] = $this->NewsModel->getCategories();

        return view('App\Modules\News\Views\admin\categories\index', $this->data);
    }

    public function createCategory()
    {
        $this->validation = \Config\Services::validation();

        $this->validation->setRule('name', lang('News.category_name_label'), 'trim|required|is_unique[news_categories.name]');
        $this->validation->setRule('friendly_url', lang('News.index_friendly_url'), 'trim|required|is_unique[news_categories.friendly_url]');
        $data = [
            'name'          => $this->request->getPost('name'),
            'description'   => $this->request->getPost('description'),
            'friendly_url'  => $this->request->getPost('friendly_url'),
            'visible'       => $this->request->getPost('visible'),
        ];

        if (isset($data) && $this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

            $id = $this->NewsModel->createCategory($data);
            if ($id) {
                // SEO
                $SeoModel = new SeoModel();
                $SeoModel->seoCreate('news_categories', ['item' => $id]);

                $response['errors']     = [];
                $response['success']    = ['id' => $id];
                return $this->response->setJSON($response);
            } else {
                $response['errors'] = [lang('News.create_category_error_key') => lang('News.create_category_error_messages')];
                return $this->response->setJSON($response);
            }
        }

        $response['errors'] = $this->validation->getErrors();

        return $this->response->setJSON($response);
    }

    public function create_category()
    {
        helper(['form', 'ocms_form']);

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('News.news'), route_to('admin_news'));
        $this->breadcrumb->add(lang('News.categories'), route_to('admin_news_categories'));
        $this->breadcrumb->add(lang('OvenCms.create'), route_to('admin_news_category_create'));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        $this->data['name'] = [
            'name'        => 'name',
            'id'          => 'name',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('News.category_name_placeholder'),
            'required'    => 'required',
        ];
        $this->data['visible'] = [
            'name'        => 'visible',
            'id'          => 'visible',
            'type'        => 'checkbox',
            'value'       => 1,
            'checked'     => FALSE,
        ];
        $this->data['description'] = [
            'name'        => 'description',
            'id'          => 'description',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('News.description_placeholder'),
        ];

        return view('App\Modules\News\Views\admin\categories\create', $this->data);
    }

    public function view_category(int $id)
    {


        $this->data['category'] = $this->NewsModel->getCategory($id);
        if (!$this->data['category'])
            return redirect()->to('/admin/news/categories');

        return view('App\Modules\News\Views\admin\categories\view', $this->data);
    }

    public function editCategory()
    {
        $this->validation = \Config\Services::validation();

        $categoryId = $this->request->getPost('categoryId');

        if ($this->request->getPost('name') != $this->request->getPost('categoryName')) {
            $is_uniqueName =  '|is_unique[news_categories.name]';
        } else {
            $is_uniqueName =  '';
        }

        if ($this->request->getPost('friendly_url') != $this->request->getPost('categoryFriendlyUrl')) {
            $is_uniqueFriendlyUrl =  '|is_unique[news_categories.friendly_url]';
        } else {
            $is_uniqueFriendlyUrl =  '';
        }

        $this->validation->setRule('name', lang('News.category_name_label'), 'trim|required' . $is_uniqueName);
        $this->validation->setRule('friendly_url', lang('News.index_friendly_url'), 'trim|required' . $is_uniqueFriendlyUrl);
        $data = [
            'name'          => $this->request->getPost('name'),
            'description'   => $this->request->getPost('description'),
            'friendly_url'  => $this->request->getPost('friendly_url'),
            'visible'       => $this->request->getPost('visible'),
        ];

        if (isset($data) && $this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

            if ($this->NewsModel->updateCategory($categoryId, $data)) {

                $response['errors'] = [];
                return $this->response->setJSON($response);
            } else {
                $response['errors'] = [lang('News.edit_category_error_key') => lang('News.edit_category_error_messages')];
                return $this->response->setJSON($response);
            }
        }

        $response['errors'] = $this->validation->getErrors();

        return $this->response->setJSON($response);
    }

    public function edit_category(int $id)
    {
        helper(['form', 'ocms_form']);

        $category = $this->NewsModel->getCategory($id);
        $this->data['category'] = $category;
        if (!$this->data['category'])
            return redirect()->to('/admin/news/categories');

        $this->data['name'] = [
            'name'        => 'name',
            'id'          => 'name',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('News.category_name_placeholder'),
            'value'       => set_value('name', $category->name ?: ''),
            'required'    => 'required',
        ];
        $this->data['visible'] = [
            'name'        => 'visible',
            'id'          => 'visible',
            'type'        => 'checkbox',
            'value'       => 1,
            'checked'     => $category->visible ? TRUE : FALSE,
        ];
        $this->data['description'] = [
            'name'        => 'description',
            'id'          => 'description',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('News.description_placeholder'),
        ];

        // SEO form
        $SeoModel = new SeoModel();
        $itemSeo = $SeoModel->getModuleItem('news_categories', $id);
        $this->data['itemSeo'] = $itemSeo;
        $this->data['seoTitle'] = [
            'name'        => 'title',
            'id'          => 'seoTitle',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('PageSeo.seo_title_placeholder'),
            'value'       => set_value('title', $itemSeo->title ?: ''),
        ];
        $this->data['seoDescription'] = [
            'name'        => 'description',
            'id'          => 'seoDescription',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('PageSeo.seo_description_placeholder'),
            'value'       => set_value('description', $itemSeo->description ?: ''),
        ];
        $this->data['seoImage'] = [
            'name'        => 'image',
            'id'          => 'seoImage',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('PageSeo.seo_image_placeholder'),
            'value'       => set_value('title', $itemSeo->image ?: ''),
        ];
        $this->data['seoRobot'] = [
            'name'        => 'robot',
            'id'          => 'seoRobot',
            'type'        => 'checkbox',
            'value'       => 1,
            'checked'     => $itemSeo->robot ? TRUE : FALSE,
        ];
        $this->data['seoJson'] = [
            'name'        => 'json',
            'id'          => 'seoJson',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('PageSeo.seo_json_placeholder'),
        ];
        $this->data['seoOther'] = [
            'name'        => 'other',
            'id'          => 'seoOther',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('PageSeo.seo_other_placeholder'),
        ];

        return view('App\Modules\News\Views\admin\categories\edit', $this->data);
    }

    public function removeCategory()
    {
        $categoryId = $this->request->getPost('categoryId');


        if ($categoryId && $this->NewsModel->removeCategory($categoryId)) {
            $response['errors'] = [];
        } else {
            $response['errors'] = [lang('News.remove_category_error_key') => lang('News.remove_category_error_messages')];
        }

        return $this->response->setJSON($response);
    }

    public function remove_category(int $id)
    {
        helper(['form', 'ocms_form']);


        $this->data['category'] = $this->NewsModel->getCategory($id);
        if (!$this->data['category'])
            return redirect()->to('/admin/news/categories');

        return view('App\Modules\News\Views\admin\categories\remove', $this->data);
    }

    public function arrayUsersToFormSelect()
    {
        $this->data['users'] = $this->ionAuth->users()->result();
        $a = [];
        foreach ($this->data['users'] as $k => $user) {
            $a[$user->id] = $user->id . ' - ' . $user->first_name . ' ' . $user->last_name;
        }
        return $a;
    }

    public function arrayEstatesToFormSelect()
    {

        return $this->NewsModel->arrayEstatesToFormSelect();
    }

    public function arrayCategoriesToFormSelect()
    {

        return $this->NewsModel->arrayCategoriesToFormSelect();
    }

    public function arrayCategoriesSelectedToFormSelect($categories)
    {
        foreach ($categories as $category)
            $a[] = $category->id;
        return isset($a) ? $a : NULL;
    }
}
