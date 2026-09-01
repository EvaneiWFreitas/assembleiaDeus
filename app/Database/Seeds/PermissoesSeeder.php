<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Cria permissões granulares (modulo.acao) para os módulos da Etapa 1.
 * Novas etapas adicionam novos módulos aqui.
 */
class PermissoesSeeder extends Seeder
{
    private const ACOES = ['visualizar', 'cadastrar', 'editar', 'excluir', 'imprimir', 'exportar', 'aprovar'];

    private const MODULOS = [
        'dashboard' => ['visualizar'],
        'usuarios'  => self::ACOES,
        'roles'     => self::ACOES,
        // Etapa 2
        'igreja'        => ['visualizar', 'editar'],
        'banners'       => ['visualizar', 'cadastrar', 'editar', 'excluir'],
        'congregacoes'  => self::ACOES,
        'membros'       => self::ACOES,
        'visitantes'    => self::ACOES,
        'obreiros'      => self::ACOES,
        // Etapa 3
        'departamentos' => self::ACOES,
        'ministerios'   => self::ACOES,
        'celulas'       => self::ACOES,
        'discipulados'  => self::ACOES,
        'cursos'        => self::ACOES,
        // Módulos futuros já previstos (habilitam o filtro sem alterar código):
        'financeiro'  => self::ACOES,
        'relatorios'  => ['visualizar', 'imprimir', 'exportar'],
    ];

    public function run(): void
    {
        foreach (self::MODULOS as $modulo => $acoes) {
            foreach ($acoes as $acao) {
                $existe = $this->db->table('permissoes')
                    ->where('modulo', $modulo)->where('acao', $acao)
                    ->countAllResults();

                if ($existe === 0) {
                    $this->db->table('permissoes')->insert([
                        'modulo'    => $modulo,
                        'acao'      => $acao,
                        'descricao' => ucfirst($acao) . ' em ' . ucfirst($modulo),
                    ]);
                }
            }
        }
    }
}
