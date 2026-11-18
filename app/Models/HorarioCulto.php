<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class HorarioCulto
{
    private const ORDEM_DIAS = "FIELD(`dia_semana`, 'DOMINGO', 'SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA', 'SABADO')";

    public static function todos(): array
    {
        return Database::fetchAll(
            'SELECT * FROM `horarios_cultos` ORDER BY ' . self::ORDEM_DIAS . ', `horario`, `id`'
        );
    }

    public static function publicos(): array
    {
        return Database::fetchAll(
            'SELECT * FROM `horarios_cultos` WHERE `ativo` = 1 ORDER BY ' . self::ORDEM_DIAS . ', `horario`, `id`'
        );
    }

    public static function buscar(int $id): ?array
    {
        return Database::fetch('SELECT * FROM `horarios_cultos` WHERE `id` = ? LIMIT 1', [$id]);
    }

    public static function criar(array $dados): int
    {
        return (int) Database::insert(
            'INSERT INTO `horarios_cultos`
                (`local_tipo`, `local_id`, `congregacao_id`, `dia_semana`, `horario`, `descricao`, `duracao_minutos`, `ativo`)
             VALUES
                (:local_tipo, :local_id, :congregacao_id, :dia_semana, :horario, :descricao, :duracao_minutos, :ativo)',
            self::parametros($dados)
        );
    }

    public static function atualizar(int $id, array $dados): int
    {
        return Database::execute(
            'UPDATE `horarios_cultos`
                SET `local_tipo` = :local_tipo,
                    `local_id` = :local_id,
                    `congregacao_id` = :congregacao_id,
                    `dia_semana` = :dia_semana,
                    `horario` = :horario,
                    `descricao` = :descricao,
                    `duracao_minutos` = :duracao_minutos,
                    `ativo` = :ativo
              WHERE `id` = :id',
            array_merge(self::parametros($dados), [':id' => $id])
        );
    }

    public static function excluir(int $id): int
    {
        return Database::execute('DELETE FROM `horarios_cultos` WHERE `id` = ?', [$id]);
    }

    private static function parametros(array $dados): array
    {
        return [
            ':local_tipo'      => $dados['local_tipo'],
            ':local_id'        => $dados['local_id'],
            ':congregacao_id'  => $dados['congregacao_id'],
            ':dia_semana'      => $dados['dia_semana'],
            ':horario'         => $dados['horario'],
            ':descricao'       => $dados['descricao'],
            ':duracao_minutos' => $dados['duracao_minutos'],
            ':ativo'           => $dados['ativo'],
        ];
    }
}
