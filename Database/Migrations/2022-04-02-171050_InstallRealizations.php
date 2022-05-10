<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InstallRealizations extends Migration
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
        $config = config('App\Modules\Realizations\Config\Realizations');

        parent::__construct();

        $this->tables = $config->database;
    }

    public function up()
    {
        /*
         * Table realizations categories
         */
        $this->forge->dropTable($this->tables['categories'], true);
        $fields = [
            'id'            => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'         => ['type' => 'varchar', 'constraint' => 255],
            'friendly_url'  => ['type' => 'TEXT', 'null' => true],
            'content'       => ['type' => 'TEXT', 'null' => true],
            'order_by'      => ['type' => 'int', 'constraint' => 11, 'default' => '1'],
            'visible'       => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => '0'],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable($this->tables['categories'], true);

        /*
         * Table realizations
         */
        $this->forge->dropTable($this->tables['realizations'], true);
        $fields = [
            'id' => array(
                'type'           => 'int',
                'constraint'     => 11,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE
            ),
            'category' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'title' => array(
                'type'          => 'VARCHAR',
                'constraint' => '100',
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'content' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'friendly_url' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'visible' => array(
                'type'          => 'TINYINT',
                'constraint' => '1',
                'null'          => TRUE,
                'default'      => '0',
            ),
            'order_by'        => ['type' => 'int', 'constraint' => '11', 'default' => '1'],
            'created_at datetime default current_timestamp',
            'edited_at datetime default current_timestamp on update current_timestamp',
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['category']);
        $this->forge->addForeignKey('category', $this->tables['categories'], 'id', false, 'CASCADE');
        $this->forge->createTable($this->tables['realizations'], true, ['COLLATE' => 'utf8_general_ci']);

        /*
         * Table realizations images
         */
        $this->forge->dropTable($this->tables['images'], true);
        $fields = [
            'id'            => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'realization'   => ['type' => 'int', 'constraint' => '11', 'unsigned' => true],
            'title'         => ['type' => 'varchar', 'constraint' => 255],
            'friendly_url'  => ['type' => 'TEXT', 'null' => true],
            'image'         => ['type' => 'VARCHAR', 'constraint' => '400'],
            'order_by'      => ['type' => 'int', 'constraint' => 11, 'default' => '1'],
            'visible'       => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => '0'],
            'created_at datetime default current_timestamp',
            'edited_at datetime default current_timestamp on update current_timestamp',
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['realization']);
        $this->forge->addForeignKey('realization', $this->tables['realizations'], 'id', false, 'CASCADE');
        $this->forge->createTable($this->tables['images'], true);
    }

    //--------------------------------------------------------------------

    public function down()
    {
        $this->forge->dropTable($this->tables['categories']);
        $this->forge->dropTable($this->tables['realizations']);
        $this->forge->dropTable($this->tables['images']);
    }
}
