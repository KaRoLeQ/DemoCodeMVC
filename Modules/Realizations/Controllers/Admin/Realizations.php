<?php

namespace App\Modules\Realizations\Controllers\Admin;

use App\Modules\Realizations\Models\Admin\RealizationsModel;
use App\Controllers\Admin\AdminController;
use App\Models\SeoModel;

class Realizations extends AdminController
{
    private $RealizationsModel;
    private $cfgRealizations;

    public function __construct()
    {
        parent::__construct();

        $this->RealizationsModel = new RealizationsModel();

        $this->cfgRealizations = config('App\Modules\Realizations\Config\Realizations');
        $this->imageWeight = $this->cfgRealizations->image['maxWeight'];
    }

    public function index()
    {
        $this->data['items']   = $this->RealizationsModel->getRealizations();
        $this->data['itemsCount']   = count((array) $this->data['items']);

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('admin_realizations'));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        return view('App\Modules\Realizations\Views\admin\index', $this->data);
    }

    public function view_item(int $id)
    {
        helper(['form', 'ocms_form']);

        $item = $this->RealizationsModel->getItem($id);
        $this->data['item'] = $item;
        if (!$this->data['item']) {
            return redirect()->to(route_to('admin_realizations'));
        }

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('admin_realizations'));
        $this->breadcrumb->add(lang('Realizations.realization') . ' #' . $item->id, route_to('admin_realizations_item', $item->id));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        return view('App\Modules\Realizations\Views\admin\view', $this->data);
    }

    public function createItem()
    {
        $this->validation = \Config\Services::validation();

        $this->validation->setRule('title', lang('Realizations.item_title_label'), 'trim|required|is_unique[realizations.title]');
        $this->validation->setRule('friendly_url', lang('Realizations.index_friendly_url'), 'trim|required');
        $this->validation->setRule('category', lang('Realziaitons.item_category_label'), 'trim|is_natural|required');
        $data = [
            'title'         => $this->request->getPost('title'),
            'category'      => $this->request->getPost('category'),
            'description'   => $this->request->getPost('description'),
            'content'       => $this->request->getPost('content'),
            'friendly_url'  => trim($this->request->getPost('friendly_url')),
            'visible'       => 0
        ];

        if (isset($data) && $this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $realizationId = $this->RealizationsModel->createItem($data);
            if ($realizationId) {
                $response['errors'] = [];
                $response['success'] = ['id' => $realizationId];
                return $this->response->setJSON($response);
            } else {
                $response['errors'] = [lang('Realizations.create_item_error_key') => lang('Realizations.create_item_error_messages')];
                return $this->response->setJSON($response);
            }
        }

        $response['errors'] = $this->validation->getErrors();
        return $this->response->setJSON($response);
    }

    public function create_item()
    {
        helper(['form', 'ocms_form']);

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('admin_realizations'));
        $this->breadcrumb->add(lang('OvenCms.create'), route_to('admin_realizations_item_create'));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        $this->data = array_merge($this->data, $this->realization_form_elements());
        $this->data['imageWeight'] = $this->imageWeight;

        return view('App\Modules\Realizations\Views\admin\create', $this->data);
    }

    public function editItem()
    {
        $this->validation = \Config\Services::validation();

        $itemId = $this->request->getPost('itemId');

        $this->validation->setRule('title', lang('Realizations.item_title_label'), 'trim|required|is_unique[realizations.title,id,' . $itemId . ']');
        $this->validation->setRule('friendly_url', lang('Realizations.index_friendly_url'), 'trim|required');
        $this->validation->setRule('category', lang('Realziaitons.item_category_label'), 'trim|is_natural|required');

        $data = [
            'title'         => $this->request->getPost('title'),
            'category'      => $this->request->getPost('category'),
            'description'   => $this->request->getPost('description'),
            'content'       => $this->request->getPost('content'),
            'friendly_url'  => trim($this->request->getPost('friendly_url')),
            'visible'       => $this->request->getPost('visible'),
            'offers'        => $this->request->getPost('offers')
        ];

        // return $this->response->setJSON($data['offers']);
        // exit;

        if (isset($data) && $this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            if ($this->RealizationsModel->updateItem($itemId, $data)) {
                $response['errors'] = [];
                return $this->response->setJSON($response);
            } else {
                $response['errors'] = [lang('Realizations.edit_item_error_key') => lang('Realizations.edit_item_error_messages')];
                return $this->response->setJSON($response);
            }
        }
        $response['errors'] = $this->validation->getErrors();

        return $this->response->setJSON($response);
    }

    public function edit_item(int $id)
    {
        helper(['form', 'ocms_form']);
        $RealizationsModel = new RealizationsModel();

        $item = $RealizationsModel->getItem($id);
        $this->data['item'] = $item;
        if (!$this->data['item'])
            return redirect()->to(route_to('admin_realizations'));

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('admin_realizations'));
        $this->breadcrumb->add(lang('Realizations.realization') . ' #' . $item->id, route_to('admin_realizations_item', $item->id));
        $this->breadcrumb->add(lang('OvenCms.edit'), route_to('admin_realizations_item_edit', $item->id));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        $this->data = array_merge($this->data, $this->realization_form_elements($item));
        $this->data['imageWeight'] = $this->imageWeight;

        // SEO form
        $SeoModel = new SeoModel();
        $itemSeo = $SeoModel->getModuleItem('realizations', $id);
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

        return view('App\Modules\Realizations\Views\admin\edit', $this->data);
    }

    public function removeItem()
    {
        $itemId = $this->request->getPost('itemId');

        if ($itemId && $this->RealizationsModel->removeItem($itemId)) {
            $response['errors'] = [];
        } else {
            $response['errors'] = [lang('Realizations.remove_item_error_key') => lang('Realizations.remove_item_error_messages')];
        }

        return $this->response->setJSON($response);
    }

    public function remove_item($id)
    {
        helper(['form', 'html']);

        $item = $this->RealizationsModel->getItem($id);
        $this->data['item'] = $item;
        if (!$this->data['item'])
            return redirect()->to(route_to('admin_realizations'));

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('admin_realizations'));
        $this->breadcrumb->add(lang('Realizations.realization') . ' #' . $item->id, route_to('admin_realizations_item', $item->id));
        $this->breadcrumb->add(lang('OvenCms.remove'), route_to('admin_realizations_item_remove', $item->id));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        return view('App\Modules\Realizations\Views\admin\remove', $this->data);
    }

    public function realization_form_elements($data = NULL)
    {
        $el['title'] = [
            'name'        => 'title',
            'id'          => 'title',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('Realizations.item_title_placeholder'),
            'required'    => 'required',
            'autofocus'   => 'autofocus',
        ];
        $el['categoriesList'] = $this->RealizationsModel->arrayCategoriesToFormSelect();
        $el['categoriesOptions'] = [
            'name'        => 'category',
            'id'          => 'category',
            'class'       => 'form-control select2',
            'style'       => 'width: 100%;',
            'required'    => 'required',
            'data-placeholder' => lang('Realizations.item_category_placeholder'),
        ];
        $el['description'] = [
            'name'        => 'description',
            'id'          => 'description',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('Realizations.item_description_placeholder'),
        ];
        $el['content'] = [
            'name'        => 'content',
            'id'          => 'content',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('Realizations.item_content_placeholder'),
        ];
        $el['visible'] = [
            'name'        => 'visible',
            'id'          => 'visible',
            'type'        => 'checkbox',
            'value'       => 1
        ];

        if (!empty($data)) {
            $el['title']['value']       = set_value('title', $data->title ?: '');
            $el['description']['value'] = set_value('description', $data->description ?: '');
            $el['content']['value']     = set_value('description', $data->description ?: '');
            $el['visible']['checked']   = $data->visible ? true : false;


            $el['offersSelected']   = $data->offers ? $this->elementsSelectedToFormSelect($data->offers) : '';
            $el['offersList']       = $this->RealizationsModel->arrayOffersToFormSelect();
            $el['offersOptions'] = [
                'name'              => 'offers',
                'id'                => 'offers',
                'multiple'          => 'multiple',
                'class'             => 'form-control select2',
                'style'             => 'width: 100%;',
                'data-placeholder'  => lang('Realizations.offers_placeholder'),
            ];
        } else {
            $el['visible']['checked']           = false;
        }
        return $el;
    }

    public function elementsSelectedToFormSelect($c)
    {
        foreach ($c as $cc) {
            $a[] = $cc->id;
        }
        return isset($a) ? $a : null;
    }

    /* gallery */
    public function gallery(int $id)
    {
        $item = $this->RealizationsModel->getItem($id);
        $this->data['realization'] = $item;

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('admin_realizations'));
        $this->breadcrumb->add(lang('Realizations.realization') . ' #' . $item->id, route_to('admin_realizations_item', $item->id));
        $this->breadcrumb->add(lang('Realizations.gallery'), route_to('admin_realizations_item_gallery', $item->id));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        return view('App\Modules\Realizations\Views\admin\gallery\index', $this->data);
    }

    public function createImage()
    {
        $this->validation = \Config\Services::validation();

        $this->validation->setRule('title', lang('Realizations.image_title_label'), 'trim|required');
        $this->validation->setRule('friendly_url', lang('Realizations.index_friendly_url'), 'trim|required');
        $this->validation->setRule('order', lang('Realizations.image_order_by_label'), 'trim|is_natural|required');
        $this->validation->setRule('image', lang('Realizations.image_label'), 'uploaded[image]|max_size[image,' . $this->imageWeight . ']|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]');
        $data = [
            'title'         => trim($this->request->getPost('title')),
            'realization'   => trim($this->request->getPost('realizationId')),
            'friendly_url'  => trim($this->request->getPost('friendly_url')),
            'order_by'      => trim($this->request->getPost('order')),
            'visible'       => trim($this->request->getPost('visible')),
        ];

        if (isset($data) && $this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            if ($this->request->getFileMultiple('image')) {
                foreach ($this->request->getFileMultiple('image') as $image) {
                    $id = $this->RealizationsModel->createImage($data);

                    $imageFileName = $id . '-' . $data['friendly_url'] . '_' . time();
                    $imageSrc = $this->cfgRealizations->image['fileLocation'] . '/' . $imageFileName;
                    $imageFileName .= '.' . $image->guessExtension();

                    //Move new image
                    $image->move($this->cfgRealizations->image['fileLocation'], $imageFileName);
                    //Create image format WEBP
                    copy($this->cfgRealizations->image['fileLocation'] . '/' . $imageFileName, $imageSrc . '.webp');

                    //? Remove additional image
                    if ($this->cfgRealizations->image['saveOrginal']) {
                        $dataImage['image'] = '/' . $this->cfgRealizations->image['fileLocation'] . '/' . $imageFileName;
                    } else {
                        unlink($this->cfgRealizations->image['fileLocation'] . '/' . $imageFileName);
                        $dataImage['image'] = '/' . $imageSrc . '.webp';
                    }

                    $this->RealizationsModel->updateImage($id, ['image' => $dataImage]);
                    $data['order_by']++;
                }

                $response['errors'] = [];
                $response['success'] = true;
                return $this->response->setJSON($response);
            } else {
                $response['errors'] = [lang('Realizations.create_realization_image_error_key') => lang('Realizations.create_realization_image_error_messages')];
                return $this->response->setJSON($response);
            }

            if (!empty($_FILES['image']['name'])) {
                $image = $this->request->getFile('image');
                $response['varImage'] = $this->request->getFile('image');
            }
            return $this->response->setJSON($response);
        }
        $response['errors'] = $this->validation->getErrors();

        return $this->response->setJSON($response);
    }

    public function create_image(int $idRealization)
    {
        helper(['form', 'ocms_form']);

        $realization = $this->RealizationsModel->getItem($idRealization);
        $this->data['realization'] = $realization;
        if (!$this->data['realization']) {
            return redirect()->to(route_to('admin_realizations'));
        }

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('admin_realizations'));
        $this->breadcrumb->add(lang('Realizations.realization') . ' #' . $realization->id, route_to('admin_realizations_item', $realization->id));
        $this->breadcrumb->add(lang('Realizations.gallery'), route_to('admin_realizations_item_gallery', $realization->id));
        $this->breadcrumb->add(lang('OvenCms.create'), route_to('admin_realizations_image_create', $realization->id));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        $this->data = array_merge($this->data, $this->image_form_elements());
        $this->data['imageWeight'] = $this->imageWeight;

        return view('App\Modules\Realizations\Views\admin\gallery\create', $this->data);
    }

    public function editImage()
    {
        $this->validation = \Config\Services::validation();

        $realizationID = $this->request->getPost('realizationId');
        $imageID = $this->request->getPost('imageId');

        $this->validation->setRule('title', lang('Hotel.room_name_label'), 'trim|required');
        $this->validation->setRule('friendly_url', lang('Hotel.index_friendly_url'), 'trim|required');
        $this->validation->setRule('order', lang('Hotel.room_order_by_label'), 'trim|is_natural|required');
        if (!empty($_FILES['image']['name']))
            $this->validation->setRule('image', lang('Hotel.room_image_main_label'), 'uploaded[image]|max_size[image,' . $this->imageWeight . ']|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]');
        $data = [
            'title'         => trim($this->request->getPost('title')),
            'realization'   => trim($realizationID),
            'friendly_url'  => trim($this->request->getPost('friendly_url')),
            'order_by'      => trim($this->request->getPost('order')),
            'visible'       => trim($this->request->getPost('visible')),
        ];

        if (!empty($_FILES['image']['name'])) {
            $image = $this->request->getFile('image');
            $imageFileName = $imageID . '-' . $data['friendly_url'] . '_' . time();
            $imageFormat = $image->guessExtension();
            $imageSrc = $this->cfgRealizations->image['fileLocation'] . '/' . $imageFileName;

            //? Additional image
            if ($this->cfgRealizations->image['saveOrginal']) {
                $data['image'] = '/' . $this->cfgRealizations->image['fileLocation'] . '/' . $imageFileName . '.' . $image->guessExtension();
            } else {
                $data['image'] = '/' . $imageSrc . '.webp';
            }
        }

        if (isset($data) && $this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            if ($this->RealizationsModel->updateImage($imageID, $data)) {
                $orgImage = $this->request->getPost('imageSrc');

                $response['orgImage'] = $orgImage;
                if (!empty($_FILES['image']['name'])) {
                    //Move new image
                    $image->move($this->cfgRealizations->image['fileLocation'], $imageFileName . '.' . $imageFormat);
                    //Create image format WEBP
                    copy($imageSrc . '.' . $imageFormat, $imageSrc . '.webp');

                    //? Remove additional image
                    if ($this->cfgRealizations->image['saveOrginal']) {
                        $response['image'] = base_url('/' . $imageSrc . '.' . $imageFormat);
                    } else {
                        unlink($imageSrc . '.' . $imageFormat);
                        $response['image'] = base_url('/' . $imageSrc . '.webp');
                    }

                    $orgImage = str_replace(base_url(), '', $orgImage);
                    $orgImage = substr($orgImage, 1);

                    if (!empty($orgImage) && file_exists($orgImage)) {
                        unlink($orgImage);
                        $imgSeparator = explode('.', $orgImage);
                        $imgFormat = end($imgSeparator);
                        if ($imgFormat != 'webp' && file_exists($imgSeparator[0] . '.webp')) {
                            unlink($imgSeparator[0] . '.webp');
                        }
                    }
                } else {
                    $response['image'] = $orgImage;
                    if (($this->request->getPost('title') != $this->request->getPost('imageTitle')) && !empty($orgImage)) {
                        $orgImage = str_replace(base_url(), '', $orgImage);
                        $orgImage = substr($orgImage, 1);

                        $newFileImage = '/' . $this->cfgRealizations->image['fileLocation'] . '/' . $imageID . '-' . $data['friendly_url'] . '_' . time() . '.' . pathinfo($orgImage, PATHINFO_EXTENSION);
                        rename('./' . $orgImage, '.' . $newFileImage);
                        $response['image'] = $newFileImage;
                        $this->RealizationsModel->updateImage($imageID, ['image' => $newFileImage]);
                    }
                }

                $response['errors'] = [];
                return $this->response->setJSON($response);
            } else {
                $response['errors'] = [lang('Realizations.remove_realization_image_error_key') => lang('Realizations.remove_realization_image_error_messages')];
                return $this->response->setJSON($response);
            }
        }
        $response['errors'] = $this->validation->getErrors();

        return $this->response->setJSON($response);
    }

    public function edit_image(int $idRealization, int $idImage)
    {
        helper(['form', 'ocms_form']);

        $realization = $this->RealizationsModel->getItem($idRealization);
        $this->data['realization'] = $realization;
        if (!$this->data['realization']) {
            return redirect()->to(route_to('admin_realizations'));
        }

        $image = $this->RealizationsModel->getRealizationImage($idRealization, $idImage);
        $this->data['image'] = $image;
        if (!$this->data['image']) {
            return redirect()->to(route_to('admin_realizations_item_gallery', $idRealization));
        }

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('admin_realizations'));
        $this->breadcrumb->add(lang('Realizations.realization') . ' #' . $realization->id, route_to('admin_realizations_item', $realization->id));
        $this->breadcrumb->add(lang('Realizations.gallery'), route_to('admin_realizations_item_gallery', $realization->id));

        $this->breadcrumb->add(lang('OvenCms.edit'), route_to('admin_realizations_image_edit', $idRealization, $image->id));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        $this->data = array_merge($this->data, $this->image_form_elements($image));
        $this->data['imageWeight'] = $this->imageWeight;

        return view('App\Modules\Realizations\Views\admin\gallery\edit', $this->data);
    }

    public function removeImage()
    {
        $imageId = $this->request->getPost('imageId');
        $imageSrc = $this->request->getPost('imageSrc');

        if ($imageId && $this->RealizationsModel->removeImage($imageId)) {
            $response['errors'] = [];

            $imageSrc = substr(str_replace(base_url(''), '', $imageSrc), 1);
            if (!empty($imageSrc) && file_exists($imageSrc)) {
                unlink($imageSrc);
                $imgSeparator = explode('.', $imageSrc);
                $imgFormat = end($imgSeparator);
                if ($imgFormat != 'webp' && file_exists($imgSeparator[0] . '.webp')) {
                    unlink($imgSeparator[0] . '.webp');
                }
                $response['removeImage'] = true;
            } else {
                $response['removeImage'] = false;
            }
        } else {
            $response['errors'] = [lang('Realizations.remove_realization_image_error_key') => lang('Realizations.remove_realization_image_error_messages')];
        }

        return $this->response->setJSON($response);
    }

    public function remove_image($idRealization, $idImage)
    {
        helper(['form', 'html']);

        $realization = $this->RealizationsModel->getItem($idRealization);
        $this->data['realization'] = $realization;
        if (!$this->data['realization']) {
            return redirect()->to(route_to('admin_realizations'));
        }

        $image = $this->RealizationsModel->getRealizationImage($idRealization, $idImage);
        $this->data['image'] = $image;
        if (!$this->data['image']) {
            return redirect()->to(route_to('admin_realizations_item_gallery', $idRealization));
        }

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('admin_realizations'));
        $this->breadcrumb->add(lang('Realizations.realization') . ' #' . $realization->id, route_to('admin_realizations_item', $realization->id));
        $this->breadcrumb->add(lang('Realizations.gallery'), route_to('admin_realizations_item_gallery', $realization->id));

        $this->breadcrumb->add(lang('OvenCms.remove'), route_to('admin_realizations_image_remove', $idRealization, $image->id));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        return view('App\Modules\Realizations\Views\admin\gallery\remove', $this->data);
    }

    public function image_form_elements($data = NULL)
    {
        $el['title'] = [
            'name'        => 'title',
            'id'          => 'title',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('Realizations.image_title_placeholder'),
            'required'    => 'required',
        ];
        $el['visible'] = [
            'name'        => 'visible',
            'id'          => 'visible',
            'type'        => 'checkbox',
            'value'       => 1,
            'checked'     => false,
        ];
        $el['order'] = [
            'name'        => 'order',
            'id'          => 'order',
            'type'        => 'number',
            'min'         => '0',
            'step'        => '1',
            'class'       => 'form-control',
            'placeholder' => lang('Realizations.image_order_by_placeholder'),
            'required'    => 'required',
        ];
        $el['imageForm'] = [
            'name'                          => 'image[]',
            'id'                            => 'image',
            'type'                          => 'file',
            'class'                         => 'b-fileinput',
            'multiple'                      => 'multiple',
            'data-browse-on-zone-click'     => 'true',
            'required'                      => 'required',
        ];

        if (!empty($data)) {
            $el['title']['value']       = set_value('title', $data->title ?: '');
            $el['order']['value']       = set_value('order', $data->order_by ?: '');
            $el['visible']['checked']   = $data->visible ? true : false;
            $el['imageForm'] = [
                'name'                          => 'image',
                'class'                         => 'dropify',
                'id'                            => 'image',
                'type'                          => 'file',
                'data-default-file'             => $data->image->basic ?: '',
                'data-allowed-file-extensions'  => 'jpg jpeg png',
                'accept'                        => 'image/jpeg,image/jpg,image/png',
            ];
        } else {
            $el['visible']['checked']   = false;
        }
        return $el;
    }

    // Categories
    public function categories()
    {
        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('admin_realizations'));
        $this->breadcrumb->add(lang('Realizations.categories'), route_to('admin_realizations_categories'));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        $this->data['categories'] = $this->RealizationsModel->getCategories();

        return view('App\Modules\Realizations\Views\admin\categories\index', $this->data);
    }

    public function createCategory()
    {
        $this->validation = \Config\Services::validation();

        $this->validation->setRule('name', lang('Realizations.category_name_label'), 'trim|required|is_unique[news_categories.name]');
        $this->validation->setRule('friendly_url', lang('Realizations.index_friendly_url'), 'trim|required|is_unique[news_categories.friendly_url]');
        $data = [
            'name'          => $this->request->getPost('name'),
            'description'   => $this->request->getPost('description'),
            'friendly_url'  => $this->request->getPost('friendly_url'),
            'visible'       => $this->request->getPost('visible'),
        ];

        if (isset($data) && $this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $NewsModel = new NewsModel();
            $id = $NewsModel->createCategory($data);
            if ($id) {
                // SEO
                $SeoModel = new SeoModel();
                $SeoModel->seoCreate('news_categories', ['item' => $id]);

                $response['errors']     = [];
                $response['success']    = ['id' => $id];
                return $this->response->setJSON($response);
            } else {
                $response['errors'] = [lang('Realizations.create_category_error_key') => lang('Realizations.create_category_error_messages')];
                return $this->response->setJSON($response);
            }
        }

        $response['errors'] = $this->validation->getErrors();

        return $this->response->setJSON($response);
    }

    public function create_category()
    {
        helper(['form', 'ocms_form']);

        $this->data['name'] = [
            'name'        => 'name',
            'id'          => 'name',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('Realizations.category_name_placeholder'),
            'required'    => 'required',
        ];
        $this->data['visible'] = [
            'name'        => 'visible',
            'id'          => 'visible',
            'type'        => 'checkbox',
            'value'       => 1,
            'checked'     => false,
        ];
        $this->data['description'] = [
            'name'        => 'description',
            'id'          => 'description',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('Realizations.description_placeholder'),
        ];

        return view('App\Modules\Realizations\Views\admin\categories\create', $this->data);
    }

    public function view_category(int $id)
    {
        $NewsModel = new NewsModel();

        $this->data['category'] = $this->RealizationsModel->getCategory($id);
        if (!$this->data['category']) {
            return redirect()->to('/admin/news/categories');
        }

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('admin_realizations'));
        $this->breadcrumb->add(lang('Realizations.realization') . ' #' . $realization->id, route_to('admin_realizations_item', $realization->id));
        $this->breadcrumb->add(lang('Realizations.gallery'), route_to('admin_realizations_item_gallery', $realization->id));

        $this->breadcrumb->add(lang('OvenCms.edit'), route_to('admin_realizations_image_edit', $idRealization, $image->id));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        $this->data = array_merge($this->data, $this->image_form_elements($image));

        return view('App\Modules\Realizations\Views\admin\categories\view', $this->data);
    }

    public function editCategory()
    {
        $this->validation = \Config\Services::validation();

        $itemId = $this->request->getPost('categoryId');

        $this->validation->setRule('title', lang('Realizations.item_title_label'), 'trim|required|is_unique[realizations.title,id,' . $itemId . ']');
        $this->validation->setRule('friendly_url', lang('Realizations.index_friendly_url'), 'trim|required');
        $this->validation->setRule('order', lang('Realizations.item_order_by_label'), 'trim|is_natural|required');

        $data = [
            'title'         => $this->request->getPost('title'),
            'content'       => $this->request->getPost('content'),
            'order_by'      => $this->request->getPost('order'),
            'friendly_url'  => trim($this->request->getPost('friendly_url')),
            'visible'       => $this->request->getPost('visible')
        ];

        if (isset($data) && $this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            if ($this->RealizationsModel->updateCategory($itemId, $data)) {
                $response['errors'] = [];
                return $this->response->setJSON($response);
            } else {
                $response['errors'] = [lang('Realizations.edit_category_error_key') => lang('Realizations.edit_category_error_messages')];
                return $this->response->setJSON($response);
            }
        }
        $response['errors'] = $this->validation->getErrors();

        return $this->response->setJSON($response);
    }

    public function edit_category(int $id)
    {
        helper(['form', 'ocms_form']);

        $category = $this->RealizationsModel->getCategory($id);
        $this->data['category'] = $category;
        if (!$this->data['category']) {
            return redirect()->to(route_to('admin_realizations_categories'));
        }

        $this->breadcrumb->add(lang('OvenCms.ovencms_dashboard'), route_to('admin'));
        $this->breadcrumb->add(lang('Realizations.realizations'), route_to('admin_realizations'));
        $this->breadcrumb->add(lang('Realizations.categories'), route_to('admin_realizations_categories'));
        $this->breadcrumb->add(lang('Realizations.category') . ' #' . $category->id, route_to('admin_realizations_category_view', $category->id));
        $this->breadcrumb->add(lang('OvenCms.edit'), route_to('admin_realizations_category_edit', $category->id));
        $this->data['breadcrumb'] = $this->breadcrumb->render('adminlte');

        $this->data = array_merge($this->data, $this->category_form_elements($category));

        // SEO form
        $SeoModel = new SeoModel();
        $itemSeo = $SeoModel->getModuleItem('realizations_categories', $id);
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
            'checked'     => $itemSeo->robot ? true : false,
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

        return view('App\Modules\Realizations\Views\admin\categories\edit', $this->data);
    }

    public function removeCategory()
    {
        $categoryId = $this->request->getPost('categoryId');

        $NewsModel = new NewsModel();
        if ($categoryId && $NewsModel->removeCategory($categoryId)) {
            $response['errors'] = [];
        } else {
            $response['errors'] = [lang('Realizations.remove_category_error_key') => lang('Realizations.remove_category_error_messages')];
        }

        return $this->response->setJSON($response);
    }

    public function remove_category(int $id)
    {
        helper(['form', 'ocms_form']);
        $NewsModel = new NewsModel();

        $this->data['category'] = $NewsModel->getCategory($id);
        if (!$this->data['category']) {
            return redirect()->to('/admin/news/categories');
        }

        return view('App\Modules\Realizations\Views\admin\categories\remove', $this->data);
    }

    public function category_form_elements($data = NULL)
    {
        $el['title'] = [
            'name'        => 'title',
            'id'          => 'title',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('Realizations.category_title_placeholder'),
            'required'    => 'required',
        ];
        $el['visible'] = [
            'name'        => 'visible',
            'id'          => 'visible',
            'type'        => 'checkbox',
            'value'       => 1,
            'checked'     => false,
        ];
        $el['content'] = [
            'name'        => 'content',
            'id'          => 'content',
            'type'        => 'text',
            'class'       => 'form-control',
            'placeholder' => lang('Realizations.content_placeholder'),
        ];
        $el['order'] = [
            'name'        => 'order',
            'id'          => 'order',
            'type'        => 'number',
            'min'         => '0',
            'step'        => '1',
            'class'       => 'form-control',
            'placeholder' => lang('Realizations.item_order_by_placeholder'),
            'required'    => 'required',
        ];

        if (!empty($data)) {
            $el['title']['value']       = set_value('title', $data->title ?: '');
            $el['order']['value']       = set_value('order', $data->order_by ?: '');
            $el['visible']['checked']   = $data->visible ? true : false;
        } else {
            $el['visible']['checked']   = false;
        }
        return $el;
    }
}
