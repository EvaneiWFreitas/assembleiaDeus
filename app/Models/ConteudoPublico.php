<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Consultas utilizadas pelas paginas publicas do site.
 */
final class ConteudoPublico
{
    public static function igrejas(): array
    {
        return Database::fetchAll(
            "SELECT * FROM `igrejas_matriz` WHERE `situacao` = 'ATIVA' ORDER BY `nome`"
        );
    }

    public static function congregacoes(): array
    {
        return Database::fetchAll(
            "SELECT c.*, i.`nome` AS igreja_nome
               FROM `congregacoes` c
               LEFT JOIN `igrejas_matriz` i ON i.`id` = c.`igreja_matriz_id`
              WHERE c.`situacao` = 'ATIVA'
              ORDER BY c.`nome`"
        );
    }

    public static function pontos(): array
    {
        return Database::fetchAll(
            "SELECT p.*, c.`nome` AS congregacao_nome
               FROM `pontos_pregacao` p
               LEFT JOIN `congregacoes` c ON c.`id` = p.`congregacao_id`
              WHERE p.`situacao` = 'ATIVO'
              ORDER BY p.`nome`"
        );
    }

    public static function horarios(): array
    {
        return HorarioCulto::publicos();
    }

    public static function eventos(int $limit = 6, bool $proximos = true): array
    {
        $where = $proximos ? 'AND DATE(e.`data_inicio`) >= CURDATE()' : '';
        $sql   = "SELECT e.*, t.`nome` AS tipo_nome, t.`cor` AS tipo_cor, c.`nome` AS congregacao_nome
                    FROM `eventos` e
                    LEFT JOIN `tipos_eventos` t ON t.`id` = e.`tipo_id`
                    LEFT JOIN `congregacoes` c ON c.`id` = e.`congregacao_id`
                   WHERE e.`publicar_site` = 1 {$where}
                   ORDER BY e.`destaque` DESC, e.`data_inicio` ASC
                   LIMIT {$limit}";

        return Database::fetchAll($sql);
    }

    public static function evento(int $id): ?array
    {
        return Database::fetch(
            "SELECT e.*, t.`nome` AS tipo_nome, c.`nome` AS congregacao_nome
               FROM `eventos` e
               LEFT JOIN `tipos_eventos` t ON t.`id` = e.`tipo_id`
               LEFT JOIN `congregacoes` c ON c.`id` = e.`congregacao_id`
              WHERE e.`id` = ? AND e.`publicar_site` = 1
              LIMIT 1",
            [$id]
        );
    }

    public static function noticias(int $limit = 4): array
    {
        return Database::fetchAll(
            'SELECT * FROM `noticias` WHERE `publicada` = 1 ORDER BY `destaque` DESC, `publicada_em` DESC LIMIT ' . $limit
        );
    }

    public static function noticia(string $slug): ?array
    {
        return Database::fetch(
            'SELECT * FROM `noticias` WHERE `slug` = ? AND `publicada` = 1 LIMIT 1',
            [$slug]
        );
    }

    public static function galeria(int $limit = 12): array
    {
        return Database::fetchAll(
            'SELECT * FROM `galeria` WHERE `ativa` = 1 ORDER BY `ordem`, `id` LIMIT ' . $limit
        );
    }

    public static function redesSociais(): array
    {
        return Database::fetchAll('SELECT * FROM `redes_sociais` WHERE `ativa` = 1 ORDER BY `ordem`');
    }

    public static function estatisticas(): array
    {
        return [
            'membros'      => (int) Database::fetchColumn("SELECT COUNT(*) FROM `membros` WHERE `situacao` = 'ATIVO'"),
            'obreiros'     => (int) Database::fetchColumn("SELECT COUNT(*) FROM `obreiros` WHERE `situacao` = 'ATIVO'"),
            'congregacoes' => (int) Database::fetchColumn("SELECT COUNT(*) FROM `congregacoes` WHERE `situacao` = 'ATIVA'"),
            'pontos'       => (int) Database::fetchColumn("SELECT COUNT(*) FROM `pontos_pregacao` WHERE `situacao` = 'ATIVO'"),
        ];
    }
}
