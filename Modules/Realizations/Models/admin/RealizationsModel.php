<?php

namespace App\Modules\Realizations\Models\Admin;

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
		$this->cfgOffers = config('App\Modules\Offers\Config\Offers');
	}

	// Admin
	public function getRealizations()
	{
		$items = $this->db
			->table($this->cfgRealizations->database['realizations'] . ' realization')
			->select("realization.*,category.title AS 'category_title',category.visible AS 'category_visible',category.content AS 'category_content'")
			->join($this->cfgRealizations->database['categories'] . ' category', 'category.id=realization.category')
			->orderBy('order_by', 'ASC')
			->get()
			->getResultObject();
		if ($items > 0) {
			foreach ($items as $item) {
				$item = $this->convertRealization($item);
			}
		}

		return $items;
	}


	public function getItem(int $id)
	{
		$item = $this->db
			->table($this->cfgRealizations->database['realizations'] . ' realization')
			->select("realization.*,category.title AS 'category_title',category.visible AS 'category_visible',category.content AS 'category_content'")
			->join($this->cfgRealizations->database['categories'] . ' category', 'category.id=realization.category')
			->where('realization.id', $id)
			->get()
			->getRowObject();
		if ($item) {
			$item = $this->convertRealization($item);
		}

		return $item;
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
		return $this->db
			->table($this->cfgRealizations->database['images'])
			->select('*')
			->where('realization', $id)
			->orderBy('order_by', 'ASC')
			->get()
			->getResultObject();
	}

	public function arrayCategoriesToFormSelect(): array
	{
		$items = $this->db
			->table($this->cfgRealizations->database['categories'])
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

	public function createItem($data)
	{

		$this->db->table($this->table)->insert($data);
		$postId = $this->db->insertId($this->table);

		if ($postId) {
			return $postId;
		}

		return false;
	}

	public function updateItem($id, $data)
	{
		$data['edited_at'] = date('Y-m-d H:i:s');

		$offers = $data['offers'];
		unset($data['offers']);

		$this->updateItemOffers($id, $offers);

		$this->db->table($this->table)->update($data, ['id' => $id]);

		if ($this->db->transStatus() === false) {
			return false;
		}
		return true;
	}

	public function updateItemOffers($realization, $offers)
	{
		$this->db->table($this->cfgRealizations->database['offers'])->delete(['realization' => $realization]);

		if (!empty($offers)) {
			$data = [];
			foreach ($offers as $offer) {
				$data[] = ['realization' => $realization, 'offer' => $offer];
			}

			$this->db->table($this->cfgRealizations->database['offers'])->insertBatch($data);
		}

		if ($this->db->transStatus() === false) {
			return false;
		}
		return true;
	}

	public function removeItem($id)
	{
		$this->db->table($this->table)->delete(['id' => $id]);
		$this->db->table($this->cfgRealizations->database['realizations'] . '_seo')->delete(['item' => $id]);
		$this->db->table($this->cfgRealizations->database['offers'])->delete(['realization' => $id]);

		if ($this->db->transStatus() === false) {
			return false;
		}
		return true;
	}

	public function convertRealization($r)
	{

		$r->title 	= html_entity_decode($r->title);
		$r->content 	= html_entity_decode($r->content);
		$r->description = html_entity_decode($r->description);

		$r->images      = $this->getRealizationImages($r->id);
		$r->imagesCount = count((array) $r->images);

		$r->offers = $this->db
			->table($this->cfgRealizations->database['offers'] . ' ro')
			->join($this->cfgOffers->database['offers'] . ' offer', 'ro.offer=offer.id')
			->select('offer.*')
			->where('ro.realization', $r->id)
			->get()
			->getResultObject();

		$r->category = (object) [
			'id' => $r->category,
			'title' => $r->category_title,
			'content' => $r->category_content,
			'visible' => $r->category_visible,
		];

		unset($r->category_title, $r->category_content, $r->category_visible);

		$r->url = (object) [
			'admin' => (object) [
				'view' => base_url(route_to('admin_realizations_item', $r->id)),
				'edit' => base_url(route_to('admin_realizations_item_edit', $r->id)),
				'remove' => base_url(route_to('admin_realizations_item_remove', $r->id)),
			]
		];

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

		$r->url = (object) [
			'admin' => (object) [
				'view' => base_url(route_to('admin_realizations_image_view', $r->realization, $r->id)),
				'edit' => base_url(route_to('admin_realizations_image_edit', $r->realization, $r->id)),
				'remove' => base_url(route_to('admin_realizations_image_remove', $r->realization, $r->id)),
			]
		];

		return $r;
	}

	/**
	 * Creates a realization image
	 *
	 * @param array $data
	 * @return integer|boolean Image ID or false
	 */
	public function createImage($data)
	{
		$this->db->table($this->cfgRealizations->database['images'])->insert($data);
		$ImageId = $this->db->insertId($this->cfgRealizations->database['images']);

		if ($ImageId) {
			return $ImageId;
		}

		return false;
	}

	/**
	 * Updates a realization image according to Image ID
	 *
	 * @param integer $id Image id
	 * @param array $data
	 * @return boolean
	 */
	public function updateImage(int $id, $data)
	{
		$data['edited_at'] = date('Y-m-d H:i:s');
		$this->db->table($this->cfgRealizations->database['images'])->update($data, ['id' => $id]);

		if ($this->db->transStatus() === false) {
			return false;
		}
		return true;
	}

	/**
	 * Deletes a realization image according to Image ID
	 *
	 * @param integer $id Image id
	 * @return boolean
	 */
	public function removeImage(int $id)
	{
		$this->db->table($this->cfgRealizations->database['images'])->delete(['id' => $id]);

		if ($this->db->transStatus() === false) {
			return false;
		}
		return true;
	}

	// Categories

	public function getCategories()
	{
		$categories = $this->db
			->table($this->cfgRealizations->database['categories'])
			->get()
			->getResultObject();
		if ($categories > 0) {
			foreach ($categories as $category) {
				$category = $this->convertCategory($category);
				$category->items = $this->getCategoryPosts($category->id);
			}
		}

		return $categories;
	}

	public function getCategoryPosts($id)
	{
		$items = $this->db
			->table($this->cfgRealizations->database['realizations'] . ' realization')
			->select("realization.*,category.title AS 'category_title',category.visible AS 'category_visible',category.content AS 'category_content'")
			->join($this->cfgRealizations->database['categories'] . ' category', 'category.id=realization.category')
			->where('category.id', $id)
			->get()
			->getResultObject();
		if ($items > 0) {
			foreach ($items as $item) {
				$item = $this->convertRealization($item);
			}
		}

		return $items;
	}

	public function convertCategory($r)
	{
		$r->content = html_entity_decode($r->content);

		$r->url = (object) [
			'admin' => (object) [
				'view' => base_url(route_to('admin_realizations_category_view', $r->id)),
				'edit' => base_url(route_to('admin_realizations_category_edit', $r->id)),
				'remove' => base_url(route_to('admin_realizations_category_remove', $r->id)),
			]
		];

		return $r;
	}

	public function arrayOffersToFormSelect()
	{
		$a = [];
		$categories = $this->db
			->table($this->cfgOffers->database['offers'] . ' offer')
			->select('id,title')
			->get()
			->getResultObject();
		if ($categories > 0) {
			foreach ($categories as $category) {
				$a[$category->id] = $category->title;
			}
		}

		return $a;
	}

	public function getCategory($id)
	{
		$category = $this->db
			->table($this->cfgRealizations->database['categories'])
			->where('id', $id)
			->get()
			->getRowObject();
		if ($category) {
			$category = $this->convertCategory($category);
		}

		return $category;
	}

	public function getPostCategories($id)
	{
		$categories =  (object) $this->db
			->table('news_post_categories pc')
			->select('category.*')
			->where('pc.post', $id)
			->join('news_categories category', 'pc.category=category.id')
			->orderBy('category.name', 'ASC')
			->get()
			->getResultObject();

		// $categories->description = htmlspecialchars_decode($post->description);

		return $categories;
	}

	public function createCategory($data)
	{
		$this->db->table($this->cfgRealizations->database['categories'] . ' category')->insert($data);

		$categoryId = $this->db->insertId($this->cfgRealizations->database['categories'] . ' category');

		if ($categoryId) {
			return $categoryId;
		}

		return false;
	}

	public function updateCategory($id, $data)
	{
		$this->db->table($this->cfgRealizations->database['categories'] . ' category')->update($data, ['id' => $id]);

		if ($this->db->transStatus() === false) {
			return false;
		}
		return true;
	}

	public function removeCategory($id)
	{
		$this->db->table($this->cfgRealizations->database['realizations'])->delete(['category' => $id]);
		$this->db->table($this->cfgRealizations->database['categories'])->delete(['id' => $id]);
		$this->db->table($this->cfgRealizations->database['categories'] . '_seo')->delete(['item' => $id]);

		if ($this->db->transStatus() === false) {
			return false;
		}
		return true;
	}
}
