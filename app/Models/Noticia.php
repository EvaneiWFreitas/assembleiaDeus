<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Noticia
{
    public static function todas(): array
    {
        return Database::fetchAll(
            'SELECT n.*, u.nome AS autor_nome
               FROM noticias n
               LEFT JOIN usuarios u ON u.id = n.autor_id
              ORDER BY n.publicada_em DESC, n.created_at DESC'
        );
    }

    public static function buscar(int $id): ?array
    {
        return Database::fetch('SELECT * FROM noticias WHERE id = ? LIMIT 1', [$id]);
    }

    public static function criar(array $dados): int
    {
        return (int) Database::insert(
            'INSERT INTO noticias
                (titulo, slug, resumo, conteudo, imagem, video, autor_id, publicada_em, publicada, destaque)
             VALUES
                (:titulo, :slug, :resumo, :conteudo, :imagem, :video, :autor_id, :publicada_em, :publicada, :destaque)',
            self::parametros($dados)
        );
    }

    public static function atualizar(int $id, array $dados): int
    {
        return Database::execute(
            'UPDATE noticias
                SET titulo = :titulo,
                    slug = :slug,
                    resumo = :resumo,
                    conteudo = :conteudo,
                    imagem = :imagem,
                    video = :video,
                    autor_id = :autor_id,
                    publicada_em = :publicada_em,
                    publicada = :publicada,
                    destaque = :destaque
              WHERE id = :id',
            array_merge(self::parametros($dados), [':id' => $id])
        );
    }

    public static function atualizarCampo(int $id, string $campo, mixed $valor): int
    {
        $permitidos = ['imagem', 'video'];
        if (!in_array($campo, $permitidos, true)) {
            return 0;
        }

        return Database::execute(
            'UPDATE noticias SET ' . $campo . ' = :valor WHERE id = :id',
            [':valor' => $valor, ':id' => $id]
        );
    }

    public static function excluir(int $id): int
    {
        return Database::execute('DELETE FROM noticias WHERE id = ?', [$id]);
    }

    public static function slugExiste(string $slug, ?int $id = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM noticias WHERE slug = ?';
        $params = [$slug];
        if ($id !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $id;
        }

        return (int) Database::fetchColumn($sql, $params) > 0;
    }

    public static function gerarSlug(string $titulo): string
    {
        $slug = mb_strtolower(trim($titulo));
        $slug = preg_replace('/[áàãâä]/u', 'a', $slug) ?? $slug;
        $slug = preg_replace('/[éèêë]/u', 'e', $slug) ?? $slug;
        $slug = preg_replace('/[íìîï]/u', 'i', $slug) ?? $slug;
        $slug = preg_replace('/[óòõôö]/u', 'o', $slug) ?? $slug;
        $slug = preg_replace('/[úùûü]/u', 'u', $slug) ?? $slug;
        $slug = preg_replace('/[ç]/u', 'c', $slug) ?? $slug;
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? $slug;
        $slug = trim($slug, '-');

        return $slug !== '' ? mb_substr($slug, 0, 190) : 'noticia';
    }

    private static function parametros(array $dados): array
    {
        return [
            ':titulo'       => $dados['titulo'],
            ':slug'         => $dados['slug'],
            ':resumo'       => $dados['resumo'],
            ':conteudo'     => $dados['conteudo'],
            ':imagem'       => $dados['imagem'],
            ':video'        => $dados['video'],
            ':autor_id'     => $dados['autor_id'],
            ':publicada_em' => $dados['publicada_em'],
            ':publicada'    => $dados['publicada'],
            ':destaque'     => $dados['destaque'],
        ];
    }
}
