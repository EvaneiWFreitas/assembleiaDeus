<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Dados fictícios da Etapa 2: igreja, congregações, membros, visitantes e obreiros.
 * Apenas para ambiente de desenvolvimento.
 */
class Etapa2Seeder extends Seeder
{
    public function run(): void
    {
        // Igreja
        if ($this->db->table('igrejas')->countAllResults() === 0) {
            $this->db->table('igrejas')->insert([
                'nome'          => 'Assembleia de Deus Central',
                'razao_social'  => 'Assembleia de Deus Central - Min. Belém',
                'cnpj'          => '12345678000190',
                'telefone'      => '(11) 3333-4444',
                'whatsapp'      => '(11) 99999-8888',
                'email'         => 'contato@adcentral.com.br',
                'cidade'        => 'São Paulo',
                'estado'        => 'SP',
                'pastor_responsavel' => 'Pr. João da Silva',
                'data_fundacao' => '1975-05-10',
            ]);
        }
        $igrejaId = (int) $this->db->table('igrejas')->select('id')->get()->getFirstRow('array')['id'];

        // Congregações
        $congregacoes = [
            ['nome' => 'Sede Central', 'codigo' => 'CONG-01', 'cidade' => 'São Paulo', 'estado' => 'SP'],
            ['nome' => 'Congregação Jardim das Flores', 'codigo' => 'CONG-02', 'cidade' => 'Guarulhos', 'estado' => 'SP'],
            ['nome' => 'Congregação Vila Nova', 'codigo' => 'CONG-03', 'cidade' => 'Osasco', 'estado' => 'SP'],
        ];
        foreach ($congregacoes as $c) {
            if ($this->db->table('congregacoes')->where('codigo', $c['codigo'])->countAllResults() === 0) {
                $this->db->table('congregacoes')->insert($c + ['igreja_id' => $igrejaId, 'ativo' => 1, 'data_abertura' => '2000-01-15']);
            }
        }
        $congIds = array_column($this->db->table('congregacoes')->select('id')->get()->getResultArray(), 'id');

        // Membros
        $membros = [
            ['nome' => 'João da Silva', 'cargo' => 'Pastor', 'sexo' => 'M'],
            ['nome' => 'Maria Souza', 'cargo' => 'Líder', 'sexo' => 'F'],
            ['nome' => 'Pedro Santos', 'cargo' => 'Obreiro', 'sexo' => 'M'],
            ['nome' => 'Ana Oliveira', 'cargo' => 'Membro', 'sexo' => 'F'],
            ['nome' => 'Carlos Pereira', 'cargo' => 'Diácono', 'sexo' => 'M'],
            ['nome' => 'Fernanda Lima', 'cargo' => 'Membro', 'sexo' => 'F'],
            ['nome' => 'Lucas Ferreira', 'cargo' => 'Membro', 'sexo' => 'M'],
            ['nome' => 'Patrícia Alves', 'cargo' => 'Membro', 'sexo' => 'F'],
            ['nome' => 'Marcos Ribeiro', 'cargo' => 'Presbítero', 'sexo' => 'M'],
            ['nome' => 'Juliana Costa', 'cargo' => 'Membro', 'sexo' => 'F'],
        ];
        $i = 0;
        foreach ($membros as $m) {
            if ($this->db->table('membros')->where('nome', $m['nome'])->countAllResults() > 0) {
                $i++;
                continue;
            }
            $nascimento = sprintf('19%02d-%02d-%02d', rand(50, 98), rand(1, 12), rand(1, 28));
            $this->db->table('membros')->insert([
                'congregacao_id'  => $congIds[$i % count($congIds)],
                'nome'            => $m['nome'],
                'data_nascimento' => $nascimento,
                'sexo'            => $m['sexo'],
                'estado_civil'    => 'Casado',
                'telefone'        => sprintf('(11) 9%04d-%04d', rand(1000, 9999), rand(1000, 9999)),
                'tipo_membro'     => 'Comunhão',
                'cargo'           => $m['cargo'],
                'status'          => 'Ativo',
                'data_conversao'  => date('Y-m-d', strtotime('-' . rand(365, 5000) . ' days')),
                'data_batismo'    => date('Y-m-d', strtotime('-' . rand(100, 4000) . ' days')),
                'local_batismo'   => 'Sede Central',
            ]);
            $i++;
        }

        // Obreiros vinculados a membros
        $cargosObreiros = ['Pastor', 'Diácono', 'Presbítero', 'Obreiro'];
        foreach ($cargosObreiros as $cargo) {
            $membro = $this->db->table('membros')->where('cargo', $cargo)->get()->getFirstRow('array');
            if ($membro !== null && $this->db->table('obreiros')->where('membro_id', $membro['id'])->countAllResults() === 0) {
                $this->db->table('obreiros')->insert([
                    'membro_id'      => $membro['id'],
                    'cargo'          => $cargo,
                    'congregacao_id' => $membro['congregacao_id'],
                    'data_conexao'   => date('Y-m-d', strtotime('-' . rand(200, 3000) . ' days')),
                    'ativo'          => 1,
                ]);
            }
        }

        // Visitantes
        $visitantes = [
            ['nome' => 'Rafael Mendes', 'status' => 'Primeira visita'],
            ['nome' => 'Camila Duarte', 'status' => 'Contato realizado'],
            ['nome' => 'Bruno Cardoso', 'status' => 'Acompanhamento'],
            ['nome' => 'Larissa Martins', 'status' => 'Discipulado'],
        ];
        foreach ($visitantes as $v) {
            if ($this->db->table('visitantes')->where('nome', $v['nome'])->countAllResults() === 0) {
                $this->db->table('visitantes')->insert([
                    'congregacao_id'       => $congIds[array_rand($congIds)],
                    'nome'                 => $v['nome'],
                    'telefone'             => sprintf('(11) 9%04d-%04d', rand(1000, 9999), rand(1000, 9999)),
                    'data_primeira_visita' => date('Y-m-d', strtotime('-' . rand(1, 90) . ' days')),
                    'como_conheceu'        => 'Convite de um membro',
                    'status'               => $v['status'],
                ]);
            }
        }
    }
}
