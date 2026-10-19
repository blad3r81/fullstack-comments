<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateComments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 254],
            'text' => ['type' => 'TEXT'],
            'date' => ['type' => 'VARCHAR', 'constraint' => 10],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('created_at');
        $this->forge->createTable('comments');
    }

    public function down()
    {
        $this->forge->dropTable('comments');
    }
}
