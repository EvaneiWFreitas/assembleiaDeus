<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Banners/imagens de fundo da página principal do site.
 */
class CreateBanners extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'titulo'     => ['type' => 'VARCHAR', 'constraint' => 150],
            'subtitulo'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'imagem'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'link'       => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'ordem'      => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'ativo'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('ordem');
        $this->forge->createTable('banners', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('banners', true);
    }
}