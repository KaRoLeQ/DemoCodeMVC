<?php

namespace App\Modules\Realizations\Models;

use CodeIgniter\Model;

class RealizationsModel extends Model
{
    protected $table      = 'realizations';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    public function __construct()
    {
        parent::__construct();

        $this->cfgRealizations = config('App\Modules\Realizations\Config\Realizations');
    }

    public function getRealizations($categoryKey = NULL, $categoryValue = NULL)
    {
        $items = $this->db
            ->table($this->cfgRealizations->database['realizations'] . ' realization')
            ->select("realization.*,category.title AS 'category_title',category.friendly_url AS 'category_friendly_url',category.visible AS 'category_visible',category.content AS 'category_content'")
            ->join($this->cfgRealizations->database['categories'] . ' category', 'category.id=realization.category')
            ->where('realization.visible', 1)
            ->where('category.visible', 1);

        // $items = $items->orderBy('RAND()');
        $items = $items->orderBy('realization.id', 'DESC');

        if ($categoryKey) {
            $items = $items->where('category.' . $categoryKey, $categoryValue);
        }

        $items = $items
            ->get()
            ->getResultObject();

        if ($items > 0) {
            foreach ($items as $item) {
                $item = $this->convertRealization($item);
            }
        }

        return $items;
    }

    public function getSimilarRealizations(int $realizationID, int $categoryId)
    {
        $items = $this->db
            ->table($this->cfgRealizations->database['realizations'] . ' realization')
            ->select("realization.*,category.title AS 'category_title',category.friendly_url AS 'category_friendly_url',category.visible AS 'category_visible',category.content AS 'category_content'")
            ->join($this->cfgRealizations->database['categories'] . ' category', 'category.id=realization.category')
            ->where('realization.visible', 1)
            ->where('realization.id !=', $realizationID)
            ->where('category.id', $categoryId)
            ->where('category.visible', 1)
            ->orderBy('RAND()')
            ->get(16)
            ->getResultObject();
        if ($items > 0) {
            foreach ($items as $item) {
                $item = $this->convertRealization($item);
            }
        }

        return $items;
    }

    public function getSimilarRealizationsOffer(int $offerId, int $limit = null)
    {
        $items = $this->db
            ->table($this->cfgRealizations->database['realizations'] . ' realization')
            ->select("realization.*,category.title AS 'category_title',category.friendly_url AS 'category_friendly_url',category.visible AS 'category_visible',category.content AS 'category_content'")
            ->join($this->cfgRealizations->database['categories'] . ' category', 'category.id=realization.category')
            ->join($this->cfgRealizations->database['offers'] . ' offer', 'offer.realization=realization.id')
            ->where('realization.visible', 1)
            ->where('category.visible', 1)
            ->where('offer.offer', $offerId)
            ->orderBy('RAND()');

        if (!empty($limit)) {
            $items = $items->get($limit);
        } else {
            $items = $items->get();
        }

        $items = $items->getResultObject();
        if ($items > 0) {
            foreach ($items as $item) {
                $item = $this->convertRealization($item);
            }
        }

        return $items;
    }

    public function getRealization(string $col = 'id', $parm)
    {
        $item = $this->db
            ->table($this->cfgRealizations->database['realizations'] . ' realization')
            ->select("realization.*,category.title AS 'category_title',,category.friendly_url AS 'category_friendly_url',category.visible AS 'category_visible',category.content AS 'category_content'")
            ->join($this->cfgRealizations->database['categories'] . ' category', 'category.id=realization.category')
            ->where('realization.' . $col, $parm)
            ->where('realization.visible', 1)
            ->where('category.visible', 1)
            ->get()
            ->getRowObject();
        if ($item) {
            $item = $this->convertRealization($item);
        }

        return $item;
    }

    public function getCategory(string $col = 'id', $parm)
    {
        $item = $this->db
            ->table($this->cfgRealizations->database['categories'] . ' category')
            ->select("category.*")
            ->where('category.' . $col, $parm)
            ->where('category.visible', 1)
            ->get()
            ->getRowObject();
        if ($item) {
            $item = $this->convertCategory($item);
        }

        return $item;
    }

    public function getCategoryRealizations(string $col = 'id', $parm)
    {
        $items = $this->db
            ->table($this->cfgRealizations->database['realizations'] . ' realization')
            ->select("realization.*,category.title AS 'category_title',category.friendly_url AS 'category_friendly_url',category.visible AS 'category_visible',category.content AS 'category_content'")
            ->join($this->cfgRealizations->database['categories'] . ' category', 'category.id=realization.category')
            ->where('realization.visible', 1)
            ->where('category.visible', 1)
            ->where('category.' . $col, $parm)
            ->orderBy('realization.id', 'DESC')
            ->get()
            ->getResultObject();
        if ($items > 0) {
            foreach ($items as $item) {
                $item = $this->convertRealization($item);
            }
        }

        return $items;
    }

    /**
     * Get realization image according to ID realization and ID image
     *
     * @param integer $idRealization ID room
     * @param integer $idImage ID image
     * @return object Rows
     */
    public function getRealizationImage(int $idRealization, int $idImage)
    {
        $image = $this->db
            ->table($this->cfgRealizations->database['images'])
            ->select('*')
            ->where('realization', $idRealization)
            ->where('id', $idImage)
            ->where('visible', 1)
            ->orderBy('order_by', 'ASC')
            ->get()
            ->getRowObject();

        if ($image) {
            $image = $this->convertImage($image);
        }

        return $image;
    }

    /**
     * Get realization images according to ID relization
     *
     * @param integer $id
     * @return object Rows
     */
    public function getRealizationImages(int $id)
    {
        $items = $this->db
            ->table($this->cfgRealizations->database['images'])
            ->select('*')
            ->where('realization', $id)
            ->where('visible', 1)
            ->orderBy('order_by', 'ASC')
            ->get()
            ->getResultObject();

        if ($items > 0) {
            foreach ($items as $item) {
                $item = $this->convertImage($item);
            }
        }

        return $items;
    }

    public function getCategories()
    {
        $items = $this->db
            ->table($this->cfgRealizations->database['categories'])
            ->where('visible', 1)
            ->orderBy('order_by', 'ASC')
            ->get()
            ->getResultObject();
        if ($items > 0) {
            foreach ($items as $item) {
                $item = $this->convertCategory($item);
            }
        }

        return $items;
    }

    public function arrayCategoriesToFormSelect(): array
    {
        $items = $this->db
            ->table($this->cfgRealizations->database['categories'])
            ->where('visible', 1)
            ->orderBy('order_by', 'ASC')
            ->get()
            ->getResultObject();
        $a = [];
        if ($items > 0) {
            foreach ($items as $item) {
                $a[$item->id] = $item->title;
            }
        }

        return $a;
    }

    public function convertRealization($r)
    {
        $r->content     = html_entity_decode($r->content);
        $r->description = html_entity_decode($r->description);

        $r->images      = $this->getRealizationImages($r->id);
        $r->imagesCount = count((array) $r->images);

        unset($r->visible, $r->created_at);

        $r->category = (object) [
            'id' => $r->category,
            'title' => $r->category_title,
            'friendly_url' => $r->category_friendly_url,
            'content' => html_entity_decode($r->category_content),
            'visible' => $r->category_visible,
            'url' => base_url(route_to('realizations_category', $r->category_friendly_url))
        ];
        unset($r->category_title, $r->category_content, $r->category_visible, $r->category_friendly_url);

        $r->url = base_url(route_to('realizations_item', $r->category->friendly_url, $r->friendly_url));

        return $r;
    }

    public function convertImage($r)
    {
        if ($this->cfgRealizations->image['saveOrginal']) {
            $imgSeparator = explode('.', $r->image);
            $r->image = (object) [
                'basic' => base_url($r->image),
                'webp' => base_url($imgSeparator[0] . '.webp'),
                'type' => $imgSeparator[1]
            ];
        } else {
            $r->image = (object) [
                'basic' => base_url($r->image),
                'webp' => base_url($r->image),
            ];
        }

        $r->url = base_url($r->image->basic);

        unset($r->visible, $r->realization, $r->friendly_url, $r->created_at, $r->edited_at);

        return $r;
    }

    public function convertCategory($r)
    {
        $r->content = html_entity_decode($r->content);
        $r->url = base_url(route_to('realizations_category', $r->friendly_url));
        $r->items = $this->getRealizations('id', $r->id);
        unset($r->visible);
        return $r;
    }
}
