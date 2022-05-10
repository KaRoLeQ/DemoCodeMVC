<?php

namespace App\Modules\News\Models\Admin;

use CodeIgniter\Model;

class NewsModel extends Model
{
	protected $table      = 'news_posts';
	protected $primaryKey = 'id';
	protected $returnType = 'object';

	private $cfgNews;

	public function __construct()
	{
		parent::__construct();

		$this->cfgNews = config('App\Modules\News\Config\News');
	}

	public function getPosts()
	{
		$posts = $this->db
			->table($this->cfgNews->database['news'] . ' news')
			->select("news.*,author.username AS 'author_username',author.first_name AS 'author_first_name',author.last_name AS 'author_last_name'")
			->join('users author', 'news.author=author.id')
			->orderBy('news.publication', 'DESC')
			->get()
			->getResultObject();
		if ($posts > 0) {
			foreach ($posts as $post) {
				$post = $this->convertAuthor($post);
				$post->categories = (object) $this->getPostCategories($post->id);
			}
		}

		return $posts;
	}

	public function getPost($id)
	{
		$post = $this->db
			->table($this->cfgNews->database['news'] . ' news')
			->select("news.*,author.username AS 'author_username',author.first_name AS 'author_first_name',author.last_name AS 'author_last_name',category.id AS 'category_id',category.name AS 'category_name',category.friendly_url AS 'category_friendly_url',category.description AS 'category_description',category.visible AS 'category_visible'")
			->join('users author', 'news.author=author.id')
			->join($this->cfgNews->database['post_categories'] . ' pc', 'news.id=pc.post')
			->join($this->cfgNews->database['categories'] . ' category', 'category.id=pc.category')
			->where('news.id', $id)
			->get()
			->getRowObject();
		$post = $this->db
			->table($this->cfgNews->database['news'] . ' news')
			->select("news.*,author.username AS 'author_username',author.first_name AS 'author_first_name',author.last_name AS 'author_last_name',category.id AS 'category_id',category.name AS 'category_name',category.friendly_url AS 'category_friendly_url',category.description AS 'category_description',category.visible AS 'category_visible'")
			->join('users author', 'news.author=author.id')
			->join($this->cfgNews->database['post_categories'] . ' pc', 'news.id=pc.post')
			->join($this->cfgNews->database['categories'] . ' category', 'category.id=pc.category')
			->where('news.id', $id)
			->get()
			->getRowObject();
		if ($post) {
			$post = $this->convertPost($post);
		}

		return $post;
	}

	public function createPost($data)
	{

		if (isset($data['categories'])) {
			$categories = $data['categories'];
			unset($data['categories']);
		}

		$this->db->table('news_posts')->insert($data);
		$postId = $this->db->insertId('news_posts');

		if ($postId) {
			if (isset($categories)) {
				foreach ($categories as $categoryId) {
					$this->db->table('news_post_categories')->insert(['category' => $categoryId, 'post' => $postId]);
				}
			}

			return $postId;
		}

		return false;
	}

	public function updatePost($id, $data)
	{
		if (isset($data['categories'])) {
			$this->db->table('news_post_categories')->delete(['post' => $id]);

			foreach ($data['categories'] as $categoryId) {
				$this->db->table('news_post_categories')->insert(['category' => $categoryId, 'post' => $id]);
			}
			unset($data['categories']);
		}

		$data['edited_at'] = date('Y-m-d H:i:s');

		$this->db->table('news_posts')->update($data, ['id' => $id]);

		if ($this->db->transStatus() === false) {
			return false;
		}
		return true;
	}

	public function removePost($id)
	{
		$this->db->table('news_post_categories')->delete(['post' => $id]);
		$this->db->table('news_posts')->delete(['id' => $id]);
		$this->db->table('news_posts_seo')->delete(['item' => $id]);

		if ($this->db->transStatus() === false) {
			return false;
		}
		return true;
	}

	public function getCategories()
	{
		$categories = $this->db
			->table($this->cfgNews->database['categories'])
			->get()
			->getResultObject();
		if ($categories > 0) {
			foreach ($categories as $category) {
				$category->posts = (object) $this->getCategoryPosts($category->id);
			}
		}

		return $categories;
	}

	public function arrayCategoriesToFormSelect()
	{
		$a = [];
		$categories = $this->db
			->table($this->cfgNews->database['categories'])
			->select('id,name')
			->get()
			->getResultObject();
		if ($categories > 0) {
			foreach ($categories as $category) {
				//$category->posts = (object) $this->getCategoryPosts($category->id);
				$a[$category->id] = $category->name;
			}
		}

		return $a;
	}

	public function arrayEstatesToFormSelect()
	{
		$a = [];
		$estates = $this->db
			->table('hc_estates')
			->select('id,name')
			->get()
			->getResultObject();
		if ($estates > 0) {
			foreach ($estates as $estate) {
				$a[$estate->id] = $estate->id . ' | ' . $estate->name;
			}
		}

		return $a;
	}

	public function getCategory($id)
	{
		$category = $this->db
			->table($this->cfgNews->database['categories'])
			->where('id', $id)
			->get()
			->getRowObject();
		if ($category) {
			$category->description = html_entity_decode($category->description);
			$category->posts = (object) $this->getCategoryPosts($id);
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

	public function getCategoryPosts($id)
	{
		$posts = $this->db
			->table('news_post_categories pc')
			->select("post.*,author.username AS 'author_username',author.first_name AS 'author_first_name',author.last_name AS 'author_last_name'")
			->where('pc.category', $id)
			->join('news_posts post', 'pc.post=post.id')
			->join('users author', 'post.author=author.id')
			->orderBy('post.publication', 'DESC')
			->get()
			->getResultObject();
		if ((array)$posts > 0) {
			foreach ($posts as $post) {
				$post = $this->convertAuthor($post);
				$post->content = html_entity_decode($post->content);
				$post->categories = (object) $this->getPostCategories($post->id);
			}
		}

		return $posts;
	}

	public function createCategory($data)
	{
		$this->db->table($this->cfgNews->database['post_categories'])->insert($data);

		$categoryId = $this->db->insertId($this->cfgNews->database['post_categories']);

		if ($categoryId) {
			return $categoryId;
		}

		return false;
	}

	public function updateCategory($id, $data)
	{
		$this->db->table($this->cfgNews->database['post_categories'])->update($data, ['id' => $id]);

		if ($this->db->transStatus() === false) {
			return false;
		}
		return true;
	}

	public function removeCategory($id)
	{
		$this->db->table($this->cfgNews->database['post_categories'])->delete(['category' => $id]);
		$this->db->table($this->cfgNews->database['categories'])->delete(['id' => $id]);
		$this->db->table($this->cfgNews->database['categories'] . '_seo')->delete(['item' => $id]);

		if ($this->db->transStatus() === false) {
			return false;
		}
		return true;
	}

	public function convertPost($r)
	{
		$r->content = html_entity_decode($r->content);
		$r->category = (object) [
			'id' => $r->category_id,
			'name' => $r->category_name,
			'description' => $r->category_description,
			'friendly_url' => $r->category_friendly_url,
			'visible' => $r->category_visible
		];
		unset($r->category_id, $r->category_name, $r->category_description, $r->category_friendly_url, $r->category_visible);

		$r->author = (object) array(
			'id'			=> $r->author,
			'username'		=> $r->author_username,
			'first_name'	=> $r->author_first_name,
			'last_name'		=> $r->author_last_name,
		);
		unset($r->author_username, $r->author_first_name, $r->author_last_name);

		if (empty($r->image)) {
			$r->image = (object) [
				'basic' => '',
				'webp' => ''
			];
		} elseif ($this->cfgNews->image['saveOrginal']) {
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
				'view' => base_url(route_to('admin_customersitem', $r->id)),
				'edit' => base_url(route_to('admin_customersedit_item', $r->id)),
				'remove' => base_url(route_to('admin_customersremove_item', $r->id)),
			]
		];

		return $r;
	}

	public function convertAuthor($data)
	{
		$data->author = (object) array(
			'id'			=> $data->author,
			'username'		=> $data->author_username,
			'first_name'	=> $data->author_first_name,
			'last_name'		=> $data->author_last_name,
		);
		unset($data->author_username, $data->author_first_name, $data->author_last_name);

		return $data;
	}
}
