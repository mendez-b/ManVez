<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePasswordResetsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        // No usamos ID autoincremental aquí, el token es nuestra clave de búsqueda
        $this->forge->createTable('password_resets');
    }
    public function down()
    {
        $this->forge->dropTable('password_resets', true);
    }
}
