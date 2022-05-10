<?php

namespace App\Modules\News\Models;

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

	public function getPosts(int $limit = null, int $start = null, string $categoryFriendlyUrl = '')
	{
		$posts = $this->db
			->table($this->cfgNews->database['news'] . ' news')
			->select("news.*,author.username AS 'author_username',author.first_name AS 'author_first_name',author.last_name AS 'author_last_name',category.id AS 'category_id',category.name AS 'category_name',category.friendly_url AS 'category_friendly_url',category.description AS 'category_description'")
			->join('users author', 'news.author=author.id')
			->join($this->cfgNews->database['post_categories'] . ' pc', 'news.id=pc.post')
			->join($this->cfgNews->database['categories'] . ' category', 'category.id=pc.category')
			->where('news.visible', 1)
			->where('category.visible', 1)
			->where('news.publication<=now()')
			->orderBy('news.publication', 'DESC');

		if (!empty($categoryFriendlyUrl)) {
			$posts = $posts->where('category.friendly_url', $categoryFriendlyUrl);
		}

		if (!empty($limit) && !empty($start)) {
			$posts = $posts->get($limit, $start)
				->getResultObject();
		} elseif (!empty($limit)) {
			$posts = $posts->get($limit)
				->getResultObject();
		} else {
			$posts = $posts->get()
				->getResultObject();
		}

		if ((array) $posts > 0) {
			foreach ($posts as $post) {
				$post = $this->convertPost($post);
			}
		}

		return $posts;
	}

	public function getPost(int $postId, string $postFriendlyUrl)
	{
		$post = $this->db
			->table($this->cfgNews->database['news'] . ' news')
			->select("news.*,author.username AS 'author_username',author.first_name AS 'author_first_name',author.last_name AS 'author_last_name',category.id AS 'category_id',category.name AS 'category_name',category.friendly_url AS 'category_friendly_url',category.description AS 'category_description'")
			->join('users author', 'news.author=author.id')
			->join($this->cfgNews->database['post_categories'] . ' pc', 'news.id=pc.post')
			->join($this->cfgNews->database['categories'] . ' category', 'category.id=pc.category')
			->where('news.id', $postId)
			->where('news.friendly_url', $postFriendlyUrl)
			->where('news.visible', 1)
			->where('category.visible', 1)
			->where('news.publication<=now()')
			->get()
			->getRowObject();

		if ($post) {
			$post = $this->convertPost($post);
		}

		return $post;
	}

	public function getPopularPosts(int $limit = null, string $categoryFriendlyUrl = null)
	{
		$posts = $this->db
			->table($this->cfgNews->database['news'] . ' news')
			->select("news.*,author.username AS 'author_username',author.first_name AS 'author_first_name',author.last_name AS 'author_last_name',category.id AS 'category_id',category.name AS 'category_name',category.friendly_url AS 'category_friendly_url',category.description AS 'category_description'")
			->join('users author', 'news.author=author.id')
			->join($this->cfgNews->database['post_categories'] . ' pc', 'news.id=pc.post')
			->join($this->cfgNews->database['categories'] . ' category', 'category.id=pc.category')
			->where('news.visible', 1)
			->where('category.visible', 1)
			->where('news.publication<=now()')
			->orderBy('news.views', 'DESC');

		if (!empty($categoryFriendlyUrl)) {
			$posts = $posts->where('category.friendly_url', $categoryFriendlyUrl);
		}

		if (!empty($limit)) {
			$posts = $posts->get($limit)
				->getResultObject();
		} else {
			$posts = $posts->get()
				->getResultObject();
		}

		if ((array) $posts > 0) {
			foreach ($posts as $post) {
				$post = $this->convertPost($post);
			}
		}

		return $posts;
	}

	public function addViewPost($id, $views)
	{
		$this->db->table($this->cfgNews->database['news'])->update(['views' => ++$views], ['id' => $id]);

		if ($this->db->transStatus() === false) {
			return false;
		}
		return true;
	}

	public function convertPost($r)
	{
		$r->description = character_limiter(strip_tags(html_entity_decode($r->content)), 300);
		$r->content = html_entity_decode($r->content);
		$r->tags = explode(',', $r->tags);
		$r->category = (object) [
			'id' => $r->category_id,
			'name' => $r->category_name,
			'description' => $r->category_description,
			'friendly_url' => $r->category_friendly_url,
			'url' => base_url(route_to('news_category', $r->category_friendly_url))
		];
		unset($r->category_id, $r->category_name, $r->category_description, $r->category_friendly_url, $r->category_visible);

		unset($r->visible, $r->created_at);

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

		$r->url = base_url(route_to('news_post', $r->id, $r->friendly_url));

		return $r;
	}

	//Categories
	public function getCategories()
	{
		$categories =  (object) $this->db
			->table($this->cfgNews->database['categories'] . ' category')
			->select('id,name,friendly_url,description')
			->where('visible', 1)
			->orderBy('name', 'ASC')
			->get()
			->getResultObject();

		if ((array)$categories > 0) {
			foreach ($categories as $category) {
				$category = $this->convertCategory($category);
			}
		}

		return $categories;
	}

	public function getCategory(string $friendlyUrl)
	{
		$category = $this->db
			->table($this->cfgNews->database['categories'] . ' category')
			->select("id,name,friendly_url,description")
			->where('friendly_url', $friendlyUrl)
			->where('visible', 1)
			->get()
			->getRowObject();
		if ($category) {
			$category = $this->convertCategory($category);
		}

		return $category;
	}

	public function convertCategory($r)
	{
		$r->description = html_entity_decode($r->description);
		$r->posts = $this->getPosts(null, null, $r->friendly_url);
		$r->postsCount = count((array) $r->posts);
		$r->url = base_url(route_to('news_category', $r->friendly_url));

		return $r;
	}
}
