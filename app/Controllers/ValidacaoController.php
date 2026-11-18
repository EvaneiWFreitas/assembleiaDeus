<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Configuracao;

final class ValidacaoController extends Controller
{
    public function verificar(string $codigo): void
    {
        $carteirinha = Database::fetch(
            "SELECT c.*,
                    CASE WHEN c.`tipo` = 'MEMBRO' THEN m.`nome_completo` ELSE o.`nome_completo` END AS `nome_pessoa`,
                    CASE WHEN c.`tipo` = 'MEMBRO' THEN m.`codigo` ELSE o.`codigo` END AS `codigo_pessoa`,
                    ca.`nome` AS `cargo`,
                    CASE WHEN c.`tipo` = 'MEMBRO' THEN cg.`nome` ELSE co.`nome` END AS `congregacao`
               FROM `carteirinhas` c
               LEFT JOIN `membros` m ON c.`tipo` = 'MEMBRO' AND m.`id` = c.`referencia_id`
               LEFT JOIN `obreiros` o ON c.`tipo` = 'OBREIRO' AND o.`id` = c.`referencia_id`
               LEFT JOIN `cargos_ministeriais` ca ON ca.`id` = o.`cargo_id`
               LEFT JOIN `congregacoes` cg ON cg.`id` = m.`congregacao_id`
               LEFT JOIN `congregacoes` co ON co.`id` = o.`congregacao_id`
              WHERE c.`codigo_validacao` = ? AND c.`ativa` = 1
              LIMIT 1",
            [urldecode($codigo)]
        );

        $valida = false;
        if ($carteirinha !== null) {
            $valida = $carteirinha['data_validade'] === null
                || strtotime((string) $carteirinha['data_validade']) >= strtotime('today');
        }

        $this->render('site.validar-carteirinha', [
            'title'       => 'Validação de Carteirinha',
            'carteirinha' => $carteirinha,
            'valida'      => $valida,
            'config'      => Configuracao::all(),
        ]);
    }
}
