<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Discipulado: pares discípulo/discipulador + encontros com presença.
 */
class CreateDiscipulados extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'discipulo_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'discipulador_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'data_inicio'      => ['type' => 'DATE', 'null' => true],
            'data_conclusao'   => ['type' => 'DATE', 'null' => true],
            'status'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Em andamento'], // Em andamento|Concluído|Cancelado
            'observacoes'      => ['type' => 'TEXT', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('discipulo_id', 'membros', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('discipulador_id', 'membros', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('discipulados', true);

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'discipulado_id'=> ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'data'          => ['type' => 'DATE'],
            'etapa'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'presente'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'observacoes'   => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('discipulado_id', 'discipulados', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('discipulado_encontros', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('discipulado_encontros', true);
        $this->forge->dropTable('discipulados', true);
    }
}
