<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Membros — cadastro completo com dados pessoais, contato,
 * endereço e informações eclesiásticas.
 */
class CreateMembros extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'congregacao_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nome'                => ['type' => 'VARCHAR', 'constraint' => 150],
            'nome_social'         => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'cpf'                 => ['type' => 'VARCHAR', 'constraint' => 14, 'null' => true],
            'rg'                  => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'data_nascimento'     => ['type' => 'DATE', 'null' => true],
            'sexo'                => ['type' => 'CHAR', 'constraint' => 1, 'null' => true],          // M | F
            'estado_civil'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'nacionalidade'       => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'naturalidade'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'foto'                => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'telefone'            => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'whatsapp'            => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'               => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'cep'                 => ['type' => 'VARCHAR', 'constraint' => 9, 'null' => true],
            'logradouro'          => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'numero'              => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'complemento'         => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'bairro'              => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'cidade'              => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'estado'              => ['type' => 'CHAR', 'constraint' => 2, 'null' => true],
            'data_conversao'      => ['type' => 'DATE', 'null' => true],
            'data_batismo'        => ['type' => 'DATE', 'null' => true],
            'local_batismo'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'igreja_anterior'     => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'data_recebimento'    => ['type' => 'DATE', 'null' => true],
            'tipo_membro'         => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'Comunhão'],
            'cargo'               => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'observacoes'         => ['type' => 'TEXT', 'null' => true],
            'status'              => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Ativo'], // Ativo|Inativo|Transferido|Desligado|Falecido
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('cpf');
        $this->forge->addForeignKey('congregacao_id', 'congregacoes', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addKey('status');
        $this->forge->addKey('data_nascimento');
        $this->forge->createTable('membros', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('membros', true);
    }
}
