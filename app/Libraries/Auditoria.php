<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Models\AuditoriaModel;

/**
 * Facade para registrar ações de auditoria de forma simples:
 *   (new Auditoria())->log('editar', 'usuarios', $id, $antes, $depois);
 */
class Auditoria
{
    public function log(string $acao, string $modulo, ?int $registroId = null, ?array $anteriores = null, ?array $novos = null): void
    {
        try {
            model(AuditoriaModel::class)->registrar($acao, $modulo, $registroId, $anteriores, $novos);
        } catch (\Throwable $e) {
            log_message('error', 'Falha ao registrar auditoria: {msg}', ['msg' => $e->getMessage()]);
        }
    }
}
