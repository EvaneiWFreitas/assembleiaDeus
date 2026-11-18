<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Concerns\ScopedByCongregacao;
use App\Core\Database;

/**
 * Igrejas matriz cadastradas no sistema.
 */
final class Igreja
{
    use ScopedByCongregacao;

    public static function all(): array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return [];
        }

        $sql = 'SELECT i.*, u.nome AS pastor_nome,
                       (SELECT COUNT(*) FROM congregacoes c WHERE c.igreja_matriz_id = i.id) AS total_congregacoes
                  FROM igrejas_matriz i
                  LEFT JOIN usuarios u ON u.id = i.pastor_responsavel_id';
        $params = [];

        if ($escopo !== null) {
            $sql .= ' WHERE EXISTS (
                        SELECT 1 FROM congregacoes sc
                         WHERE sc.igreja_matriz_id = i.id AND sc.id = ?
                      )';
            $params[] = $escopo;
        }

        $sql .= ' ORDER BY i.nome';

        return Database::fetchAll($sql, $params);
    }

    public static function find(int $id): ?array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return null;
        }

        $sql = 'SELECT i.*, u.nome AS pastor_nome
                  FROM igrejas_matriz i
                  LEFT JOIN usuarios u ON u.id = i.pastor_responsavel_id
                 WHERE i.id = ?';
        $params = [$id];

        if ($escopo !== null) {
            $sql .= ' AND EXISTS (
                        SELECT 1 FROM congregacoes sc
                         WHERE sc.igreja_matriz_id = i.id AND sc.id = ?
                      )';
            $params[] = $escopo;
        }

        return Database::fetch($sql . ' LIMIT 1', $params);
    }

    public static function options(): array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return [];
        }

        $sql = 'SELECT id, nome FROM igrejas_matriz';
        $params = [];

        if ($escopo !== null) {
            $sql .= ' WHERE EXISTS (
                        SELECT 1 FROM congregacoes sc
                         WHERE sc.igreja_matriz_id = igrejas_matriz.id AND sc.id = ?
                      )';
            $params[] = $escopo;
        }

        return Database::fetchAll($sql . ' ORDER BY nome', $params);
    }

    public static function cnpjExiste(string $cnpj, ?int $ignorarId = null): bool
    {
        if ($cnpj === '') {
            return false;
        }

        $sql = 'SELECT COUNT(*) FROM igrejas_matriz WHERE cnpj = ?';
        $params = [$cnpj];

        if ($ignorarId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $ignorarId;
        }

        return (int) Database::fetchColumn($sql, $params) > 0;
    }

    public static function create(array $dados): string
    {
        return Database::insert(
            'INSERT INTO igrejas_matriz
                (nome, cnpj, endereco, numero, complemento, bairro, cidade, estado,
                 cep, telefone, email, pastor_responsavel_id, data_fundacao, foto,
                 situacao, observacoes)
             VALUES
                (:nome, :cnpj, :endereco, :numero, :complemento, :bairro, :cidade, :estado,
                 :cep, :telefone, :email, :pastor_responsavel_id, :data_fundacao, :foto,
                 :situacao, :observacoes)',
            self::parametros($dados)
        );
    }

    public static function update(int $id, array $dados): void
    {
        $params = self::parametros($dados);
        $params[':id'] = $id;

        Database::execute(
            'UPDATE igrejas_matriz SET
                nome = :nome, cnpj = :cnpj, endereco = :endereco, numero = :numero,
                complemento = :complemento, bairro = :bairro, cidade = :cidade,
                estado = :estado, cep = :cep, telefone = :telefone, email = :email,
                pastor_responsavel_id = :pastor_responsavel_id, data_fundacao = :data_fundacao,
                foto = :foto, situacao = :situacao, observacoes = :observacoes
             WHERE id = :id',
            $params
        );
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM igrejas_matriz WHERE id = ?', [$id]);
    }

    private static function parametros(array $dados): array
    {
        return [
            ':nome'                  => $dados['nome'],
            ':cnpj'                  => $dados['cnpj'],
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
            ':data_fundacao'         => $dados['data_fundacao'],
            ':foto'                  => $dados['foto'],
            ':situacao'              => $dados['situacao'],
            ':observacoes'           => $dados['observacoes'],
        ];
    }
}
