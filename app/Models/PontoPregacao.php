<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Concerns\ScopedByCongregacao;
use App\Core\Database;

/**
 * Pontos de pregação vinculados às congregações.
 */
final class PontoPregacao
{
    use ScopedByCongregacao;

    public static function all(): array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return [];
        }

        $sql = 'SELECT p.*, c.nome AS congregacao_nome, i.nome AS igreja_nome,
                       u.nome AS responsavel_usuario
                  FROM pontos_pregacao p
                  INNER JOIN congregacoes c ON c.id = p.congregacao_id
                  INNER JOIN igrejas_matriz i ON i.id = c.igreja_matriz_id
                  LEFT JOIN usuarios u ON u.id = p.responsavel_id';
        $params = [];

        if ($escopo !== null) {
            $sql .= ' WHERE p.congregacao_id = ?';
            $params[] = $escopo;
        }

        $sql .= ' ORDER BY i.nome, c.nome, p.nome';

        return Database::fetchAll($sql, $params);
    }

    public static function find(int $id): ?array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return null;
        }

        $sql = 'SELECT p.*, c.nome AS congregacao_nome, i.nome AS igreja_nome,
                       u.nome AS responsavel_usuario
                  FROM pontos_pregacao p
                  INNER JOIN congregacoes c ON c.id = p.congregacao_id
                  INNER JOIN igrejas_matriz i ON i.id = c.igreja_matriz_id
                  LEFT JOIN usuarios u ON u.id = p.responsavel_id
                 WHERE p.id = ?';
        $params = [$id];

        if ($escopo !== null) {
            $sql .= ' AND p.congregacao_id = ?';
            $params[] = $escopo;
        }

        return Database::fetch($sql . ' LIMIT 1', $params);
    }

    public static function options(?int $congregacaoId = null): array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return [];
        }

        $sql = 'SELECT c.id, c.nome AS congregacao_nome, i.nome AS igreja_nome
                  FROM congregacoes c
                  INNER JOIN igrejas_matriz i ON i.id = c.igreja_matriz_id
                 WHERE 1 = 1';
        $params = [];

        if ($escopo !== null) {
            $sql .= ' AND c.id = ?';
            $params[] = $escopo;
        }

        if ($congregacaoId !== null) {
            $sql .= ' AND c.id = ?';
            $params[] = $congregacaoId;
        }

        return Database::fetchAll($sql . ' ORDER BY i.nome, c.nome', $params);
    }

    public static function congregacaoPermitida(int $congregacaoId): bool
    {
        return self::emEscopo($congregacaoId);
    }

    public static function create(array $dados): string
    {
        return Database::insert(
            'INSERT INTO pontos_pregacao
                (nome, congregacao_id, endereco, numero, bairro, cidade, estado, cep,
                 responsavel_id, responsavel_nome, dias_horarios, data_abertura,
                 foto, situacao, observacoes)
             VALUES
                (:nome, :congregacao_id, :endereco, :numero, :bairro, :cidade, :estado, :cep,
                 :responsavel_id, :responsavel_nome, :dias_horarios, :data_abertura,
                 :foto, :situacao, :observacoes)',
            self::parametros($dados)
        );
    }

    public static function update(int $id, array $dados): void
    {
        $params = self::parametros($dados);
        $params[':id'] = $id;

        Database::execute(
            'UPDATE pontos_pregacao SET
                nome = :nome, congregacao_id = :congregacao_id, endereco = :endereco,
                numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado,
                cep = :cep, responsavel_id = :responsavel_id,
                responsavel_nome = :responsavel_nome, dias_horarios = :dias_horarios,
                data_abertura = :data_abertura, foto = :foto,
                situacao = :situacao, observacoes = :observacoes
             WHERE id = :id',
            $params
        );
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM pontos_pregacao WHERE id = ?', [$id]);
    }

    private static function parametros(array $dados): array
    {
        return [
            ':nome'             => $dados['nome'],
            ':congregacao_id'   => $dados['congregacao_id'],
            ':endereco'         => $dados['endereco'],
            ':numero'           => $dados['numero'],
            ':bairro'           => $dados['bairro'],
            ':cidade'           => $dados['cidade'],
            ':estado'           => $dados['estado'],
            ':cep'              => $dados['cep'],
            ':responsavel_id'   => $dados['responsavel_id'],
            ':responsavel_nome' => $dados['responsavel_nome'],
            ':dias_horarios'    => $dados['dias_horarios'],
            ':data_abertura'    => $dados['data_abertura'],
            ':foto'             => $dados['foto'],
            ':situacao'         => $dados['situacao'],
            ':observacoes'      => $dados['observacoes'],
        ];
    }
}
