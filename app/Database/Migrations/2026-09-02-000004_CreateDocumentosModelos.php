<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Modelos de Documentos (Certificados, Papel Timbrado, Atas, Convites, etc.)
 */
class CreateDocumentosModelos extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'titulo'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'tipo'          => ['type' => 'VARCHAR', 'constraint' => 50], // certificado_curso, certificado_consagracao, papel_timbrado_ata, papel_timbrado_convite, outro
            'conteudo'      => ['type' => 'LONGTEXT'], // HTML do modelo
            'variaveis'     => ['type' => 'TEXT', 'null' => true], // JSON com variáveis disponíveis
            'ativo'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'padrao'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('documentos_modelos', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('documentos_modelos', true);
    }
}