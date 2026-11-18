<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Registros de acesso e auditoria de acoes sensiveis.
 */
final class Audit
{
    public static function access(?int $usuarioId, string $acao, string $detalhes = ''): void
    {
        $request = Request::capture();

        Database::execute(
            'INSERT INTO logs_acesso
                (usuario_id, acao, ip, user_agent, detalhes)
             VALUES
                (:usuario_id, :acao, :ip, :user_agent, :detalhes)',
            [
                ':usuario_id' => $usuarioId,
                ':acao'       => $acao,
                ':ip'         => mb_substr($request->ip(), 0, 45),
                ':user_agent' => mb_substr($request->userAgent(), 0, 255),
                ':detalhes'   => mb_substr($detalhes, 0, 255),
            ]
        );
    }

    public static function record(
        string $acao,
        string $modulo,
        array $dadosNovos = [],
        array $dadosAnteriores = [],
        ?string $tabela = null,
        ?string $registroId = null
    ): void {
        $request = Request::capture();

        Database::execute(
            'INSERT INTO auditoria
                (usuario_id, acao, modulo, tabela, registro_id,
                 dados_anteriores, dados_novos, ip, user_agent)
             VALUES
                (:usuario_id, :acao, :modulo, :tabela, :registro_id,
                 :dados_anteriores, :dados_novos, :ip, :user_agent)',
            [
                ':usuario_id'       => Auth::id(),
                ':acao'             => mb_substr($acao, 0, 80),
                ':modulo'           => mb_substr($modulo, 0, 60),
                ':tabela'           => $tabela === null ? null : mb_substr($tabela, 0, 80),
                ':registro_id'      => $registroId === null ? null : mb_substr($registroId, 0, 60),
                ':dados_anteriores' => $dadosAnteriores === [] ? null : json_encode($dadosAnteriores, JSON_UNESCAPED_UNICODE),
                ':dados_novos'      => $dadosNovos === [] ? null : json_encode($dadosNovos, JSON_UNESCAPED_UNICODE),
                ':ip'               => mb_substr($request->ip(), 0, 45),
                ':user_agent'       => mb_substr($request->userAgent(), 0, 255),
            ]
        );
    }
}
