<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Concerns\ScopedByCongregacao;
use App\Core\Database;

final class Evento
{
    use ScopedByCongregacao;

    private const SITUACOES = ['PLANEJADO', 'EM_ANDAMENTO', 'CONCLUIDO', 'CANCELADO'];

    public static function situacoes(): array
    {
        return self::SITUACOES;
    }

    public static function todos(): array
    {
        $sql = "SELECT e.*, t.`nome` AS tipo_nome, c.`nome` AS congregacao_nome, u.`nome` AS responsavel_nome
                  FROM `eventos` e
                  LEFT JOIN `tipos_eventos` t ON t.`id` = e.`tipo_id`
                  LEFT JOIN `congregacoes` c ON c.`id` = e.`congregacao_id`
                  LEFT JOIN `usuarios` u ON u.`id` = e.`responsavel_id`";
        $parametros = [];

        $escopo = self::escopoCongregacao();
        if ($escopo !== null) {
            $sql .= ' WHERE e.`congregacao_id` = ?';
            $parametros[] = $escopo;
        }

        $sql .= ' ORDER BY e.`data_inicio` DESC, e.`id` DESC';

        return Database::fetchAll($sql, $parametros);
    }

    public static function buscar(int $id): ?array
    {
        $sql = 'SELECT * FROM `eventos` WHERE `id` = ?';
        $parametros = [$id];

        $escopo = self::escopoCongregacao();
        if ($escopo !== null) {
            $sql .= ' AND `congregacao_id` = ?';
            $parametros[] = $escopo;
        }

        return Database::fetch($sql . ' LIMIT 1', $parametros);
    }

    public static function tipos(): array
    {
        return Database::fetchAll("SELECT `id`, `nome` FROM `tipos_eventos` WHERE `ativo` = 1 ORDER BY `nome`");
    }

    public static function congregacoes(): array
    {
        $sql = "SELECT `id`, `nome` FROM `congregacoes` WHERE `situacao` = 'ATIVA'";
        $parametros = [];

        $escopo = self::escopoCongregacao();
        if ($escopo !== null) {
            $sql .= ' AND `id` = ?';
            $parametros[] = $escopo;
        }

        return Database::fetchAll($sql . ' ORDER BY `nome`', $parametros);
    }

    public static function responsaveis(): array
    {
        $sql = 'SELECT `id`, `nome` FROM `usuarios` WHERE `ativo` = 1';
        $parametros = [];

        $escopo = self::escopoCongregacao();
        if ($escopo !== null) {
            $sql .= ' AND `congregacao_id` = ?';
            $parametros[] = $escopo;
        }

        return Database::fetchAll($sql . ' ORDER BY `nome`', $parametros);
    }

    public static function tipoExiste(int $id): bool
    {
        return Database::fetch('SELECT `id` FROM `tipos_eventos` WHERE `id` = ? AND `ativo` = 1 LIMIT 1', [$id]) !== null;
    }

    public static function congregacaoExiste(int $id): bool
    {
        return Database::fetch("SELECT `id` FROM `congregacoes` WHERE `id` = ? AND `situacao` = 'ATIVA' LIMIT 1", [$id]) !== null;
    }

    public static function responsavelExiste(int $id): bool
    {
        return Database::fetch('SELECT `id` FROM `usuarios` WHERE `id` = ? AND `ativo` = 1 LIMIT 1', [$id]) !== null;
    }

    public static function congregacaoPermitida(?int $id): bool
    {
        return self::emEscopo($id);
    }

    public static function temEscopo(): bool
    {
        return self::escopoCongregacao() !== null;
    }

    public static function criar(array $dados): int
    {
        return (int) Database::insert(
            'INSERT INTO `eventos`
                (`titulo`, `descricao`, `tipo_id`, `data_inicio`, `data_fim`, `horario`, `local_evento`,
                 `endereco`, `foto`, `responsavel_id`, `congregacao_id`, `publicar_site`, `destaque`, `situacao`)
             VALUES
                (:titulo, :descricao, :tipo_id, :data_inicio, :data_fim, :horario, :local_evento,
                 :endereco, :foto, :responsavel_id, :congregacao_id, :publicar_site, :destaque, :situacao)',
            self::parametros($dados)
        );
    }

    public static function atualizar(int $id, array $dados): int
    {
        return Database::execute(
            'UPDATE `eventos`
                SET `titulo` = :titulo,
                    `descricao` = :descricao,
                    `tipo_id` = :tipo_id,
                    `data_inicio` = :data_inicio,
                    `data_fim` = :data_fim,
                    `horario` = :horario,
                    `local_evento` = :local_evento,
                    `endereco` = :endereco,
                    `foto` = :foto,
                    `responsavel_id` = :responsavel_id,
                    `congregacao_id` = :congregacao_id,
                    `publicar_site` = :publicar_site,
                    `destaque` = :destaque,
                    `situacao` = :situacao
              WHERE `id` = :id',
            array_merge(self::parametros($dados), [':id' => $id])
        );
    }

    public static function atualizarFoto(int $id, ?string $foto): int
    {
        return Database::execute('UPDATE `eventos` SET `foto` = :foto WHERE `id` = :id', [
            ':foto' => $foto,
            ':id'   => $id,
        ]);
    }

    public static function excluir(int $id): int
    {
        if (self::buscar($id) === null) {
            return 0;
        }

        return Database::execute('DELETE FROM `eventos` WHERE `id` = ?', [$id]);
    }

    private static function parametros(array $dados): array
    {
        return [
            ':titulo'         => $dados['titulo'],
            ':descricao'      => $dados['descricao'],
            ':tipo_id'        => $dados['tipo_id'],
            ':data_inicio'    => $dados['data_inicio'],
            ':data_fim'       => $dados['data_fim'],
            ':horario'        => $dados['horario'],
            ':local_evento'   => $dados['local_evento'],
            ':endereco'       => $dados['endereco'],
            ':foto'           => $dados['foto'],
            ':responsavel_id' => $dados['responsavel_id'],
            ':congregacao_id' => $dados['congregacao_id'],
            ':publicar_site'  => $dados['publicar_site'],
            ':destaque'       => $dados['destaque'],
            ':situacao'       => $dados['situacao'],
        ];
    }
}
