<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Galeria
{
    public static function todas(): array
    {
        return Database::fetchAll(
            'SELECT g.*, e.titulo AS evento_titulo
               FROM galeria g
               LEFT JOIN eventos e ON e.id = g.evento_id
              ORDER BY g.ordem, g.id'
        );
    }

    public static function buscar(int $id): ?array
    {
        return Database::fetch(
            'SELECT g.*, e.titulo AS evento_titulo
               FROM galeria g
               LEFT JOIN eventos e ON e.id = g.evento_id
              WHERE g.id = ?
              LIMIT 1',
            [$id]
        );
    }

    public static function criar(array $dados): int
    {
        return (int) Database::insert(
            'INSERT INTO galeria
                (titulo, descricao, imagem, evento_id, ordem, ativa)
             VALUES
                (:titulo, :descricao, :imagem, :evento_id, :ordem, :ativa)',
            self::parametros($dados)
        );
    }

    public static function atualizar(int $id, array $dados): int
    {
        return Database::execute(
            'UPDATE galeria
                SET titulo = :titulo,
                    descricao = :descricao,
                    imagem = :imagem,
                    evento_id = :evento_id,
                    ordem = :ordem,
                    ativa = :ativa
              WHERE id = :id',
            array_merge(self::parametros($dados), [':id' => $id])
        );
    }

    public static function excluir(int $id): int
    {
        return Database::execute('DELETE FROM galeria WHERE id = ?', [$id]);
    }

    private static function parametros(array $dados): array
    {
        return [
            ':titulo'    => $dados['titulo'],
            ':descricao' => $dados['descricao'],
            ':imagem'    => $dados['imagem'],
            ':evento_id' => $dados['evento_id'],
            ':ordem'     => $dados['ordem'],
            ':ativa'     => $dados['ativa'],
        ];
    }
}
