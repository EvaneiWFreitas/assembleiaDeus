<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Numeros e listas exibidos no dashboard.
 */
final class Dashboard
{
    public static function resumo(?int $congregacaoId, bool $verContatos): array
    {
        $escopo = $congregacaoId === null ? '' : ' AND congregacao_id = ?';
        $params = $congregacaoId === null ? [] : [$congregacaoId];

        $escopoCongregacao = $congregacaoId === null ? '' : ' WHERE id = ?';
        $paramsCongregacao = $congregacaoId === null ? [] : [$congregacaoId];

        $resumo = [
            'igrejas' => (int) Database::fetchColumn('SELECT COUNT(*) FROM igrejas_matriz'),
            'membros_ativos' => (int) Database::fetchColumn(
                'SELECT COUNT(*) FROM membros WHERE situacao = \'ATIVO\'' . $escopo,
                $params
            ),
            'obreiros_ativos' => (int) Database::fetchColumn(
                'SELECT COUNT(*) FROM obreiros WHERE situacao = \'ATIVO\'' . $escopo,
                $params
            ),
            'congregacoes' => (int) Database::fetchColumn(
                'SELECT COUNT(*) FROM congregacoes' . $escopoCongregacao,
                $paramsCongregacao
            ),
            'pontos_pregacao' => (int) Database::fetchColumn(
                'SELECT COUNT(*) FROM pontos_pregacao WHERE situacao = \'ATIVO\'' . $escopo,
                $params
            ),
            'eventos_proximos' => (int) Database::fetchColumn(
                'SELECT COUNT(*) FROM eventos
                  WHERE data_inicio >= NOW() AND situacao <> \'CANCELADO\'' . $escopo,
                $params
            ),
            'entradas_mes' => (float) Database::fetchColumn(
                'SELECT COALESCE(SUM(valor), 0)
                   FROM lancamentos_financeiros
                  WHERE estado = \'CONFIRMADO\'
                    AND tipo IN (\'DIZIMO\', \'OFERTA\', \'ENTRADA\')
                    AND DATE_FORMAT(data_lancamento, \'%Y-%m\') = ?' . $escopo,
                array_merge([date('Y-m')], $params)
            ),
        ];

        $resumo['contatos_nao_lidos'] = $verContatos
            ? (int) Database::fetchColumn('SELECT COUNT(*) FROM contatos WHERE lido = 0')
            : null;

        return $resumo;
    }

    public static function proximosEventos(?int $congregacaoId, int $limite = 5): array
    {
        $escopo = $congregacaoId === null ? '' : ' AND congregacao_id = ?';
        $params = $congregacaoId === null ? [] : [$congregacaoId];

        return Database::fetchAll(
            'SELECT id, titulo, data_inicio, local_evento
               FROM eventos
              WHERE data_inicio >= NOW() AND situacao <> \'CANCELADO\'' . $escopo . '
              ORDER BY data_inicio ASC
              LIMIT ' . (int) $limite,
            $congregacaoId === null ? [] : $params
        );
    }

    public static function ultimosAcessos(int $usuarioId, int $limite = 5): array
    {
        return Database::fetchAll(
            'SELECT acao, detalhes, created_at
               FROM logs_acesso
              WHERE usuario_id = ?
              ORDER BY id DESC
              LIMIT ' . (int) $limite,
            [$usuarioId]
        );
    }
}
