<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Pagina
{
    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch(
            'SELECT * FROM `paginas` WHERE `slug` = ? AND `ativa` = 1 LIMIT 1',
            [$slug]
        );
    }

    public static function todas(): array
    {
        return Database::fetchAll('SELECT * FROM `paginas` WHERE `ativa` = 1 ORDER BY `ordem`, `id`');
    }

    public static function buscarPorSlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM `paginas` WHERE `slug` = ? LIMIT 1', [$slug]);
    }

    public static function salvarPorSlug(string $slug, array $dados): int
    {
        return Database::execute(
            'INSERT INTO `paginas` (`slug`, `titulo`, `conteudo`, `meta_descricao`, `imagem`, `ativa`)
             VALUES (:slug, :titulo, :conteudo, :meta_descricao, :imagem, :ativa)
             ON DUPLICATE KEY UPDATE
                `titulo` = VALUES(`titulo`),
                `conteudo` = VALUES(`conteudo`),
                `meta_descricao` = VALUES(`meta_descricao`),
                `imagem` = VALUES(`imagem`),
                `ativa` = VALUES(`ativa`)',
            [
                ':slug'           => $slug,
                ':titulo'         => $dados['titulo'],
                ':conteudo'       => $dados['conteudo'],
                ':meta_descricao' => $dados['meta_descricao'],
                ':imagem'         => $dados['imagem'],
                ':ativa'          => $dados['ativa'],
            ]
        );
    }
}
