<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Concerns\ScopedByCongregacao;
use App\Core\Database;

/**
 * Congregações vinculadas à igreja matriz.
 */
final class Congregacao
{
    use ScopedByCongregacao;

    public static function all(): array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return [];
        }

        $sql = 'SELECT c.*, i.nome AS igreja_nome, u.nome AS pastor_nome,
                       (SELECT COUNT(*) FROM pontos_pregacao p WHERE p.congregacao_id = c.id) AS total_pontos
                  FROM congregacoes c
                  INNER JOIN igrejas_matriz i ON i.id = c.igreja_matriz_id
                  LEFT JOIN usuarios u ON u.id = c.pastor_responsavel_id';
        $params = [];

        if ($escopo !== null) {
            $sql .= ' WHERE c.id = ?';
            $params[] = $escopo;
        }

        $sql .= ' ORDER BY i.nome, c.nome';

        return Database::fetchAll($sql, $params);
    }

    public static function find(int $id): ?array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return null;
        }

        $sql = 'SELECT c.*, i.nome AS igreja_nome, u.nome AS pastor_nome
                  FROM congregacoes c
                  INNER JOIN igrejas_matriz i ON i.id = c.igreja_matriz_id
                  LEFT JOIN usuarios u ON u.id = c.pastor_responsavel_id
                 WHERE c.id = ?';
        $params = [$id];

        if ($escopo !== null) {
            $sql .= ' AND c.id = ?';
            $params[] = $escopo;
        }

        return Database::fetch($sql . ' LIMIT 1', $params);
    }

    public static function options(?int $igrejaId = null): array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return [];
        }

        $sql = 'SELECT c.id, c.nome, i.nome AS igreja_nome
                  FROM congregacoes c
                  INNER JOIN igrejas_matriz i ON i.id = c.igreja_matriz_id
                 WHERE 1 = 1';
        $params = [];

        if ($escopo !== null) {
            $sql .= ' AND c.id = ?';
            $params[] = $escopo;
        }

        if ($igrejaId !== null) {
            $sql .= ' AND c.igreja_matriz_id = ?';
            $params[] = $igrejaId;
        }

        return Database::fetchAll($sql . ' ORDER BY i.nome, c.nome', $params);
    }

    public static function igrejaPermitida(int $igrejaId): bool
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === null) {
            return true;
        }
        if ($escopo === 0) {
            return false;
        }

        $matrizId = Database::fetchColumn('SELECT igreja_matriz_id FROM congregacoes WHERE id = ?', [$escopo]);

        return $matrizId !== false && (int) $matrizId === $igrejaId;
    }

    public static function codigoExiste(string $codigo, ?int $ignorarId = null): bool
    {
        if ($codigo === '') {
            return false;
        }

        $sql = 'SELECT COUNT(*) FROM congregacoes WHERE codigo = ?';
        $params = [$codigo];

        if ($ignorarId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $ignorarId;
        }

        return (int) Database::fetchColumn($sql, $params) > 0;
    }

    public static function create(array $dados): string
    {
        return Database::insert(
            'INSERT INTO congregacoes
                (codigo, nome, igreja_matriz_id, endereco, numero, complemento, bairro,
                 cidade, estado, cep, telefone, email, pastor_responsavel_id,
                 data_abertura, foto, situacao, observacoes)
             VALUES
                (:codigo, :nome, :igreja_matriz_id, :endereco, :numero, :complemento, :bairro,
                 :cidade, :estado, :cep, :telefone, :email, :pastor_responsavel_id,
                 :data_abertura, :foto, :situacao, :observacoes)',
            self::parametros($dados)
        );
    }

    public static function update(int $id, array $dados): void
    {
        $params = self::parametros($dados);
        $params[':id'] = $id;

        Database::execute(
            'UPDATE congregacoes SET
                codigo = :codigo, nome = :nome, igreja_matriz_id = :igreja_matriz_id,
                endereco = :endereco, numero = :numero, complemento = :complemento,
                bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep,
                telefone = :telefone, email = :email,
                pastor_responsavel_id = :pastor_responsavel_id,
                data_abertura = :data_abertura, foto = :foto,
                situacao = :situacao, observacoes = :observacoes
             WHERE id = :id',
            $params
        );
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM congregacoes WHERE id = ?', [$id]);
    }

    private static function parametros(array $dados): array
    {
        return [
            ':codigo'                => $dados['codigo'],
            ':nome'                  => $dados['nome'],
            ':igreja_matriz_id'      => $dados['igreja_matriz_id'],
            ':endereco'              => $dados['endereco'],
            ':numero'                => $dados['numero'],
            ':complemento'           => $dados['complemento'],
            ':bairro'                => $dados['bairro'],
            ':cidade'                => $dados['cidade'],
            ':estado'                => $dados['estado'],
            ':cep'                   => $dados['cep'],
            ':telefone'              => $dados['telefone'],
            ':email'                 => $dados['email'],
            ':pastor_responsavel_id' => $dados['pastor_responsavel_id'],
            ':data_abertura'         => $dados['data_abertura'],
            ':foto'                  => $dados['foto'],
            ':situacao'              => $dados['situacao'],
            ':observacoes'           => $dados['observacoes'],
        ];
    }
}
