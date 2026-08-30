<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Dados cadastrais da igreja (registro único).
 */
class CreateIgrejas extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'razao_social'      => ['type' => 'VARCHAR', 'constraint' => 191],
            'nome'              => ['type' => 'VARCHAR', 'constraint' => 191],
            'cnpj'              => ['type' => 'VARCHAR', 'constraint' => 18, 'null' => true],
            'telefone'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'whatsapp'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'             => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'site'              => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'cep'               => ['type' => 'VARCHAR', 'constraint' => 9, 'null' => true],
            'logradouro'        => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'numero'            => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'complemento'       => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'bairro'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'cidade'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'estado'            => ['type' => 'CHAR', 'constraint' => 2, 'null' => true],
            'pais'              => ['type' => 'VARCHAR', 'constraint' => 60, 'default' => 'Brasil'],
            'pastor_responsavel'=> ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'data_fundacao'     => ['type' => 'DATE', 'null' => true],
            'logo'              => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ativo'             => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('cnpj');
        $this->forge->createTable('igrejas', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('igrejas', true);
    }
}
