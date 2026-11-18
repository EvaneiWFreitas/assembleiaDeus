<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Concerns\ScopedByCongregacao;
use App\Core\Database;

/**
 * Arquivo morto de obreiros desligados, com preservação de snapshot.
 */
final class ArquivoMorto
{
    use ScopedByCongregacao;

    public static function all(array $filtros = []): array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return [];
        }

        $sql = 'SELECT am.*, o.codigo, o.nome_completo, o.cpf, o.situacao AS situacao_obreiro,
                       cm.nome AS cargo_nome, c.nome AS congregacao_nome,
                       u.nome AS responsavel_nome, ur.nome AS restaurado_por_nome
                  FROM arquivo_morto_obreiros am
                  INNER JOIN obreiros o ON o.id = am.obreiro_id
                  LEFT JOIN cargos_ministeriais cm ON cm.id = o.cargo_id
                  LEFT JOIN congregacoes c ON c.id = o.congregacao_id
                  LEFT JOIN usuarios u ON u.id = am.responsavel_id
                  LEFT JOIN usuarios ur ON ur.id = am.restaurado_por
                 WHERE 1 = 1';
        $params = [];

        if ($escopo !== null) {
            $sql .= ' AND o.congregacao_id = ?';
            $params[] = $escopo;
        }

        $busca = trim((string) ($filtros['busca'] ?? ''));
        if ($busca !== '') {
            $sql .= ' AND (o.nome_completo LIKE ? OR o.codigo LIKE ? OR o.cpf LIKE ? OR am.motivo LIKE ?)';
            $like = '%' . $busca . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $estado = (string) ($filtros['estado'] ?? '');
        if ($estado === 'ARQUIVADO') {
            $sql .= ' AND am.restaurado_em IS NULL';
        } elseif ($estado === 'RESTAURADO') {
            $sql .= ' AND am.restaurado_em IS NOT NULL';
        }

        if (!empty($filtros['congregacao_id'])) {
            $sql .= ' AND o.congregacao_id = ?';
            $params[] = (int) $filtros['congregacao_id'];
        }

        $sql .= ' ORDER BY am.restaurado_em IS NOT NULL, am.data_desligamento DESC, o.nome_completo';

        return Database::fetchAll($sql, $params);
    }

    public static function find(int $id): ?array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return null;
        }

        $sql = 'SELECT am.*, o.codigo, o.nome_completo, o.cpf, o.email, o.telefone,
                       o.numero_registro_ministerial, o.situacao AS situacao_obreiro,
                       cm.nome AS cargo_nome, c.nome AS congregacao_nome,
                       u.nome AS responsavel_nome, ur.nome AS restaurado_por_nome
                  FROM arquivo_morto_obreiros am
                  INNER JOIN obreiros o ON o.id = am.obreiro_id
                  LEFT JOIN cargos_ministeriais cm ON cm.id = o.cargo_id
                  LEFT JOIN congregacoes c ON c.id = o.congregacao_id
                  LEFT JOIN usuarios u ON u.id = am.responsavel_id
                  LEFT JOIN usuarios ur ON ur.id = am.restaurado_por
                 WHERE am.id = ?';
        $params = [$id];

        if ($escopo !== null) {
            $sql .= ' AND o.congregacao_id = ?';
            $params[] = $escopo;
        }

        return Database::fetch($sql . ' LIMIT 1', $params);
    }

    public static function obreirosDisponiveis(): array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return [];
        }

        $sql = 'SELECT o.id, o.codigo, o.nome_completo, cm.nome AS cargo_nome, c.nome AS congregacao_nome
                  FROM obreiros o
                  INNER JOIN cargos_ministeriais cm ON cm.id = o.cargo_id
                  LEFT JOIN congregacoes c ON c.id = o.congregacao_id
                 WHERE o.situacao <> ?
                   AND NOT EXISTS (
                       SELECT 1 FROM arquivo_morto_obreiros am WHERE am.obreiro_id = o.id
                   )';
        $params = ['ARQUIVADO'];

        if ($escopo !== null) {
            $sql .= ' AND o.congregacao_id = ?';
            $params[] = $escopo;
        }

        $sql .= ' ORDER BY o.nome_completo';

        return Database::fetchAll($sql, $params);
    }

    public static function obreiroDisponivel(int $obreiroId): ?array
    {
        foreach (self::obreirosDisponiveis() as $obreiro) {
            if ((int) $obreiro['id'] === $obreiroId) {
                return $obreiro;
            }
        }

        return null;
    }

    public static function estaArquivado(int $obreiroId): bool
    {
        return (int) Database::fetchColumn(
            'SELECT COUNT(*) FROM arquivo_morto_obreiros WHERE obreiro_id = ?',
            [$obreiroId]
        ) > 0;
    }

    public static function arquivar(array $dados): string
    {
        return Database::transaction(static function () use ($dados): string {
            $obreiro = Database::fetch(
                'SELECT * FROM obreiros WHERE id = ?',
                [$dados['obreiro_id']]
            );
            if ($obreiro === null) {
                throw new \RuntimeException('Obreiro não encontrado para arquivamento.');
            }

            $historico = Database::fetchAll(
                'SELECT * FROM historico_ministerial WHERE obreiro_id = ? ORDER BY data_evento, id',
                [$dados['obreiro_id']]
            );

            $id = Database::insert(
                'INSERT INTO arquivo_morto_obreiros
                    (obreiro_id, motivo, data_desligamento, responsavel_id,
                     dados_snapshot, historico_snapshot, observacoes)
                 VALUES
                    (:obreiro_id, :motivo, :data_desligamento, :responsavel_id,
                     :dados_snapshot, :historico_snapshot, :observacoes)',
                [
                    ':obreiro_id'         => $dados['obreiro_id'],
                    ':motivo'             => $dados['motivo'],
                    ':data_desligamento'  => $dados['data_desligamento'],
                    ':responsavel_id'     => $dados['responsavel_id'],
                    ':dados_snapshot'     => self::codificar($obreiro),
                    ':historico_snapshot' => self::codificar($historico),
                    ':observacoes'        => $dados['observacoes'],
                ]
            );

            Database::execute(
                "UPDATE obreiros SET situacao = 'ARQUIVADO', updated_by = ? WHERE id = ?",
                [$dados['responsavel_id'], $dados['obreiro_id']]
            );

            Database::execute(
                'INSERT INTO historico_ministerial
                    (obreiro_id, tipo, data_evento, descricao, responsavel_id)
                 VALUES (?, ?, ?, ?, ?)',
                [
                    $dados['obreiro_id'],
                    'DESLIGAMENTO',
                    $dados['data_desligamento'],
                    'Arquivado no arquivo morto: ' . $dados['motivo'],
                    $dados['responsavel_id'],
                ]
            );

            return $id;
        });
    }

    public static function restaurar(int $id, int $responsavelId): void
    {
        Database::transaction(static function () use ($id, $responsavelId): void {
            $arquivo = Database::fetch(
                'SELECT * FROM arquivo_morto_obreiros WHERE id = ?',
                [$id]
            );
            if ($arquivo === null) {
                throw new \RuntimeException('Registro de arquivo morto não encontrado.');
            }
            if (!empty($arquivo['restaurado_em'])) {
                throw new \RuntimeException('Este obreiro já foi restaurado.');
            }

            Database::execute(
                'UPDATE arquivo_morto_obreiros
                    SET restaurado_em = NOW(), restaurado_por = ?
                  WHERE id = ?',
                [$responsavelId, $id]
            );

            Database::execute(
                "UPDATE obreiros SET situacao = 'ATIVO', updated_by = ? WHERE id = ?",
                [$responsavelId, (int) $arquivo['obreiro_id']]
            );

            Database::execute(
                'INSERT INTO historico_ministerial
                    (obreiro_id, tipo, data_evento, descricao, responsavel_id)
                 VALUES (?, ?, CURDATE(), ?, ?)',
                [
                    (int) $arquivo['obreiro_id'],
                    'RETORNO',
                    'Restaurado do arquivo morto por solicitação do administrador.',
                    $responsavelId,
                ]
            );
        });
    }

    public static function decodificar(?string $json): array
    {
        if ($json === null || trim($json) === '') {
            return [];
        }

        $dados = json_decode($json, true);

        return is_array($dados) ? $dados : [];
    }

    private static function codificar(mixed $dados): string
    {
        return json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]';
    }
}
