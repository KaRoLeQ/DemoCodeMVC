<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InstallNews extends Migration
{
	/**
	 * Tables
	 *
	 * @var array
	 */
	private $tables;

	/**
	 * Construct
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$config = config('App\Modules\News\Config\News');
		$this->tables = $config->database;
	}

	public function up()
	{
		/*
         * Table news Posts
         */
		$fields = [
			'id' => array(
				'type' 			 => 'int',
				'constraint' 	 => 11,
				'unsigned' 		 => TRUE,
				'auto_increment' => TRUE
			),
			'title' => array(
				'type' 		 => 'VARCHAR',
				'constraint' => '100',
			),
			'content' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
			'description' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
			'tags' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
			'friendly_url' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
			'publication' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),
			'image' => array(
				'type' 		 => 'VARCHAR',
				'constraint' => '400',
			),
			'visible' => array(
				'type' 		 => 'TINYINT',
				'constraint' => '1',
				'null' 		 => TRUE,
				'default' 	 => '0',
			),
			'views' => array(
				'type' 		=> 'INT',
				'default' 	=> '0',
			),
			'author' => array(
				'type'           => 'MEDIUMINT',
				'constraint'     => '8',
				'unsigned'       => true,
			),
			'created_at datetime default current_timestamp',
			'edited_at datetime default current_timestamp on update current_timestamp',
		];

		$this->forge->addField($fields);
		$this->forge->addKey('id', true);
		$this->forge->addKey(['author']);
		$this->forge->addForeignKey('author', 'users', 'id', false, 'CASCADE');
		$this->forge->createTable($this->tables['news'], true, ['COLLATE' => 'utf8_general_ci']);

		/*
         * Table news categories
         */
		$fields = [
			'id'          => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'name'        => ['type' => 'varchar', 'constraint' => 255],
			'friendly_url' => ['type' => 'TEXT', 'null' => true],
			'description' => ['type' => 'TEXT', 'null' => true],
			'visible' 	  => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => '0'],
		];

		$this->forge->addField($fields);
		$this->forge->addKey('id', true);
		$this->forge->createTable($this->tables['categories'], true);

		/*
         * Table news post categories
         */
		$fields = [
			'category' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
			'post'  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
		];

		$this->forge->addField($fields);
		$this->forge->addKey(['category', 'post']);
		$this->forge->addForeignKey('category', $this->tables['categories'], 'id', false, 'CASCADE');
		$this->forge->addForeignKey('post', $this->tables['news'], 'id', false, 'CASCADE');
		$this->forge->createTable($this->tables['post_categories'], true);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable($this->tables['news']);
		$this->forge->dropTable($this->tables['categories']);
		$this->forge->dropTable($this->tables['post_categories']);
	}
}
